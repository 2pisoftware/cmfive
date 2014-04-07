<?php











namespace Composer\Repository\Vcs;

use Composer\Config;
use Composer\Downloader\TransportException;
use Composer\Json\JsonFile;
use Composer\Cache;
use Composer\IO\IOInterface;
use Composer\Util\GitHub;




class GitHubDriver extends VcsDriver
{
protected $cache;
protected $owner;
protected $repository;
protected $tags;
protected $branches;
protected $rootIdentifier;
protected $hasIssues;
protected $infoCache = array();
protected $isPrivate = false;






protected $gitDriver;




public function initialize()
{
preg_match('#^(?:(?:https?|git)://([^/]+)/|git@([^:]+):)([^/]+)/(.+?)(?:\.git)?$#', $this->url, $match);
$this->owner = $match[3];
$this->repository = $match[4];
$this->originUrl = !empty($match[1]) ? $match[1] : $match[2];
$this->cache = new Cache($this->io, $this->config->get('cache-repo-dir').'/'.$this->originUrl.'/'.$this->owner.'/'.$this->repository);

$this->fetchRootIdentifier();
}




public function getRootIdentifier()
{
if ($this->gitDriver) {
return $this->gitDriver->getRootIdentifier();
}

return $this->rootIdentifier;
}




public function getUrl()
{
if ($this->gitDriver) {
return $this->gitDriver->getUrl();
}

return 'https://' . $this->originUrl . '/'.$this->owner.'/'.$this->repository.'.git';
}




protected function getApiUrl()
{
if ('github.com' === $this->originUrl) {
$apiUrl = 'api.github.com';
} else {
$apiUrl = $this->originUrl . '/api/v3';
}

return 'https://' . $apiUrl;
}




public function getSource($identifier)
{
if ($this->gitDriver) {
return $this->gitDriver->getSource($identifier);
}
if ($this->isPrivate) {

 
 $url = $this->generateSshUrl();
} else {
$url = $this->getUrl();
}

return array('type' => 'git', 'url' => $url, 'reference' => $identifier);
}




public function getDist($identifier)
{
if ($this->gitDriver) {
return $this->gitDriver->getDist($identifier);
}

$url = $this->getApiUrl() . '/repos/'.$this->owner.'/'.$this->repository.'/zipball/'.$identifier;

return array('type' => 'zip', 'url' => $url, 'reference' => $identifier, 'shasum' => '');
}




public function getComposerInformation($identifier)
{
if ($this->gitDriver) {
return $this->gitDriver->getComposerInformation($identifier);
}

if (preg_match('{[a-f0-9]{40}}i', $identifier) && $res = $this->cache->read($identifier)) {
$this->infoCache[$identifier] = JsonFile::parseJson($res);
}

if (!isset($this->infoCache[$identifier])) {
$notFoundRetries = 2;
while ($notFoundRetries) {
try {
$resource = $this->getApiUrl() . '/repos/'.$this->owner.'/'.$this->repository.'/contents/composer.json?ref='.urlencode($identifier);
$composer = JsonFile::parseJson($this->getContents($resource));
if (empty($composer['content']) || $composer['encoding'] !== 'base64' || !($composer = base64_decode($composer['content']))) {
throw new \RuntimeException('Could not retrieve composer.json from '.$resource);
}
break;
} catch (TransportException $e) {
if (404 !== $e->getCode()) {
throw $e;
}


 
 $notFoundRetries--;
$composer = false;
}
}

if ($composer) {
$composer = JsonFile::parseJson($composer, $resource);

if (!isset($composer['time'])) {
$resource = $this->getApiUrl() . '/repos/'.$this->owner.'/'.$this->repository.'/commits/'.urlencode($identifier);
$commit = JsonFile::parseJson($this->getContents($resource), $resource);
$composer['time'] = $commit['commit']['committer']['date'];
}
if (!isset($composer['support']['source'])) {
$label = array_search($identifier, $this->getTags()) ?: array_search($identifier, $this->getBranches()) ?: $identifier;
$composer['support']['source'] = sprintf('https://%s/%s/%s/tree/%s', $this->originUrl, $this->owner, $this->repository, $label);
}
if (!isset($composer['support']['issues']) && $this->hasIssues) {
$composer['support']['issues'] = sprintf('https://%s/%s/%s/issues', $this->originUrl, $this->owner, $this->repository);
}
}

if (preg_match('{[a-f0-9]{40}}i', $identifier)) {
$this->cache->write($identifier, json_encode($composer));
}

$this->infoCache[$identifier] = $composer;
}

return $this->infoCache[$identifier];
}




public function getTags()
{
if ($this->gitDriver) {
return $this->gitDriver->getTags();
}
if (null === $this->tags) {
$resource = $this->getApiUrl() . '/repos/'.$this->owner.'/'.$this->repository.'/tags';
$tagsData = JsonFile::parseJson($this->getContents($resource), $resource);
$this->tags = array();
foreach ($tagsData as $tag) {
$this->tags[$tag['name']] = $tag['commit']['sha'];
}
}

return $this->tags;
}




public function getBranches()
{
if ($this->gitDriver) {
return $this->gitDriver->getBranches();
}
if (null === $this->branches) {
$resource = $this->getApiUrl() . '/repos/'.$this->owner.'/'.$this->repository.'/git/refs/heads';
$branchData = JsonFile::parseJson($this->getContents($resource), $resource);
$this->branches = array();
foreach ($branchData as $branch) {
$name = substr($branch['ref'], 11);
$this->branches[$name] = $branch['object']['sha'];
}
}

return $this->branches;
}




public static function supports(IOInterface $io, Config $config, $url, $deep = false)
{
if (!preg_match('#^((?:https?|git)://([^/]+)/|git@([^:]+):)([^/]+)/(.+?)(?:\.git)?$#', $url, $matches)) {
return false;
}

$originUrl = !empty($matches[2]) ? $matches[2] : $matches[3];
if (!in_array($originUrl, $config->get('github-domains'))) {
return false;
}

if (!extension_loaded('openssl')) {
if ($io->isVerbose()) {
$io->write('Skipping GitHub driver for '.$url.' because the OpenSSL PHP extension is missing.');
}

return false;
}

return true;
}






protected function generateSshUrl()
{
return 'git@' . $this->originUrl . ':'.$this->owner.'/'.$this->repository.'.git';
}




protected function getContents($url, $fetchingRepoData = false)
{
try {
return parent::getContents($url);
} catch (TransportException $e) {
$gitHubUtil = new GitHub($this->io, $this->config, $this->process, $this->remoteFilesystem);

switch ($e->getCode()) {
case 401:
case 404:

 if (!$fetchingRepoData) {
throw $e;
}

if ($gitHubUtil->authorizeOAuth($this->originUrl)) {
return parent::getContents($url);
}

if (!$this->io->isInteractive()) {
return $this->attemptCloneFallback();
}

$gitHubUtil->authorizeOAuthInteractively($this->originUrl, 'Your GitHub credentials are required to fetch private repository metadata (<info>'.$this->url.'</info>)');

return parent::getContents($url);

case 403:
if (!$this->io->hasAuthentication($this->originUrl) && $gitHubUtil->authorizeOAuth($this->originUrl)) {
return parent::getContents($url);
}

if (!$this->io->isInteractive() && $fetchingRepoData) {
return $this->attemptCloneFallback();
}

$rateLimited = false;
foreach ($e->getHeaders() as $header) {
if (preg_match('{^X-RateLimit-Remaining: *0$}i', trim($header))) {
$rateLimited = true;
}
}

if (!$this->io->hasAuthentication($this->originUrl)) {
if (!$this->io->isInteractive()) {
$this->io->write('<error>GitHub API limit exhausted. Failed to get metadata for the '.$this->url.' repository, try running in interactive mode so that you can enter your GitHub credentials to increase the API limit</error>');
throw $e;
}

$gitHubUtil->authorizeOAuthInteractively($this->originUrl, 'API limit exhausted. Enter your GitHub credentials to get a larger API limit (<info>'.$this->url.'</info>)');

return parent::getContents($url);
}

if ($rateLimited) {
$rateLimit = $this->getRateLimit($e->getHeaders());
$this->io->write(sprintf(
'<error>GitHub API limit (%d calls/hr) is exhausted. You are already authorized so you have to wait until %s before doing more requests</error>',
$rateLimit['limit'],
$rateLimit['reset']
));
}

throw $e;

default:
throw $e;
}
}
}








protected function getRateLimit(array $headers)
{
$rateLimit = array(
'limit' => '?',
'reset' => '?',
);

foreach ($headers as $header) {
$header = trim($header);
if (false === strpos($header, 'X-RateLimit-')) {
continue;
}
list($type, $value) = explode(':', $header, 2);
switch ($type) {
case 'X-RateLimit-Limit':
$rateLimit['limit'] = (int) trim($value);
break;
case 'X-RateLimit-Reset':
$rateLimit['reset'] = date('Y-m-d H:i:s', (int) trim($value));
break;
}
}

return $rateLimit;
}






protected function fetchRootIdentifier()
{
$repoDataUrl = $this->getApiUrl() . '/repos/'.$this->owner.'/'.$this->repository;

$repoData = JsonFile::parseJson($this->getContents($repoDataUrl, true), $repoDataUrl);
if (null === $repoData && null !== $this->gitDriver) {
return;
}

$this->isPrivate = !empty($repoData['private']);
if (isset($repoData['default_branch'])) {
$this->rootIdentifier = $repoData['default_branch'];
} elseif (isset($repoData['master_branch'])) {
$this->rootIdentifier = $repoData['master_branch'];
} else {
$this->rootIdentifier = 'master';
}
$this->hasIssues = !empty($repoData['has_issues']);
}

protected function attemptCloneFallback()
{
$this->isPrivate = true;

try {

 
 
 
 $this->gitDriver = new GitDriver(
array('url' => $this->generateSshUrl()),
$this->io,
$this->config,
$this->process,
$this->remoteFilesystem
);
$this->gitDriver->initialize();

return;
} catch (\RuntimeException $e) {
$this->gitDriver = null;

$this->io->write('<error>Failed to clone the '.$this->generateSshUrl().' repository, try running in interactive mode so that you can enter your GitHub credentials</error>');
throw $e;
}
}
}

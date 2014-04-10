<?php











namespace Composer\Package\Loader;

use Composer\Package;
use Composer\Package\AliasPackage;
use Composer\Package\RootAliasPackage;
use Composer\Package\RootPackageInterface;
use Composer\Package\Version\VersionParser;





class ArrayLoader implements LoaderInterface
{
protected $versionParser;

public function __construct(VersionParser $parser = null)
{
if (!$parser) {
$parser = new VersionParser;
}
$this->versionParser = $parser;
}

public function load(array $config, $class = 'Composer\Package\CompletePackage')
{
if (!isset($config['name'])) {
throw new \UnexpectedValueException('Unknown package has no name defined ('.json_encode($config).').');
}
if (!isset($config['version'])) {
throw new \UnexpectedValueException('Package '.$config['name'].' has no version defined.');
}


 if (isset($config['version_normalized'])) {
$version = $config['version_normalized'];
} else {
$version = $this->versionParser->normalize($config['version']);
}
$package = new $class($config['name'], $version, $config['version']);
$package->setType(isset($config['type']) ? strtolower($config['type']) : 'library');

if (isset($config['target-dir'])) {
$package->setTargetDir($config['target-dir']);
}

if (isset($config['extra']) && is_array($config['extra'])) {
$package->setExtra($config['extra']);
}

if (isset($config['bin'])) {
if (!is_array($config['bin'])) {
throw new \UnexpectedValueException('Package '.$config['name'].'\'s bin key should be an array, '.gettype($config['bin']).' given.');
}
foreach ($config['bin'] as $key => $bin) {
$config['bin'][$key]= ltrim($bin, '/');
}
$package->setBinaries($config['bin']);
}

if (isset($config['installation-source'])) {
$package->setInstallationSource($config['installation-source']);
}

if (isset($config['source'])) {
if (!isset($config['source']['type']) || !isset($config['source']['url']) || !isset($config['source']['reference'])) {
throw new \UnexpectedValueException(sprintf(
"Package %s's source key should be specified as {\"type\": ..., \"url\": ..., \"reference\": ...},\n%s given.",
$config['name'],
json_encode($config['source'])
));
}
$package->setSourceType($config['source']['type']);
$package->setSourceUrl($config['source']['url']);
$package->setSourceReference($config['source']['reference']);
}

if (isset($config['dist'])) {
if (!isset($config['dist']['type'])
|| !isset($config['dist']['url'])) {
throw new \UnexpectedValueException(sprintf(
"Package %s's dist key should be specified as ".
"{\"type\": ..., \"url\": ..., \"reference\": ..., \"shasum\": ...},\n%s given.",
$config['name'],
json_encode($config['dist'])
));
}
$package->setDistType($config['dist']['type']);
$package->setDistUrl($config['dist']['url']);
$package->setDistReference(isset($config['dist']['reference']) ? $config['dist']['reference'] : null);
$package->setDistSha1Checksum(isset($config['dist']['shasum']) ? $config['dist']['shasum'] : null);
}

foreach (Package\BasePackage::$supportedLinkTypes as $type => $opts) {
if (isset($config[$type])) {
$method = 'set'.ucfirst($opts['method']);
$package->{$method}(
$this->versionParser->parseLinks(
$package->getName(),
$package->getPrettyVersion(),
$opts['description'],
$config[$type]
)
);
}
}

if (isset($config['suggest']) && is_array($config['suggest'])) {
foreach ($config['suggest'] as $target => $reason) {
if ('self.version' === trim($reason)) {
$config['suggest'][$target] = $package->getPrettyVersion();
}
}
$package->setSuggests($config['suggest']);
}

if (isset($config['autoload'])) {
$package->setAutoload($config['autoload']);
}

if (isset($config['include-path'])) {
$package->setIncludePaths($config['include-path']);
}

if (!empty($config['time'])) {
$time = ctype_digit($config['time']) ? '@'.$config['time'] : $config['time'];

try {
$date = new \DateTime($time, new \DateTimeZone('UTC'));
$package->setReleaseDate($date);
} catch (\Exception $e) {
}
}

if (!empty($config['notification-url'])) {
$package->setNotificationUrl($config['notification-url']);
}

if (!empty($config['archive']['exclude'])) {
$package->setArchiveExcludes($config['archive']['exclude']);
}

if ($package instanceof Package\CompletePackageInterface) {
if (isset($config['scripts']) && is_array($config['scripts'])) {
foreach ($config['scripts'] as $event => $listeners) {
$config['scripts'][$event] = (array) $listeners;
}
$package->setScripts($config['scripts']);
}

if (!empty($config['description']) && is_string($config['description'])) {
$package->setDescription($config['description']);
}

if (!empty($config['homepage']) && is_string($config['homepage'])) {
$package->setHomepage($config['homepage']);
}

if (!empty($config['keywords']) && is_array($config['keywords'])) {
$package->setKeywords($config['keywords']);
}

if (!empty($config['license'])) {
$package->setLicense(is_array($config['license']) ? $config['license'] : array($config['license']));
}

if (!empty($config['authors']) && is_array($config['authors'])) {
$package->setAuthors($config['authors']);
}

if (isset($config['support'])) {
$package->setSupport($config['support']);
}
}

if ($aliasNormalized = $this->getBranchAlias($config)) {
if ($package instanceof RootPackageInterface) {
$package = new RootAliasPackage($package, $aliasNormalized, preg_replace('{(\.9{7})+}', '.x', $aliasNormalized));
} else {
$package = new AliasPackage($package, $aliasNormalized, preg_replace('{(\.9{7})+}', '.x', $aliasNormalized));
}
}

return $package;
}







public function getBranchAlias(array $config)
{
if ('dev-' !== substr($config['version'], 0, 4)
|| !isset($config['extra']['branch-alias'])
|| !is_array($config['extra']['branch-alias'])
) {
return;
}

foreach ($config['extra']['branch-alias'] as $sourceBranch => $targetBranch) {

 if ('-dev' !== substr($targetBranch, -4)) {
continue;
}


 $validatedTargetBranch = $this->versionParser->normalizeBranch(substr($targetBranch, 0, -4));
if ('-dev' !== substr($validatedTargetBranch, -4)) {
continue;
}


 if (strtolower($config['version']) !== strtolower($sourceBranch)) {
continue;
}

return $validatedTargetBranch;
}
}
}

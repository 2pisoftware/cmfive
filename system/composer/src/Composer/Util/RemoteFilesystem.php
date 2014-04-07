<?php











namespace Composer\Util;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Downloader\TransportException;






class RemoteFilesystem
{
private $io;
private $firstCall;
private $bytesMax;
private $originUrl;
private $fileUrl;
private $fileName;
private $retry;
private $progress;
private $lastProgress;
private $options;







public function __construct(IOInterface $io, $options = array())
{
$this->io = $io;
$this->options = $options;
}












public function copy($originUrl, $fileUrl, $fileName, $progress = true, $options = array())
{
return $this->get($originUrl, $fileUrl, $options, $fileName, $progress);
}











public function getContents($originUrl, $fileUrl, $progress = true, $options = array())
{
return $this->get($originUrl, $fileUrl, $options, null, $progress);
}






public function getOptions()
{
return $this->options;
}















protected function get($originUrl, $fileUrl, $additionalOptions = array(), $fileName = null, $progress = true)
{
$this->bytesMax = 0;
$this->originUrl = $originUrl;
$this->fileUrl = $fileUrl;
$this->fileName = $fileName;
$this->progress = $progress;
$this->lastProgress = null;


 if (preg_match('{^https?://(.+):(.+)@([^/]+)}i', $fileUrl, $match)) {
$this->io->setAuthentication($originUrl, urldecode($match[1]), urldecode($match[2]));
}

$options = $this->getOptionsForUrl($originUrl, $additionalOptions);

if ($this->io->isDebug()) {
$this->io->write((substr($fileUrl, 0, 4) === 'http' ? 'Downloading ' : 'Reading ') . $fileUrl);
}
if (isset($options['github-token'])) {
$fileUrl .= (false === strpos($fileUrl, '?') ? '?' : '&') . 'access_token='.$options['github-token'];
unset($options['github-token']);
}
$ctx = StreamContextFactory::getContext($fileUrl, $options, array('notification' => array($this, 'callbackGet')));

if ($this->progress) {
$this->io->write("    Downloading: <comment>connection...</comment>", false);
}

$errorMessage = '';
$errorCode = 0;
$result = false;
set_error_handler(function ($code, $msg) use (&$errorMessage) {
if ($errorMessage) {
$errorMessage .= "\n";
}
$errorMessage .= preg_replace('{^file_get_contents\(.*?\): }', '', $msg);
});
try {
$result = file_get_contents($fileUrl, false, $ctx);
} catch (\Exception $e) {
if ($e instanceof TransportException && !empty($http_response_header[0])) {
$e->setHeaders($http_response_header);
}
}
if ($errorMessage && !ini_get('allow_url_fopen')) {
$errorMessage = 'allow_url_fopen must be enabled in php.ini ('.$errorMessage.')';
}
restore_error_handler();
if (isset($e) && !$this->retry) {
throw $e;
}


 if (!empty($http_response_header[0]) && preg_match('{^HTTP/\S+ ([45]\d\d)}i', $http_response_header[0], $match)) {
$result = false;
$errorCode = $match[1];
}


 if ($result && extension_loaded('zlib') && substr($fileUrl, 0, 4) === 'http') {
$decode = false;
foreach ($http_response_header as $header) {
if (preg_match('{^content-encoding: *gzip *$}i', $header)) {
$decode = true;
continue;
} elseif (preg_match('{^HTTP/}i', $header)) {
$decode = false;
}
}

if ($decode) {
if (version_compare(PHP_VERSION, '5.4.0', '>=')) {
$result = zlib_decode($result);
} else {

 $result = file_get_contents('compress.zlib://data:application/octet-stream;base64,'.base64_encode($result));
}
}
}

if ($this->progress) {
$this->io->overwrite("    Downloading: <comment>100%</comment>");
}


 if (false !== $result && null !== $fileName) {
if ('' === $result) {
throw new TransportException('"'.$this->fileUrl.'" appears broken, and returned an empty 200 response');
}

$errorMessage = '';
set_error_handler(function ($code, $msg) use (&$errorMessage) {
if ($errorMessage) {
$errorMessage .= "\n";
}
$errorMessage .= preg_replace('{^file_put_contents\(.*?\): }', '', $msg);
});
$result = (bool) file_put_contents($fileName, $result);
restore_error_handler();
if (false === $result) {
throw new TransportException('The "'.$this->fileUrl.'" file could not be written to '.$fileName.': '.$errorMessage);
}
}

if ($this->retry) {
$this->retry = false;

return $this->get($this->originUrl, $this->fileUrl, $additionalOptions, $this->fileName, $this->progress);
}

if (false === $result) {
$e = new TransportException('The "'.$this->fileUrl.'" file could not be downloaded: '.$errorMessage, $errorCode);
if (!empty($http_response_header[0])) {
$e->setHeaders($http_response_header);
}

throw $e;
}

return $result;
}












protected function callbackGet($notificationCode, $severity, $message, $messageCode, $bytesTransferred, $bytesMax)
{
switch ($notificationCode) {
case STREAM_NOTIFY_FAILURE:
case STREAM_NOTIFY_AUTH_REQUIRED:
if (401 === $messageCode) {
if (!$this->io->isInteractive()) {
$message = "The '" . $this->fileUrl . "' URL required authentication.\nYou must be using the interactive console";

throw new TransportException($message, 401);
}

$this->promptAuthAndRetry();
break;
}

if ($notificationCode === STREAM_NOTIFY_AUTH_REQUIRED) {
break;
}

throw new TransportException('The "'.$this->fileUrl.'" file could not be downloaded ('.trim($message).')', $messageCode);

case STREAM_NOTIFY_AUTH_RESULT:
if (403 === $messageCode) {
if (!$this->io->isInteractive() || $this->io->hasAuthentication($this->originUrl)) {
$message = "The '" . $this->fileUrl . "' URL could not be accessed: " . $message;

throw new TransportException($message, 403);
}

$this->promptAuthAndRetry();
break;
}
break;

case STREAM_NOTIFY_FILE_SIZE_IS:
if ($this->bytesMax < $bytesMax) {
$this->bytesMax = $bytesMax;
}
break;

case STREAM_NOTIFY_PROGRESS:
if ($this->bytesMax > 0 && $this->progress) {
$progression = 0;

if ($this->bytesMax > 0) {
$progression = round($bytesTransferred / $this->bytesMax * 100);
}

if ((0 === $progression % 5) && $progression !== $this->lastProgress) {
$this->lastProgress = $progression;
$this->io->overwrite("    Downloading: <comment>$progression%</comment>", false);
}
}
break;

default:
break;
}
}

protected function promptAuthAndRetry()
{
$this->io->overwrite('    Authentication required (<info>'.parse_url($this->fileUrl, PHP_URL_HOST).'</info>):');
$username = $this->io->ask('      Username: ');
$password = $this->io->askAndHideAnswer('      Password: ');
$this->io->setAuthentication($this->originUrl, $username, $password);

$this->retry = true;
throw new TransportException('RETRY');
}

protected function getOptionsForUrl($originUrl, $additionalOptions)
{
$headers = array(
sprintf(
'User-Agent: Composer/%s (%s; %s; PHP %s.%s.%s)',
Composer::VERSION === '@package_version@' ? 'source' : Composer::VERSION,
php_uname('s'),
php_uname('r'),
PHP_MAJOR_VERSION,
PHP_MINOR_VERSION,
PHP_RELEASE_VERSION
)
);

if (extension_loaded('zlib')) {
$headers[] = 'Accept-Encoding: gzip';
}

$options = array_replace_recursive($this->options, $additionalOptions);

if ($this->io->hasAuthentication($originUrl)) {
$auth = $this->io->getAuthentication($originUrl);
if ('github.com' === $originUrl && 'x-oauth-basic' === $auth['password']) {
$options['github-token'] = $auth['username'];
} else {
$authStr = base64_encode($auth['username'] . ':' . $auth['password']);
$headers[] = 'Authorization: Basic '.$authStr;
}
}

if (isset($options['http']['header']) && !is_array($options['http']['header'])) {
$options['http']['header'] = explode("\r\n", trim($options['http']['header'], "\r\n"));
}
foreach ($headers as $header) {
$options['http']['header'][] = $header;
}

return $options;
}
}

<?php











namespace Composer\Repository\Pear;

use Composer\Downloader\TransportException;












class ChannelRest10Reader extends BaseChannelReader
{
private $dependencyReader;

public function __construct($rfs)
{
parent::__construct($rfs);

$this->dependencyReader = new PackageDependencyParser();
}








public function read($baseUrl)
{
return $this->readPackages($baseUrl);
}








private function readPackages($baseUrl)
{
$result = array();

$xmlPath = '/p/packages.xml';
$xml = $this->requestXml($baseUrl, $xmlPath);
$xml->registerXPathNamespace('ns', self::ALL_PACKAGES_NS);
foreach ($xml->xpath('ns:p') as $node) {
$packageName = (string) $node;
$packageInfo = $this->readPackage($baseUrl, $packageName);
$result[] = $packageInfo;
}

return $result;
}









private function readPackage($baseUrl, $packageName)
{
$xmlPath = '/p/' . strtolower($packageName) . '/info.xml';
$xml = $this->requestXml($baseUrl, $xmlPath);
$xml->registerXPathNamespace('ns', self::PACKAGE_INFO_NS);

$channelName = (string) $xml->c;
$packageName = (string) $xml->n;
$license = (string) $xml->l;
$shortDescription = (string) $xml->s;
$description = (string) $xml->d;

return new PackageInfo(
$channelName,
$packageName,
$license,
$shortDescription,
$description,
$this->readPackageReleases($baseUrl, $packageName)
);
}










private function readPackageReleases($baseUrl, $packageName)
{
$result = array();

try {
$xmlPath = '/r/' . strtolower($packageName) . '/allreleases.xml';
$xml = $this->requestXml($baseUrl, $xmlPath);
$xml->registerXPathNamespace('ns', self::ALL_RELEASES_NS);
foreach ($xml->xpath('ns:r') as $node) {
$releaseVersion = (string) $node->v;
$releaseStability = (string) $node->s;

try {
$result[$releaseVersion] = new ReleaseInfo(
$releaseStability,
$this->readPackageReleaseDependencies($baseUrl, $packageName, $releaseVersion)
);
} catch (TransportException $exception) {
if ($exception->getCode() != 404) {
throw $exception;
}
}
}
} catch (TransportException $exception) {
if ($exception->getCode() != 404) {
throw $exception;
}
}

return $result;
}










private function readPackageReleaseDependencies($baseUrl, $packageName, $version)
{
$dependencyReader = new PackageDependencyParser();

$depthPath = '/r/' . strtolower($packageName) . '/deps.' . $version . '.txt';
$content = $this->requestContent($baseUrl, $depthPath);
$dependencyArray = unserialize($content);
$result = $dependencyReader->buildDependencyInfo($dependencyArray);

return $result;
}
}

<?php











namespace Composer\Repository\Pear;










class ChannelRest11Reader extends BaseChannelReader
{
private $dependencyReader;

public function __construct($rfs)
{
parent::__construct($rfs);

$this->dependencyReader = new PackageDependencyParser();
}








public function read($baseUrl)
{
return $this->readChannelPackages($baseUrl);
}








private function readChannelPackages($baseUrl)
{
$result = array();

$xml = $this->requestXml($baseUrl, "/c/categories.xml");
$xml->registerXPathNamespace('ns', self::ALL_CATEGORIES_NS);
foreach ($xml->xpath('ns:c') as $node) {
$categoryName = (string) $node;
$categoryPackages = $this->readCategoryPackages($baseUrl, $categoryName);
$result = array_merge($result, $categoryPackages);
}

return $result;
}









private function readCategoryPackages($baseUrl, $categoryName)
{
$result = array();

$categoryPath = '/c/'.urlencode($categoryName).'/packagesinfo.xml';
$xml = $this->requestXml($baseUrl, $categoryPath);
$xml->registerXPathNamespace('ns', self::CATEGORY_PACKAGES_INFO_NS);
foreach ($xml->xpath('ns:pi') as $node) {
$packageInfo = $this->parsePackage($node);
$result[] = $packageInfo;
}

return $result;
}







private function parsePackage($packageInfo)
{
$packageInfo->registerXPathNamespace('ns', self::CATEGORY_PACKAGES_INFO_NS);
$channelName = (string) $packageInfo->p->c;
$packageName = (string) $packageInfo->p->n;
$license = (string) $packageInfo->p->l;
$shortDescription = (string) $packageInfo->p->s;
$description = (string) $packageInfo->p->d;

$dependencies = array();
foreach ($packageInfo->xpath('ns:deps') as $node) {
$dependencyVersion = (string) $node->v;
$dependencyArray = unserialize((string) $node->d);

$dependencyInfo = $this->dependencyReader->buildDependencyInfo($dependencyArray);

$dependencies[$dependencyVersion] = $dependencyInfo;
}

$releases = array();
$releasesInfo = $packageInfo->xpath('ns:a/ns:r');
if ($releasesInfo) {
foreach ($releasesInfo as $node) {
$releaseVersion = (string) $node->v;
$releaseStability = (string) $node->s;
$releases[$releaseVersion] = new ReleaseInfo(
$releaseStability,
isset($dependencies[$releaseVersion]) ? $dependencies[$releaseVersion] : new DependencyInfo(array(), array())
);
}
}

return new PackageInfo(
$channelName,
$packageName,
$license,
$shortDescription,
$description,
$releases
);
}
}

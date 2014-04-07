<?php











namespace Composer\Repository\Pear;

use Composer\Util\RemoteFilesystem;








abstract class BaseChannelReader
{



const CHANNEL_NS = 'http://pear.php.net/channel-1.0';
const ALL_CATEGORIES_NS = 'http://pear.php.net/dtd/rest.allcategories';
const CATEGORY_PACKAGES_INFO_NS = 'http://pear.php.net/dtd/rest.categorypackageinfo';
const ALL_PACKAGES_NS = 'http://pear.php.net/dtd/rest.allpackages';
const ALL_RELEASES_NS = 'http://pear.php.net/dtd/rest.allreleases';
const PACKAGE_INFO_NS = 'http://pear.php.net/dtd/rest.package';


private $rfs;

protected function __construct(RemoteFilesystem $rfs)
{
$this->rfs = $rfs;
}









protected function requestContent($origin, $path)
{
$url = rtrim($origin, '/') . '/' . ltrim($path, '/');
$content = $this->rfs->getContents($origin, $url, false);
if (!$content) {
throw new \UnexpectedValueException('The PEAR channel at ' . $url . ' did not respond.');
}

return $content;
}









protected function requestXml($origin, $path)
{

 $xml = simplexml_load_string($this->requestContent($origin, $path), "SimpleXMLElement", LIBXML_NOERROR);

if (false == $xml) {
$url = rtrim($origin, '/') . '/' . ltrim($path, '/');
throw new \UnexpectedValueException(sprintf('The PEAR channel at ' . $origin . ' is broken. (Invalid XML at file `%s`)', $path));
}

return $xml;
}
}

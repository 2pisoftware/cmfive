<?php











namespace Composer\Repository\Pear;






class PackageInfo
{
private $channelName;
private $packageName;
private $license;
private $shortDescription;
private $description;
private $releases;









public function __construct($channelName, $packageName, $license, $shortDescription, $description, $releases)
{
$this->channelName = $channelName;
$this->packageName = $packageName;
$this->license = $license;
$this->shortDescription = $shortDescription;
$this->description = $description;
$this->releases = $releases;
}




public function getChannelName()
{
return $this->channelName;
}




public function getPackageName()
{
return $this->packageName;
}




public function getDescription()
{
return $this->description;
}




public function getShortDescription()
{
return $this->shortDescription;
}




public function getLicense()
{
return $this->license;
}




public function getReleases()
{
return $this->releases;
}
}

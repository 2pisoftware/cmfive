<?php











namespace Composer\Package;

use Composer\Package\Version\VersionParser;






class Package extends BasePackage
{
protected $type;
protected $targetDir;
protected $installationSource;
protected $sourceType;
protected $sourceUrl;
protected $sourceReference;
protected $distType;
protected $distUrl;
protected $distReference;
protected $distSha1Checksum;
protected $version;
protected $prettyVersion;
protected $releaseDate;
protected $extra = array();
protected $binaries = array();
protected $dev;
protected $stability;
protected $notificationUrl;

protected $requires = array();
protected $conflicts = array();
protected $provides = array();
protected $replaces = array();
protected $devRequires = array();
protected $suggests = array();
protected $autoload = array();
protected $includePaths = array();
protected $archiveExcludes = array();








public function __construct($name, $version, $prettyVersion)
{
parent::__construct($name);

$this->version = $version;
$this->prettyVersion = $prettyVersion;

$this->stability = VersionParser::parseStability($version);
$this->dev = $this->stability === 'dev';
}




public function isDev()
{
return $this->dev;
}




public function setType($type)
{
$this->type = $type;
}




public function getType()
{
return $this->type ?: 'library';
}




public function getStability()
{
return $this->stability;
}




public function setTargetDir($targetDir)
{
$this->targetDir = $targetDir;
}




public function getTargetDir()
{
if (null === $this->targetDir) {
return;
}

return ltrim(preg_replace('{ (?:^|[\\\\/]+) \.\.? (?:[\\\\/]+|$) (?:\.\.? (?:[\\\\/]+|$) )*}x', '/', $this->targetDir), '/');
}




public function setExtra(array $extra)
{
$this->extra = $extra;
}




public function getExtra()
{
return $this->extra;
}




public function setBinaries(array $binaries)
{
$this->binaries = $binaries;
}




public function getBinaries()
{
return $this->binaries;
}




public function setInstallationSource($type)
{
$this->installationSource = $type;
}




public function getInstallationSource()
{
return $this->installationSource;
}




public function setSourceType($type)
{
$this->sourceType = $type;
}




public function getSourceType()
{
return $this->sourceType;
}




public function setSourceUrl($url)
{
$this->sourceUrl = $url;
}




public function getSourceUrl()
{
return $this->sourceUrl;
}




public function setSourceReference($reference)
{
$this->sourceReference = $reference;
}




public function getSourceReference()
{
return $this->sourceReference;
}




public function setDistType($type)
{
$this->distType = $type;
}




public function getDistType()
{
return $this->distType;
}




public function setDistUrl($url)
{
$this->distUrl = $url;
}




public function getDistUrl()
{
return $this->distUrl;
}




public function setDistReference($reference)
{
$this->distReference = $reference;
}




public function getDistReference()
{
return $this->distReference;
}




public function setDistSha1Checksum($sha1checksum)
{
$this->distSha1Checksum = $sha1checksum;
}




public function getDistSha1Checksum()
{
return $this->distSha1Checksum;
}




public function getVersion()
{
return $this->version;
}




public function getPrettyVersion()
{
return $this->prettyVersion;
}






public function setReleaseDate(\DateTime $releaseDate)
{
$this->releaseDate = $releaseDate;
}




public function getReleaseDate()
{
return $this->releaseDate;
}






public function setRequires(array $requires)
{
$this->requires = $requires;
}




public function getRequires()
{
return $this->requires;
}






public function setConflicts(array $conflicts)
{
$this->conflicts = $conflicts;
}




public function getConflicts()
{
return $this->conflicts;
}






public function setProvides(array $provides)
{
$this->provides = $provides;
}




public function getProvides()
{
return $this->provides;
}






public function setReplaces(array $replaces)
{
$this->replaces = $replaces;
}




public function getReplaces()
{
return $this->replaces;
}






public function setDevRequires(array $devRequires)
{
$this->devRequires = $devRequires;
}




public function getDevRequires()
{
return $this->devRequires;
}






public function setSuggests(array $suggests)
{
$this->suggests = $suggests;
}




public function getSuggests()
{
return $this->suggests;
}






public function setAutoload(array $autoload)
{
$this->autoload = $autoload;
}




public function getAutoload()
{
return $this->autoload;
}






public function setIncludePaths(array $includePaths)
{
$this->includePaths = $includePaths;
}




public function getIncludePaths()
{
return $this->includePaths;
}






public function setNotificationUrl($notificationUrl)
{
$this->notificationUrl = $notificationUrl;
}




public function getNotificationUrl()
{
return $this->notificationUrl;
}






public function setArchiveExcludes(array $excludes)
{
$this->archiveExcludes = $excludes;
}




public function getArchiveExcludes()
{
return $this->archiveExcludes;
}
}

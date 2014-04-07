<?php











namespace Composer\Package\Archiver;

use Composer\Downloader\DownloadManager;
use Composer\Package\PackageInterface;
use Composer\Package\RootPackage;
use Composer\Util\Filesystem;
use Composer\Json\JsonFile;





class ArchiveManager
{
protected $downloadManager;

protected $archivers = array();




protected $overwriteFiles = true;




public function __construct(DownloadManager $downloadManager)
{
$this->downloadManager = $downloadManager;
}




public function addArchiver(ArchiverInterface $archiver)
{
$this->archivers[] = $archiver;
}








public function setOverwriteFiles($overwriteFiles)
{
$this->overwriteFiles = $overwriteFiles;

return $this;
}








public function getPackageFilename(PackageInterface $package)
{
$nameParts = array(preg_replace('#[^a-z0-9-_.]#i', '-', $package->getName()));

if (preg_match('{^[a-f0-9]{40}$}', $package->getDistReference())) {
$nameParts = array_merge($nameParts, array($package->getDistReference(), $package->getDistType()));
} else {
$nameParts = array_merge($nameParts, array($package->getPrettyVersion(), $package->getDistReference()));
}

if ($package->getSourceReference()) {
$nameParts[] = substr(sha1($package->getSourceReference()), 0, 6);
}

$name = implode('-', array_filter($nameParts, function ($p) {
return !empty($p);
}));

return str_replace('/', '-', $name);
}











public function archive(PackageInterface $package, $format, $targetDir)
{
if (empty($format)) {
throw new \InvalidArgumentException('Format must be specified');
}


 $usableArchiver = null;
foreach ($this->archivers as $archiver) {
if ($archiver->supports($format, $package->getSourceType())) {
$usableArchiver = $archiver;
break;
}
}


 if (null === $usableArchiver) {
throw new \RuntimeException(sprintf('No archiver found to support %s format', $format));
}

$filesystem = new Filesystem();
$packageName = $this->getPackageFilename($package);


 $filesystem->ensureDirectoryExists($targetDir);
$target = realpath($targetDir).'/'.$packageName.'.'.$format;
$filesystem->ensureDirectoryExists(dirname($target));

if (!$this->overwriteFiles && file_exists($target)) {
return $target;
}

if ($package instanceof RootPackage) {
$sourcePath = realpath('.');
} else {

 $sourcePath = sys_get_temp_dir().'/composer_archiver/'.$packageName;
$filesystem->ensureDirectoryExists($sourcePath);


 $this->downloadManager->download($package, $sourcePath);


 if (file_exists($composerJsonPath = $sourcePath.'/composer.json')) {
$jsonFile = new JsonFile($composerJsonPath);
$jsonData = $jsonFile->read();
if (!empty($jsonData['archive']['exclude'])) {
$package->setArchiveExcludes($jsonData['archive']['exclude']);
}
}
}


 $archivePath = $usableArchiver->archive($sourcePath, $target, $format, $package->getArchiveExcludes());


 if (!$package instanceof RootPackage) {
$filesystem->removeDirectory($sourcePath);
}

return $archivePath;
}
}

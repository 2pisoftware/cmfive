<?php











namespace Composer\Installer;

use Composer\Package\PackageInterface;
use Composer\Downloader\DownloadManager;
use Composer\Repository\InstalledRepositoryInterface;
use Composer\Util\Filesystem;







class ProjectInstaller implements InstallerInterface
{
private $installPath;
private $downloadManager;
private $filesystem;

public function __construct($installPath, DownloadManager $dm)
{
$this->installPath = rtrim(strtr($installPath, '\\', '/'), '/').'/';
$this->downloadManager = $dm;
$this->filesystem = new Filesystem;
}







public function supports($packageType)
{
return true;
}




public function isInstalled(InstalledRepositoryInterface $repo, PackageInterface $package)
{
return false;
}




public function install(InstalledRepositoryInterface $repo, PackageInterface $package)
{
$installPath = $this->installPath;
if (file_exists($installPath) && !$this->filesystem->isDirEmpty($installPath)) {
throw new \InvalidArgumentException("Project directory $installPath is not empty.");
}
if (!is_dir($installPath)) {
mkdir($installPath, 0777, true);
}
$this->downloadManager->download($package, $installPath);
}




public function update(InstalledRepositoryInterface $repo, PackageInterface $initial, PackageInterface $target)
{
throw new \InvalidArgumentException("not supported");
}




public function uninstall(InstalledRepositoryInterface $repo, PackageInterface $package)
{
throw new \InvalidArgumentException("not supported");
}







public function getInstallPath(PackageInterface $package)
{
return $this->installPath;
}
}

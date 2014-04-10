<?php











namespace Composer\Installer;

use Composer\Repository\InstalledRepositoryInterface;
use Composer\Package\PackageInterface;






class MetapackageInstaller implements InstallerInterface
{



public function supports($packageType)
{
return $packageType === 'metapackage';
}




public function isInstalled(InstalledRepositoryInterface $repo, PackageInterface $package)
{
return $repo->hasPackage($package);
}




public function install(InstalledRepositoryInterface $repo, PackageInterface $package)
{
$repo->addPackage(clone $package);
}




public function update(InstalledRepositoryInterface $repo, PackageInterface $initial, PackageInterface $target)
{
if (!$repo->hasPackage($initial)) {
throw new \InvalidArgumentException('Package is not installed: '.$initial);
}

$repo->removePackage($initial);
$repo->addPackage(clone $target);
}




public function uninstall(InstalledRepositoryInterface $repo, PackageInterface $package)
{
if (!$repo->hasPackage($package)) {
throw new \InvalidArgumentException('Package is not installed: '.$package);
}

$repo->removePackage($package);
}




public function getInstallPath(PackageInterface $package)
{
return '';
}
}

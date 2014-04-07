<?php











namespace Composer\Installer;

use Composer\Repository\InstalledRepositoryInterface;
use Composer\Package\PackageInterface;








class NoopInstaller implements InstallerInterface
{



public function supports($packageType)
{
return true;
}




public function isInstalled(InstalledRepositoryInterface $repo, PackageInterface $package)
{
return $repo->hasPackage($package);
}




public function install(InstalledRepositoryInterface $repo, PackageInterface $package)
{
if (!$repo->hasPackage($package)) {
$repo->addPackage(clone $package);
}
}




public function update(InstalledRepositoryInterface $repo, PackageInterface $initial, PackageInterface $target)
{
if (!$repo->hasPackage($initial)) {
throw new \InvalidArgumentException('Package is not installed: '.$initial);
}

$repo->removePackage($initial);
if (!$repo->hasPackage($target)) {
$repo->addPackage(clone $target);
}
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
$targetDir = $package->getTargetDir();

return $package->getPrettyName() . ($targetDir ? '/'.$targetDir : '');
}
}

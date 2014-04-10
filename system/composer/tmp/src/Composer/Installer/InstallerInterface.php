<?php











namespace Composer\Installer;

use Composer\Package\PackageInterface;
use Composer\Repository\InstalledRepositoryInterface;







interface InstallerInterface
{






public function supports($packageType);









public function isInstalled(InstalledRepositoryInterface $repo, PackageInterface $package);







public function install(InstalledRepositoryInterface $repo, PackageInterface $package);










public function update(InstalledRepositoryInterface $repo, PackageInterface $initial, PackageInterface $target);







public function uninstall(InstalledRepositoryInterface $repo, PackageInterface $package);







public function getInstallPath(PackageInterface $package);
}

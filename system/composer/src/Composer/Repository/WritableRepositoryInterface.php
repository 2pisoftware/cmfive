<?php











namespace Composer\Repository;

use Composer\Package\PackageInterface;






interface WritableRepositoryInterface extends RepositoryInterface
{



public function write();






public function addPackage(PackageInterface $package);






public function removePackage(PackageInterface $package);






public function getCanonicalPackages();




public function reload();
}

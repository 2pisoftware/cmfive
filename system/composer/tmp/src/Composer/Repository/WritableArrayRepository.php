<?php











namespace Composer\Repository;

use Composer\Package\AliasPackage;






class WritableArrayRepository extends ArrayRepository implements WritableRepositoryInterface
{



public function write()
{
}




public function reload()
{
}




public function getCanonicalPackages()
{
$packages = $this->getPackages();


 $packagesByName = array();
foreach ($packages as $package) {
if (!isset($packagesByName[$package->getName()]) || $packagesByName[$package->getName()] instanceof AliasPackage) {
$packagesByName[$package->getName()] = $package;
}
}

$canonicalPackages = array();


 foreach ($packagesByName as $package) {
while ($package instanceof AliasPackage) {
$package = $package->getAliasOf();
}

$canonicalPackages[] = $package;
}

return $canonicalPackages;
}
}

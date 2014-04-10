<?php











namespace Composer\DependencyResolver\Operation;

use Composer\Package\PackageInterface;






class UninstallOperation extends SolverOperation
{
protected $package;







public function __construct(PackageInterface $package, $reason = null)
{
parent::__construct($reason);

$this->package = $package;
}






public function getPackage()
{
return $this->package;
}






public function getJobType()
{
return 'uninstall';
}




public function __toString()
{
return 'Uninstalling '.$this->package->getPrettyName().' ('.$this->formatVersion($this->package).')';
}
}

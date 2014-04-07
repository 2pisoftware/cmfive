<?php











namespace Composer\DependencyResolver\Operation;

use Composer\Package\PackageInterface;






class InstallOperation extends SolverOperation
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
return 'install';
}




public function __toString()
{
return 'Installing '.$this->package->getPrettyName().' ('.$this->formatVersion($this->package).')';
}
}

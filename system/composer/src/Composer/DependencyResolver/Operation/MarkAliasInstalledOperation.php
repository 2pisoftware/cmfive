<?php











namespace Composer\DependencyResolver\Operation;

use Composer\Package\AliasPackage;






class MarkAliasInstalledOperation extends SolverOperation
{
protected $package;







public function __construct(AliasPackage $package, $reason = null)
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
return 'markAliasInstalled';
}




public function __toString()
{
return 'Marking '.$this->package->getPrettyName().' ('.$this->formatVersion($this->package).') as installed, alias of '.$this->package->getAliasOf()->getPrettyName().' ('.$this->formatVersion($this->package->getAliasOf()).')';
}
}

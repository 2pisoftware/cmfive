<?php











namespace Composer\DependencyResolver\Operation;

use Composer\Package\AliasPackage;






class MarkAliasUninstalledOperation extends SolverOperation
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
return 'markAliasUninstalled';
}




public function __toString()
{
return 'Marking '.$this->package->getPrettyName().' ('.$this->formatVersion($this->package).') as uninstalled, alias of '.$this->package->getAliasOf()->getPrettyName().' ('.$this->formatVersion($this->package->getAliasOf()).')';
}
}

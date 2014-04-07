<?php











namespace Composer\DependencyResolver\Operation;

use Composer\Package\PackageInterface;






class UpdateOperation extends SolverOperation
{
protected $initialPackage;
protected $targetPackage;








public function __construct(PackageInterface $initial, PackageInterface $target, $reason = null)
{
parent::__construct($reason);

$this->initialPackage = $initial;
$this->targetPackage = $target;
}






public function getInitialPackage()
{
return $this->initialPackage;
}






public function getTargetPackage()
{
return $this->targetPackage;
}






public function getJobType()
{
return 'update';
}




public function __toString()
{
return 'Updating '.$this->initialPackage->getPrettyName().' ('.$this->formatVersion($this->initialPackage).') to '.
$this->targetPackage->getPrettyName(). ' ('.$this->formatVersion($this->targetPackage).')';
}
}

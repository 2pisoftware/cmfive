<?php











namespace Composer\Repository\Pear;






class ReleaseInfo
{
private $stability;
private $dependencyInfo;





public function __construct($stability, $dependencyInfo)
{
$this->stability = $stability;
$this->dependencyInfo = $dependencyInfo;
}




public function getDependencyInfo()
{
return $this->dependencyInfo;
}




public function getStability()
{
return $this->stability;
}
}

<?php











namespace Composer\Package;






class RootPackage extends CompletePackage implements RootPackageInterface
{
protected $minimumStability = 'stable';
protected $preferStable = false;
protected $stabilityFlags = array();
protected $references = array();
protected $aliases = array();






public function setMinimumStability($minimumStability)
{
$this->minimumStability = $minimumStability;
}




public function getMinimumStability()
{
return $this->minimumStability;
}






public function setStabilityFlags(array $stabilityFlags)
{
$this->stabilityFlags = $stabilityFlags;
}




public function getStabilityFlags()
{
return $this->stabilityFlags;
}






public function setPreferStable($preferStable)
{
$this->preferStable = $preferStable;
}




public function getPreferStable()
{
return $this->preferStable;
}






public function setReferences(array $references)
{
$this->references = $references;
}




public function getReferences()
{
return $this->references;
}






public function setAliases(array $aliases)
{
$this->aliases = $aliases;
}




public function getAliases()
{
return $this->aliases;
}
}

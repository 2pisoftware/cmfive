<?php











namespace Composer\Repository\Pear;






class ChannelInfo
{
private $name;
private $alias;
private $packages;






public function __construct($name, $alias, array $packages)
{
$this->name = $name;
$this->alias = $alias;
$this->packages = $packages;
}






public function getName()
{
return $this->name;
}






public function getAlias()
{
return $this->alias;
}






public function getPackages()
{
return $this->packages;
}
}

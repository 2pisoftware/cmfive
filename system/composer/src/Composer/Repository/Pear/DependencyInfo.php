<?php











namespace Composer\Repository\Pear;






class DependencyInfo
{
private $requires;
private $optionals;





public function __construct($requires, $optionals)
{
$this->requires = $requires;
$this->optionals = $optionals;
}




public function getRequires()
{
return $this->requires;
}




public function getOptionals()
{
return $this->optionals;
}
}

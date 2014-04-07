<?php











namespace Composer\Package;

use Composer\Package\LinkConstraint\LinkConstraintInterface;
use Composer\Package\PackageInterface;






class Link
{
protected $source;
protected $target;
protected $constraint;
protected $description;
protected $prettyConstraint;










public function __construct($source, $target, LinkConstraintInterface $constraint = null, $description = 'relates to', $prettyConstraint = null)
{
$this->source = strtolower($source);
$this->target = strtolower($target);
$this->constraint = $constraint;
$this->description = $description;
$this->prettyConstraint = $prettyConstraint;
}

public function getSource()
{
return $this->source;
}

public function getTarget()
{
return $this->target;
}

public function getConstraint()
{
return $this->constraint;
}

public function getPrettyConstraint()
{
if (null === $this->prettyConstraint) {
throw new \UnexpectedValueException(sprintf('Link %s has been misconfigured and had no prettyConstraint given.', $this));
}

return $this->prettyConstraint;
}

public function __toString()
{
return $this->source.' '.$this->description.' '.$this->target.' ('.$this->constraint.')';
}

public function getPrettyString(PackageInterface $sourcePackage)
{
return $sourcePackage->getPrettyString().' '.$this->description.' '.$this->target.' '.$this->constraint->getPrettyString().'';
}
}

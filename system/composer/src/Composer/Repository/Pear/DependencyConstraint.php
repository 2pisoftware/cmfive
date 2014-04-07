<?php











namespace Composer\Repository\Pear;






class DependencyConstraint
{
private $type;
private $constraint;
private $channelName;
private $packageName;







public function __construct($type, $constraint, $channelName, $packageName)
{
$this->type = $type;
$this->constraint = $constraint;
$this->channelName = $channelName;
$this->packageName = $packageName;
}

public function getChannelName()
{
return $this->channelName;
}

public function getConstraint()
{
return $this->constraint;
}

public function getPackageName()
{
return $this->packageName;
}

public function getType()
{
return $this->type;
}
}

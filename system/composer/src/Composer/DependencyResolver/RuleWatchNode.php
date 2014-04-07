<?php











namespace Composer\DependencyResolver;








class RuleWatchNode
{
public $watch1;
public $watch2;

protected $rule;






public function __construct($rule)
{
$this->rule = $rule;

$literals = $rule->getLiterals();

$this->watch1 = count($literals) > 0 ? $literals[0] : 0;
$this->watch2 = count($literals) > 1 ? $literals[1] : 0;
}









public function watch2OnHighest(Decisions $decisions)
{
$literals = $this->rule->getLiterals();


 if ($literals < 3) {
return;
}

$watchLevel = 0;

foreach ($literals as $literal) {
$level = $decisions->decisionLevel($literal);

if ($level > $watchLevel) {
$this->watch2 = $literal;
$watchLevel = $level;
}
}
}






public function getRule()
{
return $this->rule;
}







public function getOtherWatch($literal)
{
if ($this->watch1 == $literal) {
return $this->watch2;
} else {
return $this->watch1;
}
}







public function moveWatch($from, $to)
{
if ($this->watch1 == $from) {
$this->watch1 = $to;
} else {
$this->watch2 = $to;
}
}
}

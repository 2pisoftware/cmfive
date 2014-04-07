<?php











namespace Composer\DependencyResolver;











class RuleWatchGraph
{
protected $watchChains = array();













public function insert(RuleWatchNode $node)
{
if ($node->getRule()->isAssertion()) {
return;
}

foreach (array($node->watch1, $node->watch2) as $literal) {
if (!isset($this->watchChains[$literal])) {
$this->watchChains[$literal] = new RuleWatchChain;
}

$this->watchChains[$literal]->unshift($node);
}
}
























public function propagateLiteral($decidedLiteral, $level, $decisions)
{

 
 
 $literal = -$decidedLiteral;

if (!isset($this->watchChains[$literal])) {
return null;
}

$chain = $this->watchChains[$literal];

$chain->rewind();
while ($chain->valid()) {
$node = $chain->current();
$otherWatch = $node->getOtherWatch($literal);

if (!$node->getRule()->isDisabled() && !$decisions->satisfy($otherWatch)) {
$ruleLiterals = $node->getRule()->getLiterals();

$alternativeLiterals = array_filter($ruleLiterals, function ($ruleLiteral) use ($literal, $otherWatch, $decisions) {
return $literal !== $ruleLiteral &&
$otherWatch !== $ruleLiteral &&
!$decisions->conflict($ruleLiteral);
});

if ($alternativeLiterals) {
reset($alternativeLiterals);
$this->moveWatch($literal, current($alternativeLiterals), $node);
continue;
}

if ($decisions->conflict($otherWatch)) {
return $node->getRule();
}

$decisions->decide($otherWatch, $level, $node->getRule());
}

$chain->next();
}

return null;
}










protected function moveWatch($fromLiteral, $toLiteral, $node)
{
if (!isset($this->watchChains[$toLiteral])) {
$this->watchChains[$toLiteral] = new RuleWatchChain;
}

$node->moveWatch($fromLiteral, $toLiteral);
$this->watchChains[$fromLiteral]->remove();
$this->watchChains[$toLiteral]->unshift($node);
}
}

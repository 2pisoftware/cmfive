<?php











namespace Composer\DependencyResolver;




class RuleSetIterator implements \Iterator
{
protected $rules;
protected $types;

protected $currentOffset;
protected $currentType;
protected $currentTypeOffset;

public function __construct(array $rules)
{
$this->rules = $rules;
$this->types = array_keys($rules);
sort($this->types);

$this->rewind();
}

public function current()
{
return $this->rules[$this->currentType][$this->currentOffset];
}

public function key()
{
return $this->currentType;
}

public function next()
{
$this->currentOffset++;

if (!isset($this->rules[$this->currentType])) {
return;
}

if ($this->currentOffset >= sizeof($this->rules[$this->currentType])) {
$this->currentOffset = 0;

do {
$this->currentTypeOffset++;

if (!isset($this->types[$this->currentTypeOffset])) {
$this->currentType = -1;
break;
}

$this->currentType = $this->types[$this->currentTypeOffset];
} while (isset($this->types[$this->currentTypeOffset]) && !sizeof($this->rules[$this->currentType]));
}
}

public function rewind()
{
$this->currentOffset = 0;

$this->currentTypeOffset = -1;
$this->currentType = -1;

do {
$this->currentTypeOffset++;

if (!isset($this->types[$this->currentTypeOffset])) {
$this->currentType = -1;
break;
}

$this->currentType = $this->types[$this->currentTypeOffset];
} while (isset($this->types[$this->currentTypeOffset]) && !sizeof($this->rules[$this->currentType]));
}

public function valid()
{
return isset($this->rules[$this->currentType])
&& isset($this->rules[$this->currentType][$this->currentOffset]);
}
}

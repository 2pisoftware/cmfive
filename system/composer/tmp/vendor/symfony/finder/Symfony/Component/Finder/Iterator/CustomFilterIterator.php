<?php










namespace Symfony\Component\Finder\Iterator;









class CustomFilterIterator extends FilterIterator
{
private $filters = array();









public function __construct(\Iterator $iterator, array $filters)
{
foreach ($filters as $filter) {
if (!is_callable($filter)) {
throw new \InvalidArgumentException('Invalid PHP callback.');
}
}
$this->filters = $filters;

parent::__construct($iterator);
}






public function accept()
{
$fileinfo = $this->current();

foreach ($this->filters as $filter) {
if (false === call_user_func($filter, $fileinfo)) {
return false;
}
}

return true;
}
}

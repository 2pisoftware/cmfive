<?php











namespace Composer\DependencyResolver;









class RuleWatchChain extends \SplDoublyLinkedList
{
protected $offset = 0;






public function seek($offset)
{
$this->rewind();
for ($i = 0; $i < $offset; $i++, $this->next());
}









public function remove()
{
$offset = $this->key();
$this->offsetUnset($offset);
$this->seek($offset);
}
}

<?php










namespace Symfony\Component\Finder\Iterator;






class DepthRangeFilterIterator extends FilterIterator
{
private $minDepth = 0;








public function __construct(\RecursiveIteratorIterator $iterator, $minDepth = 0, $maxDepth = PHP_INT_MAX)
{
$this->minDepth = $minDepth;
$iterator->setMaxDepth(PHP_INT_MAX === $maxDepth ? -1 : $maxDepth);

parent::__construct($iterator);
}






public function accept()
{
return $this->getInnerIterator()->getDepth() >= $this->minDepth;
}
}

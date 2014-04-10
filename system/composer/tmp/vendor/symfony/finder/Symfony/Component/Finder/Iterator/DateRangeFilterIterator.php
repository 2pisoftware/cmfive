<?php










namespace Symfony\Component\Finder\Iterator;

use Symfony\Component\Finder\Comparator\DateComparator;






class DateRangeFilterIterator extends FilterIterator
{
private $comparators = array();







public function __construct(\Iterator $iterator, array $comparators)
{
$this->comparators = $comparators;

parent::__construct($iterator);
}






public function accept()
{
$fileinfo = $this->current();

if (!$fileinfo->isFile()) {
return true;
}

$filedate = $fileinfo->getMTime();
foreach ($this->comparators as $compare) {
if (!$compare->test($filedate)) {
return false;
}
}

return true;
}
}

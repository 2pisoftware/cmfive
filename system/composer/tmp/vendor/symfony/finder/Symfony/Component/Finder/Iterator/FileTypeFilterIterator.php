<?php










namespace Symfony\Component\Finder\Iterator;






class FileTypeFilterIterator extends FilterIterator
{
const ONLY_FILES = 1;
const ONLY_DIRECTORIES = 2;

private $mode;







public function __construct(\Iterator $iterator, $mode)
{
$this->mode = $mode;

parent::__construct($iterator);
}






public function accept()
{
$fileinfo = $this->current();
if (self::ONLY_DIRECTORIES === (self::ONLY_DIRECTORIES & $this->mode) && $fileinfo->isFile()) {
return false;
} elseif (self::ONLY_FILES === (self::ONLY_FILES & $this->mode) && $fileinfo->isDir()) {
return false;
}

return true;
}
}

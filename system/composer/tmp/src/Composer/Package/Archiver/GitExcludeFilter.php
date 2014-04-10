<?php











namespace Composer\Package\Archiver;








class GitExcludeFilter extends BaseExcludeFilter
{





public function __construct($sourcePath)
{
parent::__construct($sourcePath);

if (file_exists($sourcePath.'/.gitignore')) {
$this->excludePatterns = $this->parseLines(
file($sourcePath.'/.gitignore'),
array($this, 'parseGitIgnoreLine')
);
}
if (file_exists($sourcePath.'/.gitattributes')) {
$this->excludePatterns = array_merge(
$this->excludePatterns,
$this->parseLines(
file($sourcePath.'/.gitattributes'),
array($this, 'parseGitAttributesLine')
));
}
}








public function parseGitIgnoreLine($line)
{
return $this->generatePattern($line);
}








public function parseGitAttributesLine($line)
{
$parts = preg_split('#\s+#', $line);

if (count($parts) != 2) {
return null;
}

if ($parts[1] === 'export-ignore') {
return $this->generatePattern($parts[0]);
}
}
}

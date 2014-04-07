<?php











namespace Composer\Package\Archiver;

use Symfony\Component\Finder;






class HgExcludeFilter extends BaseExcludeFilter
{
const HG_IGNORE_REGEX = 1;
const HG_IGNORE_GLOB = 2;





protected $patternMode;






public function __construct($sourcePath)
{
parent::__construct($sourcePath);

$this->patternMode = self::HG_IGNORE_REGEX;

if (file_exists($sourcePath.'/.hgignore')) {
$this->excludePatterns = $this->parseLines(
file($sourcePath.'/.hgignore'),
array($this, 'parseHgIgnoreLine')
);
}
}








public function parseHgIgnoreLine($line)
{
if (preg_match('#^syntax\s*:\s*(glob|regexp)$#', $line, $matches)) {
if ($matches[1] === 'glob') {
$this->patternMode = self::HG_IGNORE_GLOB;
} else {
$this->patternMode = self::HG_IGNORE_REGEX;
}

return null;
}

if ($this->patternMode == self::HG_IGNORE_GLOB) {
return $this->patternFromGlob($line);
} else {
return $this->patternFromRegex($line);
}
}








protected function patternFromGlob($line)
{
$pattern = '#'.substr(Finder\Glob::toRegex($line), 2, -1).'#';
$pattern = str_replace('[^/]*', '.*', $pattern);

return array($pattern, false, true);
}








public function patternFromRegex($line)
{

 $pattern = '#'.preg_replace('/((?:\\\\\\\\)*)(\\\\?)#/', '\1\2\2\\#', $line).'#';

return array($pattern, false, true);
}
}

<?php











namespace Composer\Package\Archiver;






class ComposerExcludeFilter extends BaseExcludeFilter
{




public function __construct($sourcePath, array $excludeRules)
{
parent::__construct($sourcePath);
$this->excludePatterns = $this->generatePatterns($excludeRules);
}
}

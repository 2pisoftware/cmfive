<?php










namespace Symfony\Component\Console\Input;








class InputOption
{
const VALUE_NONE = 1;
const VALUE_REQUIRED = 2;
const VALUE_OPTIONAL = 4;
const VALUE_IS_ARRAY = 8;

private $name;
private $shortcut;
private $mode;
private $default;
private $description;














public function __construct($name, $shortcut = null, $mode = null, $description = '', $default = null)
{
if (0 === strpos($name, '--')) {
$name = substr($name, 2);
}

if (empty($name)) {
throw new \InvalidArgumentException('An option name cannot be empty.');
}

if (empty($shortcut)) {
$shortcut = null;
}

if (null !== $shortcut) {
if (is_array($shortcut)) {
$shortcut = implode('|', $shortcut);
}
$shortcuts = preg_split('{(\|)-?}', ltrim($shortcut, '-'));
$shortcuts = array_filter($shortcuts);
$shortcut = implode('|', $shortcuts);

if (empty($shortcut)) {
throw new \InvalidArgumentException('An option shortcut cannot be empty.');
}
}

if (null === $mode) {
$mode = self::VALUE_NONE;
} elseif (!is_int($mode) || $mode > 15 || $mode < 1) {
throw new \InvalidArgumentException(sprintf('Option mode "%s" is not valid.', $mode));
}

$this->name = $name;
$this->shortcut = $shortcut;
$this->mode = $mode;
$this->description = $description;

if ($this->isArray() && !$this->acceptValue()) {
throw new \InvalidArgumentException('Impossible to have an option mode VALUE_IS_ARRAY if the option does not accept a value.');
}

$this->setDefault($default);
}






public function getShortcut()
{
return $this->shortcut;
}






public function getName()
{
return $this->name;
}






public function acceptValue()
{
return $this->isValueRequired() || $this->isValueOptional();
}






public function isValueRequired()
{
return self::VALUE_REQUIRED === (self::VALUE_REQUIRED & $this->mode);
}






public function isValueOptional()
{
return self::VALUE_OPTIONAL === (self::VALUE_OPTIONAL & $this->mode);
}






public function isArray()
{
return self::VALUE_IS_ARRAY === (self::VALUE_IS_ARRAY & $this->mode);
}








public function setDefault($default = null)
{
if (self::VALUE_NONE === (self::VALUE_NONE & $this->mode) && null !== $default) {
throw new \LogicException('Cannot set a default value when using InputOption::VALUE_NONE mode.');
}

if ($this->isArray()) {
if (null === $default) {
$default = array();
} elseif (!is_array($default)) {
throw new \LogicException('A default value for an array option must be an array.');
}
}

$this->default = $this->acceptValue() ? $default : false;
}






public function getDefault()
{
return $this->default;
}






public function getDescription()
{
return $this->description;
}







public function equals(InputOption $option)
{
return $option->getName() === $this->getName()
&& $option->getShortcut() === $this->getShortcut()
&& $option->getDefault() === $this->getDefault()
&& $option->isArray() === $this->isArray()
&& $option->isValueRequired() === $this->isValueRequired()
&& $option->isValueOptional() === $this->isValueOptional()
;
}
}

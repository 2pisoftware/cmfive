<?php











namespace Composer\Package\Loader;




class InvalidPackageException extends \Exception
{
private $errors;
private $warnings;
private $data;

public function __construct(array $errors, array $warnings, array $data)
{
$this->errors = $errors;
$this->warnings = $warnings;
$this->data = $data;
parent::__construct("Invalid package information: \n".implode("\n", array_merge($errors, $warnings)));
}

public function getData()
{
return $this->data;
}

public function getErrors()
{
return $this->errors;
}

public function getWarnings()
{
return $this->warnings;
}
}

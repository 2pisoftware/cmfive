<?php











namespace Composer\DependencyResolver;




class SolverBugException extends \RuntimeException
{
public function __construct($message)
{
parent::__construct(
$message."\nThis exception was most likely caused by a bug in Composer.\n".
"Please report the command you ran, the exact error you received, and your composer.json on https://github.com/composer/composer/issues - thank you!\n");
}
}

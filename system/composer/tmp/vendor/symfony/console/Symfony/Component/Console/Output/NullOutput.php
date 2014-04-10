<?php










namespace Symfony\Component\Console\Output;

use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Formatter\OutputFormatterInterface;











class NullOutput implements OutputInterface
{



public function setFormatter(OutputFormatterInterface $formatter)
{

 }




public function getFormatter()
{

 return new OutputFormatter();
}




public function setDecorated($decorated)
{

 }




public function isDecorated()
{
return false;
}




public function setVerbosity($level)
{

 }




public function getVerbosity()
{
return self::VERBOSITY_QUIET;
}




public function writeln($messages, $type = self::OUTPUT_NORMAL)
{

 }




public function write($messages, $newline = false, $type = self::OUTPUT_NORMAL)
{

 }
}

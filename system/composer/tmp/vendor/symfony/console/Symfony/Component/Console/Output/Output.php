<?php










namespace Symfony\Component\Console\Output;

use Symfony\Component\Console\Formatter\OutputFormatterInterface;
use Symfony\Component\Console\Formatter\OutputFormatter;
















abstract class Output implements OutputInterface
{
private $verbosity;
private $formatter;










public function __construct($verbosity = self::VERBOSITY_NORMAL, $decorated = false, OutputFormatterInterface $formatter = null)
{
$this->verbosity = null === $verbosity ? self::VERBOSITY_NORMAL : $verbosity;
$this->formatter = $formatter ?: new OutputFormatter();
$this->formatter->setDecorated($decorated);
}




public function setFormatter(OutputFormatterInterface $formatter)
{
$this->formatter = $formatter;
}




public function getFormatter()
{
return $this->formatter;
}




public function setDecorated($decorated)
{
$this->formatter->setDecorated($decorated);
}




public function isDecorated()
{
return $this->formatter->isDecorated();
}




public function setVerbosity($level)
{
$this->verbosity = (int) $level;
}




public function getVerbosity()
{
return $this->verbosity;
}

public function isQuiet()
{
return self::VERBOSITY_QUIET === $this->verbosity;
}

public function isVerbose()
{
return self::VERBOSITY_VERBOSE <= $this->verbosity;
}

public function isVeryVerbose()
{
return self::VERBOSITY_VERY_VERBOSE <= $this->verbosity;
}

public function isDebug()
{
return self::VERBOSITY_DEBUG <= $this->verbosity;
}




public function writeln($messages, $type = self::OUTPUT_NORMAL)
{
$this->write($messages, true, $type);
}




public function write($messages, $newline = false, $type = self::OUTPUT_NORMAL)
{
if (self::VERBOSITY_QUIET === $this->verbosity) {
return;
}

$messages = (array) $messages;

foreach ($messages as $message) {
switch ($type) {
case OutputInterface::OUTPUT_NORMAL:
$message = $this->formatter->format($message);
break;
case OutputInterface::OUTPUT_RAW:
break;
case OutputInterface::OUTPUT_PLAIN:
$message = strip_tags($this->formatter->format($message));
break;
default:
throw new \InvalidArgumentException(sprintf('Unknown output type given (%s)', $type));
}

$this->doWrite($message, $newline);
}
}







abstract protected function doWrite($message, $newline);
}

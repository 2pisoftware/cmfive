<?php










namespace Symfony\Component\Console\Descriptor;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;




abstract class Descriptor implements DescriptorInterface
{



private $output;




public function describe(OutputInterface $output, $object, array $options = array())
{
$this->output = $output;

switch (true) {
case $object instanceof InputArgument:
$this->describeInputArgument($object, $options);
break;
case $object instanceof InputOption:
$this->describeInputOption($object, $options);
break;
case $object instanceof InputDefinition:
$this->describeInputDefinition($object, $options);
break;
case $object instanceof Command:
$this->describeCommand($object, $options);
break;
case $object instanceof Application:
$this->describeApplication($object, $options);
break;
default:
throw new \InvalidArgumentException(sprintf('Object of type "%s" is not describable.', get_class($object)));
}
}







protected function write($content, $decorated = false)
{
$this->output->write($content, false, $decorated ? OutputInterface::OUTPUT_NORMAL : OutputInterface::OUTPUT_RAW);
}









abstract protected function describeInputArgument(InputArgument $argument, array $options = array());









abstract protected function describeInputOption(InputOption $option, array $options = array());









abstract protected function describeInputDefinition(InputDefinition $definition, array $options = array());









abstract protected function describeCommand(Command $command, array $options = array());









abstract protected function describeApplication(Application $application, array $options = array());
}

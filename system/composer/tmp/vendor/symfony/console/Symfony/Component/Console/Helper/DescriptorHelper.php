<?php










namespace Symfony\Component\Console\Helper;

use Symfony\Component\Console\Descriptor\DescriptorInterface;
use Symfony\Component\Console\Descriptor\JsonDescriptor;
use Symfony\Component\Console\Descriptor\MarkdownDescriptor;
use Symfony\Component\Console\Descriptor\TextDescriptor;
use Symfony\Component\Console\Descriptor\XmlDescriptor;
use Symfony\Component\Console\Output\OutputInterface;






class DescriptorHelper extends Helper
{



private $descriptors = array();




public function __construct()
{
$this
->register('txt', new TextDescriptor())
->register('xml', new XmlDescriptor())
->register('json', new JsonDescriptor())
->register('md', new MarkdownDescriptor())
;
}














public function describe(OutputInterface $output, $object, array $options = array())
{
$options = array_merge(array(
'raw_text' => false,
'format' => 'txt',
), $options);

if (!isset($this->descriptors[$options['format']])) {
throw new \InvalidArgumentException(sprintf('Unsupported format "%s".', $options['format']));
}

$descriptor = $this->descriptors[$options['format']];
$descriptor->describe($output, $object, $options);
}









public function register($format, DescriptorInterface $descriptor)
{
$this->descriptors[$format] = $descriptor;

return $this;
}




public function getName()
{
return 'descriptor';
}
}

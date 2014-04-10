<?php










namespace Symfony\Component\Console\Formatter;








interface OutputFormatterStyleInterface
{







public function setForeground($color = null);








public function setBackground($color = null);








public function setOption($option);






public function unsetOption($option);






public function setOptions(array $options);








public function apply($text);
}

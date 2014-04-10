<?php










namespace Symfony\Component\Console\Formatter;








interface OutputFormatterInterface
{







public function setDecorated($decorated);








public function isDecorated();









public function setStyle($name, OutputFormatterStyleInterface $style);










public function hasStyle($name);










public function getStyle($name);










public function format($message);
}

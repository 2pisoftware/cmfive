<?php










namespace Symfony\Component\Finder\Comparator;






class DateComparator extends Comparator
{








public function __construct($test)
{
if (!preg_match('#^\s*(==|!=|[<>]=?|after|since|before|until)?\s*(.+?)\s*$#i', $test, $matches)) {
throw new \InvalidArgumentException(sprintf('Don\'t understand "%s" as a date test.', $test));
}

try {
$date = new \DateTime($matches[2]);
$target = $date->format('U');
} catch (\Exception $e) {
throw new \InvalidArgumentException(sprintf('"%s" is not a valid date.', $matches[2]));
}

$operator = isset($matches[1]) ? $matches[1] : '==';
if ('since' === $operator || 'after' === $operator) {
$operator = '>';
}

if ('until' === $operator || 'before' === $operator) {
$operator = '<';
}

$this->setOperator($operator);
$this->setTarget($target);
}
}

<?php










namespace Symfony\Component\Yaml;

use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Exception\DumpException;






class Inline
{
const REGEX_QUOTED_STRING = '(?:"([^"\\\\]*(?:\\\\.[^"\\\\]*)*)"|\'([^\']*(?:\'\'[^\']*)*)\')';

private static $exceptionOnInvalidType = false;
private static $objectSupport = false;












public static function parse($value, $exceptionOnInvalidType = false, $objectSupport = false)
{
self::$exceptionOnInvalidType = $exceptionOnInvalidType;
self::$objectSupport = $objectSupport;

$value = trim($value);

if (0 == strlen($value)) {
return '';
}

if (function_exists('mb_internal_encoding') && ((int) ini_get('mbstring.func_overload')) & 2) {
$mbEncoding = mb_internal_encoding();
mb_internal_encoding('ASCII');
}

$i = 0;
switch ($value[0]) {
case '[':
$result = self::parseSequence($value, $i);
++$i;
break;
case '{':
$result = self::parseMapping($value, $i);
++$i;
break;
default:
$result = self::parseScalar($value, null, array('"', "'"), $i);
}


 if (preg_replace('/\s+#.*$/A', '', substr($value, $i))) {
throw new ParseException(sprintf('Unexpected characters near "%s".', substr($value, $i)));
}

if (isset($mbEncoding)) {
mb_internal_encoding($mbEncoding);
}

return $result;
}












public static function dump($value, $exceptionOnInvalidType = false, $objectSupport = false)
{
switch (true) {
case is_resource($value):
if ($exceptionOnInvalidType) {
throw new DumpException(sprintf('Unable to dump PHP resources in a YAML file ("%s").', get_resource_type($value)));
}

return 'null';
case is_object($value):
if ($objectSupport) {
return '!!php/object:'.serialize($value);
}

if ($exceptionOnInvalidType) {
throw new DumpException('Object support when dumping a YAML file has been disabled.');
}

return 'null';
case is_array($value):
return self::dumpArray($value, $exceptionOnInvalidType, $objectSupport);
case null === $value:
return 'null';
case true === $value:
return 'true';
case false === $value:
return 'false';
case ctype_digit($value):
return is_string($value) ? "'$value'" : (int) $value;
case is_numeric($value):
$locale = setlocale(LC_NUMERIC, 0);
if (false !== $locale) {
setlocale(LC_NUMERIC, 'C');
}
$repr = is_string($value) ? "'$value'" : (is_infinite($value) ? str_ireplace('INF', '.Inf', strval($value)) : strval($value));

if (false !== $locale) {
setlocale(LC_NUMERIC, $locale);
}

return $repr;
case Escaper::requiresDoubleQuoting($value):
return Escaper::escapeWithDoubleQuotes($value);
case Escaper::requiresSingleQuoting($value):
return Escaper::escapeWithSingleQuotes($value);
case '' == $value:
return "''";
case preg_match(self::getTimestampRegex(), $value):
case in_array(strtolower($value), array('null', '~', 'true', 'false')):
return "'$value'";
default:
return $value;
}
}










private static function dumpArray($value, $exceptionOnInvalidType, $objectSupport)
{

 $keys = array_keys($value);
if ((1 == count($keys) && '0' == $keys[0])
|| (count($keys) > 1 && array_reduce($keys, function ($v, $w) { return (integer) $v + $w; }, 0) == count($keys) * (count($keys) - 1) / 2)
) {
$output = array();
foreach ($value as $val) {
$output[] = self::dump($val, $exceptionOnInvalidType, $objectSupport);
}

return sprintf('[%s]', implode(', ', $output));
}


 $output = array();
foreach ($value as $key => $val) {
$output[] = sprintf('%s: %s', self::dump($key, $exceptionOnInvalidType, $objectSupport), self::dump($val, $exceptionOnInvalidType, $objectSupport));
}

return sprintf('{ %s }', implode(', ', $output));
}














public static function parseScalar($scalar, $delimiters = null, $stringDelimiters = array('"', "'"), &$i = 0, $evaluate = true)
{
if (in_array($scalar[$i], $stringDelimiters)) {

 $output = self::parseQuotedScalar($scalar, $i);

if (null !== $delimiters) {
$tmp = ltrim(substr($scalar, $i), ' ');
if (!in_array($tmp[0], $delimiters)) {
throw new ParseException(sprintf('Unexpected characters (%s).', substr($scalar, $i)));
}
}
} else {

 if (!$delimiters) {
$output = substr($scalar, $i);
$i += strlen($output);


 if (false !== $strpos = strpos($output, ' #')) {
$output = rtrim(substr($output, 0, $strpos));
}
} elseif (preg_match('/^(.+?)('.implode('|', $delimiters).')/', substr($scalar, $i), $match)) {
$output = $match[1];
$i += strlen($output);
} else {
throw new ParseException(sprintf('Malformed inline YAML string (%s).', $scalar));
}

$output = $evaluate ? self::evaluateScalar($output) : $output;
}

return $output;
}











private static function parseQuotedScalar($scalar, &$i)
{
if (!preg_match('/'.self::REGEX_QUOTED_STRING.'/Au', substr($scalar, $i), $match)) {
throw new ParseException(sprintf('Malformed inline YAML string (%s).', substr($scalar, $i)));
}

$output = substr($match[0], 1, strlen($match[0]) - 2);

$unescaper = new Unescaper();
if ('"' == $scalar[$i]) {
$output = $unescaper->unescapeDoubleQuotedString($output);
} else {
$output = $unescaper->unescapeSingleQuotedString($output);
}

$i += strlen($match[0]);

return $output;
}











private static function parseSequence($sequence, &$i = 0)
{
$output = array();
$len = strlen($sequence);
$i += 1;


 while ($i < $len) {
switch ($sequence[$i]) {
case '[':

 $output[] = self::parseSequence($sequence, $i);
break;
case '{':

 $output[] = self::parseMapping($sequence, $i);
break;
case ']':
return $output;
case ',':
case ' ':
break;
default:
$isQuoted = in_array($sequence[$i], array('"', "'"));
$value = self::parseScalar($sequence, array(',', ']'), array('"', "'"), $i);

if (!$isQuoted && false !== strpos($value, ': ')) {

 try {
$value = self::parseMapping('{'.$value.'}');
} catch (\InvalidArgumentException $e) {

 }
}

$output[] = $value;

--$i;
}

++$i;
}

throw new ParseException(sprintf('Malformed inline YAML string %s', $sequence));
}











private static function parseMapping($mapping, &$i = 0)
{
$output = array();
$len = strlen($mapping);
$i += 1;


 while ($i < $len) {
switch ($mapping[$i]) {
case ' ':
case ',':
++$i;
continue 2;
case '}':
return $output;
}


 $key = self::parseScalar($mapping, array(':', ' '), array('"', "'"), $i, false);


 $done = false;
while ($i < $len) {
switch ($mapping[$i]) {
case '[':

 $output[$key] = self::parseSequence($mapping, $i);
$done = true;
break;
case '{':

 $output[$key] = self::parseMapping($mapping, $i);
$done = true;
break;
case ':':
case ' ':
break;
default:
$output[$key] = self::parseScalar($mapping, array(',', '}'), array('"', "'"), $i);
$done = true;
--$i;
}

++$i;

if ($done) {
continue 2;
}
}
}

throw new ParseException(sprintf('Malformed inline YAML string %s', $mapping));
}








private static function evaluateScalar($scalar)
{
$scalar = trim($scalar);

switch (true) {
case 'null' == strtolower($scalar):
case '' == $scalar:
case '~' == $scalar:
return null;
case 0 === strpos($scalar, '!str'):
return (string) substr($scalar, 5);
case 0 === strpos($scalar, '! '):
return intval(self::parseScalar(substr($scalar, 2)));
case 0 === strpos($scalar, '!!php/object:'):
if (self::$objectSupport) {
return unserialize(substr($scalar, 13));
}

if (self::$exceptionOnInvalidType) {
throw new ParseException('Object support when parsing a YAML file has been disabled.');
}

return null;
case ctype_digit($scalar):
$raw = $scalar;
$cast = intval($scalar);

return '0' == $scalar[0] ? octdec($scalar) : (((string) $raw == (string) $cast) ? $cast : $raw);
case '-' === $scalar[0] && ctype_digit(substr($scalar, 1)):
$raw = $scalar;
$cast = intval($scalar);

return '0' == $scalar[1] ? octdec($scalar) : (((string) $raw == (string) $cast) ? $cast : $raw);
case 'true' === strtolower($scalar):
return true;
case 'false' === strtolower($scalar):
return false;
case is_numeric($scalar):
return '0x' == $scalar[0].$scalar[1] ? hexdec($scalar) : floatval($scalar);
case 0 == strcasecmp($scalar, '.inf'):
case 0 == strcasecmp($scalar, '.NaN'):
return -log(0);
case 0 == strcasecmp($scalar, '-.inf'):
return log(0);
case preg_match('/^(-|\+)?[0-9,]+(\.[0-9]+)?$/', $scalar):
return floatval(str_replace(',', '', $scalar));
case preg_match(self::getTimestampRegex(), $scalar):
return strtotime($scalar);
default:
return (string) $scalar;
}
}








private static function getTimestampRegex()
{
return <<<EOF
        ~^
        (?P<year>[0-9][0-9][0-9][0-9])
        -(?P<month>[0-9][0-9]?)
        -(?P<day>[0-9][0-9]?)
        (?:(?:[Tt]|[ \t]+)
        (?P<hour>[0-9][0-9]?)
        :(?P<minute>[0-9][0-9])
        :(?P<second>[0-9][0-9])
        (?:\.(?P<fraction>[0-9]*))?
        (?:[ \t]*(?P<tz>Z|(?P<tz_sign>[-+])(?P<tz_hour>[0-9][0-9]?)
        (?::(?P<tz_minute>[0-9][0-9]))?))?)?
        $~x
EOF;
}
}

<?php










namespace Symfony\Component\Yaml;







class Unescaper
{

 
 const ENCODING = 'UTF-8';


 
 const REGEX_ESCAPED_CHARACTER = "\\\\([0abt\tnvfre \\\"\\/\\\\N_LP]|x[0-9a-fA-F]{2}|u[0-9a-fA-F]{4}|U[0-9a-fA-F]{8})";








public function unescapeSingleQuotedString($value)
{
return str_replace('\'\'', '\'', $value);
}








public function unescapeDoubleQuotedString($value)
{
$self = $this;
$callback = function ($match) use ($self) {
return $self->unescapeCharacter($match[0]);
};


 return preg_replace_callback('/'.self::REGEX_ESCAPED_CHARACTER.'/u', $callback, $value);
}








public function unescapeCharacter($value)
{
switch ($value{1}) {
case '0':
return "\x0";
case 'a':
return "\x7";
case 'b':
return "\x8";
case 't':
return "\t";
case "\t":
return "\t";
case 'n':
return "\n";
case 'v':
return "\xb";
case 'f':
return "\xc";
case 'r':
return "\xd";
case 'e':
return "\x1b";
case ' ':
return ' ';
case '"':
return '"';
case '/':
return '/';
case '\\':
return '\\';
case 'N':

 return $this->convertEncoding("\x00\x85", self::ENCODING, 'UCS-2BE');
case '_':

 return $this->convertEncoding("\x00\xA0", self::ENCODING, 'UCS-2BE');
case 'L':

 return $this->convertEncoding("\x20\x28", self::ENCODING, 'UCS-2BE');
case 'P':

 return $this->convertEncoding("\x20\x29", self::ENCODING, 'UCS-2BE');
case 'x':
$char = pack('n', hexdec(substr($value, 2, 2)));

return $this->convertEncoding($char, self::ENCODING, 'UCS-2BE');
case 'u':
$char = pack('n', hexdec(substr($value, 2, 4)));

return $this->convertEncoding($char, self::ENCODING, 'UCS-2BE');
case 'U':
$char = pack('N', hexdec(substr($value, 2, 8)));

return $this->convertEncoding($char, self::ENCODING, 'UCS-4BE');
}
}












private function convertEncoding($value, $to, $from)
{
if (function_exists('mb_convert_encoding')) {
return mb_convert_encoding($value, $to, $from);
} elseif (function_exists('iconv')) {
return iconv($from, $to, $value);
}

throw new \RuntimeException('No suitable convert encoding function (install the iconv or mbstring extension).');
}
}

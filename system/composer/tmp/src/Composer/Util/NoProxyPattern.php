<?php











namespace Composer\Util;




class NoProxyPattern
{



protected $rules = array();




public function __construct($pattern)
{
$this->rules = preg_split("/[\s,]+/", $pattern);
}








public function test($url)
{
$host = parse_url($url, PHP_URL_HOST);
$port = parse_url($url, PHP_URL_PORT);

if (empty($port)) {
switch (parse_url($url, PHP_URL_SCHEME)) {
case 'http':
$port = 80;
break;
case 'https':
$port = 443;
break;
}
}

foreach ($this->rules as $rule) {
if ($rule == '*') {
return true;
}

$match = false;

list($ruleHost) = explode(':', $rule);
list($base) = explode('/', $ruleHost);

if (filter_var($base, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {


if (!isset($ip)) {
$ip = gethostbyname($host);
}

if (strpos($ruleHost, '/') === false) {
$match = $ip === $ruleHost;
} else {

 
 if ($ip === $host) {
$match = false;
} else {

 $match = self::inCIDRBlock($ruleHost, $ip);
}
}
} else {


$haystack = '.' . trim($host, '.') . '.';
$needle = '.'. trim($ruleHost, '.') .'.';
$match = stripos(strrev($haystack), strrev($needle)) === 0;
}


 if ($match && strpos($rule, ':') !== false) {
list(, $rulePort) = explode(':', $rule);
if (!empty($rulePort) && $port != $rulePort) {
$match = false;
}
}

if ($match) {
return true;
}
}

return false;
}











private static function inCIDRBlock($cidr, $ip)
{

 list($base, $bits) = explode('/', $cidr);


 list($a, $b, $c, $d) = explode('.', $base);


 $i = ($a << 24) + ($b << 16) + ($c << 8) + $d;
$mask = $bits == 0 ? 0: (~0 << (32 - $bits));


 $low = $i & $mask;


 $high = $i | (~$mask & 0xFFFFFFFF);


 list($a, $b, $c, $d) = explode('.', $ip);


 $check = ($a << 24) + ($b << 16) + ($c << 8) + $d;


 
 return $check >= $low && $check <= $high;
}
}

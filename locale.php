<?php
$locale = 'en_US';
$locale = 'slang';
$domain = 'example';

$results = putenv("LC_ALL=$locale");
if (!$results) {
    exit ('putenv failed');
}

// http://msdn.microsoft.com/en-us/library/39cwe7zf%28v=vs.100%29.aspx
$results = setlocale(LC_ALL, $locale);
if (!$results) {
    exit ('setlocale failed: locale function is not available on this platform, or the given local does not exist in this environment');
}

$results = bindtextdomain($domain, "/var/www/cmfive/modules/example/translations");
echo 'new text domain is set: ' . $results. "\n";

$results = textdomain($domain);
echo 'current message domain is set: ' . $results. "\n";

$results = gettext("New Data");
if ($results === "New Data") {
    echo "original English was returned. Something wrong\n";
}
echo $results . "\n";

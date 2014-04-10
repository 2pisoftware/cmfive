<?php











namespace Composer\Package\Loader;

use Composer\Json\JsonFile;




class JsonLoader
{
private $loader;

public function __construct(LoaderInterface $loader)
{
$this->loader = $loader;
}





public function load($json)
{
if ($json instanceof JsonFile) {
$config = $json->read();
} elseif (file_exists($json)) {
$config = JsonFile::parseJson(file_get_contents($json), $json);
} elseif (is_string($json)) {
$config = JsonFile::parseJson($json);
}

return $this->loader->load($config);
}
}

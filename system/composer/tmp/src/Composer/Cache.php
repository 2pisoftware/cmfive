<?php











namespace Composer;

use Composer\IO\IOInterface;
use Composer\Util\Filesystem;
use Symfony\Component\Finder\Finder;






class Cache
{
private static $cacheCollected = false;
private $io;
private $root;
private $enabled = true;
private $whitelist;
private $filesystem;







public function __construct(IOInterface $io, $cacheDir, $whitelist = 'a-z0-9.', Filesystem $filesystem = null)
{
$this->io = $io;
$this->root = rtrim($cacheDir, '/\\') . '/';
$this->whitelist = $whitelist;
$this->filesystem = $filesystem ?: new Filesystem();

if (!is_dir($this->root)) {
if (!@mkdir($this->root, 0777, true)) {
$this->enabled = false;
}
}
}

public function isEnabled()
{
return $this->enabled;
}

public function getRoot()
{
return $this->root;
}

public function read($file)
{
$file = preg_replace('{[^'.$this->whitelist.']}i', '-', $file);
if ($this->enabled && file_exists($this->root . $file)) {
if ($this->io->isDebug()) {
$this->io->write('Reading '.$this->root . $file.' from cache');
}

return file_get_contents($this->root . $file);
}

return false;
}

public function write($file, $contents)
{
if ($this->enabled) {
$file = preg_replace('{[^'.$this->whitelist.']}i', '-', $file);

if ($this->io->isDebug()) {
$this->io->write('Writing '.$this->root . $file.' into cache');
}

return file_put_contents($this->root . $file, $contents);
}

return false;
}




public function copyFrom($file, $source)
{
if ($this->enabled) {
$file = preg_replace('{[^'.$this->whitelist.']}i', '-', $file);
$this->filesystem->ensureDirectoryExists(dirname($this->root . $file));

if ($this->io->isDebug()) {
$this->io->write('Writing '.$this->root . $file.' into cache');
}

return copy($source, $this->root . $file);
}

return false;
}




public function copyTo($file, $target)
{
$file = preg_replace('{[^'.$this->whitelist.']}i', '-', $file);
if ($this->enabled && file_exists($this->root . $file)) {
touch($this->root . $file);

if ($this->io->isDebug()) {
$this->io->write('Reading '.$this->root . $file.' from cache');
}

return copy($this->root . $file, $target);
}

return false;
}

public function gcIsNecessary()
{
return (!self::$cacheCollected && !mt_rand(0, 50));
}

public function remove($file)
{
$file = preg_replace('{[^'.$this->whitelist.']}i', '-', $file);
if ($this->enabled && file_exists($this->root . $file)) {
return unlink($this->root . $file);
}

return false;
}

public function gc($ttl, $maxSize)
{
$expire = new \DateTime();
$expire->modify('-'.$ttl.' seconds');

$finder = $this->getFinder()->date('until '.$expire->format('Y-m-d H:i:s'));
foreach ($finder as $file) {
unlink($file->getRealPath());
}

$totalSize = $this->filesystem->size($this->root);
if ($totalSize > $maxSize) {
$iterator = $this->getFinder()->sortByAccessedTime()->getIterator();
while ($totalSize > $maxSize && $iterator->valid()) {
$filepath = $iterator->current()->getRealPath();
$totalSize -= $this->filesystem->size($filepath);
unlink($filepath);
$iterator->next();
}
}

self::$cacheCollected = true;

return true;
}

public function sha1($file)
{
$file = preg_replace('{[^'.$this->whitelist.']}i', '-', $file);
if ($this->enabled && file_exists($this->root . $file)) {
return sha1_file($this->root . $file);
}

return false;
}

public function sha256($file)
{
$file = preg_replace('{[^'.$this->whitelist.']}i', '-', $file);
if ($this->enabled && file_exists($this->root . $file)) {
return hash_file('sha256', $this->root . $file);
}

return false;
}

protected function getFinder()
{
return Finder::create()->in($this->root)->files();
}
}

<?php











namespace Composer\Downloader;






class PharDownloader extends ArchiveDownloader
{



protected function extract($file, $path)
{

 $archive = new \Phar($file);
$archive->extractTo($path, null, true);





}
}

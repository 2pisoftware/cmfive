<?php











namespace Composer\Downloader;






class TarDownloader extends ArchiveDownloader
{



protected function extract($file, $path)
{

 $archive = new \PharData($file);
$archive->extractTo($path, null, true);
}
}

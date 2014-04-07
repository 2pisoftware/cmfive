<?php











namespace Composer\Plugin;

use Composer\EventDispatcher\Event;
use Composer\Util\RemoteFilesystem;






class PreFileDownloadEvent extends Event
{



private $rfs;




private $processedUrl;








public function __construct($name, RemoteFilesystem $rfs, $processedUrl)
{
parent::__construct($name);
$this->rfs = $rfs;
$this->processedUrl = $processedUrl;
}






public function getRemoteFilesystem()
{
return $this->rfs;
}






public function setRemoteFilesystem(RemoteFilesystem $rfs)
{
$this->rfs = $rfs;
}






public function getProcessedUrl()
{
return $this->processedUrl;
}
}

<?php











namespace Composer\Repository\Vcs;

use Composer\Config;
use Composer\IO\IOInterface;




interface VcsDriverInterface
{



public function initialize();







public function getComposerInformation($identifier);






public function getRootIdentifier();






public function getBranches();






public function getTags();





public function getDist($identifier);





public function getSource($identifier);






public function getUrl();








public function hasComposerFile($identifier);





public function cleanup();










public static function supports(IOInterface $io, Config $config, $url, $deep = false);
}

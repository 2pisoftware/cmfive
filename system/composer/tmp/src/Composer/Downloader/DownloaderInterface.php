<?php











namespace Composer\Downloader;

use Composer\Package\PackageInterface;







interface DownloaderInterface
{





public function getInstallationSource();







public function download(PackageInterface $package, $path);








public function update(PackageInterface $initial, PackageInterface $target, $path);







public function remove(PackageInterface $package, $path);







public function setOutputProgress($outputProgress);
}

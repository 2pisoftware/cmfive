<?php











namespace Composer\Installer;

use Composer\Package\PackageInterface;
use Composer\Package\AliasPackage;
use Composer\Plugin\PluginInstaller;
use Composer\Repository\RepositoryInterface;
use Composer\Repository\InstalledRepositoryInterface;
use Composer\DependencyResolver\Operation\OperationInterface;
use Composer\DependencyResolver\Operation\InstallOperation;
use Composer\DependencyResolver\Operation\UpdateOperation;
use Composer\DependencyResolver\Operation\UninstallOperation;
use Composer\DependencyResolver\Operation\MarkAliasInstalledOperation;
use Composer\DependencyResolver\Operation\MarkAliasUninstalledOperation;
use Composer\Util\StreamContextFactory;








class InstallationManager
{
private $installers = array();
private $cache = array();
private $notifiablePackages = array();

public function reset()
{
$this->notifiablePackages = array();
}






public function addInstaller(InstallerInterface $installer)
{
array_unshift($this->installers, $installer);
$this->cache = array();
}






public function removeInstaller(InstallerInterface $installer)
{
if (false !== ($key = array_search($installer, $this->installers, true))) {
array_splice($this->installers, $key, 1);
$this->cache = array();
}
}








public function disablePlugins()
{
foreach ($this->installers as $i => $installer) {
if (!$installer instanceof PluginInstaller) {
continue;
}

unset($this->installers[$i]);
}
}










public function getInstaller($type)
{
$type = strtolower($type);

if (isset($this->cache[$type])) {
return $this->cache[$type];
}

foreach ($this->installers as $installer) {
if ($installer->supports($type)) {
return $this->cache[$type] = $installer;
}
}

throw new \InvalidArgumentException('Unknown installer type: '.$type);
}









public function isPackageInstalled(InstalledRepositoryInterface $repo, PackageInterface $package)
{
if ($package instanceof AliasPackage) {
return $repo->hasPackage($package) && $this->isPackageInstalled($repo, $package->getAliasOf());
}

return $this->getInstaller($package->getType())->isInstalled($repo, $package);
}







public function execute(RepositoryInterface $repo, OperationInterface $operation)
{
$method = $operation->getJobType();
$this->$method($repo, $operation);
}







public function install(RepositoryInterface $repo, InstallOperation $operation)
{
$package = $operation->getPackage();
$installer = $this->getInstaller($package->getType());
$installer->install($repo, $package);
$this->markForNotification($package);
}







public function update(RepositoryInterface $repo, UpdateOperation $operation)
{
$initial = $operation->getInitialPackage();
$target = $operation->getTargetPackage();

$initialType = $initial->getType();
$targetType = $target->getType();

if ($initialType === $targetType) {
$installer = $this->getInstaller($initialType);
$installer->update($repo, $initial, $target);
$this->markForNotification($target);
} else {
$this->getInstaller($initialType)->uninstall($repo, $initial);
$this->getInstaller($targetType)->install($repo, $target);
}
}







public function uninstall(RepositoryInterface $repo, UninstallOperation $operation)
{
$package = $operation->getPackage();
$installer = $this->getInstaller($package->getType());
$installer->uninstall($repo, $package);
}







public function markAliasInstalled(RepositoryInterface $repo, MarkAliasInstalledOperation $operation)
{
$package = $operation->getPackage();

if (!$repo->hasPackage($package)) {
$repo->addPackage(clone $package);
}
}







public function markAliasUninstalled(RepositoryInterface $repo, MarkAliasUninstalledOperation $operation)
{
$package = $operation->getPackage();

$repo->removePackage($package);
}







public function getInstallPath(PackageInterface $package)
{
$installer = $this->getInstaller($package->getType());

return $installer->getInstallPath($package);
}

public function notifyInstalls()
{
foreach ($this->notifiablePackages as $repoUrl => $packages) {

 if (strpos($repoUrl, '%package%')) {
foreach ($packages as $package) {
$url = str_replace('%package%', $package->getPrettyName(), $repoUrl);

$params = array(
'version' => $package->getPrettyVersion(),
'version_normalized' => $package->getVersion(),
);
$opts = array('http' =>
array(
'method' => 'POST',
'header' => array('Content-type: application/x-www-form-urlencoded'),
'content' => http_build_query($params, '', '&'),
'timeout' => 3,
)
);

$context = StreamContextFactory::getContext($url, $opts);
@file_get_contents($url, false, $context);
}

continue;
}

$postData = array('downloads' => array());
foreach ($packages as $package) {
$postData['downloads'][] = array(
'name' => $package->getPrettyName(),
'version' => $package->getVersion(),
);
}

$opts = array('http' =>
array(
'method' => 'POST',
'header' => array('Content-Type: application/json'),
'content' => json_encode($postData),
'timeout' => 6,
)
);

$context = StreamContextFactory::getContext($repoUrl, $opts);
@file_get_contents($repoUrl, false, $context);
}

$this->reset();
}

private function markForNotification(PackageInterface $package)
{
if ($package->getNotificationUrl()) {
$this->notifiablePackages[$package->getNotificationUrl()][$package->getName()] = $package;
}
}
}

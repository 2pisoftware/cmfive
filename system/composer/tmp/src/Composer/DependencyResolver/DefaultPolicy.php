<?php











namespace Composer\DependencyResolver;

use Composer\Package\PackageInterface;
use Composer\Package\AliasPackage;
use Composer\Package\BasePackage;
use Composer\Package\LinkConstraint\VersionConstraint;





class DefaultPolicy implements PolicyInterface
{
private $preferStable;

public function __construct($preferStable = false)
{
$this->preferStable = $preferStable;
}

public function versionCompare(PackageInterface $a, PackageInterface $b, $operator)
{
if ($this->preferStable && ($stabA = $a->getStability()) !== ($stabB = $b->getStability())) {
return BasePackage::$stabilities[$stabA] < BasePackage::$stabilities[$stabB];
}

$constraint = new VersionConstraint($operator, $b->getVersion());
$version = new VersionConstraint('==', $a->getVersion());

return $constraint->matchSpecific($version, true);
}

public function findUpdatePackages(Pool $pool, array $installedMap, PackageInterface $package)
{
$packages = array();

foreach ($pool->whatProvides($package->getName()) as $candidate) {
if ($candidate !== $package) {
$packages[] = $candidate;
}
}

return $packages;
}

public function getPriority(Pool $pool, PackageInterface $package)
{
return $pool->getPriority($package->getRepository());
}

public function selectPreferedPackages(Pool $pool, array $installedMap, array $literals, $requiredPackage = null)
{
$packages = $this->groupLiteralsByNamePreferInstalled($pool, $installedMap, $literals);

foreach ($packages as &$literals) {
$policy = $this;
usort($literals, function ($a, $b) use ($policy, $pool, $installedMap, $requiredPackage) {
return $policy->compareByPriorityPreferInstalled($pool, $installedMap, $pool->literalToPackage($a), $pool->literalToPackage($b), $requiredPackage, true);
});
}

foreach ($packages as &$literals) {
$literals = $this->pruneToBestVersion($pool, $literals);

$literals = $this->pruneToHighestPriorityOrInstalled($pool, $installedMap, $literals);

$literals = $this->pruneRemoteAliases($pool, $literals);
}

$selected = call_user_func_array('array_merge', $packages);


 usort($selected, function ($a, $b) use ($policy, $pool, $installedMap, $requiredPackage) {
return $policy->compareByPriorityPreferInstalled($pool, $installedMap, $pool->literalToPackage($a), $pool->literalToPackage($b), $requiredPackage);
});

return $selected;
}

protected function groupLiteralsByNamePreferInstalled(Pool $pool, array $installedMap, $literals)
{
$packages = array();
foreach ($literals as $literal) {
$packageName = $pool->literalToPackage($literal)->getName();

if (!isset($packages[$packageName])) {
$packages[$packageName] = array();
}

if (isset($installedMap[abs($literal)])) {
array_unshift($packages[$packageName], $literal);
} else {
$packages[$packageName][] = $literal;
}
}

return $packages;
}




public function compareByPriorityPreferInstalled(Pool $pool, array $installedMap, PackageInterface $a, PackageInterface $b, $requiredPackage = null, $ignoreReplace = false)
{
if ($a->getRepository() === $b->getRepository()) {

 if ($a->getName() === $b->getName()) {
$aAliased = $a instanceof AliasPackage;
$bAliased = $b instanceof AliasPackage;
if ($aAliased && !$bAliased) {
return -1; 
 }
if (!$aAliased && $bAliased) {
return 1; 
 }
}

if (!$ignoreReplace) {

 if ($this->replaces($a, $b)) {
return 1; 
 }
if ($this->replaces($b, $a)) {
return -1; 
 }


 
 if ($requiredPackage && false !== ($pos = strpos($requiredPackage, '/'))) {
$requiredVendor = substr($requiredPackage, 0, $pos);

$aIsSameVendor = substr($a->getName(), 0, $pos) === $requiredVendor;
$bIsSameVendor = substr($b->getName(), 0, $pos) === $requiredVendor;

if ($bIsSameVendor !== $aIsSameVendor) {
return $aIsSameVendor ? -1 : 1;
}
}
}


 if ($a->getId() === $b->getId()) {
return 0;
}

return ($a->getId() < $b->getId()) ? -1 : 1;
}

if (isset($installedMap[$a->getId()])) {
return -1;
}

if (isset($installedMap[$b->getId()])) {
return 1;
}

return ($this->getPriority($pool, $a) > $this->getPriority($pool, $b)) ? -1 : 1;
}











protected function replaces(PackageInterface $source, PackageInterface $target)
{
foreach ($source->getReplaces() as $link) {
if ($link->getTarget() === $target->getName()


 ) {
return true;
}
}

return false;
}

protected function pruneToBestVersion(Pool $pool, $literals)
{
$bestLiterals = array($literals[0]);
$bestPackage = $pool->literalToPackage($literals[0]);
foreach ($literals as $i => $literal) {
if (0 === $i) {
continue;
}

$package = $pool->literalToPackage($literal);

if ($this->versionCompare($package, $bestPackage, '>')) {
$bestPackage = $package;
$bestLiterals = array($literal);
} elseif ($this->versionCompare($package, $bestPackage, '==')) {
$bestLiterals[] = $literal;
}
}

return $bestLiterals;
}

protected function selectNewestPackages(array $installedMap, array $literals)
{
$maxLiterals = array($literals[0]);
$maxPackage = $literals[0]->getPackage();
foreach ($literals as $i => $literal) {
if (0 === $i) {
continue;
}

if ($this->versionCompare($literal->getPackage(), $maxPackage, '>')) {
$maxPackage = $literal->getPackage();
$maxLiterals = array($literal);
} elseif ($this->versionCompare($literal->getPackage(), $maxPackage, '==')) {
$maxLiterals[] = $literal;
}
}

return $maxLiterals;
}




protected function pruneToHighestPriorityOrInstalled(Pool $pool, array $installedMap, array $literals)
{
$selected = array();

$priority = null;

foreach ($literals as $literal) {
$package = $pool->literalToPackage($literal);

if (isset($installedMap[$package->getId()])) {
$selected[] = $literal;
continue;
}

if (null === $priority) {
$priority = $this->getPriority($pool, $package);
}

if ($this->getPriority($pool, $package) != $priority) {
break;
}

$selected[] = $literal;
}

return $selected;
}






protected function pruneRemoteAliases(Pool $pool, array $literals)
{
$hasLocalAlias = false;

foreach ($literals as $literal) {
$package = $pool->literalToPackage($literal);

if ($package instanceof AliasPackage && $package->isRootPackageAlias()) {
$hasLocalAlias = true;
break;
}
}

if (!$hasLocalAlias) {
return $literals;
}

$selected = array();
foreach ($literals as $literal) {
$package = $pool->literalToPackage($literal);

if ($package instanceof AliasPackage && $package->isRootPackageAlias()) {
$selected[] = $literal;
}
}

return $selected;
}
}

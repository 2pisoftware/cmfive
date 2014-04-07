<?php











namespace Composer\Package;

use Composer\Repository\RepositoryInterface;
use Composer\Repository\PlatformRepository;






abstract class BasePackage implements PackageInterface
{
public static $supportedLinkTypes = array(
'require' => array('description' => 'requires', 'method' => 'requires'),
'conflict' => array('description' => 'conflicts', 'method' => 'conflicts'),
'provide' => array('description' => 'provides', 'method' => 'provides'),
'replace' => array('description' => 'replaces', 'method' => 'replaces'),
'require-dev' => array('description' => 'requires (for development)', 'method' => 'devRequires'),
);

const STABILITY_STABLE = 0;
const STABILITY_RC = 5;
const STABILITY_BETA = 10;
const STABILITY_ALPHA = 15;
const STABILITY_DEV = 20;

public static $stabilities = array(
'stable' => self::STABILITY_STABLE,
'RC' => self::STABILITY_RC,
'beta' => self::STABILITY_BETA,
'alpha' => self::STABILITY_ALPHA,
'dev' => self::STABILITY_DEV,
);

protected $name;
protected $prettyName;

protected $repository;
protected $id;






public function __construct($name)
{
$this->prettyName = $name;
$this->name = strtolower($name);
$this->id = -1;
}




public function getName()
{
return $this->name;
}




public function getPrettyName()
{
return $this->prettyName;
}




public function getNames()
{
$names = array(
$this->getName() => true,
);

foreach ($this->getProvides() as $link) {
$names[$link->getTarget()] = true;
}

foreach ($this->getReplaces() as $link) {
$names[$link->getTarget()] = true;
}

return array_keys($names);
}




public function setId($id)
{
$this->id = $id;
}




public function getId()
{
return $this->id;
}




public function setRepository(RepositoryInterface $repository)
{
if ($this->repository && $repository !== $this->repository) {
throw new \LogicException('A package can only be added to one repository');
}
$this->repository = $repository;
}




public function getRepository()
{
return $this->repository;
}






public function isPlatform()
{
return $this->getRepository() instanceof PlatformRepository;
}






public function getUniqueName()
{
return $this->getName().'-'.$this->getVersion();
}

public function equals(PackageInterface $package)
{
$self = $this;
if ($this instanceof AliasPackage) {
$self = $this->getAliasOf();
}
if ($package instanceof AliasPackage) {
$package = $package->getAliasOf();
}

return $package === $self;
}






public function __toString()
{
return $this->getUniqueName();
}

public function getPrettyString()
{
return $this->getPrettyName().' '.$this->getPrettyVersion();
}

public function __clone()
{
$this->repository = null;
$this->id = -1;
}
}

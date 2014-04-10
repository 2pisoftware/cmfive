<?php











namespace Composer\DependencyResolver\Operation;

use Composer\Package\Version\VersionParser;
use Composer\Package\PackageInterface;






abstract class SolverOperation implements OperationInterface
{
protected $reason;






public function __construct($reason = null)
{
$this->reason = $reason;
}






public function getReason()
{
return $this->reason;
}

protected function formatVersion(PackageInterface $package)
{
return VersionParser::formatVersion($package);
}
}

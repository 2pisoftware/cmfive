<?php











namespace Composer\Package;

use Composer\Repository\RepositoryInterface;






interface PackageInterface
{





public function getName();






public function getPrettyName();









public function getNames();






public function setId($id);






public function getId();






public function isDev();






public function getType();






public function getTargetDir();






public function getExtra();






public function setInstallationSource($type);






public function getInstallationSource();






public function getSourceType();






public function getSourceUrl();






public function getSourceReference();






public function getDistType();






public function getDistUrl();






public function getDistReference();






public function getDistSha1Checksum();






public function getVersion();






public function getPrettyVersion();






public function getReleaseDate();






public function getStability();







public function getRequires();







public function getConflicts();







public function getProvides();







public function getReplaces();







public function getDevRequires();







public function getSuggests();











public function getAutoload();







public function getIncludePaths();






public function setRepository(RepositoryInterface $repository);






public function getRepository();






public function getBinaries();






public function getUniqueName();






public function getNotificationUrl();






public function __toString();






public function getPrettyString();






public function getArchiveExcludes();
}

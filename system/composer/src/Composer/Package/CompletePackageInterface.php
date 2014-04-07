<?php











namespace Composer\Package;






interface CompletePackageInterface extends PackageInterface
{





public function getScripts();








public function getRepositories();






public function getLicense();






public function getKeywords();






public function getDescription();






public function getHomepage();








public function getAuthors();






public function getSupport();
}

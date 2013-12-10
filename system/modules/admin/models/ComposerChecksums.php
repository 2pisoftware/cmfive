<?php

class ComposerChecksums extends DbObject {

	public $location;
	public $checksum;

	public function isEqual($checksum) {
		return (strcmp($this->checksum, $checksum) == 0);
	}

}
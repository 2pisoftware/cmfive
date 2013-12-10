<?php

function composeradd_ALL(Web &$w) {

	if ($w->Composer->shouldAddFiles()) {
		$w->Composer->addComposerFiles();
	}

	if ($w->Composer->count() > 0) {
		$w->ctx("result", $w->Composer->count() . " files added to DB.");
	}

}
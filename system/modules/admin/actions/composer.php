<?php

function composer_ALL(Web &$w) {

	$w->setLayout(null);

	echo "Use this page to track execution of the Composer update processes<br/>The system will be usable again when this has finished<br/><br/>";

	// tell php to automatically flush after every output
	// including lines of output produced by shell commands
	// $w->Composer->disable_ob();
	$descriptorspec = array(
	   0 => array("pipe", "r"),   // stdin is a pipe that the child will read from
	   1 => array("pipe", "w"),   // stdout is a pipe that the child will write to
	   2 => array("pipe", "w")    // stderr is a pipe that the child will write to
	);

	$checksums = $w->Composer->getAll();
	$timeout = 15;
	if (!empty($checksums)) {
		foreach($checksums as $chk) {
			flush();
			// Get checksum of current file
			$current_checksum = md5_file($chk->location);
			if (!$chk->isEqual($current_checksum)) {
				$cmd = "COMPOSER_HOME=".realpath(SYSTEM_PATH) . " php composer.phar --working-dir=" . dirname($chk->location) . " update -vv";
				echo "Running \"" . $cmd . "\"...<br/><br/>";
				// Something has checked, we need to update
				$process = proc_open($cmd, $descriptorspec, $pipes, getcwd() . '/system', array());

				if (is_resource($process)) {
				    while ($s = fgets($pipes[1])) {
				        print str_replace("\n", "<br/>", $s);//$s;
				        flush();
				    }
				}

				proc_close($process);
				echo "<br/>";

				$chk->checksum = md5_file($chk->location);
				$chk->insertOrUpdate();
				echo "Checksum updated in DB<br/>-----------------------------<br/><br/>";
				$timeout += 15;
			} else {
				echo "<hr/>Detected no changes in " . $chk->location . "<br/><hr/><br/>";
			}
		}
	}

	echo <<<EOF
		Done!<br/>Going back to admin in <div id="countdown">$timeout</div>
		<script type='text/javascript'> 
			function updateVal() {
				updateVal.counter--;
				document.getElementById("countdown").innerHTML=updateVal.counter;
				if (updateVal.counter <= 0) {
					window.location="/admin";
				}
			}

			setInterval(function(){
				updateVal();
			}, 1000);
			updateVal.counter = $timeout; 
		</script>
EOF;
}
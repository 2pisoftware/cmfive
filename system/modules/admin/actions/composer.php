<?php
ini_set('memory_limit', '512M');
ini_set('max_execution_time', 300);

define('EXTRACT_DIRECTORY', SYSTEM_PATH . "/composer");

//if (is_dir(EXTRACT_DIRECTORY . "/tmp")) {
//    delTemp(EXTRACT_DIRECTORY . "/tmp");
//}

if (file_exists(EXTRACT_DIRECTORY.'/tmp/vendor/autoload.php') !== true) {
    ini_set("phar.readonly", 0);
    $composerPhar = new Phar(SYSTEM_PATH . "/composer.phar");
    //php.ini setting phar.readonly must be set to 0
    $composerPhar->extractTo(EXTRACT_DIRECTORY . "/tmp");
}

//This requires the phar to have been extracted successfully.
require_once (EXTRACT_DIRECTORY.'/tmp/vendor/autoload.php');

//Use the Composer classes
use Composer\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Composer\Command\UpdateCommand;
use Symfony\Component\Console\Output\StreamOutput;

function composer_ALL(Web $w) {
    $w->setLayout(null);    
    
    // Collect dependencies
    $dependencies_array = array();
    foreach($w->modules() as $module) {
        $dependencies = Config::get("{$module}.dependencies");
        if (!empty($dependencies)) {
            $dependencies_array = array_merge($dependencies, $dependencies_array);
        }
    }
    
    $json_obj = array();
    $json_obj["config"] = array();
    $json_obj["config"]["vendor-dir"] = 'composer/vendor';
    $json_obj["config"]["cache-dir"] = 'composer/cache';
    $json_obj["config"]["bin-dir"] = 'composer/bin';
    $json_obj["require"] = $dependencies_array;

    chdir(SYSTEM_PATH);
    // Create the JSON file
    file_put_contents(SYSTEM_PATH . "/composer.json", json_encode($json_obj, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_FORCE_OBJECT));

    //Create the commands
    $input = new ArrayInput(array('command' => 'update'));
    $filestream = new StreamOutput(fopen(ROOT_PATH . '/log/composer.log', 'w'));
    
    //Create the application and run it with the commands
    $application = new Application();
    $application->run($input, $filestream);
    
    chdir(ROOT_PATH);
}
//
//function delTemp($dir) {
//    foreach(glob($dir . '/*') as $file) { 
//        if(is_dir($file)) {
//            delTemp($file); 
//        } else {
//            unlink($file);
//        }
//    }
//    rmdir($dir); 
// } 
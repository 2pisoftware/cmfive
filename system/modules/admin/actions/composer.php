<?php

define('EXTRACT_DIRECTORY', SYSTEM_PATH . "/composer");
if (file_exists(EXTRACT_DIRECTORY.'/vendor/autoload.php') !== true) {
    ini_set("phar.readonly", 0);
    $composerPhar = new Phar(SYSTEM_PATH . "/composer.phar");
    //php.ini setting phar.readonly must be set to 0
    $composerPhar->extractTo(EXTRACT_DIRECTORY);
}

//This requires the phar to have been extracted successfully.
require_once (EXTRACT_DIRECTORY.'/vendor/autoload.php');

//Use the Composer classes
use Composer\Console\Application;
use Composer\Command\UpdateCommand;
use Symfony\Component\Console\Input\ArrayInput;

function composer_ALL(Web $w) {
    $w->setLayout(null);
    ob_implicit_flush(true);
    ob_end_flush();
    
    // Collect dependencies
    $dependencies_array = array();
    foreach($w->modules() as $module) {
        $dependencies = Config::get("{$module}.dependencies");
        if (!empty($dependencies)) {
            $dependencies_array = array_merge($dependencies, $dependencies_array);
        }
    }
    
    // Json structures are objects not arrays so we need some translation
    $json_obj = new stdClass();
    $json_obj->config = new stdClass();
    $json_obj->config->{"vendor-dir"} = 'composer/vendor';
    $json_obj->config->{"cache-dir"} = 'composer/cache';
    $json_obj->config->{"bin-dir"} = 'composer/bin';
    $json_obj->require = new stdClass();
    foreach($dependencies_array as $project => $version) {
        $json_obj->require->{$project} = $version;
    }
    
    chdir(SYSTEM_PATH);
    // Create the JSON file
    file_put_contents(SYSTEM_PATH . "/composer.json", json_encode($json_obj, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));

    $cmd = "./composer.phar update > ".ROOT_PATH."/log/composer.log 2>&1 &";

    echo "Composer update is now running in the background. You can read the log at " . ROOT_PATH . "/log/composer.log=";
    echo "Composer updates can take some time, please be patient...<br/>";
    flush();
    system($cmd);
    // Change dir back
    chdir(ROOT_PATH);
}

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

    $cmd = "./composer.phar update";
    
    // Only use live process output on unix machines
    if (substr(PHP_OS, 0, 3) == "WIN") {
        system($cmd);
        exit;
    }
    
    // Turn off output buffering
    ini_set('output_buffering', 'off');
    // Turn off PHP output compression
    ini_set('zlib.output_compression', false);
    // Implicitly flush the buffer(s)
    ini_set('implicit_flush', true);
    ob_implicit_flush(true);
    // Clear, and turn off output buffering
    while (ob_get_level() > 0) {
        // Get the curent level
        $level = ob_get_level();
        // End the buffering
        ob_end_clean();
        // If the current level has not changed, abort
        if (ob_get_level() == $level) break;
    }
    // Disable apache output buffering/compression
    if (function_exists('apache_setenv')) {
        apache_setenv('no-gzip', '1');
        apache_setenv('dont-vary', '1');
    }
    
    echo "<pre>";
    echo "Composer updates can take some time, please be patient...\n";
    flush();
    system($cmd);
    echo "</pre>";
    
    // Change dir back
    chdir(ROOT_PATH);
}

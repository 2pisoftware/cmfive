<?php











namespace Composer\Plugin;

use Composer\Composer;
use Composer\IO\IOInterface;






interface PluginInterface
{





const PLUGIN_API_VERSION = '1.0.0';







public function activate(Composer $composer, IOInterface $io);
}

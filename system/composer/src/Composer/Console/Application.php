<?php











namespace Composer\Console;

use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Formatter\OutputFormatter;
use Composer\Command;
use Composer\Command\Helper\DialogHelper;
use Composer\Composer;
use Composer\Factory;
use Composer\IO\IOInterface;
use Composer\IO\ConsoleIO;
use Composer\Json\JsonValidationException;
use Composer\Util\ErrorHandler;








class Application extends BaseApplication
{



protected $composer;




protected $io;

private static $logo = '   ______
  / ____/___  ____ ___  ____  ____  ________  _____
 / /   / __ \/ __ `__ \/ __ \/ __ \/ ___/ _ \/ ___/
/ /___/ /_/ / / / / / / /_/ / /_/ (__  )  __/ /
\____/\____/_/ /_/ /_/ .___/\____/____/\___/_/
                    /_/
';

public function __construct()
{
if (function_exists('ini_set')) {
ini_set('xdebug.show_exception_trace', false);
ini_set('xdebug.scream', false);

}
if (function_exists('date_default_timezone_set') && function_exists('date_default_timezone_get')) {
date_default_timezone_set(@date_default_timezone_get());
}

ErrorHandler::register();
parent::__construct('Composer', Composer::VERSION);
}




public function run(InputInterface $input = null, OutputInterface $output = null)
{
if (null === $output) {
$styles = Factory::createAdditionalStyles();
$formatter = new OutputFormatter(null, $styles);
$output = new ConsoleOutput(ConsoleOutput::VERBOSITY_NORMAL, null, $formatter);
}

return parent::run($input, $output);
}




public function doRun(InputInterface $input, OutputInterface $output)
{
$this->io = new ConsoleIO($input, $output, $this->getHelperSet());

if (version_compare(PHP_VERSION, '5.3.2', '<')) {
$output->writeln('<warning>Composer only officially supports PHP 5.3.2 and above, you will most likely encounter problems with your PHP '.PHP_VERSION.', upgrading is strongly recommended.</warning>');
}

if (defined('COMPOSER_DEV_WARNING_TIME') && $this->getCommandName($input) !== 'self-update' && $this->getCommandName($input) !== 'selfupdate') {
if (time() > COMPOSER_DEV_WARNING_TIME) {
$output->writeln(sprintf('<warning>Warning: This development build of composer is over 30 days old. It is recommended to update it by running "%s self-update" to get the latest version.</warning>', $_SERVER['PHP_SELF']));
}
}

if (getenv('COMPOSER_NO_INTERACTION')) {
$input->setInteractive(false);
}

if ($input->hasParameterOption('--profile')) {
$startTime = microtime(true);
$this->io->enableDebugging($startTime);
}

if ($newWorkDir = $this->getNewWorkingDir($input)) {
$oldWorkingDir = getcwd();
chdir($newWorkDir);
}

$result = parent::doRun($input, $output);

if (isset($oldWorkingDir)) {
chdir($oldWorkingDir);
}

if (isset($startTime)) {
$output->writeln('<info>Memory usage: '.round(memory_get_usage() / 1024 / 1024, 2).'MB (peak: '.round(memory_get_peak_usage() / 1024 / 1024, 2).'MB), time: '.round(microtime(true) - $startTime, 2).'s');
}

return $result;
}





private function getNewWorkingDir(InputInterface $input)
{
$workingDir = $input->getParameterOption(array('--working-dir', '-d'));
if (false !== $workingDir && !is_dir($workingDir)) {
throw new \RuntimeException('Invalid working directory specified.');
}

return $workingDir;
}




public function renderException($exception, $output)
{
try {
$composer = $this->getComposer(false);
if ($composer) {
$config = $composer->getConfig();

$minSpaceFree = 1024*1024;
if ((($df = @disk_free_space($dir = $config->get('home'))) !== false && $df < $minSpaceFree)
|| (($df = @disk_free_space($dir = $config->get('vendor-dir'))) !== false && $df < $minSpaceFree)
) {
$output->writeln('<error>The disk hosting '.$dir.' is full, this may be the cause of the following exception</error>');
}
}
} catch (\Exception $e) {}

return parent::renderException($exception, $output);
}







public function getComposer($required = true, $disablePlugins = false)
{
if (null === $this->composer) {
try {
$this->composer = Factory::create($this->io, null, $disablePlugins);
} catch (\InvalidArgumentException $e) {
if ($required) {
$this->io->write($e->getMessage());
exit(1);
}
} catch (JsonValidationException $e) {
$errors = ' - ' . implode(PHP_EOL . ' - ', $e->getErrors());
$message = $e->getMessage() . ':' . PHP_EOL . $errors;
throw new JsonValidationException($message);
}

}

return $this->composer;
}




public function getIO()
{
return $this->io;
}

public function getHelp()
{
return self::$logo . parent::getHelp();
}




protected function getDefaultCommands()
{
$commands = parent::getDefaultCommands();
$commands[] = new Command\AboutCommand();
$commands[] = new Command\ConfigCommand();
$commands[] = new Command\DependsCommand();
$commands[] = new Command\InitCommand();
$commands[] = new Command\InstallCommand();
$commands[] = new Command\CreateProjectCommand();
$commands[] = new Command\UpdateCommand();
$commands[] = new Command\SearchCommand();
$commands[] = new Command\ValidateCommand();
$commands[] = new Command\ShowCommand();
$commands[] = new Command\RequireCommand();
$commands[] = new Command\DumpAutoloadCommand();
$commands[] = new Command\StatusCommand();
$commands[] = new Command\ArchiveCommand();
$commands[] = new Command\DiagnoseCommand();
$commands[] = new Command\RunScriptCommand();
$commands[] = new Command\LicensesCommand();
$commands[] = new Command\GlobalCommand();

if ('phar:' === substr(__FILE__, 0, 5)) {
$commands[] = new Command\SelfUpdateCommand();
}

return $commands;
}




public function getLongVersion()
{
return parent::getLongVersion() . ' ' . Composer::RELEASE_DATE;
}




protected function getDefaultInputDefinition()
{
$definition = parent::getDefaultInputDefinition();
$definition->addOption(new InputOption('--profile', null, InputOption::VALUE_NONE, 'Display timing and memory usage information'));
$definition->addOption(new InputOption('--working-dir', '-d', InputOption::VALUE_REQUIRED, 'If specified, use the given directory as working directory.'));

return $definition;
}




protected function getDefaultHelperSet()
{
$helperSet = parent::getDefaultHelperSet();

$helperSet->set(new DialogHelper());

return $helperSet;
}
}

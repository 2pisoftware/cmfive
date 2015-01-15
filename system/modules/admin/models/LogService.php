<?php

use Monolog\Logger as Logger;
use Monolog\Handler\RotatingFileHandler as RotatingFileHandler;
use Monolog\Processor\WebProcessor as WebProcessor;
use Monolog\Processor\IntrospectionProcessor as IntrospectionProcessor;
use Monolog\Formatter\JsonFormatter as JsonFormatter;

class LogService extends \DbService {
    private $loggers = array();
    private $logger;
    private static $system_logger = 'cmfive';
    
    public function __construct(\Web $w) {
        parent::__construct($w);
        
        $this->addLogger(LogService::$system_logger);
    }
    
    public function logger() { return $this->loggers['cmfive']; }
    
    public function getLogger($name) {
        if (!empty($this->loggers[$name])) {
            return $this->loggers[$name];
        } else {
            return NULL;
        }
    }
    
    public function addLogger($name, $logToSystemFile = true) {
        if (!empty($this->loggers[$name])) {
            return;
        }
        
        $this->loggers[$name] = new Logger($name);
        
        if ($logToSystemFile === true) {
            $filename = ROOT_PATH . "/log/" . LogService::$system_logger . ".log";
        } else {
            $filename = ROOT_PATH . "/log/{$name}.log";
        }
        $handler = new RotatingFileHandler($filename);
        // $handler->setFormatter(new JsonFormatter());
        $this->loggers[$name]->pushHandler($handler);
    }
    
    public function setLogger($name) {
        if (empty($this->loggers[$name])) {
            $this->addLogger($name);
        }
        
        $this->logger = $this->loggers[$name];
        return $this;
    }
    
    // Pass on missed calls to the logger (info, error, warning etc)
    public function __call($name, $arguments) {
        if (empty($this->logger)) {
            $this->logger = $this->loggers[LogService::$system_logger];
        }
        
        if ((!empty($arguments[0]) && $arguments[0] === "info") || stristr($name, "err") !== FALSE) {
            // Add the introspection processor if an error (Adds the line/file/class/method from which the log call originated)
            // $this->logger->pushProcessor(new IntrospectionProcessor());
            $this->logger->pushProcessor(new WebProcessor());
        }
        $this->logger->$name($arguments[0], array("user" => $this->w->session('user_id')));
        
        // In the interest of not breaking system logs, we will return the logger back to cmfive
        // This means for every log that isn't system, the call should look something like this:
        // $w->Log->setLogger('my_log', true|false)->info('Hello, world!');
        $this->logger = $this->loggers[LogService::$system_logger];
    }
}

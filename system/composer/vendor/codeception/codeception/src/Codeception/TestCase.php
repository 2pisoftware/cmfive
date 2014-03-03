<?php

namespace Codeception;

use Codeception\Exception\ConditionalAssertionFailed;
use Codeception\Util\Debug;
use Symfony\Component\EventDispatcher\Event;

abstract class TestCase extends \PHPUnit_Framework_TestCase implements \PHPUnit_Framework_SelfDescribing
{
    protected $scenario;

    protected $trace = array();

    protected $backupGlobalsBlacklist = array('app');

    public function getFeature()
    {
        return null;
    }

    public function getFileName()
    {
        return get_class($this) . '::' . $this->getName(false);
    }

    public function runStep(\Codeception\Step $step)
    {
        $this->trace[] = $step;
        $this->fire('step.before', new \Codeception\Event\Step($this, $step));
        try {
            $result = $step->run();
        } catch (ConditionalAssertionFailed $f) {
            $result = $this->getTestResultObject();
            $result->addFailure(clone($this), $f, $result->time());
        } catch (\Exception $e) {
            $this->fire('step.after', new \Codeception\Event\Step($this, $step));
            throw $e;
        }
        $this->fire('step.after', new \Codeception\Event\Step($this, $step));
        return $result;
    }

    protected function fire($event, Event $eventType)
    {
        foreach ($this->scenario->getGroups() as $group) {
            $this->dispatcher->dispatch($event.'.'.$group, $eventType);
        }
        $this->dispatcher->dispatch($event, $eventType);
    }

    /**
     * @return \Codeception\Scenario
     */
    public function getScenario()
    {
        return $this->scenario;
    }

    public function getTrace()
    {
        return $this->trace;
    }

    public function toString()
    {
        return $this->getFeature();
    }

    protected $dependencies;

    public function setDependencies(array $dependencies)
    {
        $this->dependencies = $dependencies;
    }

    protected function handleDependencies()
    {
        if (empty($this->dependencies)) return true;

        $passed     = $this->getTestResultObject()->passed();
        $testNames = array_map(function($testname) { return preg_replace('~with data set (.*?)~','', $testname); }, array_keys($passed));
        $testNames = array_unique($testNames);

        foreach ($this->dependencies as $dependency) {
            if (in_array($dependency, $testNames)) continue;
            $this->getTestResultObject()->addError($this, new \PHPUnit_Framework_SkippedTestError("This test depends on '$dependency' to pass."),0);
            return false;
        }

        return true;
    }


}

<?php
namespace workFlowManager;


class Action {
    private $actionName;
    private $actionFunction; // A callback function for the action

    public function __construct($name, callable $function) {
        $this->actionName = $name;
        $this->actionFunction = $function;
    }

    public function execute($context) {
        // Call the function assigned to this action, pass context if needed
        call_user_func($this->actionFunction, $context);
    }

    public function getActionName() {
        return $this->actionName;
    }
}


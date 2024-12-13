<?php

namespace workFlowManager;

require_once 'RevokeCondition.php';
require_once 'Action.php';

class Step {
    public $workflow_id_;
    public $step_id_;
    public $step_owner_role;
    public $step_position;
    public $step_name;
    public $step_description;
    public $step_next_step = null;
    public $step_previous_step = null;
    public $step_on_success = null;
    public $step_on_failure = null;
    public $revokeConditions = [];
    private $actions = [];  // Actions that can be performed on this step

    public function __construct(
        $workflow_id_,
        $step_id_,
        $step_owner_role,
        $step_position,
        $step_name = "",
        $step_description = "",
        $step_next_step = null,
        $step_previous_step = null,
        $step_on_success = null,
        $step_on_failure = null
    ) {
        $this->workflow_id_ = $workflow_id_;
        $this->step_id_ = $step_id_;
        $this->step_owner_role = $step_owner_role;
        $this->step_position = $step_position;
        $this->step_name = $step_name;
        $this->step_description = $step_description;
        $this->step_next_step = $step_next_step;
        $this->step_previous_step = $step_previous_step;
        $this->step_on_success = $step_on_success;
        $this->step_on_failure = $step_on_failure;
    }

    public function setOnSuccessStep(Step $step) {
        $this->step_on_success = $step;
    }

    public function setOnFailureStep(Step $step) {
        $this->step_on_failure = $step;
    }
    public function addRevokeCondition(RevokeCondition $condition) {
        // Optionally, add logic to validate or manage conditions before adding
        $this->revokeConditions[] = $condition;
        // For debugging or confirmation, you might log or echo details about the added condition
        // echo "Revoke Condition Added: Target Step ID - " . $condition->getTargetStepId() . ", Resume Step ID - " . $condition->getResumeStepId() . "\n";
    }
    
    

    public function getRevokeTarget($conditionName) {
        return $this->revoke_conditions[$conditionName] ?? null;
    }

    public function getAllRevokeConditions() {
        return $this->revoke_conditions;
    }

    public function addAction($actionName, callable $function) {
        $this->actions[$actionName] = new Action($actionName, $function);
    }

    public function executeAction($actionName, $context) {
        if (isset($this->actions[$actionName])) {
            $this->actions[$actionName]->execute($context);
        } else {
            throw new \Exception("Action {$actionName} not defined for this step.");
        }
    }

    public function onEnter() {
        // Example: Log entering this step
    }

    public function onExit() {
        // Example: Log exiting this step
    }
}

?>

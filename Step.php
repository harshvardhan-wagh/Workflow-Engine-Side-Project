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

    // Linked structure to form a chain of steps
    public $step_next_step = null;
    public $step_previous_step = null;

    // On success/failure transitions
    public $step_on_success = null;
    public $step_on_failure = null;

    // Conditions that may govern transitions.
    // For example, you could map condition types to steps or logic.
    // Key: condition name or type (e.g., 'revoke_to_user'), Value: target Step or ID
    private $revoke_conditions = [];

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

    /**
     * Set the step that should be reached on a successful completion.
     */
    public function setOnSuccessStep(Step $step) {
        $this->step_on_success = $step;
    }

    /**
     * Set the step that should be reached on a failure.
     */
    public function setOnFailureStep(Step $step) {
        $this->step_on_failure = $step;
    }

    /**
     * Add a revoke condition and its target step.
     * For example, if condition is 'revoke_to_user', you can map it to a previous step.
     *
     * @param string $conditionName A unique name/type for the condition (e.g., 'to_user', 'to_previous').
     * @param mixed $targetStep A Step object or a reference to where the workflow should revert to.
     */
    public function addRevokeCondition($conditionName, $targetStep) {
        $this->revoke_conditions[$conditionName] = $targetStep;
    }

    /**
     * Retrieve the target step for a given revoke condition.
     *
     * @param string $conditionName
     * @return Step|null The step associated with the given revoke condition.
     */
    public function getRevokeTarget($conditionName) {
        return isset($this->revoke_conditions[$conditionName]) ? $this->revoke_conditions[$conditionName] : null;
    }

    /**
     * Get all defined revoke conditions for this step.
     *
     * @return array
     */
    public function getAllRevokeConditions() {
        return $this->revoke_conditions;
    }

    /**
     * Optionally, you can add action hooks for when a step is entered or exited.
     * This could be beneficial if you want to log entry or perform validations.
     */
    public function onEnter() {
        // Trigger any logic or audit trail for entering this step
    }

    public function onExit() {
        // Trigger any logic or audit trail for exiting this step
    }
}
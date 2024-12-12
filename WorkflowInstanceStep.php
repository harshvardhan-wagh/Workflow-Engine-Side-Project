<?php

namespace workFlowManager;

class WorkflowInstanceStep {
    public $workflow_id_;
    public $workflow_step_id_;
    public $Instance_step_id_;
    public $Instance_id_;
    public $Instance_step_owner_name;    
    public $Instance_step_description;
    public $Instance_step_on_success = null;
    public $Instance_step_on_failure = null;
    public $Instance_step_next_step = null;
    public $Instance_step_previous_step = null;
    public $Instance_step_revoke_target = null;  // Optional: to handle custom revoke targets
    public $revokeConditions = []; 

    public function __construct(
        $workflow_id_,
        $workflow_step_id_,
        $Instance_step_id_,
        $Instance_id_,
        $Instance_step_owner_name,
        $Instance_step_description = "",
        $Instance_step_on_success = null,
        $Instance_step_on_failure = null,
        $Instance_step_next_step = null,
        $Instance_step_previous_step = null,
        $Instance_step_revoke_target = null  // Initial null, can be set based on conditions
    ) {
        $this->workflow_id_ = $workflow_id_;
        $this->workflow_step_id_ = $workflow_step_id_;
        $this->Instance_step_id_ = $Instance_step_id_;
        $this->Instance_id_ = $Instance_id_;
        $this->Instance_step_owner_name = $Instance_step_owner_name;
        $this->Instance_step_description = $Instance_step_description;
        $this->Instance_step_on_success = $Instance_step_on_success;
        $this->Instance_step_on_failure = $Instance_step_on_failure;
        $this->Instance_step_next_step = $Instance_step_next_step;
        $this->Instance_step_previous_step = $Instance_step_previous_step;
        $this->Instance_step_revoke_target = $Instance_step_revoke_target;
    }

    public function moveToNextStep() {
        if ($this->Instance_step_next_step !== null) {
            return $this->Instance_step_next_step;
        }
        return null;
    }

    public function moveToPreviousStep() {
        if ($this->Instance_step_previous_step !== null) {
            return $this->Instance_step_previous_step;
        }
        return null;
    }

    public function moveToRevokeTarget() {
        if ($this->Instance_step_revoke_target !== null) {
            return $this->Instance_step_revoke_target;
        }
        return null;
    }

    public function addRevokeCondition($revokeCondition) {
        $this->revokeConditions[] = $revokeCondition;
    }
}

?>

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
    public $revokeConditions = []; // Array of RevokeCondition objects

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
        $Instance_step_revoke_target = null
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

    public function addRevokeCondition($targetStepId) {
        $this->revokeConditions[] = new RevokeCondition($targetStepId);
    }


    public function moveToNextStep() {
        return $this->Instance_step_next_step;
    }

    public function moveToPreviousStep() {
        return $this->Instance_step_previous_step;
    }
    public function moveToRevokeTarget() {
        // If there's any condition set, just use the first one (simplified logic)
        if (!empty($this->revokeConditions)) {
            return $this->revokeConditions[0]->getTargetStepId();
        }
        return null;
    }

    public function getCurrentUserRole() {
        // You might need to fetch this from session or a user object associated with the step
        return $this->currentUserRole;  // Assuming this property is set somewhere
    }

    public function getLastAction() {
        // This method should return the last action performed; perhaps tracked via state management
        return $this->lastAction;  // Assuming this property is tracked
    }
}

?>

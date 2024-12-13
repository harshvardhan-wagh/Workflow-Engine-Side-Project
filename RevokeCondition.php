<?php

namespace workFlowManager;

class RevokeCondition {
    private $targetStepId;  // Step to revert to upon revocation
    private $resumeStepId;  // Step to resume from after revocation

    public function __construct($targetStepId, $resumeStepId) {
        $this->targetStepId = $targetStepId;
        $this->resumeStepId = $resumeStepId;
    }

    // Gets the step ID to which the workflow should revert upon revocation
    public function getTargetStepId() {
        return $this->targetStepId;
    }

    // Gets the step ID from which the workflow should resume after revocation
    public function getResumeStepId() {
        // echo " Revoke Condition Resume step Id : ".  $this->resumeStepId ."\n";
        return $this->resumeStepId;
    }
}

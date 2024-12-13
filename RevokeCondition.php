<?php

namespace workFlowManager;

class RevokeCondition {

    private $targetStepId;  // Target to revoke to
    private $resumeStepId;

    public function __construct( $targetStepId) {
        $this->targetStepId = $targetStepId;
        $this->resumeStepId = $resumeStepId;
    }

    public function evaluate($instanceStep) {
        echo "Evaluating Revoke Condition\n";
        echo "Instance Step Description: " . $instanceStep->Instance_step_description . "\n";
        echo "Condition Value: " . $this->conditionValue . "\n";
    
        switch ($this->conditionType) {
            case 'go_back':
                // Assuming the condition to go back is based on a matching description
                return trim(strtolower($instanceStep->Instance_step_description)) === trim(strtolower($this->conditionValue));
            
            // You can add more cases if there are other types of conditions to evaluate
            default:
                return false;
        }
    }
    
    

    public function getTargetStepId() {
        return $this->targetStepId;
    }

    public function getResumeStepId() {
        return $this->resumeStepId;
    }

    
}

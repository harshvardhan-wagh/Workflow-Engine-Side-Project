<?php
namespace workFlowManager;


class StateManager {
    public function transitionToNextStep($currentStep) {
        return $currentStep->step_next_step;
    }

    public function transitionToPreviousStep($currentStep) {
        return $currentStep->step_previous_step;
    }

    public function handleRevoke($currentStep, $revokeCondition) {
        if ($revokeCondition === 'to_user') {
            return $this->transitionToPreviousStep($currentStep);
        }
        
    }
}

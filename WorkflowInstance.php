<?php

namespace workFlowManager;

require_once 'Step.php';
require_once 'WorkflowInstanceStep.php';
require_once 'AuditTrail.php';
require_once 'StateManager.php'; // Make sure you have this class defined for state management

class WorkflowInstance {
    public $workflow;
    public $WorkflowInstance_id_;
    public $WorkflowInstance_name;
    public $WorkflowInstance_description;
    public $WorkflowInstance_stage = 1;
    public $WorkflowInstance_steps_head_node = null;
    public $revoked_stage = null; 
    private $auditTrail;
    private $stateManager; // State manager instance

    public function __construct($workflow, $WorkflowInstance_name, $WorkflowInstance_id_, $is_old_object = false) {
        $this->workflow = $workflow;
        $this->WorkflowInstance_id_ = $WorkflowInstance_id_;
        $this->WorkflowInstance_name = $WorkflowInstance_name;
        $this->WorkflowInstance_description = $workflow->workflow_description;
        $this->auditTrail = new AuditTrail();
        $this->stateManager = new StateManager(); // Initialize state manager
        $this->initializeWorkflowInstanceSteps($is_old_object);
    }

    private function initializeWorkflowInstanceSteps($is_old_object) {
        if (!$is_old_object) {
            $currentStep = $this->workflow->workflow_head_node;
            $lastInstanceStep = null;
    
            while ($currentStep !== null) {
                echo "\nEnter owner name for step '" . $currentStep->step_id_ . "' (Default: " . $currentStep->step_owner_role . "): ";
                $input = trim(fgets(STDIN));
                $ownerName = !empty($input) ? $input : $currentStep->step_owner_role;
    
                $instanceStep = new WorkflowInstanceStep(
                    $this->workflow->workflow_id_,
                    $currentStep->step_id_,
                    $this->WorkflowInstance_id_ . '-' . $currentStep->step_id_,
                    $this->WorkflowInstance_id_,
                    $ownerName,
                    $currentStep->step_description,
                    $currentStep->step_on_success ? $currentStep->step_on_success->step_id_ : null,
                    $currentStep->step_on_failure ? $currentStep->step_on_failure->step_id_ : null
                );
    
                foreach ($currentStep->revokeConditions as $condition) {
                    $instanceStep->addRevokeCondition(new RevokeCondition($condition->getTargetStepId(), $condition->getResumeStepId()));
                }
                // Transfer revoke conditions
                // if (isset($currentStep->revokeConditions) && is_array($currentStep->revokeConditions)) {
                //     foreach ($currentStep->revokeConditions as $condition) {
                //         $instanceStep->addRevokeCondition(clone $condition); // Use clone if necessary
                //     }
                // }

    
                if ($lastInstanceStep === null) {
                    $this->WorkflowInstance_steps_head_node = $instanceStep;
                } else {
                    $lastInstanceStep->Instance_step_next_step = $instanceStep;
                    $instanceStep->Instance_step_previous_step = $lastInstanceStep;
                }
    
                $lastInstanceStep = $instanceStep;
                $currentStep = $currentStep->step_next_step;
            }
        }
        $this->auditTrail->logAction("Initialized WorkflowInstance with ID: " . $this->WorkflowInstance_id_);
    }
    
    
    
    public function acceptStep() {
        if ($this->stateManager->isHalted()) {
            echo "\nProcess is currently halted. Cannot accept the next step.";
            return;
        }
    
        $currentStep = $this->getCurrentStep();
        // echo"\nAccept Current step : ";
       
        if ($this->revoked_stage) {
            // print_r($currentStep);
            $resumeStep = $this->revoked_stage; // Make sure this returns the correct ID
            $this->WorkflowInstance_stage = $this->findStageByStepId($resumeStep);
            $this->revoked_stage = null; // Clear the revoked stage
        } else if ($currentStep && $currentStep->Instance_step_next_step) {
            $this->WorkflowInstance_stage++; // Increment to move to the next step numerically
        } else {
            echo "\nError: No further steps to proceed.";
            return;
        }
    
        $this->stateManager->setCurrentState($this->WorkflowInstance_stage);
        $this->auditTrail->logAction("Accepted step, moved to stage: " . $this->WorkflowInstance_stage);
        echo "\nAccepted. Moved to stage: " . $this->WorkflowInstance_stage;
    }
    
    
    
    
    public function rejectStep() {
        $currentStep = $this->getCurrentStep();
        if ($currentStep) {
            $this->stateManager->setHaltedState(true);  // Set the halted state to true
            $this->auditTrail->logAction("Rejected step, process halted at stage: " . $this->WorkflowInstance_stage);
            echo "\nRejected. Process halted at current stage: " . $this->WorkflowInstance_stage;
        } else {
            echo "\nError: No current step found, cannot process rejection.";
        }
    }
    
    public function evaluateRevokeConditions() {
        // echo "evaluate revoke condition";
        // print_r($this->revokeConditions);
        foreach ($this->revokeConditions as $condition) {
         
            if ($condition->evaluate($this)) { 
                return $condition->getTargetStepId();
            }
        }
        return null;
    }
    
    public function revokeStep() {
        $currentStep = $this->getCurrentStep();
        if ($currentStep) {
            $targetStepId = $currentStep->moveToRevokeTarget();
            $resumeStepId = $currentStep->findResumeStep();
            // echo "\nSteps Id = ".$targetStepId." Resume = ".$resumeStepId;
            if ($targetStepId && $resumeStepId) {
                $this->WorkflowInstance_stage = $this->findStageByStepId($targetStepId);
                // echo " \nWorkflow instance revoked to stage: " . $this->WorkflowInstance_stage;
                $this->auditTrail->logAction("Workflow instance revoked to stage: " . $this->WorkflowInstance_stage);
                $this->stateManager->setCurrentState($this->WorkflowInstance_stage);
                // echo "\nWorkflow instance revoked to stage: " . $this->WorkflowInstance_stage . ". Ready for resubmission at stage " . $resumeStepId;
                $this->revoked_stage = $resumeStepId; // Store where to resume after revocation
            
            } else {
                echo "\nError: No revocation or resume target defined for the current step.";
            }
        } else {
            echo "\nError: Current step is undefined, revocation cannot be processed.";
        }
    }
    
    
    private function findStageByStepId($stepId) {
        $current = $this->WorkflowInstance_steps_head_node;
        // echo "findStageByStepId input : ".$stepId." current obj : ".$current->Instance_step_id_;
        $stage = 1;
        while ($current !== null) {  
            if ($current->Instance_step_id_ === "wf_instance_001-".$stepId) {
                return $stage;
            }
            // echo " \ncurrent Step : ".$current->Instance_step_id_." Step to find: ".$stepId;
            $current = $current->Instance_step_next_step;
            $stage++;
        }
        return null;
    }
    
    
    

    public function displayCurrentStatus() {
        $currentStep = $this->getCurrentStep();
        if ($currentStep) {
            echo "\nCurrent Workflow Instance State:\n";
            echo "Stage: " . $this->WorkflowInstance_stage . "\n";
            echo "Step ID: " . $currentStep->Instance_step_id_ . "\n";
            echo "Step Owner: " . $currentStep->Instance_step_owner_name . "\n";
            echo "Step Description: " . $currentStep->Instance_step_description . "\n";
        } else {
            echo "\nError: No current step found.";
        }
    }


    private function getCurrentStep() {
        $current = $this->WorkflowInstance_steps_head_node;
        $stepCounter = 1; // Start counting from 1 for the first step
    
        while ($current !== null && $stepCounter < $this->WorkflowInstance_stage) {
            $current = $current->Instance_step_next_step;
            $stepCounter++;
        }
    
        if ($current === null) {
            echo "Reached a null step at stage " . $this->WorkflowInstance_stage . ". This indicates an off-by-one error or a misconfiguration.\n";
        }
    
        return $current;
    }
    
    
    

    function traverseWorkflowInstance($workProcess) {
        while (true) {
            echo "\nEnter the operation to perform (1-Accept, 2-Reject, 3-Revoke, 4-Status, 0-Quit): ";
            $choice = trim(fgets(STDIN));
    
            switch ($choice) {
                case '1': // Accept
                    $workProcess->acceptStep();
                    break;
                case '2': // Reject
                    $workProcess->rejectStep();
                    break;
                case '3': // Revoke
                    $workProcess->revokeStep();
                    break;
                case '4': // Display current status
                    $workProcess->displayCurrentStatus();
                    break;
                case '0': // Quit the menu
                    echo "Exiting workflow traversal.";
                    return;
                default:
                    echo "Invalid choice, please try again.";
                    break;
            }
        }
    }

    public function displayAllSteps() {
        $current = $this->WorkflowInstance_steps_head_node;
        if ($current === null) {
            echo "No steps are initialized in this WorkflowInstance.\n";
            return;
        }
    
        echo "\nCurrent Workflow Instance Steps:\n";
        while ($current !== null) {
            echo "Step ID: " . $current->Instance_step_id_ . "\n";
            echo "Owner Name: " . $current->Instance_step_owner_name . "\n";
            echo "Description: " . $current->Instance_step_description . "\n";
            echo "On Success: " . ($current->Instance_step_on_success ? $current->Instance_step_on_success : "None") . "\n";
            echo "On Failure: " . ($current->Instance_step_on_failure ? $current->Instance_step_on_failure : "None") . "\n";
            echo "----\n";
            $current = $current->Instance_step_next_step;
        }
    }
    
}

?>

<?php

namespace workFlowManager;

require_once 'Step.php';
require_once 'WorkflowInstanceStep.php';
require_once 'AuditTrail.php';  // Make sure you have this class defined for logging actions

class WorkflowInstance {
    public $workflow;
    public $WorkflowInstance_id_;
    public $WorkflowInstance_name;
    public $WorkflowInstance_description;
    public $WorkflowInstance_stage = 1;
    public $WorkflowInstance_steps_head_node = null;
    public $revoked_stage = null; 
    private $auditTrail;

    public function __construct($workflow, $WorkflowInstance_name, $WorkflowInstance_id_, $is_old_object = false) {
        $this->workflow = $workflow;
        $this->WorkflowInstance_id_ = $WorkflowInstance_id_;
        $this->WorkflowInstance_name = $WorkflowInstance_name;
        $this->WorkflowInstance_description = $workflow->workflow_description;
        $this->auditTrail = new AuditTrail();
        $this->initializeWorkflowInstanceSteps($is_old_object);
    }

  
    private function initializeWorkflowInstanceSteps($is_old_object) {
        if (!$is_old_object) {
            $currentStep = $this->workflow->workflow_head_node;
            $lastInstanceStep = null;
    
            while ($currentStep !== null) {
                echo "\nEnter owner name for step '" . $currentStep->step_id_ . "' (Default: " . $currentStep->step_owner_role . "): ";
                $input = trim(fgets(STDIN));
                $ownerName = !empty($input) ? $input : $currentStep->step_owner_role; // Use default if no input
    
                $instanceStep = new WorkflowInstanceStep(
                    $this->workflow->workflow_id_,
                    $currentStep->step_id_,
                    $this->WorkflowInstance_id_ . '-' . $currentStep->step_id_,
                    $this->WorkflowInstance_id_,
                    $ownerName,
                    $currentStep->step_description,
                    $currentStep->step_on_success,
                    $currentStep->step_on_failure,
    
                );
    
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
        $this->auditTrail->logAction("Initialized WorkflowInstance with ID: " . $this->WorkflowInstance_id_,"Initialize workflow");
    }
    

  
    public function acceptStep() {
        if ($this->revoked_stage && $this->WorkflowInstance_stage == 1) {
            // If accepting from initial stage and there was a revocation, return to the revoked stage
            $this->WorkflowInstance_stage = $this->revoked_stage;
            $this->revoked_stage = null;  // Clear the revoked stage
            $this->auditTrail->logAction("Returned to stage " . $this->WorkflowInstance_stage . " after addressing revocation");
            echo "\nReturned to stage " . $this->WorkflowInstance_stage . " after addressing revocation.";
        } elseif ($this->WorkflowInstance_stage < $this->workflow->workflow_step_len) {
            $this->WorkflowInstance_stage++;
            $this->auditTrail->logAction("Accepted step, moved to next stage: " . $this->WorkflowInstance_stage, "Accept");
            echo "\nAccepted. Moved to next stage: " . $this->WorkflowInstance_stage;
        } else {
            echo "\nError: Already at the last stage, cannot accept the step.";
        }
    }

    public function rejectStep() {
        $this->last_rejected_stage = $this->WorkflowInstance_stage;
        echo "\nRejected. Workflow halted at stage: " . $this->WorkflowInstance_stage;
        $this->auditTrail->logAction("Rejected step at stage: " . $this->WorkflowInstance_stage, "Reject");
    }

    public function revokeStep() {
        // Assuming the initial stage involves user input
        if ($this->WorkflowInstance_stage > 1) {
            $this->revoked_stage = $this->WorkflowInstance_stage;  // Store the current stage before revoking
            $this->WorkflowInstance_stage = 1;  // Send back to initial stage (e.g., user submission stage)
            $this->auditTrail->logAction("Workflow instance revoked to initial stage from stage: " . $this->revoked_stage);
            echo "\nWorkflow instance revoked to initial stage. It will return to stage " . $this->revoked_stage . " upon resubmission.";
        } else {
            echo "\nError: Revocation is not applicable from the initial stage.";
        }
    }

    public function displayCurrentStatus() {
        $currentStep = $this->getCurrentStep();
        if ($currentStep !== null) {
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
        for ($i = 1; $i < $this->WorkflowInstance_stage; $i++) {
            $current = $current->Instance_step_next_step;
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
    
}

?>

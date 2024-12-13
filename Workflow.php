<?php

namespace workFlowManager;

require_once 'Step.php';
require_once 'AuditTrail.php';
require_once 'StateManager.php';

class Workflow {
    public $workflow_name;
    public $workflow_id_;
    public $workflow_head_node = null;
    public $workflow_step_len = 0;
    public $workflow_description;
    public $auditTrail;

    public function __construct($name, $workflow_id_="", $workflow_description="") {
        $this->workflow_name = $name;
        $this->workflow_id_ = $workflow_id_;
        $this->workflow_description = $workflow_description;
        $this->auditTrail = new AuditTrail();  // Assume AuditTrail has methods to log actions
    }

    public function addStep($step_id_, $step_owner_role) {
        // Calculate the position of the new step
        $step_position = $this->workflow_step_len + 1;
    
        // Create a new step with all required parameters, including position
        $new_step = new Step($this->workflow_id_, $step_id_, $step_owner_role, $step_position);
    
        // Attach the new step to the linked list of steps
        if ($this->workflow_head_node === null) {
            $this->workflow_head_node = $new_step;
        } else {
            $current = $this->workflow_head_node;
            while ($current->step_next_step !== null) {
                $current = $current->step_next_step;
            }
            $current->step_next_step = $new_step;
            $new_step->step_previous_step = $current;
        }
    
        // Increment the length of the workflow steps
        $this->workflow_step_len++;
    
        // Log the action of adding a new step
        $this->auditTrail->logAction("Added step: " . $step_id_);
    
        return $new_step;
    }
    

    public function removeStep($step_id_) {
        $current = $this->workflow_head_node;
        $previous = null;
        while ($current !== null) {
            if ($current->step_id_ === $step_id_) {
                if ($previous !== null) {
                    $previous->step_next_step = $current->step_next_step;
                } else {
                    $this->workflow_head_node = $current->step_next_step;
                }
                if ($current->step_next_step !== null) {
                    $current->step_next_step->step_previous_step = $previous;
                }
                $this->workflow_step_len--;
                $this->auditTrail->logAction("Removed step: $step_id_");
                return true;
            }
            $previous = $current;
            $current = $current->step_next_step;
        }
        return false;
    }

    public function display() {
        echo "\nWorkflow Details:";
        echo "\nWorkflow ID: {$this->workflow_id_}";
        echo "\nWorkflow Name: {$this->workflow_name}";
        echo "\nWorkflow Description: {$this->workflow_description}";
        echo "\nWorkflow Steps:";
        
        $step = $this->workflow_head_node;
        while ($step !== null) {
            echo "\nStep ID: {$step->step_id_}, Owner: {$step->step_owner_role}, Position: {$step->step_position}";
            $step = $step->step_next_step;
        }
    }
}

?>

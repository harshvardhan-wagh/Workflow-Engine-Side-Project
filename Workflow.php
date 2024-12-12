<?php

namespace workFlowManager;

require_once 'Step.php';
require_once 'AuditTrail.php';
require_once 'StateManager.php';


class Workflow {
    public $workflow_name;
    public $workflow_id_; 
    public $workflow_head_node = null;
    public $workflow_step_len;
    public $workflow_description;
    public $auditTrail; 

    public function __construct($name, $workflow_id_="",$workflow_description="") {
        $this->workflow_name = $name;
        $this->workflow_id_ = $workflow_id_;
        $this->workflow_step_len = 0;
        $this->workflow_description = $workflow_description;
        $this->auditTrail = new AuditTrail(); // Default to a new instance if not provided.
    }

//?--------------------------------------------------------------
    public function addStep($step_id_, $step_owner_role) {
        $step_position = $this->workflow_step_len + 1;
        $new_step = new Step($this->workflow_id_, $step_id_, $step_owner_role, $step_position);
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
        $this->workflow_step_len++;
        $this->auditTrail->logAction("Added step: $step_id_","add step");
        return $new_step;
    }

    public function removeStep($step_id_) {
        $current = $this->workflow_head_node;
        while ($current !== null) {
            if ($current->step_id_ == $step_id_) {
                if ($current->step_previous_step) {
                    $current->step_previous_step->step_next_step = $current->step_next_step;
                } else {
                    $this->workflow_head_node = $current->step_next_step;
                }
                if ($current->step_next_step) {
                    $current->step_next_step->step_previous_step = $current->step_previous_step;
                }
                $this->workflow_step_len--;
                $this->updateStepPositions();
                $this->auditTrail->logAction("Removed step: $step_id_", "remove step");
                return true;
            }
            $current = $current->step_next_step;
        }
        return false;
    }

//?---------------------------------------------------------------------


    public function addStepAtFront($step_id_, $step_owner_role) {
        $step_position = 1;
        $new_step = new Step($this->workflow_id_, $step_id_, $step_owner_role, $step_position);
        if ($this->workflow_head_node !== null) {
            $new_step->step_next_step = $this->workflow_head_node;
            $this->workflow_head_node->step_previous_step = $new_step;
            $this->workflow_head_node = $new_step;
            $this->updateStepPositions();
        } else {
            $this->workflow_head_node = $new_step;
        }
        $this->workflow_step_len++;
    }

    public function addStepAtEnd($step_id_, $step_owner_role) {
        $this->addStep($step_id_, $step_owner_role);
    }

    public function addStepInMiddle($position, $step_id_, $step_owner_role) {
        if ($position <= 1) {
            $this->addStepAtFront($step_id_, $step_owner_role);
            return;
        }

        $new_step = new Step($this->workflow_id_, $step_id_, $step_owner_role, $position);
        $current = $this->workflow_head_node;
        $currentPosition = 1;

        while ($current !== null && $currentPosition < $position - 1) {
            $current = $current->step_next_step;
            $currentPosition++;
        }

        if ($current === null || $current->step_next_step === null) {
            $this->addStepAtEnd($step_id_, $step_owner_role);
        } else {
            $new_step->step_next_step = $current->step_next_step;
            $new_step->step_previous_step = $current;
            if ($current->step_next_step !== null) {
                $current->step_next_step->step_previous_step = $new_step;
            }
            $current->step_next_step = $new_step;
            $this->updateStepPositions();
        }
        $this->workflow_step_len++;
    }

    public function modifyStep($step_id_, $new_step_owner) {
        $current = $this->workflow_head_node;
        while ($current !== null) {
            if ($current->step_id_ == $step_id_) {
                $current->step_owner_role = $new_step_owner;
                return;
            }
            $current = $current->step_next_step;
        }
        print("\nStep with id $step_id_ not found.");
    }

    public function deleteStep($step_id_) {
        $current = $this->workflow_head_node;
        while ($current !== null) {
            if ($current->step_id_ == $step_id_) {
                if ($current->step_previous_step !== null) {
                    $current->step_previous_step->step_next_step = $current->step_next_step;
                } else {
                    $this->workflow_head_node = $current->step_next_step;
                }
                if ($current->step_next_step !== null) {
                    $current->step_next_step->step_previous_step = $current->step_previous_step;
                }
                $this->updateStepPositions();
                $this->workflow_step_len--;
                return true;
            }
            $current = $current->step_next_step;
        }
        print("\nStep with id $step_id_ not found.");
        return false;
    }

    private function updateStepPositions() {
        $current = $this->workflow_head_node;
        $position = 1;
        while ($current !== null) {
            $current->step_position = $position;
            $position++;
            $current = $current->step_next_step;
        }
    }

    public function nextWorkflowStep($role){
    //    print("getting here");
        $current = $this->workflow_head_node;
        // print_r($current);
        while ($current !== null) {
           
            if ($current->step_owner_role == $role) {
               $next_stage =  $current->step_next_step;
               return $next_stage->step_position;
            }
            $current = $current->step_next_step;
        }
        return null;
    }

    public function display() {
        echo "\n╔═════════════════════════════════════════════════════════════════════════╗";
        echo "\n║                           Workflow Details                              ║";
        echo "\n╠═════════════════════════════════════════════════════════════════════════╣";
        printf("\n");
        printf("║ %-20s : %-48s ║\n", "Workflow Id", $this->workflow_id_);
        printf("║ %-20s : %-48s ║\n", "Workflow Name", $this->workflow_name);
        printf("║ %-20s : %-48s ║\n", "Workflow Length", $this->workflow_step_len);
        printf("║ %-20s : %-48s ║\n", "Workflow Description", $this->workflow_description);
        echo "╚═════════════════════════════════════════════════════════════════════════╝";
        
        $step = $this->workflow_head_node;
        while ($step !== null) {
            echo "\n╔═════════════════════════════════════════════════════════════════════════╗";
            echo "\n║                             Step Details                                ║";
            echo "\n╠═════════════════════════════════════════════════════════════════════════╣";
            printf("\n");
            printf("║ %-20s : %-48s ║\n", "Step Id", $step->step_id_);
            printf("║ %-20s : %-48s ║\n", "Step Owner", $step->step_owner_role);
            printf("║ %-20s : %-48s ║\n", "Step Position", $step->step_position);
            printf("║ %-20s : %-48s ║\n", "On Success", $step->step_on_success ? $step->step_on_success->step_id_ : "None");
            printf("║ %-20s : %-48s ║\n", "On Failure", $step->step_on_failure ? $step->step_on_failure->step_id_ : "None");
            
            if ($step->step_next_step) {
                printf("║ %-20s : %-48s ║\n", "Next Step", $step->step_next_step->step_id_);
            }
            if ($step->step_previous_step) {
                printf("║ %-20s : %-48s ║\n", "Previous Step", $step->step_previous_step->step_id_);
            }
    
            // Print Revoke Conditions
            $revokeConditions = $step->getAllRevokeConditions();
            if (!empty($revokeConditions)) {
                echo "║ Revoke Conditions (ConditionName => StepId)                            ║\n";
                echo "╠─────────────────────────────────────────────────────────────────────────╣\n";
                foreach ($revokeConditions as $conditionName => $targetStep) {
                    $targetStepId = $targetStep ? $targetStep->step_id_ : "None";
                    printf("║ %-20s : %-48s ║\n", $conditionName, $targetStepId);
                }
            }
    
            echo "╚═════════════════════════════════════════════════════════════════════════╝";
            $step = $step->step_next_step;
        }
    }
    
    
}

?>
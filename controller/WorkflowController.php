<?php

namespace workFlowManager;

require_once __DIR__ . '/../models/WorkflowModel.php';

class WorkflowController {
    private $model;

    public function __construct() {
        $this->model = new WorkflowModel();
    }

    public function createWorkflow($name, $description) {
        $workflow = new Workflow($name, '', $description);
        $workflowId = $this->model->saveWorkflow($workflow);
        if ($workflowId) {
            echo "Workflow created with ID: $workflowId\n";
        } else {
            echo "Failed to create workflow\n";
        }
    }

    public function saveWorkflow($workflow){
        $workflowId = $this->model->saveWorkflow($workflow);
        if ($workflowId){
            echo "Workflow Created with ID : $workflowId\n";
        }else{
            echo "Failed to Create Workflow\n";
        }
    }

    public function updateWorkflow($workflow_id, $name, $description) {
        $workflow = new Workflow($name, $workflow_id, $description);
        if ($this->model->updateWorkflow($workflow)) {
            echo "Workflow updated successfully\n";
        } else {
            echo "Failed to update workflow\n";
        }
    }

    public function deleteWorkflow($workflow_id) {
        if ($this->model->deleteWorkflow($workflow_id)) {
            echo "Workflow deleted successfully\n";
        } else {
            echo "Failed to delete workflow\n";
        }
    }
}
?>

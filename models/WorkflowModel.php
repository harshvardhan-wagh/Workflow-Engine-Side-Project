<?php

namespace workFlowManager;

require_once __DIR__ . '/../config/Database.php'; // Assuming a Database connection class

class WorkflowModel {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function saveWorkflow($workflow) {
        $sql = "INSERT INTO workflows (workflow_name, workflow_description) VALUES (?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ss", $workflow->workflow_name, $workflow->workflow_description);
        if ($stmt->execute()) {
            $workflow->workflow_id_ = $this->db->insert_id;
            return $workflow->workflow_id_;
        } else {
            return false;
        }
    }


    public function updateWorkflow($workflow) {
        $sql = "UPDATE workflows SET workflow_name = ?, workflow_description = ? WHERE workflow_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ssi", $workflow->workflow_name, $workflow->workflow_description, $workflow->workflow_id_);
        return $stmt->execute();
    }

    public function deleteWorkflow($workflow_id) {
        $sql = "DELETE FROM workflows WHERE workflow_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $workflow_id);
        return $stmt->execute();
    }
}
?>

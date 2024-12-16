<?php

namespace workFlowManager;

require_once __DIR__ . '/../config/Database.php'; // Ensure this path is correct

class WorkflowModel {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function saveWorkflow($workflow) {
        $sql = "INSERT INTO workflows (workflow_name, workflow_description) VALUES (?, ?)";
        $stmt = $this->db->prepare($sql);
        // Bind parameters using PDO's bindParam method
        $stmt->bindParam(1, $workflow->workflow_name);
        $stmt->bindParam(2, $workflow->workflow_description);
        if ($stmt->execute()) {
            $workflow->workflow_id_ = $this->db->pdo->lastInsertId();
            return $workflow->workflow_id_;
        } else {
            return false;
        }
    }

    public function updateWorkflow($workflow) {
        $sql = "UPDATE workflows SET workflow_name = ?, workflow_description = ? WHERE workflow_id = ?";
        $stmt = $this->db->prepare($sql);
        // Bind parameters
        $stmt->bindParam(1, $workflow->workflow_name);
        $stmt->bindParam(2, $workflow->workflow_description);
        $stmt->bindParam(3, $workflow->workflow_id_);
        return $stmt->execute();
    }

    public function deleteWorkflow($workflow_id) {
        $sql = "DELETE FROM workflows WHERE workflow_id = ?";
        $stmt = $this->db->prepare($sql);
        // Bind parameter
        $stmt->bindParam(1, $workflow_id);
        return $stmt->execute();
    }
}
?>

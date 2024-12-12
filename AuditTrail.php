<?php

namespace workFlowManager;

class AuditTrail {
    private $logs = [];

    public function logAction($workflowInstanceId, $action=null) {
        $this->logs[] = [
            'workflowInstanceId' => $workflowInstanceId,
            'action' => $action,
            'timestamp' => time(),
        ];
    }

    public function getLogs() {
        return $this->logs;
    }
}

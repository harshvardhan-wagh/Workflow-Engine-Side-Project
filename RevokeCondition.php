<?php

namespace workFlowManager;

class RevokeCondition {
    public $condition_type;

    public function __construct($condition_type) {
        $this->condition_type = $condition_type;
    }

    public function evaluate($context) {
        // Logic to evaluate the condition based on the context.
        return $context === $this->condition_type;
    }
}

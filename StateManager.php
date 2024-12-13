<?php
namespace workFlowManager;

class StateManager {
    private $currentState;
    private $stateHistory = [];
    private $isHalted = false;  // Flag to indicate if the workflow is halted

    /**
     * Sets the current state and tracks state history.
     *
     * @param mixed $state The new state to set.
     */
    public function setCurrentState($state) {
        if (!$this->isHalted) {  // Only change state if not halted
            if ($this->currentState !== null) {
                $this->stateHistory[] = $this->currentState;  // Push current state to history before changing it
            }
            $this->currentState = $state;
        }
    }

    /**
     * Gets the current state of the workflow.
     *
     * @return mixed The current state.
     */
    public function getCurrentState() {
        return $this->currentState;
    }

    /**
     * Reverts to the previous state, if possible.
     *
     * @return bool True if the state was reverted, false if not possible.
     */
    public function revertToPreviousState() {
        if (!empty($this->stateHistory)) {
            $this->currentState = array_pop($this->stateHistory);
            return true;
        }
        return false;
    }

    /**
     * Retrieves the state history array.
     *
     * @return array The history of states.
     */
    public function getStateHistory() {
        return $this->stateHistory;
    }

    /**
     * Sets the halted status of the workflow.
     *
     * @param bool $halted Whether the workflow is halted.
     */
    public function setHaltedState($halted) {
        $this->isHalted = $halted;
    }

    /**
     * Checks if the workflow is currently halted.
     *
     * @return bool True if the workflow is halted, false otherwise.
     */
    public function isHalted() {
        return $this->isHalted;
    }
}

<?php

namespace workFlowManager;

require_once 'Workflow.php';       // Ensure paths/namespaces are correct
require_once 'Step.php';
require_once 'StateManager.php';
require_once 'Action.php';
require_once 'AuditTrail.php';
require_once 'RevokeCondition.php';
require_once 'WorkflowInstance.php';


// 1. Create the Workflow
$workflow = new Workflow("Asset Transfer Workflow", "wf_asset_transfer", "Workflow for asset transfer process");

// 2. Add Steps
$employeeStep    = $workflow->addStep("st1", "Employee");
$flaStep         = $workflow->addStep("st2", "FLA");
$mmgAdminStep    = $workflow->addStep("st3", "MMG Admin");
$systemAdminStep = $workflow->addStep("st4", "System Admin");
$processComplete = $workflow->addStep("st5", "System"); // A final step to mark completion; owner "System" or "None"

// 3. Define On Success transitions
// Employee submission goes to FLA
$employeeStep->setOnSuccessStep($flaStep);

// FLA if approve goes to MMG Admin
$flaStep->setOnSuccessStep($mmgAdminStep);

// MMG Admin if approve goes to System Admin
$mmgAdminStep->setOnSuccessStep($systemAdminStep);

// System Admin if approve => Process Complete
$systemAdminStep->setOnSuccessStep($processComplete);

// 4. Define On Failure transitions (Reject scenario: stays at the same level)
// For "staying at the same level" on failure, just set the On Failure step to the step itself.
$flaStep->setOnFailureStep($flaStep);
$mmgAdminStep->setOnFailureStep($mmgAdminStep);
$systemAdminStep->setOnFailureStep($systemAdminStep);

// Since Employee is the start step, if failure occurs at Employee, it probably just stays there as well.
$employeeStep->setOnFailureStep($employeeStep);

// For the final process complete step, typically On Failure doesn't apply, but you can leave it as None.

// 5. Define Revoke Conditions
// Revoke always goes back to Employee (st1), for FLA, MMG Admin, and System Admin
$flaStep->addRevokeCondition("revoke_to_employee", $employeeStep);
$mmgAdminStep->addRevokeCondition("revoke_to_employee", $flaStep);
$systemAdminStep->addRevokeCondition("revoke_to_employee", $mmgAdminStep);

// Employee step may not need a revoke condition since it's the starting point.

// 6. Display the final Workflow structure
$workflow->display();

// Optionally, you can print out revoke conditions for debugging:
// Just to confirm revoke conditions for FLA step
// $revokeConditionsFLA = $flaStep->getAllRevokeConditions();
// print_r($revokeConditionsFLA);

//Process Instance Creation
// Assuming all the necessary classes have been included already.

// Create a new instance of the workflow
$workflowInstance = new WorkflowInstance($workflow, "Asset Transfer Process Instance", "wf_instance_001", false);

$workflowInstance->traverseWorkflowInstance($workflowInstance);
// Display initial state of the workflow instance
echo "\nInitial Workflow Instance State:";
$workflowInstance->displayCurrentStatus();



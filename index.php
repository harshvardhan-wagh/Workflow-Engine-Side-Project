<?php

namespace workFlowManager;

require_once 'Workflow.php';
require_once 'Step.php';
require_once 'StateManager.php';
require_once 'Action.php';
require_once 'AuditTrail.php';
require_once 'RevokeCondition.php';
require_once 'WorkflowInstance.php';
require_once 'WorkflowInstanceStep.php';

require_once 'controller\WorkflowController.php';

// 1. Create the Workflow
$workflow = new Workflow("Asset Transfer Workflow", "wf_asset_transfer", "Workflow for asset transfer process");

// 2. Add Steps with actions and transitions
$employeeStep = $workflow->addStep("st1", "Employee");
$flaStep = $workflow->addStep("st2", "FLA");
$mmgAdminStep = $workflow->addStep("st3", "MMG Admin");
$systemAdminStep = $workflow->addStep("st4", "System Admin");
$processComplete = $workflow->addStep("st5", "System");


$controller = new WorkflowController();
$controller->saveWorkflow($workflow);

// Saving Workflow


// // Define actions (e.g., approval actions)
// // Example: Add a simple action to log when a step is approved
// $logApprovalAction = function($context) {
//     echo "Action logged: Approval at " . $context->step_id_;
// };

// $employeeStep->addAction("approve", $logApprovalAction);
// $flaStep->addAction("approve", $logApprovalAction);
// $mmgAdminStep->addAction("approve", $logApprovalAction);
// $systemAdminStep->addAction("approve", $logApprovalAction);

// // 3. Define On Success transitions
// $employeeStep->setOnSuccessStep($flaStep);
// $flaStep->setOnSuccessStep($mmgAdminStep);
// $mmgAdminStep->setOnSuccessStep($systemAdminStep);
// $systemAdminStep->setOnSuccessStep($processComplete);

// // 4. Define On Failure transitions (staying at the same level or custom logic)
// $employeeStep->setOnFailureStep($employeeStep);
// $flaStep->setOnFailureStep($flaStep);
// $mmgAdminStep->setOnFailureStep($mmgAdminStep);
// $systemAdminStep->setOnFailureStep($systemAdminStep);

// // 5. Define Revoke Conditions
// // Adding conditions directly inside the step setup
// $flaStep->addRevokeCondition(new RevokeCondition($employeeStep->step_id_, $flaStep->step_id_));
// $mmgAdminStep->addRevokeCondition(new RevokeCondition($employeeStep->step_id_, $mmgAdminStep->step_id_));
// $systemAdminStep->addRevokeCondition(new RevokeCondition($employeeStep->step_id_, $systemAdminStep->step_id_));


// // 6. Display the final Workflow structure
// $workflow->display();

// // Create a new instance of the workflow
// $workflowInstance = new WorkflowInstance($workflow, "Asset Transfer Process Instance", "wf_instance_001","345412");

// $workflowInstance->displayAllSteps();

// // 7. Process Interaction (simulate user input)
// $workflowInstance->traverseWorkflowInstance($workflowInstance);




<?php

add_action( 'rest_api_init', function () {
	$ow_workflow_service = new OW_Workflow_Service();
	$ow_process_flow     = new OW_Process_Flow();
	$ow_revision_service = new OW_Revision_Service();

	// Register route to get worklfow list that are valid
	register_rest_route( 'oasis-workflow/v1', '/workflows/postId=(?P<post_id>\d+)', array(
		'methods'             => 'GET',
		'callback'            => array( $ow_workflow_service, 'api_get_valid_workflows' ),
		'permission_callback' => '__return_true'
	) );

	// Register Route to fetch step details
	register_rest_route( 'oasis-workflow/v1',
		'/workflows/submit/firstStep/workflowId=(?P<wf_id>\d+)/postId=(?P<post_id>\d+)', array(
			'methods'             => 'GET',
			'callback'            => array( $ow_process_flow, 'api_get_first_step_details' ),
			'permission_callback' => '__return_true'
		) );

	// Register route to check is role is applicable to submit to workflow
	register_rest_route( 'oasis-workflow/v1',
		'/workflows/submit/checkRoleCapability/postId=(?P<post_id>\d+)/postType=(?P<post_type>[a-zA-Z0-9-_]+)', array(
			'methods'             => 'GET',
			'callback'            => array( $ow_process_flow, 'api_check_is_role_applicable' ),
			'permission_callback' => '__return_true'
		) );


	// Register Route to save workflow submit data
	register_rest_route( 'oasis-workflow/v1', '/workflows/submit', array(
		'methods'             => 'POST',
		'callback'            => array( $ow_process_flow, 'api_submit_to_workflow' ),
		'permission_callback' => '__return_true'
	) );

	// Register Route to abort a workflow
	register_rest_route( 'oasis-workflow/v1', '/workflows/abort', array(
		'methods'             => 'POST',
		'callback'            => array( $ow_process_flow, 'api_workflow_abort' ),
		'permission_callback' => '__return_true'
	) );

	// Register Route to fetch step actions
	register_rest_route( 'oasis-workflow/v1',
		'/workflows/signoff/stepActions/actionHistoryId=(?P<action_history_id>\d+)', array(
			'methods'             => 'GET',
			'callback'            => array( $ow_workflow_service, 'api_get_step_action_details' ),
			'permission_callback' => '__return_true'
		) );

	// Register Route to fetch signoff next steps
	register_rest_route( 'oasis-workflow/v1',
		'/workflows/signoff/nextSteps/actionHistoryId=(?P<action_history_id>\d+)/decision=(?P<decision>[a-zA-Z0-9-]+)/postId=(?P<post_id>\d+)',
		array(
			'methods'             => 'GET',
			'callback'            => array( $ow_process_flow, 'api_get_signoff_next_steps' ),
			'permission_callback' => '__return_true'
		) );

	// Register Route to fetch step details
	register_rest_route( 'oasis-workflow/v1',
		'/workflows/signoff/stepDetails/actionHistoryId=(?P<action_history_id>\d+)/stepId=(?P<step_id>\d+)/postId=(?P<post_id>\d+)',
		array(
			'methods'             => 'GET',
			'callback'            => array( $ow_process_flow, 'api_get_step_details' ),
			'permission_callback' => '__return_true'
		) );

	// Register Route to save workflow signoff submit data
	register_rest_route( 'oasis-workflow/v1', '/workflows/signoff', array(
		'methods'             => 'POST',
		'callback'            => array( $ow_process_flow, 'api_submit_to_step' ),
		'permission_callback' => '__return_true'
	) );

	// Register Route for workflow complete
	register_rest_route( 'oasis-workflow/v1', '/workflows/signoff/workflowComplete', array(
		'methods'             => 'POST',
		'callback'            => array( $ow_process_flow, 'api_workflow_complete' ),
		'permission_callback' => '__return_true'
	) );

	// Register Route for workflow cancel
	register_rest_route( 'oasis-workflow/v1', '/workflows/signoff/workflowCancel', array(
		'methods'             => 'POST',
		'callback'            => array( $ow_process_flow, 'api_workflow_cancel' ),
		'permission_callback' => '__return_true'
	) );

	// Register route to check for claim
	register_rest_route( 'oasis-workflow/v1', '/workflows/claim/actionHistoryId=(?P<action_history_id>\d+)', array(
		'methods'             => 'GET',
		'callback'            => array( $ow_process_flow, 'api_check_for_claim' ),
		'permission_callback' => '__return_true'
	) );

	// Register Route to claim the task
	register_rest_route( 'oasis-workflow/v1', '/workflows/claim?', array(
		'methods'             => 'POST',
		'callback'            => array( $ow_process_flow, 'api_claim_process' ),
		'permission_callback' => '__return_true'
	) );


	// Register route to check capability of role to create revision
	register_rest_route( 'oasis-workflow/v1',
		'/workflows/documentRevision/checkRevisionCapability/postId=(?P<post_id>\d+)', array(
			'methods'             => 'GET',
			'callback'            => array( $ow_revision_service, 'api_is_make_revision_allowed' ),
			'permission_callback' => '__return_true'
		) );

	// Register route to check for existing revision
	register_rest_route( 'oasis-workflow/v1',
		'/workflows/documentRevision/checkExistingRevision/postId=(?P<post_id>\d+)', array(
			'methods'             => 'GET',
			'callback'            => array( $ow_revision_service, 'api_get_current_revision' ),
			'permission_callback' => '__return_true'
		) );

	// Register route to check for existing revision
	register_rest_route( 'oasis-workflow/v1', '/workflows/documentRevision/postId=(?P<post_id>\d+)', array(
		'methods'             => 'DELETE',
		'callback'            => array( $ow_process_flow, 'api_delete_post' ),
		'permission_callback' => '__return_true'
	) );

	// Register Route for making revision
	register_rest_route( 'oasis-workflow/v1', '/workflows/documentRevision', array(
		'methods'             => 'POST',
		'callback'            => array( $ow_revision_service, 'api_create_post_revision' ),
		'permission_callback' => '__return_true'
	) );

	// Register route to check if Compare Button is available
	register_rest_route( 'oasis-workflow/v1',
		'/workflows/documentRevision/checkCompareCapability/postId=(?P<post_id>\d+)', array(
			'methods'             => 'GET',
			'callback'            => array( $ow_revision_service, 'api_check_is_compare_revision_allowed' ),
			'permission_callback' => '__return_true'
		) );

	// Register route to get is revision compare parameters
	register_rest_route( 'oasis-workflow/v1', '/workflows/documentRevision/compare', array(
		'methods'             => 'GET',
		'callback'            => array( $ow_revision_service, 'api_revision_compare' ),
		'permission_callback' => '__return_true'
	) );

	// Register Route to fetch reassign users
	register_rest_route( 'oasis-workflow/v1',
		'/workflows/reassign/assignees/actionHistoryId=(?P<action_history_id>\d+)/taskUser=(?P<task_user>\d+|null)',
		array(
			'methods'             => 'GET',
			'callback'            => array( $ow_process_flow, 'api_get_reassign_assignees' ),
			'permission_callback' => '__return_true'
		) );

	// Register Route to reassign
	register_rest_route( 'oasis-workflow/v1', '/workflows/reassign', array(
		'methods'             => 'POST',
		'callback'            => array( $ow_process_flow, 'api_reassign_process' ),
		'permission_callback' => '__return_true'
	) );

} );
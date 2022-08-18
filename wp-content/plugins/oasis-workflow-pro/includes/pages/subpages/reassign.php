<?php
/*
 * Reassign Popup
 *
 * @copyright   Copyright (c) 2016, Nugget Solutions, Inc
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.1
 *
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

$history_id = trim( $_POST["oasiswf"] );

// If reassigning from post edit page
if ( isset( $_REQUEST["oasiswf"] ) ) {
	$history_id = $_REQUEST["oasiswf"];
}
// sanitize data
$history_id = intval( $history_id );

$task_user = ( isset( $_POST["task_user"] ) && $_POST["task_user"] ) ? trim( $_POST["task_user"] )
	: get_current_user_id();

// If reassigning from post edit page
if ( isset( $_REQUEST["user"] ) ) {
	$task_user = $_REQUEST["user"];
}

// sanitize data
$task_user = intval( $task_user );

// Get reassign users
$inbox_service = new OW_Inbox_Service();
$users         = $inbox_service->get_reassign_users( $history_id );

$assignees = array();

// no self-reassign
foreach ( $users as $key => $user ) {
	if ( $user->ID != $task_user ) {
		array_push( $assignees, $user );
	}
}

// Check if users are available for reassigning            
$user_count = count( $assignees );

$workflow_terminology_options = get_option( 'oasiswf_custom_workflow_terminology' );
$assign_actors_label          = ! empty( $workflow_terminology_options['assignActorsText'] )
	? $workflow_terminology_options['assignActorsText'] : __( 'Assign Actor(s)', 'oasisworkflow' );

?>
<div id="reassgn-setting" class="info-setting">
    <div class="dialog-title"><strong><?php echo __( "Reassign", "oasisworkflow" ); ?></strong></div>
    <br class="clear">
    <div id="ow-reassign-messages" class="owf-hidden"></div>
    <div id="multi-actors-div" class="select-info" style="height:120px;">
        <label><?php echo $assign_actors_label . " :"; ?></label>
        <div class="select-actors-div">
            <div class="select-actors-list">
                <label><?php echo __( "Available", "oasisworkflow" ); ?></label>
                <span class="assign-loading-span" style="float:right;margin-top:-18px;">&nbsp;</span>
                <br class="clear">
                <p>
                    <select id="reassign-actors-list-select" name="actors-list-select" size=10 multiple="multiple">
						<?php
						// for executing performance lets check the above condition
						if ( 0 < $user_count && $users ) {
							foreach ( $users as $user ) {
								$lblNm = OW_Utility::instance()->get_user_name( $user->ID );
								if ( $task_user != $user->ID ) {
									echo "<option value={$user->ID}>$lblNm</option>";
								}
							}
						}
						?>
                    </select>
                </p>
            </div>
            <div class="select-actors-div-point">
                <a href="#" id="reassign-assignee-set-point"><img src="<?php echo OASISWF_URL . "img/role-set.png"; ?>"
                                                                  style="border:0px;"/></a><br><br>
                <a href="#" id="reassign-assignee-unset-point"><img
                        src="<?php echo OASISWF_URL . "img/role-unset.png"; ?>" style="border:0px;"/></a>
            </div>
            <div class="select-actors-list">
                <label><?php echo __( "Assigned", "oasisworkflow" ); ?></label><br class="clear">
                <p>
                    <select id="reassign-actors-set-select" name="actors-set-select" size=10
                            multiple="multiple"></select>
                </p>
            </div>
        </div>
        <br class="clear">
    </div>

    <div class="owf-text-info left full-width">
        <div class="left">
            <label><?php echo __( 'Comments:', 'oasisworkflow' ); ?></label>
        </div>
        <div class="left">
            <textarea id="reassign_comments" class="workflow-comments"></textarea>
        </div>
    </div>
    <br class="clear">
    <p class="reassign-set">
        <input type="button" id="reassignSave" class="button-primary"
               value="<?php echo __( "Save", "oasisworkflow" ); ?>"/>
        <span>&nbsp;</span>
        <a href="#" id="reassignCancel" style="color:blue;"><?php echo __( "Cancel", "oasisworkflow" ); ?></a>
    </p>
    <input type="hidden" id="action_history_id" name="action_history_id"
           value=<?php echo esc_attr( htmlspecialchars( $history_id, ENT_QUOTES, 'UTF-8' ) ); ?>/>
    <input type="hidden" id="task_user_inbox" name="task_user_inbox"
           value=<?php echo esc_attr( htmlspecialchars( $task_user, ENT_QUOTES, 'UTF-8' ) ); ?>/>
    <input type="hidden" name="owf_reassign_ajax_nonce" id="owf_reassign_ajax_nonce"
           value="<?php echo wp_create_nonce( 'owf_reassign_ajax_nonce' ); ?>"/>
    <br class="clear">
</div>
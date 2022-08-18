<?php
/*
 * Service class for Inbox
 *
 * @copyright   Copyright (c) 2015, Nugget Solutions, Inc
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.0
 *
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*
 * OW_Inbox_Service Class
 *
 * @since 2.0
 */

class OW_Inbox_Service {

	/*
	 * Set things up.
	 *
	 * @since 2.0
	 */
	public function __construct() {
		add_action( 'wp_ajax_get_edit_inline_html', array( $this, 'get_edit_inline_html' ) );
		add_action( 'wp_ajax_get_step_signoff_page', array( $this, 'get_step_signoff_page' ) );
		add_action( 'wp_ajax_get_reassign_page', array( $this, 'get_reassign_page' ) );
		add_action( 'wp_ajax_get_step_comment_page', array( $this, 'get_step_comment_page' ) );
	}

	/*
	  * AJAX function - Get the inline edit data
	  * TODO: see if we can find an alternative for this.
	  */
	public function get_edit_inline_html() {
		global $current_screen;

		// nonce check
		check_ajax_referer( 'owf_inbox_ajax_nonce', 'security' );

		$wp_list_table             = _get_list_table( 'WP_Posts_List_Table' );
		$current_screen->post_type = sanitize_text_field( $_POST["post_type"] );
		$wp_list_table->inline_edit();
		wp_send_json_success();
	}

	/*
	  * AJAX function - Get step sign off page
	  *
	  * @since 2.0
	  */
	public function get_step_signoff_page() {
		// nonce check
		check_ajax_referer( 'owf_inbox_ajax_nonce', 'security' );

		ob_start();
		include( OASISWF_PATH . "includes/pages/subpages/submit-step.php" );
		$result = ob_get_contents();
		ob_end_clean();

		wp_send_json_success( htmlentities( $result ) );
	}

	/*
	  * AJAX function - Get reassign page
	  *
	  * @since 2.0
	  */
	public function get_reassign_page() {
		// nonce check
		// If its post edit page
		if ( $_POST["screen"] == "edit" ) {
			check_ajax_referer( 'owf_signoff_ajax_nonce', 'security' );
		} else {
			check_ajax_referer( 'owf_inbox_ajax_nonce', 'security' );
		}

		$history_id = trim( $_POST["oasiswf"] );
		// sanitize data
		$history_id = intval( $history_id );

		$task_user = ( isset( $_POST["task_user"] ) && $_POST["task_user"] ) ? trim( $_POST["task_user"] )
			: get_current_user_id();
		// sanitize data
		$task_user = intval( sanitize_text_field( $task_user ) );

		$users = $this->get_reassign_users( $history_id );

		$assignees = array();

		// no self-reassign
		foreach ( $users as $key => $user ) {
			if ( $user->ID != $task_user ) {
				array_push( $assignees, $user );
			}
		}

		$user_count = count( $assignees );

		if ( 0 === $user_count ) {
			wp_send_json_success( array( "reassign_users" => 0 ) );
		} else {
			ob_start();
			include( OASISWF_PATH . "includes/pages/subpages/reassign.php" );
			$result = ob_get_contents();
			ob_end_clean();

			wp_send_json_success( array( "reassign_users" => htmlentities( $result ) ) );
		}
	}

	/*
	  * AJAX function - Get comments page
	  *
	  * @since 2.0
	  */
	public function get_step_comment_page() {
		// nonce check
		check_ajax_referer( 'owf_inbox_ajax_nonce', 'security' );

		ob_start();
		include( OASISWF_PATH . "includes/pages/subpages/action-comments.php" );
		$result = ob_get_contents();
		ob_end_clean();

		wp_send_json_success( htmlentities( $result ) );
	}

	/**
	 * Get reassign users
	 *
	 * @param int $history_id
	 * @param int $task_user
	 *
	 * @return array $users
	 * @since 6.8
	 */
	public function get_reassign_users( $history_id ) {

		// sanitize data
		$history_id = intval( $history_id );

		$ow_process_flow    = new OW_Process_Flow();
		$ow_history_service = new OW_History_Service();
		$workflow_service   = new OW_Workflow_Service();

		$history_details = $ow_history_service->get_action_history_by_id( $history_id );
		$team_id         = get_post_meta( $history_details->post_id, '_oasis_is_in_team', true );
		$users           = array();
		if ( $team_id != null && method_exists( 'OW_Teams_Service', 'get_team_members' ) ) {
			$step             = $workflow_service->get_step_by_id( $history_details->step_id );
			$step_info        = json_decode( $step->step_info );
			$assignee_roles   = isset( $step_info->task_assignee->roles )
				? array_flip( $step_info->task_assignee->roles ) : null;
			$ow_teams_service = new OW_Teams_Service();
			$users_ids        = $ow_teams_service->get_team_members( $team_id, $assignee_roles,
				$history_details->post_id );
			foreach ( $users_ids as $user_id ) {
				$user = get_userdata( $user_id );
				array_push( $users, $user );
			}
		} else {
			$user_info = $ow_process_flow->get_users_in_step( $history_details->step_id );
			$users     = $user_info["users"];
		}

		return $users;
	}

	/*
	 * generate the table header for the inbox page
	 *
	 * @return mixed HTML for the inbox page
	 *
	 * @since 2.0
	 */
	public function get_table_header() {
		$sortby = ( isset( $_GET['order'] ) && sanitize_text_field( $_GET["order"] ) == "desc" ) ? "asc" : "desc";

		// sorting the inbox page via Author, Due Date, Post title and Post Type
		$author_class = $workflow_class = $due_date_class = $post_order_class = $post_type_class = $priority_class = '';
		if ( isset( $_GET['orderby'] ) && isset( $_GET['order'] ) ) {
			$orderby = sanitize_text_field( $_GET['orderby'] );
			switch ( $orderby ) {
				case 'author':
					$author_class = $sortby;
					break;
				case 'due_date':
					$due_date_class = $sortby;
					break;
				case 'post_title':
					$post_order_class = $sortby;
					break;
				case 'post_type':
					$post_type_class = $sortby;
					break;
				case 'priority':
					$priority_class = $sortby;
					break;
			}
		}
		$workflow_terminology_options = get_option( 'oasiswf_custom_workflow_terminology' );
		$due_date                     = ! empty( $workflow_terminology_options['dueDateText'] )
			? $workflow_terminology_options['dueDateText'] : __( 'Due Date', 'oasisworkflow' );
		$priority                     = ! empty( $workflow_terminology_options['taskPriorityText'] )
			? $workflow_terminology_options['taskPriorityText'] : __( 'Priority', 'oasisworkflow' );

		$inbox_column_headers['checkbox']
			                           = "<td scope='col' class='manage-column column-cb check-column'><input type='checkbox'></td>";
		$sorting_args                  = add_query_arg( array( 'orderby' => 'post_title', 'order' => $sortby ) );
		$inbox_column_headers['title'] = "<th width='300px' scope='col' class='column-primary sorted $post_order_class'>
		<a href='$sorting_args'>
		<span>" . __( "Post/Page", "oasisworkflow" ) . "</span>
					<span class='sorting-indicator'></span>
				</a>
				</th>";

		if ( get_option( 'oasiswf_priority_setting' ) == 'enable_priority' ) {
			$sorting_args                     = add_query_arg( array( 'orderby' => 'priority', 'order' => $sortby ) );
			$inbox_column_headers['priority'] = "<th scope='col' class='manage-column sorted column-priority $priority_class'>
		          <a href='$sorting_args'>
		             <span>" . $priority . "</span>
		             <span class='sorting-indicator'></span>
		          </a>
              </th>";
		}

		$sorting_args                   = add_query_arg( array( 'orderby' => 'post_type', 'order' => $sortby ) );
		$inbox_column_headers['type']   = "<th scope='col' class='sorted column-type $post_type_class'>
		<a href='$sorting_args'>
		<span>" . __( "Type", "oasisworkflow" ) . "</span>
					<span class='sorting-indicator'></span>
			</a>
			</th>";
		$sorting_args                   = add_query_arg( array( 'orderby' => 'post_author', 'order' => $sortby ) );
		$inbox_column_headers['author'] = "<th scope='col' class='sorted column-author $author_class'>
		<a href='$sorting_args'>
		<span>" . __( "Author", "oasisworkflow" ) . "</span>
					<span class='sorting-indicator'></span>
			</a>
			</th>";

		$inbox_column_headers['last_signoff_by'] = "<th class='column-last-signoff-by'>" .
		                                           __( "Last Signed off By", "oasisworkflow" ) . "</th>";

		$inbox_column_headers['workflow_name'] = "<th class='column-workflow-name'>" .
		                                         __( "Workflow [Step]", "oasisworkflow" ) . "</th>";

		$inbox_column_headers['category'] = "<th class='column-category'>" . __( "Category", "oasisworkflow" ) .
		                                    "</th>";

		$sorting_args                     = add_query_arg( array( 'orderby' => 'due_date', 'order' => $sortby ) );
		$inbox_column_headers['due_date'] = "<th scope='col' class='sorted column-due-date $due_date_class'>
		<a href='$sorting_args'>
					<span>" . $due_date . "</span>
					<span class='sorting-indicator'></span>
			</a>
			</th>";

		// allow for add/remove of inbox column header via a filter
		if ( has_filter( 'owf_manage_inbox_column_headers' ) ) {
			$inbox_column_headers = apply_filters( 'owf_manage_inbox_column_headers', $inbox_column_headers );
		}
		$inbox_column_headers['comments'] = "<th class='column-comments'>" . __( "Comments", "oasisworkflow" ) .
		                                    "</th>";


		return $inbox_column_headers;
	}

	/*
	 * generate the table rows for the inbox page
	 *
	 * @return mixed HTML for the inbox page
	 *
	 * @since 4.4
	 */

	public function get_table_rows( $inbox_data, $inbox_items, $inbox_column_headers, $unclaimed_task_count = 0 ) {

		global $ow_custom_statuses;

		$page_number   = intval( $inbox_data["page_number"] );
		$per_page      = intval( $inbox_data["per_page"] );
		$selected_user = intval( $inbox_data["selected_users"] );

		$ow_process_flow     = new OW_Process_Flow();
		$ow_workflow_service = new OW_Workflow_Service();
		$ow_history_service  = new OW_History_Service();

		$wf_process_status = get_site_option( "oasiswf_status" );
		$space             = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
		if ( $inbox_items ):
			$count    = 0;
			$cat_name = '----';
			$start    = ( $page_number - 1 ) * $per_page;
			$end      = $start + $per_page;
			foreach ( $inbox_items as $inbox_item ) {
				if ( $count >= $end ) {
					break;
				}
				if ( $count >= $start ) {
					if ( has_action( 'owf_manage_inbox_items_row_start' ) ) {
						do_action( 'owf_manage_inbox_items_row_start', $inbox_item );
					}
					$post = get_post( $inbox_item->post_id );

					$cat_name = OW_Utility::instance()->get_post_categories( $inbox_item->post_id );
					$user     = get_userdata( $post->post_author );
					$stepId   = $inbox_item->step_id;
					if ( $stepId <= 0 || $stepId == "" ) {
						$stepId = $inbox_item->review_step_id;
					}
					$step     = $ow_workflow_service->get_step_by_id( $stepId );
					$workflow = $ow_workflow_service->get_workflow_by_id( $step->workflow_id );

					$needs_to_be_claimed = $ow_process_flow->check_for_claim( $inbox_item->ID );

					// Get last sign-off by
					if ( $inbox_item->assign_actor_id == - 1 ) {
						$review_rows = $ow_history_service->get_review_action_by_status( "complete", $inbox_item->ID );
						// If only one user is assigned the task
						if ( $review_rows ) {
							$comments = json_decode( $review_rows[0]->comments );
						} else {
							$comments = json_decode( $inbox_item->comment );
						}
						$send_by     = $comments[0]->send_id;
						$sign_off_by = OW_Utility::instance()->get_user_name( $send_by );
					} else {
						$comments    = json_decode( $inbox_item->comment );
						$send_by     = $comments[0]->send_id;
						$sign_off_by = OW_Utility::instance()->get_user_name( $send_by );
					}

					$original_post_id = get_post_meta( $inbox_item->post_id, '_oasis_original', true );
					/* Check due date and make post item background color in red to notify the admin */
					$ow_email = new OW_Email();

					$current_date              = Date( " F j, Y " );
					$due_date                  = OW_Utility::instance()
					                                       ->format_date_for_display( $inbox_item->due_date );
					$past_due_date_row_class   = '';
					$past_due_date_field_class = '';
					if ( $due_date != "" && strtotime( $due_date ) < strtotime( $current_date ) ) {
						$past_due_date_row_class   = 'past-due-date-row';
						$past_due_date_field_class = 'past-due-date-field';
					}
					echo "<tr id='post-{$inbox_item->post_id}'
                        	class='post-{$inbox_item->post_id} post type-post $past_due_date_row_class
                        	status-pending format-standard hentry category-uncategorized alternate iedit author-other'> ";
					$workflow_post_id = esc_attr( $inbox_item->post_id );
					if ( array_key_exists( 'checkbox', $inbox_column_headers ) ) {
						echo "<th scope='row' class='check-column'>
                              <input type='checkbox' name='post[]' value={$workflow_post_id} wfid='{$inbox_item->ID}'><div class='locked-indicator'></div></th>";
					}

					if ( array_key_exists( 'title', $inbox_column_headers ) ) {
						echo '<td class="post-title page-title column-title column-primary" data-colname="Post/Page"><strong>' .
						     esc_html( $post->post_title );
						// TODO : see if we can find a better solution instead of using _post_states
						_post_states( $post );
						echo "</strong>";
						// create the action list
						if ( $needs_to_be_claimed ) { // if the item needs to be claimed, only "Claim" action is visible
							$claim_row_actions = "<div class='row-actions'>
                                    <span>
                                       <a href='#' class='claim' id='claim' userid={$selected_user} actionid={$inbox_item->ID}>" .
							                     __( "Claim", "oasisworkflow" ) . "</a>
                                       <span class='loading'>$space</span>
                                    </span>
                                    |
                                    <span>
                                       <a href='#' id='claim-and-edit' class='claim-and-edit' userid={$selected_user} actionid={$inbox_item->ID}>" .
							                     __( "Claim and Edit", "oasisworkflow" ) . "</a>
                                       <span class='loading'>$space</span>
                                    </span>
                                 </div>";
							if ( has_filter( 'owf_inbox_row_claim_actions' ) ) {
								$claim_row_actions = apply_filters( 'owf_inbox_row_claim_actions', $claim_row_actions,
									$inbox_item );
							}
							echo $claim_row_actions;
						} else {
							echo "<div class='row-actions'>";

							$inbox_row_actions_data = array(
								"post_id"             => $inbox_item->post_id,
								"user_id"             => $selected_user,
								"workflow_history_id" => $inbox_item->ID,
								"original_post_id"    => $original_post_id
							);

							$inbox_row_actions = $this->display_row_actions( $inbox_row_actions_data );
							// allow for add/remove of inbox actions via a filter
							if ( has_filter( 'owf_inbox_row_actions' ) ) {
								$inbox_row_actions = apply_filters( 'owf_inbox_row_actions', $inbox_row_actions_data,
									$inbox_row_actions, $inbox_item );
							}
							$action_count = count( $inbox_row_actions );
							$i            = 0;
							foreach ( $inbox_row_actions as $action ) {
								++ $i;
								( $i == $action_count ) ? $sep = '' : $sep = ' | ';
								echo "<span>$action$sep</span>";
							}
							echo "</div>";
							get_inline_data( $post );
						}
						echo '<button type="button" class="toggle-row"><span class="screen-reader-text">Show more details</span></button></td>';
					}

					if ( array_key_exists( 'priority', $inbox_column_headers ) ) {
						if ( get_option( 'oasiswf_priority_setting' ) == 'enable_priority' ) {
							//priority settings
							$priority = get_post_meta( $post->ID, '_oasis_task_priority', true );
							if ( empty( $priority ) ) {
								$priority = '2normal';
							}

							$priority_array = OW_Utility::instance()->get_priorities();
							$priority_value = $priority_array[ $priority ];
							// the CSS is defined without the number part
							$css_class = substr( $priority, 1 );
							echo "<td data-colname='Priority' class='column-priority' ><p class='post-priority $css_class-priority'>" .
							     $priority_value . "</p></td>";
						}
					}

					if ( array_key_exists( 'type', $inbox_column_headers ) ) {
						$post_type_obj = get_post_type_object( get_post_type( $inbox_item->post_id ) );
						echo "<td data-colname='Type' class='column-type'>{$post_type_obj->labels->singular_name}</td>";
					}

					if ( array_key_exists( 'author', $inbox_column_headers ) ) {
						echo "<td data-colname='Author' class='column-author'>" .
						     OW_Utility::instance()->get_user_name( $user->ID ) . "</td>";
					}

					if ( array_key_exists( 'last_signoff_by', $inbox_column_headers ) ) {
						echo "<td data-colname='Last Signed off By' class='column-last-signoff-by'>" . $sign_off_by .
						     "</td>";
					}

					if ( array_key_exists( 'workflow_name', $inbox_column_headers ) ) {
						$workflow_name = $workflow->name;
						if ( ! empty( $workflow->version ) ) {
							$workflow_name .= " (" . $workflow->version . ")";
						}

						echo "<td data-colname='Workflow [Step]' class='column-workflow-name'>{$workflow_name} [{$ow_workflow_service->get_gpid_dbid( $workflow->ID, $stepId, 'lbl' )}]</td>";
					}

					if ( array_key_exists( 'category', $inbox_column_headers ) ) {
						echo "<td data-colname='Category' class='column-category'>{$cat_name}</td>";
					}

					if ( array_key_exists( 'due_date', $inbox_column_headers ) ) {
						// if the due date is passed the current date show the field in a different color
						echo "<td data-colname='Due Date' class='column-due-date'><span class=' . $past_due_date_field_class . '>" .
						     OW_Utility::instance()->format_date_for_display( $inbox_item->due_date ) . "</span></td>";
					}

					if ( has_filter( 'owf_manage_inbox_column_content' ) ) {
						apply_filters( 'owf_manage_inbox_column_content', $inbox_column_headers, $inbox_item );
					}

					if ( array_key_exists( 'comments', $inbox_column_headers ) ) {
						$comment_row_actions = "<td class='comments column-comments' data-colname='Comments'>
                                 <div class='post-com-count-wrapper'>
                                    <strong>
                                       <a href='#' actionid={$inbox_item->ID} class='post-com-count post-com-count-approved' data-comment='inbox_comment' post_id={$inbox_item->post_id}>
                                          <span class='comment-count-approved'>{$ow_process_flow->get_sign_off_comments_count_by_post_id( $inbox_item->post_id )}</span>
                                       </a>
                                       <span class='loading'>$space</span>
                                    </strong>
                                 </div>
                                </td>";
						if ( has_filter( 'owf_inbox_row_comment_actions' ) ) {
							$comment_row_actions = apply_filters( 'owf_inbox_row_comment_actions', $comment_row_actions,
								$inbox_item );
						}
						echo $comment_row_actions;
					}

					echo "</tr>";
					if ( has_action( 'owf_manage_inbox_items_row_end' ) ) {
						do_action( 'owf_manage_inbox_items_row_end', $inbox_item );
					}
				}
				$count ++;
			}
		elseif ( $unclaimed_task_count > 0 ):
			$else_unclaimed_task_message = __( ' But there are currently ', 'oasisworkflow' )
			                               .
			                               sprintf( '<a href="admin.php?page=oasiswf-inbox&action=inbox-unclaimed&user=%s">%s',
				                               $selected_user, $unclaimed_task_count )
			                               . __( ' unclaimed task(s).', 'oasisworkflow' )
			                               . '</a>';
			echo "<tr>";
			echo "<td class='hurry-td' colspan='8'>
								<label class='hurray-lbl'>";
			echo __( "Hurray! No assignments.", "oasisworkflow" ) . $else_unclaimed_task_message;
			echo "</label></td>";
			echo "</tr>";
		else:
			echo "<tr>";
			echo "<td class='hurry-td' colspan='8'>
								<label class='hurray-lbl'>";
			echo __( "Hurray! No assignments.", "oasisworkflow" );
			echo "</label></td>";
			echo "</tr>";
		endif;
	}

	public function enqueue_and_localize_script() {
		wp_enqueue_script( 'owf_reassign_task',
			OASISWF_URL . 'js/pages/subpages/reassign.js',
			array( 'jquery' ),
			OASISWF_VERSION,
			true );

		wp_localize_script( 'owf_reassign_task', 'owf_reassign_task_vars', array(
			'selectUser'          => __( 'Select a user to reassign the task.', 'oasisworkflow' ),
			'isCommentsMandotory' => get_option( "oasiswf_comments_setting" ),
			'emptyComments'       => __( 'Please add comments.', 'oasisworkflow' )
		) );
	}

	/**
	 *
	 * Display row actions on inbox page
	 *
	 * @param $inbox_row_actions_data
	 *
	 * @return mixed
	 */
	public function display_row_actions( $inbox_row_actions_data ) {

		$post_id             = intval( $inbox_row_actions_data["post_id"] );
		$user_id             = intval( $inbox_row_actions_data["user_id"] );
		$workflow_history_id = intval( $inbox_row_actions_data["workflow_history_id"] );
		$original_post_id    = intval( $inbox_row_actions_data["original_post_id"] );

		$space = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

		// get workflow settings for compare button
		$hide_compare_button = get_option( "oasiswf_hide_compare_button" );

		// get custom terminology
		$workflow_terminology_options = get_option( 'oasiswf_custom_workflow_terminology' );
		$sign_off_label               = ! empty( $workflow_terminology_options['signOffText'] )
			? $workflow_terminology_options['signOffText'] : __( 'Sign Off', 'oasisworkflow' );
		$abort_workflow_label         = ! empty( $workflow_terminology_options['abortWorkflowText'] )
			? $workflow_terminology_options['abortWorkflowText'] : __( 'Abort Workflow', 'oasisworkflow' );

		// Set the row action
		$inbox_row_actions = array();
		if ( OW_Utility::instance()->is_post_editable_others( $post_id ) ) {
			$inbox_row_actions['edit'] = "<a href='" . get_admin_url() .
			                             "post.php?post={$post_id}&action=edit&oasiswf={$workflow_history_id}&user={$user_id}' class='edit' real={$post_id}>" .
			                             __( "Edit", "oasisworkflow" ) . "</a>";
		}

		if ( did_action( 'elementor/loaded' ) &&
		     \Elementor\Plugin::$instance->db->is_built_with_elementor( $post_id ) &&
		     OW_Utility::instance()->is_post_editable_others( $post_id ) ) {
			$inbox_row_actions['edit_with_elementor']
				= "<a href='post.php?post={$post_id}&action=elementor&oasiswf={$workflow_history_id}&user={$user_id}' class='edit' real={$post_id}>" .
				  __( "Edit with Elementor", "oasisworkflow" ) . "</a>";
		}

		if ( OW_Utility::instance()->is_post_editable_others( $post_id ) ) {
			$inbox_row_actions['view'] = "<a target='_blank' href='" . get_preview_post_link( $post_id ) . "'>" .
			                             __( "View", "oasisworkflow" ) . "</a>";
		}

		if ( $hide_compare_button == "" && $original_post_id && OW_Utility::instance()->is_post_editable( $post_id ) ) {
			$inbox_row_actions['compare'] = "<a href='post.php?page=oasiswf-revision&revision={$post_id}&_nonce=" .
			                                wp_create_nonce( 'owf_compare_revision_nonce' ) .
			                                "' wfid='$workflow_history_id' class='compare-post-revision' postid='$post_id'>" .
			                                __( "Compare", "oasisworkflow" ) .
			                                "</a><span class='loading'>$space</span>";
		}

		if ( current_user_can( 'ow_sign_off_step' ) && OW_Utility::instance()->is_post_editable( $post_id ) ) {
			$inbox_row_actions['sign_off']
				= "<a href='#' wfid='$workflow_history_id' postid='$post_id' class='quick_sign_off'>" .
				  $sign_off_label . "</a><span class='loading'>$space</span>";
		}

		if ( current_user_can( 'ow_reassign_task' ) ) {
			$inbox_row_actions['reassign'] = "<a href='#' wfid='$workflow_history_id' class='reassign'>" .
			                                 __( "Reassign", "oasisworkflow" ) .
			                                 "</a><span class='loading'>$space</span>";
		}

		if ( current_user_can( 'ow_abort_workflow' ) ) {
			$inbox_row_actions['abort_workflow']
				= "<a href='#' wfid='$workflow_history_id' postid='$post_id' class='abort_workflow'>" .
				  $abort_workflow_label . "</a><span class='loading'>$space</span>";
		}

		if ( current_user_can( 'ow_view_workflow_history' ) ) {
			$nonce_url                         = wp_nonce_url( "admin.php?page=oasiswf-history&post=$post_id",
				'owf_view_history_nonce' );
			$inbox_row_actions['view_history'] = "<a href='$nonce_url'> " . __( "View History", "oasisworkflow" ) .
			                                     "</a>";
		}

		return $inbox_row_actions;
	}

}

// construct an instance so that the actions get loaded
$inbox_service = new OW_Inbox_Service();
?>
<?php
/*
 * Settings class for Workflow Email settings
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
 * OW_Email_Settings Class
 *
 * @since 2.0
 */

class OW_Email_Settings {

	// email settings option name
	protected $ow_email_option_name = "oasiswf_email_settings";

	// TODO : merge this into oasiswf_email_settings
	// email reminder day before option name
	protected $ow_email_reminder_day_before_option_name = "oasiswf_reminder_days";

	// TODO : merge this into oasiswf_email_settings
	// email reminder day after option name
	protected $ow_email_reminder_day_after_option_name = "oasiswf_reminder_days_after";

	// email setting for post publish notification
	protected $ow_email_post_publish_option_name = "oasiswf_post_publish_email_settings";

	// email setting for Revised Post Published Notification
	protected $ow_email_revised_post_option_name = "oasiswf_revised_post_email_settings";

	// email setting for Unauthorized Update Notification
	protected $ow_email_unauthorized_update_option_name = "oasiswf_unauthorized_update_email_settings";

	// email setting for Task Claimed Notification
	protected $ow_email_task_claim_option_name = "oasiswf_task_claim_email_settings";

	// email setting for Post Submit Notification
	protected $ow_email_post_submit_option_name = "oasiswf_post_submit_email_settings";

	// email setting for Workflow Abort Notification
	protected $ow_email_workflow_abort_option_name = "oasiswf_workflow_abort_email_settings";

	// default values
	protected $ow_email_option_default_value
		= array(
			'from_name'                  => "",
			'from_email_address'         => "",
			'assignment_emails'          => "",
			'reminder_emails'            => "",
			'digest_emails'              => "",
			'assignment_email_intervals' => "hourly"
		);

	/*
	 * Set things up.
	 *
	 * @since 2.0
	 */
	public function __construct() {
		add_action( 'admin_init', array( $this, 'init_settings' ) );

		$this->assignment_email_intervals = array(
			'hourly'   => array( 'interval' => 60 * 60, 'display' => __( '1 hour', 'oasisworkflow' ) ),
			'hours_4'  => array( 'interval' => 240 * 60, 'display' => __( '4 hours', 'oasisworkflow' ) ),
			'hours_8'  => array( 'interval' => 480 * 60, 'display' => __( '8 hours', 'oasisworkflow' ) ),
			'hours_12' => array( 'interval' => 720 * 60, 'display' => __( '12 hours', 'oasisworkflow' ) )
		);
	}

	// White list our options using the Settings API
	public function init_settings() {
		register_setting( 'ow-settings-email', $this->ow_email_option_name, array( $this, 'validate_email_settings' ) );

		//TODO : merge this into oasiswf_email_settings
		register_setting( 'ow-settings-email', $this->ow_email_reminder_day_before_option_name, array(
			$this,
			'validate_reminder_days_before'
		) );

		//TODO : merge this into oasiswf_email_settings
		register_setting( 'ow-settings-email', $this->ow_email_reminder_day_after_option_name, array(
			$this,
			'validate_reminder_days_after'
		) );

		// Register setting for post publish notification mails
		register_setting( 'ow-settings-email', $this->ow_email_post_publish_option_name, array(
			$this,
			'validate_post_publish_emails'
		) );

		// Register setting for revised post publish notification mails
		register_setting( 'ow-settings-email', $this->ow_email_revised_post_option_name, array(
			$this,
			'validate_revised_post_publish_emails'
		) );

		// Register setting for unauthorised update notification mails
		register_setting( 'ow-settings-email', $this->ow_email_unauthorized_update_option_name, array(
			$this,
			'validate_unauthorized_update_emails'
		) );

		// Register setting for task claimed notification mails
		register_setting( 'ow-settings-email', $this->ow_email_task_claim_option_name, array(
			$this,
			'validate_task_claimed_emails'
		) );

		// Register setting for post submit notification mails
		register_setting( 'ow-settings-email', $this->ow_email_post_submit_option_name, array(
			$this,
			'validate_post_submit_emails'
		) );

		// Register setting for workflow abort notification mails
		register_setting( 'ow-settings-email', $this->ow_email_workflow_abort_option_name, array(
			$this,
			'validate_workflow_abort_emails'
		) );
	}

	/*
	 * sanitize and validate the input (if required)
	 *
	 * @since 2.0
	 */
	public function validate_email_settings( $input ) {

		$email_settings                       = array();
		$email_settings['from_name']          = sanitize_text_field( $input['from_name'] );
		$email_settings['from_email_address'] = sanitize_text_field( $input['from_email_address'] );
		if ( array_key_exists( 'assignment_emails', $input ) ) {
			$email_settings['assignment_emails'] = sanitize_text_field( $input['assignment_emails'] );
		} else {
			$email_settings['assignment_emails'] = $this->ow_email_option_default_value['assignment_emails'];
		}

		if ( array_key_exists( 'reminder_emails', $input ) ) {
			$email_settings['reminder_emails'] = sanitize_text_field( $input['reminder_emails'] );
		} else {
			$email_settings['reminder_emails'] = $this->ow_email_option_default_value['reminder_emails'];
		}

		if ( array_key_exists( 'digest_emails', $input ) ) {
			$email_settings['digest_emails'] = sanitize_text_field( $input['digest_emails'] );
		} else {
			$email_settings['digest_emails'] = $this->ow_email_option_default_value['digest_emails'];
		}

		if ( array_key_exists( 'assignment_email_intervals', $input ) ) {
			$email_settings['assignment_email_intervals'] = sanitize_text_field( $input['assignment_email_intervals'] );
		} else {
			$email_settings['assignment_email_intervals']
				= $this->ow_email_option_default_value['assignment_email_intervals'];
		}

		// Update cron if scheduled cron is different than existing cron set
		$assignment_email_intervals = $email_settings['assignment_email_intervals'];
		$schedule                   = wp_get_schedule( 'oasiswf_email_digest_schedule' );

		if ( ! empty( $assignment_email_intervals ) && $assignment_email_intervals !== $schedule ) {
			$interval  = $assignment_email_intervals;
			$timestamp = wp_next_scheduled( 'oasiswf_email_digest_schedule' );
			wp_unschedule_event( $timestamp, 'oasiswf_email_digest_schedule' );
			wp_schedule_event( time(), $interval, 'oasiswf_email_digest_schedule' );
		}

		return $email_settings;
	}

	/*
	 * sanitize and validate the input (if required)
	 *
	 * @since 2.0
	 */
	public function validate_reminder_days_before( $reminder_days_before ) {
		$reminder_days_before = intval( $reminder_days_before );

		if ( $reminder_days_before == 0 ) //blank out the value
		{
			return "";
		}

		return $reminder_days_before;
	}

	/*
	 * sanitize and validate the input (if required)
	 *
	 * @since 2.0
	 */
	public function validate_reminder_days_after( $reminder_days_after ) {
		$reminder_days_after = intval( $reminder_days_after );

		if ( $reminder_days_after == 0 ) //blank out the value
		{
			return "";
		}


		return $reminder_days_after;
	}

	/*
	  * sanitize and validate the input for post publish mails
	  * @since 4.6
	  */
	public function validate_post_publish_emails( $post_publish_input ) {
		$post_publish_settings              = array();
		$post_publish_settings['is_active'] = isset( $post_publish_input['is_active'] )
			? sanitize_text_field( $post_publish_input['is_active'] ) : '';

		//get email assignees
		$selected_email_assignees = isset( $post_publish_input['email_assignees'] ) &&
		                            $post_publish_input['email_assignees'] != '' ? array_map( 'esc_attr',
			$post_publish_input['email_assignees'] ) : array();

		// show error message if notification is active and no "To" recipient is selected.
		if ( $post_publish_settings['is_active'] === "yes" && empty( $selected_email_assignees ) ) {
			add_settings_error(
				'ow-settings-email',
				'email_assignees',
				__( 'Please select at least one "To" email recipient for "Post Publish Notification".',
					'oasisworkflow' ),
				'error'
			);
		}

		$post_publish_settings['email_assignees'] = $this->get_assignees( $selected_email_assignees );

		//get cc assignees
		$selected_cc_email_assignees = isset( $post_publish_input['email_cc'] ) && $post_publish_input['email_cc'] != ''
			? array_map( 'esc_attr', $post_publish_input['email_cc'] ) : array();

		$post_publish_settings['email_cc'] = $this->get_assignees( $selected_cc_email_assignees );

		//get bcc assignees
		$selected_bcc_email_assignees = isset( $post_publish_input['email_bcc'] ) &&
		                                $post_publish_input['email_bcc'] != '' ? array_map( 'esc_attr',
			$post_publish_input['email_bcc'] ) : array();

		$post_publish_settings['email_bcc'] = $this->get_assignees( $selected_bcc_email_assignees );

		$post_publish_settings['subject'] = trim( $post_publish_input['subject'] );
		$post_publish_settings['content'] = wp_kses_post( $post_publish_input['content'] );

		return $post_publish_settings;
	}

	/*
	  * sanitize and validate the input for revised post publish mails
	  * @since 4.6
	  */
	public function validate_revised_post_publish_emails( $revised_post_publish_input ) {
		$revised_post_publish_settings              = array();
		$revised_post_publish_settings['is_active'] = isset( $revised_post_publish_input['is_active'] )
			? sanitize_text_field( $revised_post_publish_input['is_active'] ) : '';

		//get email assignees
		$selected_email_assignees = isset( $revised_post_publish_input['email_assignees'] ) &&
		                            $revised_post_publish_input['email_assignees'] != '' ? array_map( 'esc_attr',
			$revised_post_publish_input['email_assignees'] ) : array();

		// show error message if notification is active and no "To" recipient is selected.
		if ( $revised_post_publish_settings['is_active'] === "yes" && empty( $selected_email_assignees ) ) {
			add_settings_error(
				'ow-settings-email',
				'email_assignees',
				__( 'Please select at least one "To" email recipient for "Revised Post Publish Notification".',
					'oasisworkflow' ),
				'error'
			);
		}

		$revised_post_publish_settings['email_assignees'] = $this->get_assignees( $selected_email_assignees );

		//get cc assignees
		$selected_cc_email_assignees = isset( $revised_post_publish_input['email_cc'] ) &&
		                               $revised_post_publish_input['email_cc'] != '' ? array_map( 'esc_attr',
			$revised_post_publish_input['email_cc'] ) : array();

		$revised_post_publish_settings['email_cc'] = $this->get_assignees( $selected_cc_email_assignees );

		//get bcc assignees
		$selected_bcc_email_assignees = isset( $revised_post_publish_input['email_bcc'] ) &&
		                                $revised_post_publish_input['email_bcc'] != '' ? array_map( 'esc_attr',
			$revised_post_publish_input['email_bcc'] ) : array();

		$revised_post_publish_settings['email_bcc'] = $this->get_assignees( $selected_bcc_email_assignees );

		$revised_post_publish_settings['subject'] = trim( $revised_post_publish_input['subject'] );
		$revised_post_publish_settings['content'] = wp_kses_post( $revised_post_publish_input['content'] );

		return $revised_post_publish_settings;
	}

	/*
	  * sanitize and validate the input for post submit mails
	  * @since 4.6
	  */
	public function validate_post_submit_emails( $post_submit_input ) {
		$post_submit_settings              = array();
		$post_submit_settings['is_active'] = isset( $post_submit_input['is_active'] )
			? sanitize_text_field( $post_submit_input['is_active'] ) : '';

		//get email assignees
		$selected_email_assignees = isset( $post_submit_input['email_assignees'] ) &&
		                            $post_submit_input['email_assignees'] != '' ? array_map( 'esc_attr',
			$post_submit_input['email_assignees'] ) : array();

		// show error message if notification is active and no "To" recipient is selected.
		if ( $post_submit_settings['is_active'] === "yes" && empty( $selected_email_assignees ) ) {
			add_settings_error(
				'ow-settings-email',
				'email_assignees',
				__( 'Please select at least one "To" email recipient for "Post Submit Notification".',
					'oasisworkflow' ),
				'error'
			);
		}

		$post_submit_settings['email_assignees'] = $this->get_assignees( $selected_email_assignees );

		//get cc assignees
		$selected_cc_email_assignees = isset( $post_submit_input['email_cc'] ) && $post_submit_input['email_cc'] != ''
			? array_map( 'esc_attr', $post_submit_input['email_cc'] ) : array();

		$post_submit_settings['email_cc'] = $this->get_assignees( $selected_cc_email_assignees );

		//get bcc assignees
		$selected_bcc_email_assignees = isset( $post_submit_input['email_bcc'] ) &&
		                                $post_submit_input['email_bcc'] != '' ? array_map( 'esc_attr',
			$post_submit_input['email_bcc'] ) : array();

		$post_submit_settings['email_bcc'] = $this->get_assignees( $selected_bcc_email_assignees );

		$post_submit_settings['subject'] = trim( $post_submit_input['subject'] );
		$post_submit_settings['content'] = wp_kses_post( $post_submit_input['content'] );

		return $post_submit_settings;
	}

	/*
	  * sanitize and validate the input for unauthorized mails
	  * @since 4.6
	  */
	public function validate_unauthorized_update_emails( $unauthorized_update_input ) {
		$unauthorized_update_settings              = array();
		$unauthorized_update_settings['is_active'] = isset( $unauthorized_update_input['is_active'] )
			? sanitize_text_field( $unauthorized_update_input['is_active'] ) : '';

		//get email assignees
		$selected_email_assignees = isset( $unauthorized_update_input['email_assignees'] ) &&
		                            $unauthorized_update_input['email_assignees'] != '' ? array_map( 'esc_attr',
			$unauthorized_update_input['email_assignees'] ) : array();

		// show error message if notification is active and no "To" recipient is selected.
		if ( $unauthorized_update_settings['is_active'] === "yes" && empty( $selected_email_assignees ) ) {
			add_settings_error(
				'ow-settings-email',
				'email_assignees',
				__( 'Please select at least one "To" email recipient for "Unauthorized Update Notification".',
					'oasisworkflow' ),
				'error'
			);
		}

		$unauthorized_update_settings['email_assignees'] = $this->get_assignees( $selected_email_assignees );

		//get cc assignees
		$selected_cc_email_assignees = isset( $unauthorized_update_input['email_cc'] ) &&
		                               $unauthorized_update_input['email_cc'] != '' ? array_map( 'esc_attr',
			$unauthorized_update_input['email_cc'] ) : array();

		$unauthorized_update_settings['email_cc'] = $this->get_assignees( $selected_cc_email_assignees );

		//get bcc assignees
		$selected_bcc_email_assignees = isset( $unauthorized_update_input['email_bcc'] ) &&
		                                $unauthorized_update_input['email_bcc'] != '' ? array_map( 'esc_attr',
			$unauthorized_update_input['email_bcc'] ) : array();

		$unauthorized_update_settings['email_bcc'] = $this->get_assignees( $selected_bcc_email_assignees );

		$unauthorized_update_settings['subject'] = trim( $unauthorized_update_input['subject'] );
		$unauthorized_update_settings['content'] = wp_kses_post( $unauthorized_update_input['content'] );

		return $unauthorized_update_settings;
	}

	/*
	  * sanitize and validate the input for task claimed mails
	  * @since 4.6
	  */
	public function validate_task_claimed_emails( $task_claimed_input ) {
		$task_claimed_settings              = array();
		$task_claimed_settings['is_active'] = isset( $task_claimed_input['is_active'] )
			? sanitize_text_field( $task_claimed_input['is_active'] ) : '';

		//get email assignees
		$selected_email_assignees = isset( $task_claimed_input['email_assignees'] ) &&
		                            $task_claimed_input['email_assignees'] != '' ? array_map( 'esc_attr',
			$task_claimed_input['email_assignees'] ) : array();

		// show error message if notification is active and no "To" recipient is selected.
		if ( $task_claimed_settings['is_active'] === "yes" && empty( $selected_email_assignees ) ) {
			add_settings_error(
				'ow-settings-email',
				'email_assignees',
				__( 'Please select at least one "To" email recipient for "Task Claimed Notification".',
					'oasisworkflow' ),
				'error'
			);
		}

		$task_claimed_settings['email_assignees'] = $this->get_assignees( $selected_email_assignees );

		//get cc assignees
		$selected_cc_email_assignees = isset( $task_claimed_input['email_cc'] ) && $task_claimed_input['email_cc'] != ''
			? array_map( 'esc_attr', $task_claimed_input['email_cc'] ) : array();

		$task_claimed_settings['email_cc'] = $this->get_assignees( $selected_cc_email_assignees );

		//get bcc assignees
		$selected_bcc_email_assignees = isset( $task_claimed_input['email_bcc'] ) &&
		                                $task_claimed_input['email_bcc'] != '' ? array_map( 'esc_attr',
			$task_claimed_input['email_bcc'] ) : array();

		$task_claimed_settings['email_bcc'] = $this->get_assignees( $selected_bcc_email_assignees );

		$task_claimed_settings['subject'] = trim( $task_claimed_input['subject'] );
		$task_claimed_settings['content'] = wp_kses_post( $task_claimed_input['content'] );

		return $task_claimed_settings;
	}

	/*
	  * sanitize and validate the input for workflow abort mails
	  * @since 4.6
	  */
	public function validate_workflow_abort_emails( $workflow_abort_input ) {
		$workflow_abort_settings              = array();
		$workflow_abort_settings['is_active'] = isset( $workflow_abort_input['is_active'] )
			? sanitize_text_field( $workflow_abort_input['is_active'] ) : '';

		//get email assignees
		$selected_email_assignees = isset( $workflow_abort_input['email_assignees'] ) &&
		                            $workflow_abort_input['email_assignees'] != '' ? array_map( 'esc_attr',
			$workflow_abort_input['email_assignees'] ) : array();

		// show error message if notification is active and no "To" recipient is selected.
		if ( $workflow_abort_settings['is_active'] === "yes" && empty( $selected_email_assignees ) ) {
			add_settings_error(
				'ow-settings-email',
				'email_assignees',
				__( 'Please select at least one "To" email recipient for "Workflow Abort Notification".',
					'oasisworkflow' ),
				'error'
			);
		}

		$workflow_abort_settings['email_assignees'] = $this->get_assignees( $selected_email_assignees );

		//get cc assignees
		$selected_cc_email_assignees = isset( $workflow_abort_input['email_cc'] ) &&
		                               $workflow_abort_input['email_cc'] != '' ? array_map( 'esc_attr',
			$workflow_abort_input['email_cc'] ) : array();

		$workflow_abort_settings['email_cc'] = $this->get_assignees( $selected_cc_email_assignees );

		//get bcc assignees
		$selected_bcc_email_assignees = isset( $workflow_abort_input['email_bcc'] ) &&
		                                $workflow_abort_input['email_bcc'] != '' ? array_map( 'esc_attr',
			$workflow_abort_input['email_bcc'] ) : array();

		$workflow_abort_settings['email_bcc'] = $this->get_assignees( $selected_bcc_email_assignees );

		$workflow_abort_settings['subject'] = trim( $workflow_abort_input['subject'] );
		$workflow_abort_settings['content'] = wp_kses_post( $workflow_abort_input['content'] );

		return $workflow_abort_settings;
	}

	/**
	 * Get assignees array as per role, user and external users
	 *
	 * @param array $selected_email_assignees
	 *
	 * @return array $email_assignees
	 */
	public function get_assignees( $selected_email_assignees ) {
		$roles          = array();
		$users          = array();
		$external_users = array();

		if ( is_array( $selected_email_assignees ) && ( ! empty( $selected_email_assignees ) ) ) {
			$count_email_assignees = count( $selected_email_assignees );

			for ( $i = 0; $i < $count_email_assignees; $i ++ ) {
				$assign_type   = substr( $selected_email_assignees[ $i ], 0, 2 );
				$assigned_user = substr( $selected_email_assignees[ $i ], 2 );
				switch ( $assign_type ) {
					case 'r@':
						$roles[] = $assigned_user;
						break;
					case 'u@':
						$users[] = $assigned_user;
						break;
					case 'e@':
						$external_users[] = $assigned_user;
						break;
				}
			}

			$email_assignees = array( "roles" => $roles, "users" => $users, "external_users" => $external_users );
		} else {
			$email_assignees = array( "roles" => $roles, "users" => $users, "external_users" => $external_users );
		}

		return $email_assignees;
	}

	/*
	 * generate the page
	 *
	 * @since 2.0
	 */
	public function add_settings_page() {
		$ow_email_settings_helper          = new OW_Email_Settings_Helper();
		$email_options                     = get_option( $this->ow_email_option_name );
		$email_reminder_days_before_option = get_option( $this->ow_email_reminder_day_before_option_name );
		$email_reminder_days_after_option  = get_option( $this->ow_email_reminder_day_after_option_name );
		?>
        <form id="wf_settings_form" method="post" action="options.php">
			<?php
			// adds nonce and option_page fields for the settings page
			settings_fields( 'ow-settings-email' );
			?>
            <div id="workflow-email-setting">
                <div id="settingstuff">
                    <!-- From Name -->
                    <div class="select-info">
                        <label class="settings-title">
							<?php echo __( "From Name:", "oasisworkflow" ); ?>
                        </label>
                        <input type="text" class="regular-text"
                               name="<?php echo $this->ow_email_option_name ?>[from_name]"
                               value="<?php echo esc_attr( $email_options['from_name'] ); ?>"/>
                        <br/>
                        <span
                            class="description"><?php echo __( "(Name to be used for sending the workflow related emails. If left blank, the emails will be sent from the blog name.)",
								"oasisworkflow" ); ?></span>
                    </div>
                    <!-- From Email  -->
                    <div class="select-info">
                        <label class="settings-title">
							<?php echo __( "From Email:", "oasisworkflow" ); ?>
                        </label>
                        <input type="text" class="regular-text"
                               name="<?php echo $this->ow_email_option_name ?>[from_email_address]"
                               value="<?php echo sanitize_email( $email_options['from_email_address'] ); ?>"/>
                        <br/>
                        <span
                            class="description"><?php echo __( "(Email address to be used for sending the workflow related emails. If left blank, the default email will be used.)",
								"oasisworkflow" ); ?></span>
                    </div>

                    <!--  Start of Task assignment/reminder settings -->
                    <fieldset class="owf_fieldset">
                        <legend><?php echo __( "Task Assignment/Reminder Settings", "oasisworkflow" ); ?></legend>
                        <div class="select-info">
							<?php $check = ( $email_options['assignment_emails'] == "yes" ) ? ' checked="checked" '
								: ''; ?>
                            <input type="checkbox" name="<?php echo $this->ow_email_option_name ?>[assignment_emails]"
                                   value="yes" <?php echo $check; ?>/>&nbsp;&nbsp;
                            <label
                                class="settings-title"><?php echo __( "Check this box if you want to send emails when tasks are assigned.",
									"oasisworkflow" ); ?> </label>
                        </div>

                        <div class="select-info">
							<?php $check = ( $email_options['reminder_emails'] == "yes" ) ? ' checked="checked" '
								: ''; ?>
                            <input type="checkbox" name="<?php echo $this->ow_email_option_name ?>[reminder_emails]"
                                   value="yes" <?php echo $check; ?>/>&nbsp;&nbsp;
                            <label
                                class="settings-title"><?php echo __( "Check this box if you want to send reminder emails about a pending task.",
									"oasisworkflow" ); ?> </label>
                            <br class="clearfix">
                            <label class="indented-label">
                                <input type="checkbox"
                                       id="chk_reminder_day" <?php echo ( $email_reminder_days_before_option )
									? "checked" : ""; ?> />&nbsp;&nbsp;
								<?php echo __( " Send Reminder Email", "oasisworkflow" ); ?>
                            </label>
                            <input type="text" size="4" class="reminder_days"
                                   id="<?php echo $this->ow_email_reminder_day_before_option_name ?>"
                                   name="<?php echo $this->ow_email_reminder_day_before_option_name ?>"
                                   value="<?php echo esc_attr( $email_reminder_days_before_option ); ?>" maxlength=2/>
                            <label
                                class="settings-title-normal"><?php echo __( "day(s) before due date.",
									"oasisworkflow" ); ?></label>

                            <br class="clearfix">
                            <label class="indented-label">
                                <input type="checkbox"
                                       id="chk_reminder_day_after" <?php echo ( $email_reminder_days_after_option )
									? "checked" : ""; ?> />&nbsp;&nbsp;
								<?php echo __( " Send Reminder Email", "oasisworkflow" ); ?>
                            </label>
                            <input type="text" size="4" class="reminder_days"
                                   id="<?php echo $this->ow_email_reminder_day_after_option_name ?>"
                                   name="<?php echo $this->ow_email_reminder_day_after_option_name ?>"
                                   value="<?php echo esc_attr( $email_reminder_days_after_option ); ?>" maxlength=2/>
                            <label
                                class="settings-title-normal"><?php echo __( "day(s) after due date.",
									"oasisworkflow" ); ?></label>
                        </div>
                        <br class="clearfix">
                        <hr/>
                        <div class="select-info">
							<?php $check = ( isset( $email_options['digest_emails'] ) &&
							                 $email_options['digest_emails'] == "yes" ) ? ' checked="checked" ' : ''; ?>
                            <input type="checkbox" name="<?php echo $this->ow_email_option_name ?>[digest_emails]"
                                   value="yes" <?php echo $check; ?>/>&nbsp;&nbsp;
                            <label class="settings-title"><?php echo __( "Enable Digest Email",
									"oasisworkflow" ); ?> </label>
                            <br/>
                            <span
                                class="description email-digest"><?php echo __( "(An email digest is an email that combines all assignment emails generated in the last one hour  into ",
										"oasisworkflow" ) . "<b>" . __( "one single message", "oasisworkflow" ) .
							                                                "</b>" . ")" ?></span>
                            <br/>
                            <span
                                class="description email-digest"><b><?php echo __( "This will reduce the number of emails your users get from the workflow system.",
										"oasisworkflow" ); ?></b></span>
                            <br/>
                            <br/>
                            <label
                                class="settings-title indented-label"><?php _e( "Run Email Digest every:",
									"oasisworkflow" ) ?></label>
                            <select name="<?php echo $this->ow_email_option_name; ?>[assignment_email_intervals]">
                                <option value=""><?php _e( "Please Select", "oasisworkflow" ); ?></option>
								<?php $assignment_email_intervals
									= empty( $email_options['assignment_email_intervals'] ) ? 'hourly'
									: $email_options['assignment_email_intervals']; ?>
								<?php if ( $this->assignment_email_intervals ) : ?>
									<?php foreach ( $this->assignment_email_intervals as $interval => $value ) : ?>
										<?php $is_default = $assignment_email_intervals == $interval ? 'selected'
											: ''; ?>
                                        <option
                                            value="<?php echo $interval; ?>" <?php echo $is_default; ?>><?php echo $value["display"]; ?></option>
									<?php endforeach; ?>
								<?php endif; ?>
                            </select>
                        </div>
                    </fieldset>
                    <!--  End of Task reminder settings -->

                    <!--  Start of settings for other emails -->
                    <fieldset class="owf_fieldset">
                        <legend><?php echo __( "Other Email Settings", "oasisworkflow" ); ?></legend>
                        <div class="select-info">
                            <label class="settings-title">
								<?php echo __( "Select Email Type:", "oasisworkflow" ); ?>
                            </label>
                            <select id="email-type-select" name="email-type-select">
								<?php $ow_email_settings_helper->get_email_type_dropdown(); ?>
                            </select>
                        </div>

                        <!-- Start of post publish notification -->
                        <div id="post_publish" class="email-template select-info">
							<?php $this->post_publish_email_settings(); ?>
                        </div>
                        <!-- End of post publish notification -->

                        <!-- Start of Revised Post Published Notification -->
                        <div id="revised_post" class="email-template select-info">
							<?php $this->revised_post_publish_email_settings(); ?>
                        </div>
                        <!-- End of Revised Post Published Notification -->

                        <!-- Start of Unauthorized Update Notification -->
                        <div id="unauthorized_update" class="email-template select-info">
							<?php $this->unauthorized_update_email_settings(); ?>
                        </div>
                        <!-- End of Unauthorized Update Notification -->

                        <!-- Start of Task Claimed Notification -->
                        <div id="task_claim" class="email-template select-info">
							<?php $this->task_claimed_email_settings(); ?>
                        </div>
                        <!-- End of Task Claimed Notification -->

                        <!-- Start of Post Submit Notification -->
                        <div id="post_submit" class="email-template select-info">
							<?php $this->post_submit_email_settings(); ?>
                        </div>
                        <!-- End of Post Submit Notification -->

                        <!-- Start of Workflow Abort Notification -->
                        <div id="workflow_abort" class="email-template select-info">
							<?php $this->workflow_abort_email_settings(); ?>
                        </div>
                        <!-- End of Post Workflow Abort Notification -->

                        <!-- Start of placeholders -->
						<?php $ow_email_settings_helper->get_placeholders(); ?>
                        <!-- End of placeholders -->
                    </fieldset>
                    <!--  End of settings for other emails -->

                    <div class="select-info full-width">
                        <div id="owf_settings_button_bar">
                            <input type="submit" id="emailSettingSave"
                                   class="button button-primary button-large"
                                   value="<?php echo __( "Save", "oasisworkflow" ); ?>"/>
                        </div>
                    </div>
                </div>
            </div>
        </form>
		<?php
	}

	/*
	 * Post publish notification template settings
	 * @since 4.6
	 */
	public function post_publish_email_settings() {
		$ow_email_settings_helper = new OW_Email_Settings_Helper();
		$post_publish_options     = get_option( $this->ow_email_post_publish_option_name ); ?>
        <table class="owf_email_settings">
            <tr>
                <th>
                    <label class="settings-title">
						<?php echo __( "Is Active?:", "oasisworkflow" ); ?>
                    </label>
                </th>
                <td>
					<?php $check = ( isset( $post_publish_options['is_active'] ) &&
					                 $post_publish_options['is_active'] == "yes" ) ? ' checked="checked" ' : ''; ?>
                    <input type="checkbox" name="<?php echo $this->ow_email_post_publish_option_name ?>[is_active]"
                           value="yes" <?php echo $check; ?>/>
                </td>
            </tr>
            <tr>
                <th>
                    <label class="settings-title">
						<?php echo __( "Email Recipients:", "oasisworkflow" ); ?>
                    </label>
                </th>
            </tr>
            <tr>
                <th>
                    <label class="settings-title">
						<?php echo __( "To:", "oasisworkflow" ); ?>
                    </label>
                </th>
                <td>
                    <select name="<?php echo $this->ow_email_post_publish_option_name ?>[email_assignees][]"
                            id="post_publish_email_actors" multiple="multiple">
						<?php
						$options = '';

						$roles          = isset( $post_publish_options['email_assignees']['roles'] )
							? $post_publish_options['email_assignees']['roles'] : array();
						$users          = isset( $post_publish_options['email_assignees']['users'] )
							? $post_publish_options['email_assignees']['users'] : array();
						$external_users = isset( $post_publish_options['email_assignees']['external_users'] )
							? $post_publish_options['email_assignees']['external_users'] : array();

						// display roles
						$options .= $ow_email_settings_helper->get_email_roles_option_list( OW_Email_Settings_Helper::POST_PUBLISH_EMAIL,
							$roles );

						// display all registered users
						$options .= $ow_email_settings_helper->get_email_users_option_list( $users );

						// display all external users
						$options .= $ow_email_settings_helper->get_email_external_users_option_list( $external_users );

						echo $options;
						?>
                    </select>
                </td>
            </tr>
            <tr>
                <th>
                    <label class="settings-title">
						<?php echo __( "Cc:", "oasisworkflow" ); ?>
                    </label>
                </th>
                <td>
                    <select name="<?php echo $this->ow_email_post_publish_option_name ?>[email_cc][]"
                            id="post_publish_cc_email_actors" multiple="multiple">
						<?php
						$options = '';

						$cc_roles          = isset( $post_publish_options['email_cc']['roles'] )
							? $post_publish_options['email_cc']['roles'] : array();
						$cc_users          = isset( $post_publish_options['email_cc']['users'] )
							? $post_publish_options['email_cc']['users'] : array();
						$cc_external_users = isset( $post_publish_options['email_cc']['external_users'] )
							? $post_publish_options['email_cc']['external_users'] : array();

						// display roles
						$options .= $ow_email_settings_helper->get_email_roles_option_list( OW_Email_Settings_Helper::POST_PUBLISH_EMAIL,
							$cc_roles );

						// display all registered users
						$options .= $ow_email_settings_helper->get_email_users_option_list( $cc_users );

						// display all external users
						$options .= $ow_email_settings_helper->get_email_external_users_option_list( $cc_external_users );

						echo $options;
						?>
                    </select>
                </td>
            </tr>
            <tr>
                <th>
                    <label class="settings-title">
						<?php echo __( "Bcc:", "oasisworkflow" ); ?>
                    </label>
                </th>
                <td>
                    <select name="<?php echo $this->ow_email_post_publish_option_name ?>[email_bcc][]"
                            id="post_publish_bcc_email_actors" multiple="multiple">
						<?php
						$options = '';

						$bcc_roles          = isset( $post_publish_options['email_bcc']['roles'] )
							? $post_publish_options['email_bcc']['roles'] : array();
						$bcc_users          = isset( $post_publish_options['email_bcc']['users'] )
							? $post_publish_options['email_bcc']['users'] : array();
						$bcc_external_users = isset( $post_publish_options['email_bcc']['external_users'] )
							? $post_publish_options['email_bcc']['external_users'] : array();

						// display roles
						$options .= $ow_email_settings_helper->get_email_roles_option_list( OW_Email_Settings_Helper::POST_PUBLISH_EMAIL,
							$bcc_roles );

						// display all registered users
						$options .= $ow_email_settings_helper->get_email_users_option_list( $bcc_users );

						// display all external users
						$options .= $ow_email_settings_helper->get_email_external_users_option_list( $bcc_external_users );

						echo $options;
						?>
                    </select>
                </td>
            </tr>
            <tr>
                <th>
                    <label class="settings-title">
						<?php echo __( " Email Subject:", "oasisworkflow" ); ?>
                    </label>
                </th>
                <td>
					<?php
					$subject = isset( $post_publish_options['subject'] ) && $post_publish_options['subject'] != ""
						? esc_attr( $post_publish_options['subject'] )
						: $ow_email_settings_helper->get_post_publish_subject();
					?>
                    <input type="text" class="email-subject"
                           name="<?php echo $this->ow_email_post_publish_option_name ?>[subject]"
                           value="<?php echo $subject; ?>"/>
                </td>
            </tr>
            <tr>
                <th>
                    <label class="settings-title">
						<?php echo __( " Email Content:", "oasisworkflow" ); ?>
                    </label>
                </th>
                <td>
					<?php
					$content   = isset( $post_publish_options['content'] ) && $post_publish_options['content'] != ""
						? stripslashes( $post_publish_options['content'] )
						: $ow_email_settings_helper->get_post_publish_content();
					$args      = array(
						'textarea_name' => $this->ow_email_post_publish_option_name . '[content]',
						'textarea_rows' => 15,
						'media_buttons' => false,
						'editor_height' => 400
					);
					$editor_id = 'publishpost';
					wp_editor( $content, $editor_id, $args );
					?>
                </td>
            </tr>
        </table>
		<?php
	}

	/*
	 * Revised post publish notification template settings
	 * @since 4.6
	 */
	public function revised_post_publish_email_settings() {
		$ow_email_settings_helper     = new OW_Email_Settings_Helper();
		$revised_post_publish_options = get_option( $this->ow_email_revised_post_option_name ); ?>
        <table class="owf_email_settings">
            <tr>
                <th>
                    <label class="settings-title">
						<?php echo __( "Is Active?:", "oasisworkflow" ); ?>
                    </label>
                </th>
                <td>
					<?php $check = ( isset( $revised_post_publish_options['is_active'] ) &&
					                 $revised_post_publish_options['is_active'] == "yes" ) ? ' checked="checked" '
						: ''; ?>
                    <input type="checkbox" name="<?php echo $this->ow_email_revised_post_option_name ?>[is_active]"
                           value="yes" <?php echo $check; ?>/>
                </td>
            </tr>
            <tr>
                <th>
                    <label class="settings-title">
						<?php echo __( "Email Recipients:", "oasisworkflow" ); ?>
                    </label>
                </th>
            </tr>
            <tr>
                <th>
                    <label class="settings-title">
						<?php echo __( "To:", "oasisworkflow" ); ?>
                    </label>
                </th>
                <td>
                    <select name="<?php echo $this->ow_email_revised_post_option_name ?>[email_assignees][]"
                            id="revised_post_email_actors" multiple="multiple">
						<?php
						$options        = '';
						$roles          = isset( $revised_post_publish_options['email_assignees']['roles'] )
							? $revised_post_publish_options['email_assignees']['roles'] : array();
						$users          = isset( $revised_post_publish_options['email_assignees']['users'] )
							? $revised_post_publish_options['email_assignees']['users'] : array();
						$external_users = isset( $revised_post_publish_options['email_assignees']['external_users'] )
							? $revised_post_publish_options['email_assignees']['external_users'] : array();

						// display roles
						$options .= $ow_email_settings_helper->get_email_roles_option_list( OW_Email_Settings_Helper::REVISED_POST_PUBLISH_EMAIL,
							$roles );

						// display all registered users
						$options .= $ow_email_settings_helper->get_email_users_option_list( $users );

						// display all external users
						$options .= $ow_email_settings_helper->get_email_external_users_option_list( $external_users );

						echo $options;
						?>
                    </select>
                </td>
            </tr>
            <tr>
                <th>
                    <label class="settings-title">
						<?php echo __( "Cc:", "oasisworkflow" ); ?>
                    </label>
                </th>
                <td>
                    <select name="<?php echo $this->ow_email_revised_post_option_name ?>[email_cc][]"
                            id="revised_post_cc_email_actors" multiple="multiple">
						<?php
						$options = '';

						$cc_roles          = isset( $revised_post_publish_options['email_cc']['roles'] )
							? $revised_post_publish_options['email_cc']['roles'] : array();
						$cc_users          = isset( $revised_post_publish_options['email_cc']['users'] )
							? $revised_post_publish_options['email_cc']['users'] : array();
						$cc_external_users = isset( $revised_post_publish_options['email_cc']['external_users'] )
							? $revised_post_publish_options['email_cc']['external_users'] : array();

						// display roles
						$options .= $ow_email_settings_helper->get_email_roles_option_list( OW_Email_Settings_Helper::REVISED_POST_PUBLISH_EMAIL,
							$cc_roles );

						// display all registered users
						$options .= $ow_email_settings_helper->get_email_users_option_list( $cc_users );

						// display all external users
						$options .= $ow_email_settings_helper->get_email_external_users_option_list( $cc_external_users );

						echo $options;
						?>
                    </select>
                </td>
            </tr>
            <tr>
                <th>
                    <label class="settings-title">
						<?php echo __( "Bcc:", "oasisworkflow" ); ?>
                    </label>
                </th>
                <td>
                    <select name="<?php echo $this->ow_email_revised_post_option_name ?>[email_bcc][]"
                            id="revised_post_bcc_email_actors" multiple="multiple">
						<?php
						$options = '';

						$bcc_roles          = isset( $revised_post_publish_options['email_bcc']['roles'] )
							? $revised_post_publish_options['email_bcc']['roles'] : array();
						$bcc_users          = isset( $revised_post_publish_options['email_bcc']['users'] )
							? $revised_post_publish_options['email_bcc']['users'] : array();
						$bcc_external_users = isset( $revised_post_publish_options['email_bcc']['external_users'] )
							? $revised_post_publish_options['email_bcc']['external_users'] : array();

						// display roles
						$options .= $ow_email_settings_helper->get_email_roles_option_list( OW_Email_Settings_Helper::REVISED_POST_PUBLISH_EMAIL,
							$bcc_roles );

						// display all registered users
						$options .= $ow_email_settings_helper->get_email_users_option_list( $bcc_users );

						// display all external users
						$options .= $ow_email_settings_helper->get_email_external_users_option_list( $bcc_external_users );

						echo $options;
						?>
                    </select>
                </td>
            </tr>
            <tr>
                <th>
                    <label class="settings-title">
						<?php echo __( " Email Subject:", "oasisworkflow" ); ?>
                    </label>
                </th>
                <td>
					<?php
					$subject = isset( $revised_post_publish_options['subject'] ) &&
					           $revised_post_publish_options['subject'] != ''
						? esc_attr( $revised_post_publish_options['subject'] )
						: $ow_email_settings_helper->get_revised_post_publish_subject();
					?>
                    <input type="text" class="email-subject"
                           name="<?php echo $this->ow_email_revised_post_option_name ?>[subject]"
                           value="<?php echo $subject; ?>"/>
                </td>
            </tr>
            <tr>
                <th>
                    <label class="settings-title">
						<?php echo __( " Email Content:", "oasisworkflow" ); ?>
                    </label>
                </th>
                <td>
					<?php
					$content   = isset( $revised_post_publish_options['content'] ) &&
					             $revised_post_publish_options['content'] != ''
						? stripslashes( $revised_post_publish_options['content'] )
						: $ow_email_settings_helper->get_revised_post_publish_content();
					$args      = array(
						'textarea_name' => $this->ow_email_revised_post_option_name . '[content]',
						'textarea_rows' => 15,
						'media_buttons' => false,
						'editor_height' => 400
					);
					$editor_id = 'revisedpost';
					wp_editor( $content, $editor_id, $args );
					?>
                </td>
            </tr>
        </table>
		<?php
	}

	/*
	 * Unauthorized update notification template settings
	 * @since 4.6
	 */
	public function unauthorized_update_email_settings() {
		$ow_email_settings_helper    = new OW_Email_Settings_Helper();
		$unauthorized_update_options = get_option( $this->ow_email_unauthorized_update_option_name ); ?>
        <table class="owf_email_settings">
            <tr>
                <th>
                    <label class="settings-title">
						<?php echo __( "Is Active?:", "oasisworkflow" ); ?>
                    </label>
                </th>
                <td>
					<?php $check = ( isset( $unauthorized_update_options['is_active'] ) &&
					                 $unauthorized_update_options['is_active'] == "yes" ) ? ' checked="checked" '
						: ''; ?>
                    <input type="checkbox"
                           name="<?php echo $this->ow_email_unauthorized_update_option_name ?>[is_active]"
                           value="yes" <?php echo $check; ?>/>
                </td>
            </tr>
            <tr>
                <th>
                    <label class="settings-title">
						<?php echo __( "Email Recipients:", "oasisworkflow" ); ?>
                    </label>
                </th>
            </tr>
            <tr>
                <th>
                    <label class="settings-title">
						<?php echo __( "To:", "oasisworkflow" ); ?>
                    </label>
                </th>
                <td>
                    <select name="<?php echo $this->ow_email_unauthorized_update_option_name ?>[email_assignees][]"
                            id="unauthorized_update_email_actors" multiple="multiple">
						<?php
						$options = '';

						$roles          = isset( $unauthorized_update_options['email_assignees']['roles'] )
							? $unauthorized_update_options['email_assignees']['roles'] : array();
						$users          = isset( $unauthorized_update_options['email_assignees']['users'] )
							? $unauthorized_update_options['email_assignees']['users'] : array();
						$external_users = isset( $unauthorized_update_options['email_assignees']['external_users'] )
							? $unauthorized_update_options['email_assignees']['external_users'] : array();

						// display roles
						$options .= $ow_email_settings_helper->get_email_roles_option_list( OW_Email_Settings_Helper::UNAUTHORIZED_UPDATE_EMAIL,
							$roles );

						// display all registered users
						$options .= $ow_email_settings_helper->get_email_users_option_list( $users );

						// display all external users
						$options .= $ow_email_settings_helper->get_email_external_users_option_list( $external_users );

						echo $options;
						?>
                    </select>
                </td>
            </tr>
            <tr>
                <th>
                    <label class="settings-title">
						<?php echo __( "Cc:", "oasisworkflow" ); ?>
                    </label>
                </th>
                <td>
                    <select name="<?php echo $this->ow_email_unauthorized_update_option_name ?>[email_cc][]"
                            id="unauthorized_cc_email_actors" multiple="multiple">
						<?php
						$options = '';

						$cc_roles          = isset( $unauthorized_update_options['email_cc']['roles'] )
							? $unauthorized_update_options['email_cc']['roles'] : array();
						$cc_users          = isset( $unauthorized_update_options['email_cc']['users'] )
							? $unauthorized_update_options['email_cc']['users'] : array();
						$cc_external_users = isset( $unauthorized_update_options['email_cc']['external_users'] )
							? $unauthorized_update_options['email_cc']['external_users'] : array();

						// display roles
						$options .= $ow_email_settings_helper->get_email_roles_option_list( OW_Email_Settings_Helper::UNAUTHORIZED_UPDATE_EMAIL,
							$cc_roles );

						// display all registered users
						$options .= $ow_email_settings_helper->get_email_users_option_list( $cc_users );

						// display all external users
						$options .= $ow_email_settings_helper->get_email_external_users_option_list( $cc_external_users );

						echo $options;
						?>
                    </select>
                </td>
            </tr>
            <tr>
                <th>
                    <label class="settings-title">
						<?php echo __( "Bcc:", "oasisworkflow" ); ?>
                    </label>
                </th>
                <td>
                    <select name="<?php echo $this->ow_email_unauthorized_update_option_name ?>[email_bcc][]"
                            id="unauthorized_bcc_email_actors" multiple="multiple">
						<?php
						$options = '';

						$bcc_roles          = isset( $unauthorized_update_options['email_bcc']['roles'] )
							? $unauthorized_update_options['email_bcc']['roles'] : array();
						$bcc_users          = isset( $unauthorized_update_options['email_bcc']['users'] )
							? $unauthorized_update_options['email_bcc']['users'] : array();
						$bcc_external_users = isset( $unauthorized_update_options['email_bcc']['external_users'] )
							? $unauthorized_update_options['email_bcc']['external_users'] : array();

						// display roles
						$options .= $ow_email_settings_helper->get_email_roles_option_list( OW_Email_Settings_Helper::UNAUTHORIZED_UPDATE_EMAIL,
							$bcc_roles );

						// display all registered users
						$options .= $ow_email_settings_helper->get_email_users_option_list( $bcc_users );

						// display all external users
						$options .= $ow_email_settings_helper->get_email_external_users_option_list( $bcc_external_users );

						echo $options;
						?>
                    </select>
                </td>
            </tr>
            <tr>
                <th>
                    <label class="settings-title">
						<?php echo __( " Email Subject:", "oasisworkflow" ); ?>
                    </label>
                </th>
                <td>
					<?php
					$subject = isset( $unauthorized_update_options['subject'] ) &&
					           $unauthorized_update_options['subject'] != ''
						? esc_attr( $unauthorized_update_options['subject'] )
						: $ow_email_settings_helper->get_unauthorized_update_subject();
					?>
                    <input type="text" class="email-subject"
                           name="<?php echo $this->ow_email_unauthorized_update_option_name ?>[subject]"
                           value="<?php echo $subject; ?>"/>
                </td>
            </tr>
            <tr>
                <th>
                    <label class="settings-title">
						<?php echo __( " Email Content:", "oasisworkflow" ); ?>
                    </label>
                </th>
                <td>
					<?php
					$content   = isset( $unauthorized_update_options['content'] ) &&
					             $unauthorized_update_options['content'] != ''
						? stripslashes( $unauthorized_update_options['content'] )
						: $ow_email_settings_helper->get_unauthorized_update_content();
					$args      = array(
						'textarea_name' => $this->ow_email_unauthorized_update_option_name . '[content]',
						'textarea_rows' => 15,
						'media_buttons' => false,
						'editor_height' => 400
					);
					$editor_id = 'unauthorizedpost';
					wp_editor( $content, $editor_id, $args );
					?>
                </td>
            </tr>
        </table>
		<?php
	}

	/*
	 * Task claimed notification template settings
	 * @since 4.6
	 */
	public function task_claimed_email_settings() {
		$ow_email_settings_helper = new OW_Email_Settings_Helper();
		$task_claimed_options     = get_option( $this->ow_email_task_claim_option_name ); ?>
        <table class="owf_email_settings">
            <tr>
                <th>
                    <label class="settings-title">
						<?php echo __( "Is Active?:", "oasisworkflow" ); ?>
                    </label>
                </th>
                <td>
					<?php $check = ( isset( $task_claimed_options['is_active'] ) &&
					                 $task_claimed_options['is_active'] == "yes" ) ? ' checked="checked" ' : ''; ?>
                    <input type="checkbox" name="<?php echo $this->ow_email_task_claim_option_name ?>[is_active]"
                           value="yes" <?php echo $check; ?>/>
                </td>
            </tr>
            <tr>
                <th>
                    <label class="settings-title">
						<?php echo __( "Email Recipients:", "oasisworkflow" ); ?>
                    </label>
                </th>
            </tr>
            <tr>
                <th>
                    <label class="settings-title">
						<?php echo __( "To:", "oasisworkflow" ); ?>
                    </label>
                </th>
                <td>
                    <select name="<?php echo $this->ow_email_task_claim_option_name ?>[email_assignees][]"
                            id="task_claim_email_actors" multiple="multiple">
						<?php
						$options = '';

						$roles          = isset( $task_claimed_options['email_assignees']['roles'] )
							? $task_claimed_options['email_assignees']['roles'] : array();
						$users          = isset( $task_claimed_options['email_assignees']['users'] )
							? $task_claimed_options['email_assignees']['users'] : array();
						$external_users = isset( $task_claimed_options['email_assignees']['external_users'] )
							? $task_claimed_options['email_assignees']['external_users'] : array();

						// display roles
						$options .= $ow_email_settings_helper->get_email_roles_option_list( OW_Email_Settings_Helper::TASK_CLAIMED_EMAIL,
							$roles );

						// display all registered users
						$options .= $ow_email_settings_helper->get_email_users_option_list( $users );

						// display all external users
						$options .= $ow_email_settings_helper->get_email_external_users_option_list( $external_users );

						echo $options;
						?>
                    </select>
                </td>
            </tr>
            <tr>
                <th>
                    <label class="settings-title">
						<?php echo __( "Cc:", "oasisworkflow" ); ?>
                    </label>
                </th>
                <td>
                    <select name="<?php echo $this->ow_email_task_claim_option_name ?>[email_cc][]"
                            id="claim_cc_email_actors" multiple="multiple">
						<?php
						$options = '';

						$cc_roles          = isset( $task_claimed_options['email_cc']['roles'] )
							? $task_claimed_options['email_cc']['roles'] : array();
						$cc_users          = isset( $task_claimed_options['email_cc']['users'] )
							? $task_claimed_options['email_cc']['users'] : array();
						$cc_external_users = isset( $task_claimed_options['email_cc']['external_users'] )
							? $task_claimed_options['email_cc']['external_users'] : array();

						// display roles
						$options .= $ow_email_settings_helper->get_email_roles_option_list( OW_Email_Settings_Helper::TASK_CLAIMED_EMAIL,
							$cc_roles );

						// display all registered users
						$options .= $ow_email_settings_helper->get_email_users_option_list( $cc_users );

						// display all external users
						$options .= $ow_email_settings_helper->get_email_external_users_option_list( $cc_external_users );

						echo $options;
						?>
                    </select>
                </td>
            </tr>
            <tr>
                <th>
                    <label class="settings-title">
						<?php echo __( "Bcc:", "oasisworkflow" ); ?>
                    </label>
                </th>
                <td>
                    <select name="<?php echo $this->ow_email_task_claim_option_name ?>[email_bcc][]"
                            id="claim_bcc_email_actors" multiple="multiple">
						<?php
						$options = '';

						$bcc_roles          = isset( $task_claimed_options['email_bcc']['roles'] )
							? $task_claimed_options['email_bcc']['roles'] : array();
						$bcc_users          = isset( $task_claimed_options['email_bcc']['users'] )
							? $task_claimed_options['email_bcc']['users'] : array();
						$bcc_external_users = isset( $task_claimed_options['email_bcc']['external_users'] )
							? $task_claimed_options['email_bcc']['external_users'] : array();

						// display roles
						$options .= $ow_email_settings_helper->get_email_roles_option_list( OW_Email_Settings_Helper::TASK_CLAIMED_EMAIL,
							$bcc_roles );

						// display all registered users
						$options .= $ow_email_settings_helper->get_email_users_option_list( $bcc_users );

						// display all external users
						$options .= $ow_email_settings_helper->get_email_external_users_option_list( $bcc_external_users );

						echo $options;
						?>
                    </select>
                </td>
            </tr>
            <tr>
                <th>
                    <label class="settings-title">
						<?php echo __( " Email Subject:", "oasisworkflow" ); ?>
                    </label>
                </th>
                <td>
					<?php
					$subject = isset( $task_claimed_options['subject'] ) && $task_claimed_options['subject'] != ''
						? esc_attr( $task_claimed_options['subject'] )
						: $ow_email_settings_helper->get_task_claimed_subject();
					?>
                    <input type="text" class="email-subject"
                           name="<?php echo $this->ow_email_task_claim_option_name ?>[subject]"
                           value="<?php echo $subject; ?>"/>
                </td>
            </tr>
            <tr>
                <th>
                    <label class="settings-title">
						<?php echo __( " Email Content:", "oasisworkflow" ); ?>
                    </label>
                </th>
                <td>
					<?php
					$content   = isset( $task_claimed_options['content'] ) && $task_claimed_options['content'] != ''
						? stripslashes( $task_claimed_options['content'] )
						: $ow_email_settings_helper->get_task_claimed_content();
					$args      = array(
						'textarea_name' => $this->ow_email_task_claim_option_name . '[content]',
						'textarea_rows' => 15,
						'media_buttons' => false,
						'editor_height' => 400
					);
					$editor_id = 'taskclaim';
					wp_editor( $content, $editor_id, $args );
					?>
                </td>
            </tr>
        </table>
		<?php
	}

	/*
	 * Post submit notification template settings
	 * @since 4.6
	 */
	public function post_submit_email_settings() {
		$ow_email_settings_helper = new OW_Email_Settings_Helper();
		$post_submit_options      = get_option( $this->ow_email_post_submit_option_name ); ?>
        <table class="owf_email_settings">
            <tr>
                <th>
                    <label class="settings-title">
						<?php echo __( "Is Active?:", "oasisworkflow" ); ?>
                    </label>
                </th>
                <td>
					<?php $check = ( isset( $post_submit_options['is_active'] ) &&
					                 $post_submit_options['is_active'] == "yes" ) ? ' checked="checked" ' : ''; ?>
                    <input type="checkbox" name="<?php echo $this->ow_email_post_submit_option_name ?>[is_active]"
                           value="yes" <?php echo $check; ?>/>
                </td>
            </tr>
            <tr>
                <th>
                    <label class="settings-title">
						<?php echo __( "Email Recipients:", "oasisworkflow" ); ?>
                    </label>
                </th>
            </tr>
            <tr>
                <th>
                    <label class="settings-title">
						<?php echo __( "To:", "oasisworkflow" ); ?>
                    </label>
                </th>
                <td>
                    <select name="<?php echo $this->ow_email_post_submit_option_name ?>[email_assignees][]"
                            id="post_submit_email_actors" multiple="multiple">
						<?php
						$options = '';

						$roles          = isset( $post_submit_options['email_assignees']['roles'] )
							? $post_submit_options['email_assignees']['roles'] : array();
						$users          = isset( $post_submit_options['email_assignees']['users'] )
							? $post_submit_options['email_assignees']['users'] : array();
						$external_users = isset( $post_submit_options['email_assignees']['external_users'] )
							? $post_submit_options['email_assignees']['external_users'] : array();

						// display roles
						$options .= $ow_email_settings_helper->get_email_roles_option_list( OW_Email_Settings_Helper::POST_SUBMITTED_EMAIL,
							$roles );

						// display all registered users
						$options .= $ow_email_settings_helper->get_email_users_option_list( $users );

						// display all external users
						$options .= $ow_email_settings_helper->get_email_external_users_option_list( $external_users );

						echo $options;
						?>
                    </select>
                </td>
            </tr>
            <tr>
                <th>
                    <label class="settings-title">
						<?php echo __( "Cc:", "oasisworkflow" ); ?>
                    </label>
                </th>
                <td>
                    <select name="<?php echo $this->ow_email_post_submit_option_name ?>[email_cc][]"
                            id="post_submit_cc_email_actors" multiple="multiple">
						<?php
						$options = '';

						$cc_roles          = isset( $post_submit_options['email_cc']['roles'] )
							? $post_submit_options['email_cc']['roles'] : array();
						$cc_users          = isset( $post_submit_options['email_cc']['users'] )
							? $post_submit_options['email_cc']['users'] : array();
						$cc_external_users = isset( $post_submit_options['email_cc']['external_users'] )
							? $post_submit_options['email_cc']['external_users'] : array();

						// display roles
						$options .= $ow_email_settings_helper->get_email_roles_option_list( OW_Email_Settings_Helper::POST_SUBMITTED_EMAIL,
							$cc_roles );

						// display all registered users
						$options .= $ow_email_settings_helper->get_email_users_option_list( $cc_users );

						// display all external users
						$options .= $ow_email_settings_helper->get_email_external_users_option_list( $cc_external_users );

						echo $options;
						?>
                    </select>
                </td>
            </tr>
            <tr>
                <th>
                    <label class="settings-title">
						<?php echo __( "Bcc:", "oasisworkflow" ); ?>
                    </label>
                </th>
                <td>
                    <select name="<?php echo $this->ow_email_post_submit_option_name ?>[email_bcc][]"
                            id="post_submit_bcc_email_actors" multiple="multiple">
						<?php
						$options = '';

						$bcc_roles          = isset( $post_submit_options['email_bcc']['roles'] )
							? $post_submit_options['email_bcc']['roles'] : array();
						$bcc_users          = isset( $post_submit_options['email_bcc']['users'] )
							? $post_submit_options['email_bcc']['users'] : array();
						$bcc_external_users = isset( $post_submit_options['email_bcc']['external_users'] )
							? $post_submit_options['email_bcc']['external_users'] : array();

						// display roles
						$options .= $ow_email_settings_helper->get_email_roles_option_list( OW_Email_Settings_Helper::POST_SUBMITTED_EMAIL,
							$bcc_roles );

						// display all registered users
						$options .= $ow_email_settings_helper->get_email_users_option_list( $bcc_users );

						// display all external users
						$options .= $ow_email_settings_helper->get_email_external_users_option_list( $bcc_external_users );

						echo $options;
						?>
                    </select>
                </td>
            </tr>
            <tr>
                <th>
                    <label class="settings-title">
						<?php echo __( " Email Subject:", "oasisworkflow" ); ?>
                    </label>
                </th>
                <td>
					<?php
					$subject = isset( $post_submit_options['subject'] ) && $post_submit_options['subject'] != ''
						? esc_attr( $post_submit_options['subject'] )
						: $ow_email_settings_helper->get_post_submit_subject();
					?>
                    <input type="text" class="email-subject"
                           name="<?php echo $this->ow_email_post_submit_option_name ?>[subject]"
                           value="<?php echo $subject; ?>"/>
                </td>
            </tr>
            <tr>
                <th>
                    <label class="settings-title">
						<?php echo __( " Email Content:", "oasisworkflow" ); ?>
                    </label>
                </th>
                <td>
					<?php
					$content   = isset( $post_submit_options['content'] ) && $post_submit_options['content'] != ''
						? stripslashes( $post_submit_options['content'] )
						: $ow_email_settings_helper->get_post_submit_content();
					$args      = array(
						'textarea_name' => $this->ow_email_post_submit_option_name . '[content]',
						'textarea_rows' => 15,
						'media_buttons' => false,
						'editor_height' => 400
					);
					$editor_id = 'postsubmit';
					wp_editor( $content, $editor_id, $args );
					?>
                </td>
            </tr>
        </table>
		<?php
	}

	/*
	 * Workflow abort notification template settings
	 * @since 4.6
	 */
	public function workflow_abort_email_settings() {
		$ow_email_settings_helper = new OW_Email_Settings_Helper();
		$workflow_abort_options   = get_option( $this->ow_email_workflow_abort_option_name ); ?>
        <table class="owf_email_settings">
            <tr>
                <th>
                    <label class="settings-title">
						<?php echo __( "Is Active?:", "oasisworkflow" ); ?>
                    </label>
                </th>
                <td>
					<?php $check = ( isset( $workflow_abort_options['is_active'] ) &&
					                 $workflow_abort_options['is_active'] == "yes" ) ? ' checked="checked" ' : ''; ?>
                    <input type="checkbox" name="<?php echo $this->ow_email_workflow_abort_option_name ?>[is_active]"
                           value="yes" <?php echo $check; ?>/>
                </td>
            </tr>
            <tr>
                <th>
                    <label class="settings-title">
						<?php echo __( "Email Recipients:", "oasisworkflow" ); ?>
                    </label>
                </th>
            </tr>
            <tr>
                <th>
                    <label class="settings-title">
						<?php echo __( "To:", "oasisworkflow" ); ?>
                    </label>
                </th>
                <td>
                    <select name="<?php echo $this->ow_email_workflow_abort_option_name ?>[email_assignees][]"
                            id="workflow_abort_email_actors" multiple="multiple">
						<?php
						$options = '';

						$roles          = isset( $workflow_abort_options['email_assignees']['roles'] )
							? $workflow_abort_options['email_assignees']['roles'] : array();
						$users          = isset( $workflow_abort_options['email_assignees']['users'] )
							? $workflow_abort_options['email_assignees']['users'] : array();
						$external_users = isset( $workflow_abort_options['email_assignees']['external_users'] )
							? $workflow_abort_options['email_assignees']['external_users'] : array();

						// display roles
						$options .= $ow_email_settings_helper->get_email_roles_option_list( OW_Email_Settings_Helper::WORKFLOW_ABORT_EMAIL,
							$roles );

						// display all registered users
						$options .= $ow_email_settings_helper->get_email_users_option_list( $users );

						// display all external users
						$options .= $ow_email_settings_helper->get_email_external_users_option_list( $external_users );

						echo $options;
						?>
                    </select>
                </td>
            </tr>
            <tr>
                <th>
                    <label class="settings-title">
						<?php echo __( "Cc:", "oasisworkflow" ); ?>
                    </label>
                </th>
                <td>
                    <select name="<?php echo $this->ow_email_workflow_abort_option_name ?>[email_cc][]"
                            id="abort_cc_email_actors" multiple="multiple">
						<?php
						$options = '';

						$cc_roles          = isset( $workflow_abort_options['email_cc']['roles'] )
							? $workflow_abort_options['email_cc']['roles'] : array();
						$cc_users          = isset( $workflow_abort_options['email_cc']['users'] )
							? $workflow_abort_options['email_cc']['users'] : array();
						$cc_external_users = isset( $workflow_abort_options['email_cc']['external_users'] )
							? $workflow_abort_options['email_cc']['external_users'] : array();

						// display roles
						$options .= $ow_email_settings_helper->get_email_roles_option_list( OW_Email_Settings_Helper::WORKFLOW_ABORT_EMAIL,
							$cc_roles );

						// display all registered users
						$options .= $ow_email_settings_helper->get_email_users_option_list( $cc_users );

						// display all external users
						$options .= $ow_email_settings_helper->get_email_external_users_option_list( $cc_external_users );

						echo $options;
						?>
                    </select>
                </td>
            </tr>
            <tr>
                <th>
                    <label class="settings-title">
						<?php echo __( "Bcc:", "oasisworkflow" ); ?>
                    </label>
                </th>
                <td>
                    <select name="<?php echo $this->ow_email_workflow_abort_option_name ?>[email_bcc][]"
                            id="abort_bcc_email_actors" multiple="multiple">
						<?php
						$options = '';

						$bcc_roles          = isset( $workflow_abort_options['email_bcc']['roles'] )
							? $workflow_abort_options['email_bcc']['roles'] : array();
						$bcc_users          = isset( $workflow_abort_options['email_bcc']['users'] )
							? $workflow_abort_options['email_bcc']['users'] : array();
						$bcc_external_users = isset( $workflow_abort_options['email_bcc']['external_users'] )
							? $workflow_abort_options['email_bcc']['external_users'] : array();

						// display roles
						$options .= $ow_email_settings_helper->get_email_roles_option_list( OW_Email_Settings_Helper::WORKFLOW_ABORT_EMAIL,
							$bcc_roles );

						// display all registered users
						$options .= $ow_email_settings_helper->get_email_users_option_list( $bcc_users );

						// display all external users
						$options .= $ow_email_settings_helper->get_email_external_users_option_list( $bcc_external_users );

						echo $options;
						?>
                    </select>
                </td>
            </tr>
            <tr>
                <th>
                    <label class="settings-title">
						<?php echo __( " Email Subject:", "oasisworkflow" ); ?>
                    </label>
                </th>
                <td>
					<?php
					$subject = isset( $workflow_abort_options['subject'] ) && $workflow_abort_options['subject'] != ''
						? esc_attr( $workflow_abort_options['subject'] )
						: $ow_email_settings_helper->get_workflow_abort_subject();
					?>
                    <input type="text" class="email-subject"
                           name="<?php echo $this->ow_email_workflow_abort_option_name ?>[subject]"
                           value="<?php echo $subject; ?>"/>
                </td>
            </tr>
            <tr>
                <th>
                    <label class="settings-title">
						<?php echo __( " Email Content:", "oasisworkflow" ); ?>
                    </label>
                </th>
                <td>
					<?php
					$content   = isset( $workflow_abort_options['content'] ) && $workflow_abort_options['content'] != ''
						? stripslashes( $workflow_abort_options['content'] )
						: $ow_email_settings_helper->get_workflow_abort_content();
					$args      = array(
						'textarea_name' => $this->ow_email_workflow_abort_option_name . '[content]',
						'textarea_rows' => 15,
						'media_buttons' => false,
						'editor_height' => 400
					);
					$editor_id = 'workflowabort';
					wp_editor( $content, $editor_id, $args );
					?>
                </td>
            </tr>
        </table>
		<?php
	}

}

$ow_email_settings = new OW_Email_Settings();
?>
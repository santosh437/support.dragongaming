<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

$page_number = ( isset( $_GET['paged'] ) && sanitize_text_field( $_GET["paged"] ) )
	? intval( sanitize_text_field( $_GET["paged"] ) ) : 1;

$due_date_type = ( isset( $_REQUEST['due_date_type'] ) && sanitize_text_field( $_REQUEST["due_date_type"] ) )
	? sanitize_text_field( $_REQUEST["due_date_type"] ) : "none";

$ow_report_service = new OW_Report_Service();
$ow_process_flow   = new OW_Process_Flow();
$workflow_service  = new OW_Workflow_Service();

$count_assignments = $ow_report_service->count_workflow_assignments( $due_date_type );
$assigned_tasks    = $ow_report_service->generate_workflow_assignment_report( $page_number, $due_date_type );

$per_page = OASIS_PER_PAGE;

$filters                  = array();
$filters["due_date_type"] = $due_date_type;
?>

<div class="wrap">
    <div id="view-workflow">
        <form id="duedate_report_form" method="post"
              action="<?php echo admin_url( 'admin.php?page=oasiswf-reports&tab=taskByDueDate' ); ?>">
            <div class="tablenav top">
                <div class="alignleft actions">
                    <select name="due_date_type">
                        <option value=""><?php echo __( "All Due Dates", "oasisworkflow" ); ?></option>
						<?php OW_Utility::instance()->get_due_date_dropdown( $due_date_type ); ?>
                    </select>
                    <input type="submit" class="button action" value="Filter"/>
                </div>
                <div class="tablenav-pages">
					<?php OW_Utility::instance()
					                ->get_page_link( $count_assignments, $page_number, $per_page, "", $filters ); ?>
                </div>
            </div>
        </form>
        <table class="wp-list-table widefat fixed posts" cellspacing="0" border=0>
			<?php $report_column_header = $ow_report_service->get_current_assigment_table_header(); ?>
            <thead>
            <tr>
				<?php
				echo implode( '', $report_column_header );
				?>
            </tr>
            </thead>
            <tfoot>
            <tr>
				<?php
				echo implode( '', $report_column_header );
				?>
            </tr>
            </tfoot>
            <tbody id="coupon-list">
			<?php
			$ow_report_service->get_assignment_table_rows( $assigned_tasks, $report_column_header );
			?>
            </tbody>
        </table>
        <div class="tablenav">
            <div class="tablenav-pages">
				<?php OW_Utility::instance()
				                ->get_page_link( $count_assignments, $page_number, $per_page, "", $filters ); ?>
            </div>
        </div>
    </div>
</div>   
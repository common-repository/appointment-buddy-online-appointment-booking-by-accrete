<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
if(isset($_GET['page']) && $_GET['page'] == 'view-booked-appointment-list') 
{ //if-1

	if( ! class_exists( 'WP_List_Table' ) ) {
		require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
	}
	
	class Appointment_List_Table extends WP_List_Table 
	{
		function __construct()
		{
			global $status, $page;
					
			//Set parent defaults
			parent::__construct( array(
				'singular'  => 'appointment',     //singular name of the listed records
				'plural'    => 'appointments',    //plural name of the listed records
			) );
			
		}
		
		function get_columns()
		{
			$columnNames = array(
				'cb' => '<input type="checkbox" />',
				'personName' => 'Patient Name',
				'appointmentDate' => 'Appointment Date',
				'slotTimeDesc' => 'Time Slot',
				'serviceName' => 'Services',
				'personEmailId' => 'Email Id',
				'personMobileNo' => 'Contact Number',
				'remarks' => 'Remarks',
				'isDeleted' => 'Delete'				
			);
			return $columnNames;	
		}
		
		function column_default( $item, $column_name ) 
		{
			switch( $column_name ) 
			{ 				
				case 'personName':
				case 'appointmentDate':
				case 'slotTimeDesc':
				case 'serviceName':
				case 'personEmailId':
				case 'personMobileNo':
				case 'remarks':
					return $item[ $column_name ];
				default:
					return print_r( $item, true ) ; 
			}
		}
		
		function column_cb($item)
		{
			return sprintf( '<input type="checkbox" name="appointmentId[]" value="%s">',$item['appointmentId']);
		}
		
		function column_isDeleted($item)
    	{
			$actions = array(
				'delete' => sprintf('<a href="?page=%s&action=delete&appointmentId=%s">%s</a>', $_REQUEST['page'], $item['appointmentId'], __('Delete')),
			);
	
			return $this->row_actions($actions, true);
		}
		
		function get_bulk_actions()
		{
			$actions = array(
				'delete' => 'Delete'
			);
			return $actions;
		}
		
		function process_bulk_action()
		{
			global $wpdb;
        	$abAppointmentMst = $wpdb->prefix . "abAppointmentMst";
			
			if ('delete' === $this->current_action()) {
				$ids = isset($_REQUEST['appointmentId']) ? $_REQUEST['appointmentId'] : array();
				if (is_array($ids)) $ids = implode(',', $ids);
	
				if (!empty($ids)) {
					$deleteResult = $wpdb->query("UPDATE ".$abAppointmentMst." SET isDeleted=1 WHERE appointmentId IN(".$ids.")");
				
					$plugins_url = admin_url().'admin.php?page=view-booked-appointment-list';
					
					if($deleteResult)
					{
						echo "<script>jQuery(document).ready(function() { window.location.replace('".$plugins_url."'); });</script>";
                    }
					unset($deleteResult);
				}
			}
		}
		
		function get_sortable_columns()
		{
			$sortColumn = array(
				'appointmentDate' => array('appointmentDate', true),
				'personName' => array('personName', true)
			);
			return $sortColumn;
		}
		
		
		function prepare_items() 
		{
			global $wpdb;
			$perPage = 10;
			$currentPage = $this->get_pagenum();
			
			$abAppointmentMst = $wpdb->prefix . "abAppointmentMst";
			$abTimeSlotMst = $wpdb->prefix . "abTimeSlotMst";
			$abSlotMappingDetails = $wpdb->prefix . "abSlotMappingDetails";
			$abServiceMst = $wpdb->prefix . "abServiceMst";
			$totalItems = $wpdb->get_var("SELECT COUNT(appointmentId) FROM ".$abAppointmentMst." WHERE isDeleted=0");
			
			$columns = $this->get_columns();
			$hidden = array();
			$sortable = $this->get_sortable_columns();
			$this->_column_headers = array($columns, $hidden, $sortable);
			$this->process_bulk_action();
			
			$orderby = (isset($_REQUEST['orderby']) && in_array($_REQUEST['orderby'], array_keys($sortable))) ? $_REQUEST['orderby'] : 'appointmentDate';
			
			$order = (isset($_REQUEST['order']) && in_array($_REQUEST['order'], array('asc', 'desc'))) ? $_REQUEST['order'] : 'desc';
			
			$appointmentDatas = $wpdb->get_results("SELECT ap.isDeleted, ap.appointmentId, ap.personName, DATE_FORMAT(ap.appointmentDate,'%d-%m-%Y') as appointmentDate, tr.serviceName, ts.slotName, concat(DATE_FORMAT(ts.slotStartTime, '%H:%i'), ' - ', DATE_FORMAT(ts.slotEndTime, '%H:%i')) as slotTimeDesc, ap.appointmentSlotMappingId, ap.personEmailId, ap.personMobileNo, ap.remarks
FROM ".$abAppointmentMst." ap
INNER JOIN ".$abServiceMst." tr ON ap.serviceId = tr.serviceId
INNER JOIN ".$abSlotMappingDetails." smd ON ap.appointmentSlotMappingId = smd.slotMappingId
INNER JOIN ".$abTimeSlotMst." ts ON smd.slotId = ts.slotId
WHERE ap.isDeleted=0
ORDER BY ap.".$orderby." ".$order."", ARRAY_A);
		
			$appointmentDataValues = array_slice($appointmentDatas,(($currentPage-1)*$perPage),$perPage);
			
			$this->set_pagination_args(array(
				'total_items' => $totalItems, // total items defined above
				'per_page' => $perPage, // per page constant defined at top of method
				'total_pages' => ceil($totalItems / $perPage) // calculate pages count
			));
			
			$this->items = $appointmentDataValues;
		}
	}
	
$myAppointmentTable = new Appointment_List_Table();
$myAppointmentTable->prepare_items(); 
?>
<div class="wrap">
	<h2 id="headingTextH2"><span class="dashicons dashicons-list-view" style="padding-top: 6px;"></span>&nbsp;Appointments List</h2>
	<a class="button" id="export" href="#"><i class="dashicons dashicons-download"></i> &nbsp; Export To CSV</a>    
    <form id="appointment-widget-form" method="GET">
        <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
        <div id="appointment-widget" class="table-responsive">
        <?php 
			$myAppointmentTable->display(); 
		?>
        </div>
    </form>
   
</div>
<?php
} //if-1 ends
?>
<script type='text/javascript'>
jQuery(document).ready(function() {
	
	function exportTableToCSV($table, filename) {
		var $headers = $table.find('tr:has(th)')
			,$rows = $table.find('tr:has(td)')

			// Temporary delimiter characters unlikely to be typed by keyboard
			// This is to avoid accidentally splitting the actual contents
			,tmpColDelim = String.fromCharCode(11) // vertical tab character
			,tmpRowDelim = String.fromCharCode(0) // null character

			// actual delimiter characters for CSV format
			,colDelim = '","'
			,rowDelim = '"\r\n"';

			// Grab text from table into CSV formatted string
			var csv = '"';
			csv += formatRows($headers.map(grabRow));
			csv += rowDelim + '"';
			//csv += formatRows($rows.map(grabRow)) + '"';
			
			// Data URI
			var csvData = 'data:application/csv;charset=utf-8,' + encodeURIComponent(csv);

		jQuery(this)
			.attr({
			'download': filename
				,'href': csvData
				//,'target' : '_blank' //if you want it to open in a new window
		});

		//------------------------------------------------------------
		// Helper Functions 
		//------------------------------------------------------------
		// Format the output so it has the appropriate delimiters
		function formatRows(rows){
			return rows.get().join(tmpRowDelim)
				.split(tmpRowDelim).join(rowDelim)
				.split(tmpColDelim).join(colDelim);
		}
		// Grab and format a row from the table
		function grabRow(i,row){
			 
			var $row = jQuery(row);
			//for some reason $cols = $row.find('td') || $row.find('th') won't work...
			var $cols = $row.find('td'); 
			if(!$cols.length) $cols = $row.find('th');  

			return $cols.map(grabCol)
						.get().join(tmpColDelim);
		}
		// Grab and format a column from the table 
		function grabCol(j,col){
			var $col = jQuery(col),
				$text = $col.text();

			return $text.replace('"', '""'); // escape double quotes

		}
	}


	// This must be a hyperlink
	jQuery("#export").click(function (event) {
		var outputFile = 'appointmentList';
		outputFile = outputFile.replace('.csv','') + '.csv'
		 
		// CSV
		exportTableToCSV.apply(this, [jQuery('#appointment-widget>table'), outputFile]);
		
		// IF CSV, don't do event.preventDefault() or return false
		// We actually need this to be a typical hyperlink
	});
	
	jQuery('#appointment-widget>table').find('.screen-reader-text').remove();
	jQuery('#appointment-widget>table').find('.toggle-row').remove();

});
</script>
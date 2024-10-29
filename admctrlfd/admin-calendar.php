<?php 
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
if(isset($_GET['page']) && $_GET['page'] == 'appointment-calendar') 
{ 
?>
<form action="#" method="post">

    <div class="wrap">
        <div class="row">
        	<div class="col-md-6">
            	<h1 id="headingTextH2">View Appointments</h1>
            </div>
            <div class="col-md-6">                
                <table>
                    <tr>
                        <td><span class="bookedAppointment"></span>For Booked Appointments</td>
                    </tr>
                    <tr>
                        <td><span class="holidayDefined"></span>For Holidays (Black out days)</td>
                    </tr>
                </table>
            </div>            
        </div>
        <div class="card" style="max-width: 100%; padding-top: 20px; padding-bottom: 30px;">
        	<div id="calendar"></div>
            <!-- /#calendar -->
        </div>

    </div>
    <!-- /.wrap -->
</form>
<script>
jQuery(document).ready(function()
{
	var json_events;
	
	jQuery.ajax({
		type: "post",
		cache:false,
		url:'<?php echo apbud_AJAX_URL; ?>',
		data: { 
			action: 'apbud_admin_fetch_appointments',
			dataString :  "type=fetch" ,
			ajax_nonce: '<?php echo wp_create_nonce('apbud_admin_fetch_appointments'); ?>'
		},
		dataType: 'html',
		success: function(response){ 
			json_events = response;
			viewCalendar(json_events);
		}
	});
	
	

	/****************************************************************************************************************************************/
	
	// FULL CALENDAR UI
	
	function viewCalendar(json_events)
	{
	if( jQuery('#calendar').length > 0 ){
		//alert(json_events);
		jQuery('#calendar').fullCalendar({
			events:jQuery.parseJSON(json_events),
			header: {
				left: 'today, prev, next',
				center: 'title',
				right: 'month, agendaWeek, agendaDay, listWeek'
			},
			selectable: true,
			selectHelper: true,
			editable: false,
			eventLimit: true,
			eventRender: function(event, element, view) {
				
				if(event.color != '#CB4630'){
					element.webuiPopover({
						trigger: 'click',
						placement: 'top',
						title: 'Appointment -['+moment(event.start).format("DD-MM-YYYY")+']',
						content: '<table><tr><td>Patient </td><td><strong>'+event.personName+'</strong></td></tr><tr><td>Slot Time </td><td><strong>'+event.slotStartTime+'-'+event.slotEndTime+'<strong></td></tr><tr><td>Service</td><td><strong>'+event.serviceName+'</strong></td></tr><tr><td>Email</td><td><strong>'+event.personEmailId+'</strong></td></tr><tr><td>Phone No</td><td><strong>'+event.personMobileNo+'</strong></td></tr></table>',
						closeable: true,
						dismissible: true,
						width: 280
					});
				}
			}
	
		});
	}
	}

	
}); // document.ready	
</script>
<?php } ?>
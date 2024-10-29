<?php
	// Exit if accessed directly
	if ( ! defined( 'ABSPATH' ) ) exit;
?>
<?php 
global $wpdb;
$abTimeSlotMst = $wpdb->prefix . "abTimeSlotMst";
$fetchResults = $wpdb->get_results("Select slotId,slotName,DATE_FORMAT(slotStartTime, '%H:%i') as slotStartTime,DATE_FORMAT(slotEndTime, '%H:%i') as slotEndTime,maxAppointmentsPerSlot from ".$abTimeSlotMst." where isDeleted=0",ARRAY_A);
$countTimeSlots = $wpdb->get_var( "Select count(*) from ".$abTimeSlotMst." where isDeleted=0" );
$i=0;
?>
<div class="card" style="max-width: 100%; padding-top: 20px; padding-bottom: 30px;" id="time-slot-div">
    <form action="#" method="post" name="timeSlotForm" id="timeSlotForm">
    	<div id="time-slots">
            <table class="wp-list-table widefat fixed striped time-slot-table">
                <thead>
                    <tr>
                        <th style="width: 60px;">
                        	<strong><span>#</span></strong>
                        </th>
                        <th>
                        	<strong><span>Slot Name</span></strong>
                        </th>
                        <th>
                        	<strong><span>Start Time</span></strong>
                        </th>
                        <th>
                        	<strong><span>End Time</span></strong>
                        </th>
                        <th>
                        	<strong><span>Max. # of Appointments</span></strong>
                        </th>
                        <th style="width: 50px;">
                        	<strong><span>Actions</span></strong>
                        </th>
                    </tr>
                </thead>
                <tbody>
               		 <?php if($countTimeSlots == 0)
							{?>
						<tr class="slot-row" data-slot-id="">
							<td><strong></strong></td>
                            <td><input type="text" name="slotName_" id="slotName" class="inputTimeSlots alphaNumeric" data-msg="Please select Slot Name" maxlength="20" autocomplete="off"/></td>
							<td><input type="text" class="timedropper validateStartTimeSlot inputTimeSlots" name="start_time_slot_"  data-msg="Please select Start Time" autocomplete="off"><div id="error_start_time_slot_" /></td>
							<td><input type="text" class="timedropper validateEndTimeSlot inputTimeSlots endTimeSlot" name="end_time_slot_"  data-msg="Please select End Time" autocomplete="off"></td>
							<td><input type="text" class="inputTimeSlots numeric" name="slot_max_appointments_" maxlength="2" data-msg="Please enter Maximum no of Appointments" autocomplete="off"></td>
							<td>&nbsp;
								
							</td>
						</tr>
					 <?php }else if($countTimeSlots > 0)
						   {
							//for($i=1;$i<=$countTimeSlots;$i++)
							foreach($fetchResults as $result)
								{
									$i++;
						?>
						 <tr class="slot-row" data-slot-id="">
							<td><strong></strong></td>
                            <td><input type="text" name="slotName_" class="inputTimeSlots alphaNumeric" value="<?php if(!empty($result["slotName"])){echo apbud_stripTextContent($result["slotName"]);}?>" id="slotName" data-msg="Please select Slot Name" maxlength="20" autocomplete="off"/></td>
							<td><input type="text" class="timedropper validateStartTimeSlot inputTimeSlots" name="start_time_slot_" value="<?php if(!empty($result["slotStartTime"])){echo $result["slotStartTime"];}?>"  data-msg="Please select Start Time" autocomplete="off"></td>
							<td><input type="text" class="timedropper validateEndTimeSlot inputTimeSlots endTimeSlot" name="end_time_slot_" value="<?php if(!empty($result["slotEndTime"])){echo $result["slotEndTime"];}?>" data-msg="Please select End Time" autocomplete="off"></td>
							<td><input type="text" class="inputTimeSlots numeric" name="slot_max_appointments_" maxlength="2" value="<?php if(!empty($result["maxAppointmentsPerSlot"])){echo $result["maxAppointmentsPerSlot"];}?>" data-msg="Please enter Maximum no of Appointments" autocomplete="off"></td>
							<td><input type="hidden" value="<?php if(!empty($result["slotId"])){echo $result["slotId"];} ?>" name="slotId_" /><?php if($i==1){}else{ ?><a class="button js-remove-slot-btn delete" href="" data-id="<?php if(!empty($result["slotId"])){echo $result["slotId"];} ?>"><span class="dashicons dashicons-trash"></span></a><?php } ?></td>
						 </tr>
					  <?php 	} 
						   }?>
						
                </tbody>
            </table>
            <p><a class="button js-add-slot-btn" href="#" id="addMoreSlot"><span class="dashicons dashicons-plus"></span> Add More Slots</a></p>
        </div>
        <!-- /#time-slots -->
        <?php //submit_button('Save Time Slots'); ?>
        <br />
        <input type="submit" name="submitTimeSlots" id="submitTimeSlots" class="button button-primary" value="Save Time Slots"/>
    </form>  
</div>
<?php
add_action('admin_footer', 'apbud_add_time_slot_script');
function apbud_add_time_slot_script() { ?>
<script>
var slotLength;
jQuery(document).ready(function(){
	
	jQuery(document).on('change','.timedropper',function(e)
	{
			var startTimeVal = jQuery(this).parents('.slot-row').find('input[name*="start_time_slot"]').val();
			var endTimeVal = jQuery(this).parents('.slot-row').find('input[name*="end_time_slot"]').val();
			if(startTimeVal != "" && endTimeVal != "")
			{
				var startArray = startTimeVal.split(':'); 
				var endArray = endTimeVal.split(':'); 
				var startSeconds = startArray[0] * 60 * 60 + startArray[1] * 60;
				var endSeconds = endArray[0] * 60 * 60 + endArray[1] * 60;
				if(startSeconds>endSeconds)
				{
					swal({title:"Sorry!Start Time can not be greater than End Time", type:"error"});
					jQuery(this).val("");
					jQuery(this).focus();
				}
				
			}
			e.preventDefault();
	});
	
	jQuery(document).on('click', ".delete", (function(e)
	{					
		e.preventDefault();
		
		if(jQuery(this).parents('.slot-row').find(".inputTimeSlots").val() == '')
		{
			removeSlot(e);
			updateSlotId();	
		}
		else
		{
			var rowId = jQuery(this).data("id");
			
			var $this = jQuery(this); 
	
			swal({
			  title: "Are you sure want to delete?",
			  text: " ",
			  type: "warning",
			  showCancelButton: true,
			  confirmButtonColor: "#DD6B55",
			  confirmButtonText: "Yes",
			  closeOnConfirm: false
			},
			function()
			{
				jQuery.ajax({
					type: "post",
					url:'<?php echo apbud_AJAX_URL; ?>',
					data: { 
						action: 'apbud_delete_timeSlot',
						dataString: "rowId="+rowId ,
						ajax_nonce: '<?php echo wp_create_nonce('admin_delete_timeSlot'); ?>'
					},
					dataType: 'html',
					success: function(response){ //so, if data is retrieved, store it in html
						var result=JSON.parse(response);
						
						if(result.success === true)
						{
							removeSlot(e);
							updateSlotId();
							swal({title:result.data, type:"success"});
							
							setTimeout(function() {
								location.reload();
							}, 2000);
						}
						else if(result.success === false)
						{
							swal({title:result.data, type:"error"});
						}
					}
				}); //close jQuery.ajax
			}); //swal close
		}
	}));
	  
//To add value to variable
jQuery("#addMoreSlot").on('click', function(){
	bindValidation(); 
}); //click function close
	
//To submit form
jQuery("#timeSlotForm").validate({
	
	submitHandler: function()
	{
		jQuery.ajax({
			type: "post",
			url:'<?php echo apbud_AJAX_URL; ?>',
			data: { 
				action: 'apbud_add_timeSlots',
				dataString: jQuery('#timeSlotForm').serialize()+"&countRows="+jQuery(".slot-row").length ,
				ajax_nonce: '<?php echo wp_create_nonce('admin_timeSlot'); ?>'
			},
			dataType: 'html',
			success: function(response)
			{ //so, if data is retrieved, store it in html
				
				if(jQuery.parseJSON(response).success === false)
				{
					abAlertMessage(response);
					jQuery('html, body').animate({scrollTop : 0}, 800);
				}
				else
				{
					abAlertMessage(response);
					jQuery('html, body').animate({scrollTop : 0}, 800);
					setTimeout( function() {
						location.reload(true);
					}, 3000);
				}
			}
		}); //close jQuery.ajax
	} //submitHandler ends
}); //validate function close
	
function bindValidation(){
	jQuery('.inputTimeSlots').each(function(){
		jQuery(this).rules("add", {
			required:true,
			messages: {
				required: jQuery(this).attr('data-msg')
			}
		});
	});
}
bindValidation();
		
});

</script>
<?php } ?> 
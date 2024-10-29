<?php
	// Exit if accessed directly
	if ( ! defined( 'ABSPATH' ) ) exit;
?>
<?php 
global $wpdb;
$abTimeSlotMst = $wpdb->prefix . "abTimeSlotMst";
$abSlotMappingDetails = $wpdb->prefix . "abSlotMappingDetails";
$getWorkingDayResult = $wpdb->get_results("SELECT slotId, slotName, slotStartTime, slotEndTime, maxAppointmentsPerSlot FROM ".$abTimeSlotMst." WHERE isDeleted=0", ARRAY_A);

if(!$getWorkingDayResult) 
{ //if-1
?>
<div class="card" style="max-width: 100%; padding-top: 20px; padding-bottom: 30px;" id="working-day-div">
    <div id="working-days">
        <table class="wp-list-table widefat fixed striped working-days-table">
            <tbody>
                <tr>
                    <td colspan="3" class="display-bold-msg">YOU NEED TO SELECT WORKING HOUR'S SLOTS / TIME SLOTS FIRST.</td>
                </tr>
            </tbody>
        </table>
    </div>
    <!-- /#working-days -->
</div>
<?php 
} //if-1 ends
else
{ //else-1

$fetchResults = $wpdb->get_results("Select slotId,slotName,DATE_FORMAT(slotStartTime, '%H:%i') as slotStartTime,DATE_FORMAT(slotEndTime, '%H:%i') as slotEndTime,maxAppointmentsPerSlot from ".$abTimeSlotMst ." where isDeleted=0",ARRAY_A);

$slotList=$wpdb->get_results("Select slotId from ".$abTimeSlotMst." where isDeleted=0",ARRAY_A);

?>

<div class="card" style="max-width: 100%; padding-top: 20px; padding-bottom: 30px;" id="working-day-div">
    <form action="#" method="post" name="workingDays" id="workingDays">
    	<div id="working-days">
            <table class="wp-list-table widefat fixed striped working-days-table">
                <thead>
                    <tr>
                        <th style="width: 100px;">
                        	<strong><span>Slot Name</span></strong>
                        </th>
                        <th style="width: 150px;">
                        	<strong><span>Time Slot</span></strong>
                        </th>
                        <th>
                        	<strong><span>Working Days</span></strong>
                        </th>
                    </tr>
                </thead>
                <tbody>
                	<?php
					foreach($fetchResults as $result)
					{ //foreach-1
						$workingDaysList = $wpdb->get_results("Select workingDay,slotId,isDeleted from ".$abSlotMappingDetails." where isDeleted=0 And slotId=".$result['slotId'],ARRAY_A);
                    ?>
                    <tr>
                        <td><?php if(!empty($result["slotName"])){echo apbud_stripTextContent($result["slotName"]);}?>
                        <input type="hidden" id="slotId" name="slotId" value="<?php if(!empty($result["slotId"])){echo $result["slotId"];}?>" data-id="<?php // $final = array_column($fetchWorkingDays,"slotId"); if(in_array($result["slotId"],$final)){echo "true";} ?>"  />
                        </td>
                        <td><?php if(!empty($result["slotStartTime"])){echo $result["slotStartTime"];}echo "-";if(!empty($result["slotEndTime"])){echo $result["slotEndTime"];}?> </td>
                        <td>
                            <div class="checkbox-group">
                               	<label><input type="checkbox" name="chk[<?php echo $result['slotId']; ?>][1]" value="1" id="<?php echo $result['slotId']."-1"; ?>" class="workingDay" 
								<?php  
								foreach($workingDaysList as $workDay)  
								{ 
									if($workDay['workingDay']=='1' && $workDay['isDeleted']=='0')
									{  
									?>
									checked="checked" 
									<?php
									}										   
								 }
								 ?> >Monday</label>
                                <label><input type="checkbox" name="chk[<?php echo $result['slotId']; ?>][2]" value="2" id="<?php echo $result['slotId']."-2"; ?>" class="workingDay"
                                <?php  
								foreach($workingDaysList as $workDay)  
								{ 
									 if($workDay['workingDay']=='2' && $workDay['isDeleted']=='0')
									 {  
									 ?>
										checked="checked" 
									 <?php
									 }										   
								}
								?>> Tuesday</label>
                                <label><input type="checkbox" name="chk[<?php echo $result['slotId']; ?>][3]" value="3" id="<?php echo $result['slotId']."-3"; ?>" class="workingDay"
                                <?php  
								foreach($workingDaysList as $workDay)  
								{ 
									 if($workDay['workingDay']=='3' && $workDay['isDeleted']=='0')
									 {  
									 ?>
										checked="checked" 
									 <?php
									 }										   
								}
								?>> Wednesday</label>
                                <label><input type="checkbox" name="chk[<?php echo $result['slotId']; ?>][4]" value="4" id="<?php echo $result['slotId']."-4"; ?>" class="workingDay" 
                                <?php  
								foreach($workingDaysList as $workDay)  
								{ 
									 if($workDay['workingDay']=='4' && $workDay['isDeleted']=='0')
									 {  
									 ?>
										checked="checked"
									 <?php
									 }										   
								}
								?>> Thursday</label>
                                <label><input type="checkbox" name="chk[<?php echo $result['slotId']; ?>][5]" value="5" id="<?php echo $result['slotId']."-5"; ?>" class="workingDay"
                                <?php  
								foreach($workingDaysList as $workDay)  
								{ 
									 if($workDay['workingDay']=='5' && $workDay['isDeleted']=='0')
									 {  
									 ?>
										checked="checked" 
									 <?php
									 }										   
								}
								?>> Friday</label>
                                <label><input type="checkbox" name="chk[<?php echo $result['slotId']; ?>][6]" value="6" id="<?php echo $result['slotId']."-6"; ?>" class="workingDay"
                                <?php  
								foreach($workingDaysList as $workDay)  
								{ 
									 if($workDay['workingDay']=='6' && $workDay['isDeleted']=='0')
									 {  
									 ?>
										checked="checked" 
									 <?php
									 }										   
								}
								?>> Saturday</label>
                                <label><input type="checkbox" name="chk[<?php echo $result['slotId']; ?>][0]" value="0" id="<?php echo $result['slotId']."-0"; ?>" class="workingDay"
                                <?php  
								foreach($workingDaysList as $workDay)  
								{ 
									 if($workDay['workingDay']=='0' && $workDay['isDeleted']=='0')
									 {  
									 ?>
										checked="checked"
									 <?php
									 }										   
								}
								?>> Sunday</label>   
                            </div>
                        </td>
                    </tr>
					<?php
					} //foreach-1 ends
					?>
                </tbody>
            </table>
        </div>
        <!-- /#working-days -->
         <br />
        <input type="submit" name="submitWorkingDays" id="submitWorkingDays" class="button button-primary" value="Save Working Days"/>
    </form>  
</div>

<?php
add_action('admin_footer', 'apbud_add_workingDays_script');
function apbud_add_workingDays_script() { 
?>
<script>

jQuery(document).ready(function()
{
	jQuery(document).on('click', '#submitWorkingDays', function(e)
	{		
		e.preventDefault();
		var chkArray  = [];

		/* look for all checkboes that have a class 'chk' attached to it and check if it was checked  */
		jQuery('input:checkbox[class=workingDay]:checked').each(function() {
			chkArray.push(jQuery(this).attr('name'));
		});
		
		var count = chkArray.length;
		var dataObject ;
		
		for (var i=1; i <= count; i++) {
			
			if(i == 1)
			{
				first=chkArray.pop();
				dataObject=first+"="+first;
			}
			else
			{
				next=chkArray.pop();
				dataObject+="&"+next+"="+next;
			}
		
		}
		
		jQuery.ajax({
			type: "post",
			cache:false,
			url:'<?php echo apbud_AJAX_URL; ?>',
			data: { 
				action: 'apbud_admin_add_workingDays1',
				dataString : dataObject,
				ajax_nonce: '<?php echo wp_create_nonce('admin_workingDays'); ?>'
			},
			dataType: 'html',
			success: function(response){ //so, if data is retrieved, store it in html
				abAlertMessage(response);
				jQuery('html, body').animate({scrollTop : 0}, 800);
			}
		}); //close jQuery.ajax
		
	}); //validate function close
});
</script>
<?php } ?> 
<?php } //else-1 ends ?>
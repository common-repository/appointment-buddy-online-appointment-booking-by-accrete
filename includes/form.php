<?php
//Forms to be displayed in Widget
global $wpdb;
$errorMessages = '';
$abAdminProfileDetails = $wpdb->prefix . "abAdminProfileDetails";
$abHolidayMst = $wpdb->prefix . "abHolidayMst";
$getMinMaxDate = $wpdb->get_results("SELECT priorMonthsToBook, priorDaysToBook FROM ".$abAdminProfileDetails." WHERE isDeleted=0",ARRAY_A);

	if(!empty($getMinMaxDate[0]['priorDaysToBook']) && !empty($getMinMaxDate[0]['priorMonthsToBook']))
	{//if-1 starts
		$priorDays = $getMinMaxDate[0]['priorDaysToBook'] + 1;
		$priorMonths = ($getMinMaxDate[0]['priorMonthsToBook'] * 30) - 1;
		$minDateTimeStamp = strtotime("+".$priorDays." days");
		$minDate = date("d-m-Y", $minDateTimeStamp);
		$maxDateTimeStamp = strtotime("+".$priorMonths." days", $minDateTimeStamp);
		$maxDate = date("d-m-Y", $maxDateTimeStamp);
	}
	else
	{
		$errorMessages = "You cannot book appointment. Please try again";
	}//if-1 ends
if(!empty($minDate) && !empty($maxDate))
{//if-2 starts
	$holidayResult = $wpdb->get_results("SELECT DATE_FORMAT(holidayDate,'%d-%m-%Y') as holidayDate FROM ".$abHolidayMst." WHERE holidayDate BETWEEN '".apbud_changeDateFormatWithoutTime($minDate)."' AND '".apbud_changeDateFormatWithoutTime($maxDate)."' AND isDeleted=0",ARRAY_A);
	$holidayArray=array();
		if($holidayResult)
		{//if-3 starts
			 foreach($holidayResult as $holiday)
			 {//foreach-1 starts
				 if(!empty($holiday['holidayDate']))
				 {//if-4 starts
					array_push($holidayArray, "'".$holiday['holidayDate']."'");
				 }//if-4 ends
			 }//foreach-1 ends
		}//if-3 ends
	$holidayString = implode(',',$holidayArray);
}//if-2 ends

$holidayDisplayData = $wpdb->get_results("SELECT DAY(holidayDate) as DayOfHoliday,MONTH(holidayDate) as MonthOfHoliday ,holidayName from ".$abHolidayMst." where isDeleted=0",ARRAY_A);
$holidayCount = $wpdb->num_rows;
$i=0;
if($holidayDisplayData)
{//if-5 starts
	
	$DayOfHoliday = array();
	 foreach($holidayDisplayData as $holidayData)
	 {//foreach-2 starts
		  if(!empty($holidayData['holidayName']))
			{//if-6 starts
				$i=++$i;
				$DayOfHoliday[$i]=trim($holidayData['DayOfHoliday']);
				$MonthOfHoliday[$i]=trim($holidayData['MonthOfHoliday']-1);
				$holidayName[$i]="'".trim($holidayData['holidayName'])."'";
			}//if-6 ends
	 }//foreach-2 ends
}//if-5 ends

?>
<div class="booking-widget">

    <form action="#" method="post" class="booking-form" name="bookingForm" id="bookingForm">
        <div class="booking-form-field field-icon">
        	<input class="calendar-holder" placeholder="Appointment Date" name="appointmentDate" id="appointmentDate" autocomplete="off">
            <span class="dashicons dashicons-calendar-alt"></span>
        </div>
        <div class="booking-form-field">
        <div id="timeSlot">
            <select name="appointmentSlotMappingId" id="appointmentSlotMappingId">
                <option value="">--Select Time Slot--</option>
            </select>
        </div>
        </div>
        <div class="booking-form-field">
        	<select name="serviceId" id="serviceId">
                <option value="">--Select Service--</option>
                <?php 
                $serviceMst = $wpdb->prefix . 'abServiceMst';
                $getTreatments = $wpdb->get_results("SELECT serviceId, serviceName FROM ".$serviceMst." WHERE isDeleted=0", ARRAY_A);
                if($getTreatments)
                { //if-7
                    foreach($getTreatments as $getTreat) 
                    { //foreach-3					
                        if(!empty($getTreat['serviceId']) && !empty($getTreat['serviceName']))
                        { //if-8
                    ?>
                    <option value="<?php esc_attr_e($getTreat['serviceId']); ?>"><?php if(strlen(substr($getTreat['serviceName'],0,30)) < 30){esc_attr_e(substr($getTreat['serviceName'],0,30));}else{esc_attr_e(substr($getTreat['serviceName'],0,30));echo "...";} ?></option>
                    <?php 
                        } //if-8 ends
                    } //foreach-3 ends
                } //if-7 ends
                ?>
            </select>
        </div>
        <div class="booking-form-field">
            <input type="text" placeholder="Full Name" maxlength="30" name="personName" id="personName">
        </div>
        <div class="booking-form-field">
            <input type="email" placeholder="Email" name="personEmailId" id="personEmailId"  maxlength="30">
        </div>
        <div class="booking-form-field">
            <input type="text" placeholder="Phone No." maxlength="15" name="personMobileNo" id="personMobileNo">
        </div>
        <div class="booking-form-field">
            <textarea rows="2" placeholder="Address" name="personAddress" id="personAddress"></textarea>
        </div>
        <div class="booking-form-field">
            <textarea rows="2" placeholder="Remarks" name="remarks" id="remarks"></textarea>
        </div>
        <div class="booking-form-field">
            <button type="submit">Book Appointment</button>
        </div>
    </form>

</div>
<script>
jQuery(document).ready(function()
{
	/*Get time slot from date change */
	var holidayArray = [<?php echo $holidayString; ?>];
	var calendarMinDate = '<?php echo $minDate; ?>';
	var calendarMaxDate = '<?php echo $maxDate; ?>';
	var holidayCount = <?php echo $holidayCount; ?>;
	
	if(jQuery.inArray(calendarMinDate, holidayArray) != -1)
	{		
		var newStartDate = new Date(calendarMinDate.split("-").reverse().join("-")); 
		
		var dd=newStartDate.getDate()+1;
		var mm=newStartDate.getMonth()+1;
		var yy=newStartDate.getFullYear();
				
		var calendarStartDate = dd+"-"+mm+"-"+yy;
	}
	else
	{
		var calendarStartDate = calendarMinDate;	
	}
	
	jQuery('#appointmentDate').on('keydown', function(e){
		e.preventDefault();
	});
	
	jQuery('#appointmentDate').on('change', function() {
		var appointDate = jQuery("#appointmentDate").val();
		var appointDate2 = appointDate.split('-');
		var newDate=appointDate2[1]+","+appointDate2[0]+","+appointDate2[2];
		var getSelectedDay = new Date(newDate).getDay();
		
		var dataString = {
			appointmentDay: getSelectedDay ,
			appointmentDate: appointDate ,
			action: 'apbud_getTimeSlot',
			ajax_nonce: '<?php echo wp_create_nonce('apbud_getTimeSlot'); ?>'
		}
		
		jQuery('.calendar-holder').parents('.form-field').next('.form-field').prepend('<div class="inline-loader"><img src="<?php echo AB_IMAGES . '/ajax-loader2.gif'; ?>" /></div>');
		
		jQuery.ajax({
			type: "post",
			url:'<?php echo apbud_AJAX_URL; ?>',
			data: dataString,
			dataType: 'html',
			success: function(response)
			{
				var ress = response.split("##&&##");
				jQuery("#timeSlot").html(ress[0]);
				jQuery('.inline-loader').fadeOut(300).remove();
			}
		});
	});
	
	jQuery('.calendar-holder').datetimepicker({
		timepicker: false,
		format: 'd-m-Y',
		formatDate: 'd-m-Y',
		disabledDates: holidayArray,   
		minDate:calendarMinDate,
		maxDate:calendarMaxDate,
		startDate:calendarStartDate,
		onSelectDate: function(dp,input){
			var dataString = {
				appointmentDay: dp.getDay() ,
				appointmentDate: jQuery("#appointmentDate").val() ,
				action: 'apbud_getTimeSlot',
				ajax_nonce: '<?php echo wp_create_nonce('apbud_getTimeSlot'); ?>'
			}
			
			jQuery.ajax({
				type: "post",
				url:'<?php echo apbud_AJAX_URL; ?>',
				data: dataString,
				dataType: 'html',
				success: function(response)
				{
					var ress = response.split("##&&##");
					jQuery("#timeSlot").html(ress[0]);
				}
			});
			
		},
		onGenerate:function(){
			if(holidayCount!=0 && holidayCount!='')
			{
			  <?php for($i=1;$i<=$holidayCount;$i++) {	?>
			  jQuery('td[data-date="<?php echo $DayOfHoliday[$i];?>"][data-month="<?php echo $MonthOfHoliday[$i];?>"]').addClass('xdsoft_disabled xdsoft_holiday').attr('title',<?php echo $holidayName[$i];?>);
			  <?php } ?>
			}
		},
		closeOnDateSelect:true,
		onShow: function(){
			jQuery('#nav-panel').css('overflow-y', 'hidden');
		}, 
		onClose: function(){
			jQuery('#nav-panel').css('overflow-y', 'auto');
		}
	});

	jQuery("#bookingForm").validate({
			rules:{
				personName:"required",
				personMobileNo:{
					required:true,
					minlength:10,
					maxlength:15
				},
				personEmailId:{
					required:true,
					email:true,
					maxlength:50
				},
				appointmentDate:"required",
				appointmentSlotMappingId:"required",
				serviceId:"required"				
			},	
			messages:{
				personName:"",
				personMobileNo:{
					required:"",
					minlength:"You must enter atleast 10 digit mobile number",
					maxlength:"You can enter atmost 15 digit mobile number"
				},
				personEmailId:{
					required:"",
					email:"Please enter valid email address"
				},
				appointmentDate:"",
				appointmentSlotMappingId:"",
				serviceId:""
			},	
			submitHandler:function(){
				
				jQuery.ajax({
					type: "post",
					url:'<?php echo apbud_AJAX_URL; ?>',
					data: { 
						action: 'apbud_add_appointments',
						dataString: jQuery('#bookingForm').serialize() ,
						ajax_nonce: '<?php echo wp_create_nonce('apbud_add_appointments'); ?>'
					},
					dataType: 'html',
					success: function(response)
					{ //so, if data is retrieved, store it in html
						if(jQuery.parseJSON(response).success === false)
						{
							abAlertMessage(response);
						}
						else
						{
							abAlertMessage(response);
							setTimeout(function() {
								jQuery("#bookingForm").trigger('reset');
							}, 2000);
						}
					}
				}); //close jQuery.ajax
			}
	});
			
	
}); // document.ready	
</script>




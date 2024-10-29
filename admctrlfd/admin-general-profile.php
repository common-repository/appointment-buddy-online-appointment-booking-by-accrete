<?php
	// Exit if accessed directly
	if ( ! defined( 'ABSPATH' ) ) exit;
?>
<?php 
global $wpdb;
$abAdminProfileDetails = $wpdb->prefix . "abAdminProfileDetails";
$getGeneralProfileResult = $wpdb->get_results("SELECT priorMonthsToBook, timeZoneValue, adminProfileId, name, address, emailId, mobileNo, officePhoneNo, websiteLink, facebookLink, twitterLink, priorDaysToBook FROM ".$abAdminProfileDetails." WHERE isDeleted=0", ARRAY_A);

if(get_option('timezone_string') == '')
{
	$abTimeZone = get_option('gmt_offset');
}
else
{
	$abTimeZone = get_option('timezone_string');
}
?>
<div class="card" style="max-width: 100%; padding-top: 20px; padding-bottom: 30px;" id="general-profile-div">
    <form action="#" method="post" name="generalProfileForm" id="generalProfileForm">
        <table class="form-table">
            <tbody>
            	<?php if($getGeneralProfileResult[0]['adminProfileId']) { ?>
				<tr>
                    <td><input name="adminProfileId" id="adminProfileId" type="hidden" value="<?php echo $getGeneralProfileResult[0]['adminProfileId']; ?>" readonly="readonly"></td>
                </tr>
                <?php } ?>
                <tr>
                    <th scope="row"><label for="name">Name</label><span class="star-red"> * </span></th>
                    <td><input name="name" id="name" class="regular-text alphaNumeric" type="text" maxlength="50" placeholder="Full Name" value="<?php if($getGeneralProfileResult[0]['name'] != '') { echo apbud_stripTextContent($getGeneralProfileResult[0]['name']); } else { echo ''; }?>"></td>
                </tr>
                <tr>
                    <th scope="row"><label for="address">Address</label><span class="star-red"> * </span></th>
                    <td>
                        <textarea name="address" id="address" class="regular-text" rows="5" cols="50" placeholder="Full Address...."><?php if($getGeneralProfileResult[0]['address'] != '') { echo apbud_stripTextContent($getGeneralProfileResult[0]['address']); } else { echo ''; }?></textarea>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="emailId">Email</label><span class="star-red"> * </span></th>
                    <td><input name="emailId" id="emailId" class="regular-text" type="email" placeholder="Email ID" value="<?php if($getGeneralProfileResult[0]['emailId'] != '') { echo $getGeneralProfileResult[0]['emailId']; } else { echo ''; }?>"></td>
                </tr>
                <tr>
                    <th scope="row"><label for="mobileNo">Mobile No.</label></th>
                    <td>
                        <input name="mobileNo" id="mobileNo" class="regular-text numeric" type="text" maxlength="15" placeholder="Mobile Number" value="<?php if($getGeneralProfileResult[0]['mobileNo'] != '') { echo $getGeneralProfileResult[0]['mobileNo']; } else { echo ''; }?>">
                    </td>
                </tr>
               <tr>
                    <th scope="row"><label for="officePhoneNo">Contact No. (Office)</label><span class="star-red"> * </span></th>
                    <td>
                        <input name="officePhoneNo" id="officePhoneNo" class="regular-text numeric" type="text" maxlength="15" placeholder="Office Contact Number" value="<?php if($getGeneralProfileResult[0]['officePhoneNo'] != '') { echo $getGeneralProfileResult[0]['officePhoneNo']; } else { echo ''; }?>">
                        <p class="description">Office contact number.</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="websiteLink">Website</label></th>
                    <td>
                        <input name="websiteLink" id="websiteLink" class="regular-text" type="url" placeholder="Website Link" value="<?php if($getGeneralProfileResult[0]['websiteLink'] != '') { echo $getGeneralProfileResult[0]['websiteLink']; } else { echo ''; }?>">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="priorDaysToBook">Prior Days to Book Appointment</label><span class="star-red"> * </span></th>
                    <td>
                        <input name="priorDaysToBook" id="priorDaysToBook" class="regular-text onlyNumber" type="text" maxlength="2" min="1" placeholder="Priority Time to Book Appointment" value="<?php if($getGeneralProfileResult[0]['priorDaysToBook'] != '') { echo $getGeneralProfileResult[0]['priorDaysToBook']; } else { echo ''; }?>">
                    	<p class="description">No. of days allowed from current day, after which appointments can be scheduled. It cannot be 0.</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="priorMonthsToBook">Prior Months to Book Appointment</label><span class="star-red"> * </span></th>
                    <td>
                        <input name="priorMonthsToBook" id="priorMonthsToBook" class="regular-text numeric" type="text" maxlength="2" min="1" placeholder="Priority Months to Book Appointment" value="<?php if($getGeneralProfileResult[0]['priorMonthsToBook'] != '') { echo $getGeneralProfileResult[0]['priorMonthsToBook']; } else { echo ''; }?>">
                    	<p class="description">No. of months allowed to schedule appointments. It cannot be 0.</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="timeZoneValue">Time Zone</label><span class="star-red"> * </span></th>
                    <td>
                        <select name="timeZoneValue" id="timeZoneValue" class="regular-text">
						<?php foreach(apbud_timeZone_list() as $timeZone) { ?>
                            <option value="<?php echo $timeZone['zone']; ?>" <?php if( $timeZone['zone'] == $abTimeZone ) { ?> selected="selected" <?php } ?>>
                            	<?php echo $timeZone['zone']; ?>
                            </option>
                        <?php } ?>
                        </select>
                        <p class="description">Set your time zone same as your wordpress.</p>
                    </td>
                </tr>
            </tbody>
        </table>
        <h2 class="title">Social Media Profiles (links)</h2>
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row"><label for="facebookLink">Facebook</label></th>
                    <td>
                        <input name="facebookLink" id="facebookLink" class="regular-text" type="url" placeholder="Facebook Link" value="<?php if($getGeneralProfileResult[0]['facebookLink'] != '') { echo $getGeneralProfileResult[0]['facebookLink']; } else { echo ''; }?>">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="twitterLink">Twitter</label></th>
                    <td>
                        <input name="twitterLink" id="twitterLink" class="regular-text" type="url" placeholder="Twitter Link" value="<?php if($getGeneralProfileResult[0]['twitterLink'] != '') { echo $getGeneralProfileResult[0]['twitterLink']; } else { echo ''; }?>">
                    </td>
                </tr>
            </tbody>
        </table>
        <?php submit_button('Save General Profile'); ?>
    </form>  
</div>
<?php
add_action('admin_footer', 'apbud_add_generalProfile_script');

function apbud_add_generalProfile_script() { ?>
<script>
jQuery(document).ready(function() {
			
 	jQuery("#generalProfileForm").validate({
		rules:
		{
			name:"required",
			address:"required",
			emailId: {
				required: true,
				email:true
			},
			officePhoneNo: {
				required: true,
				maxlength:15,
				minlength:10
			},
			priorDaysToBook: {
				required: true,
				maxlength:2,
				minlength:1,
				min:1
			},
			priorMonthsToBook: {
				required: true,
				maxlength:2,
				minlength:1,
				min:1
			},
			timeZoneValue:"required"
		}, 
		messages:
		{
			name:"Please enter your full name",
			address:"Please enter your full address",
			emailId: {
				required: "Please enter your email Id",
				email:"Please enter you valid email Id"
			},
			timeZoneValue:"Please select your time zone",
			officePhoneNo: {
				required: "Please enter your office contact number",
				maxlength:"You can enter maximum 15 digits office contact number",
				minlength:"You have to enter minimum 10 digits office contact number",
			},
			priorDaysToBook: {
				required: "Please enter number of priority days to book your appointment",
				maxlength:"You can enter 2 digits number for prority days",
				minlength:"You have to enter 1 digit number for prority days",
				min:"You must enter value greater than 0"
			},
			priorMonthsToBook: {
				required: "Please enter number of priority months to book your appointment",
				maxlength:"You can enter 2 digits number for prority months",
				minlength:"You have to enter 1 digit number for prority months",
				min:"You must enter value greater than 0"
			}
		},
		submitHandler: function()
		{
			if(jQuery("#adminProfileId").val() && jQuery("#adminProfileId").val().length > 0)
			{
				var dataString = {
					stringValues : jQuery('#generalProfileForm').serialize() ,
					action : 'apbud_add_general_profile',
					ajax_nonce : '<?php echo wp_create_nonce('apbud_add_general_profile'); ?>',
					crudAction:'update'
				}
			}
			else
			{
				var dataString = {
					stringValues : jQuery('#generalProfileForm').serialize() ,
					action : 'apbud_add_general_profile',
					ajax_nonce : '<?php echo wp_create_nonce('apbud_add_general_profile'); ?>',
					crudAction:'add'
				}
			}
			
			jQuery.ajax({
				type:"POST",
				dataType:"html",
				url:'<?php echo apbud_AJAX_URL; ?>',
				data:dataString,
				success: function(response)
				{
					abAlertMessage(response);
					setTimeout( function() {
						location.reload(true);
					}, 3000);
					jQuery('html, body').animate({scrollTop : 0}, 800);
				}
			}); //ajax function ends
		} //submitHandler ends
	});   
});
</script>
<?php } ?>
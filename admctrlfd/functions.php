<?php
	// Exit if accessed directly
	if ( ! defined( 'ABSPATH' ) ) exit;
?>
<?php
//VALIDATION FUNCTION
function apbud_validateTextarea($data)
{ 
	global $wpdb;
	$data1=implode( "\n", array_map( 'esc_attr', array_map( 'sanitize_text_field', explode( "\n", $data ) ) ) );
	return $data1;
}

function apbud_stripContent($data)
{
	$data=stripslashes(html_entity_decode(stripslashes($data)));
	return $data;
} 

function apbud_stripTextContent($data)
{
	$data=stripslashes(wp_specialchars_decode(stripslashes($data)));
	return $data;
} 

function apbud_validateTextContent($data)
{
	$data=sanitize_text_field($data); 
	return $data;
} 

//Date Validation Functions
function apbud_changeTimeFormat($timefmt) 
{
	$time = date("g:i a", strtotime($timefmt));
	return $time;
}

function apbud_changeDateFormat($dateValue)
{
	$date = date_create($dateValue);
	$value = date_format($date,"Y-m-d H:i:s");
	return $value;
}

function apbud_changeDateToReadableFormat($dateValue)
{
	$date = date_create($dateValue);
	$value = date_format($date,"d-m-Y H:i:s");
	return $value;
}

function apbud_changeDateFormatWithoutTime($dateValue)
{	
	$date = date_create($dateValue);
	$value = date_format($date,"Y-m-d");
	return $value;
}

function apbud_changeDateToReadableFormatWithoutTime($dateValue)
{
	$date = date_create($dateValue);
	$value = date_format($date,"d-m-Y");
	return $value;
}

//Get IP Address Function
function apbud_getIp()
{
	$ip = "";

	if (getenv("HTTP_CLIENT_IP")) 
	{
		$ip = getenv("HTTP_CLIENT_IP");
	}
	else if(getenv("HTTP_X_FORWARDED_FOR")) 
	{
		$ip = getenv("HTTP_X_FORWARDED_FOR");
	}
	else if(getenv("REMOTE_ADDR")) 
	{
		$ip = getenv("REMOTE_ADDR");
	}
	else 
	{
		$ip = "UNKNOWN";
	}
	
	return $ip;		
}

//function to get time zone
function apbud_timeZone_list()
{
	$zones_array = array();
	$timestamp = time();
	foreach(timezone_identifiers_list() as $key => $zone) 
	{
		$zones_array[$key]['zone'] = $zone;
		$zones_array[$key]['diff_from_GMT'] = 'UTC/GMT ' . date('P', $timestamp);
	}
	return $zones_array;	
}

//General Profile
add_action('wp_ajax_apbud_add_general_profile', 'apbud_add_general_profile');
add_action('wp_ajax_nopriv_apbud_add_general_profile', 'apbud_add_general_profile');

function apbud_add_general_profile() 
{
	global $wpdb;
	
	
	$checkNonce = check_ajax_referer( 'apbud_add_general_profile', 'ajax_nonce' );
	
	if($checkNonce)
	{ //if-1
		extract($_POST,EXTR_SKIP);
		parse_str($_POST['stringValues'], $stringValueArray);		
		
		$wp_abAdminProfileDetails = $wpdb->prefix .'abAdminProfileDetails';
		$user = get_current_user_id();
		$ip = apbud_getIp();		
		
		if($stringValueArray['name'] == '')
		{
			return wp_send_json_error("Please enter your full name.");
		}
		if($stringValueArray['address'] == '')
		{
			return wp_send_json_error("Please enter your full address.");
		}
		if($stringValueArray['emailId'] == '')
		{
			return wp_send_json_error("Please enter your email id.");
		}
		if($stringValueArray['officePhoneNo'] == '')
		{
			return wp_send_json_error("Please enter your office contact number.");
		}
		if($stringValueArray['priorDaysToBook'] == '')
		{
			return wp_send_json_error("Please enter number of priority days to book your appointment.");
		}
		else if($stringValueArray['priorDaysToBook'] <= 0)
		{
			return wp_send_json_error("You must enter value greater than 0 in priority days to book appointment.");
		}
		if($stringValueArray['priorMonthsToBook'] == '')
		{
			return wp_send_json_error("Please enter number of priority months to book your appointment.");
		}
		else if($stringValueArray['priorMonthsToBook'] <= 0)
		{
			return wp_send_json_error("You must enter value greater than 0 in priority months to book appointment.");
		}
		if($stringValueArray['timeZoneValue'] == '')
		{
			return wp_send_json_error("Please select your time zone.");
		}
		
		if($crudAction == 'add')
		{ //if-2
			
			$generalProfileArray = array(
				"name" => "".apbud_validateTextContent($stringValueArray['name'])."",
				"address" => "".apbud_validateTextarea($stringValueArray['address'])."",
				"emailId" => "".sanitize_email($stringValueArray['emailId'])."",
				"mobileNo" => "".apbud_validateTextContent($stringValueArray['mobileNo'])."",
				"officePhoneNo" => "".apbud_validateTextContent($stringValueArray['officePhoneNo'])."",
				"websiteLink" => "".esc_url($stringValueArray['websiteLink'])."",
				"facebookLink" => "".esc_url($stringValueArray['facebookLink'])."",
				"twitterLink" => "".esc_url($stringValueArray['twitterLink'])."",
				"priorDaysToBook" => "".absint($stringValueArray['priorDaysToBook'])."",
				"priorMonthsToBook" => "".absint($stringValueArray['priorMonthsToBook'])."",
				"timeZoneValue" => "".$stringValueArray['timeZoneValue']."",
				"createdDate" => "".AB_CURRENT_TIMEZONE."",
				"createdBy" => "".$user."",
				"ipAddress" => "".$ip."",
			);
		
			$insGeneralProfile = $wpdb->insert($wp_abAdminProfileDetails, $generalProfileArray);		
			if(!$insGeneralProfile)
			{ //if-3
				return wp_send_json_error("Profile details not saved.");
			} //if-3 ends
			else
			{ //else-3	
				update_option('timezone_string', $stringValueArray['timeZoneValue'], 'yes');						
				return wp_send_json_success("Profile details saved successfully.");
			} //else-3 ends
		} //if-2 ends
		
		if($crudAction == 'update')
		{ //if-5
		
			if(sanitize_text_field($stringValueArray['adminProfileId']) == '' || is_numeric($stringValueArray['adminProfileId']) == false)
			{ //if-4
				return wp_send_json_error("Invalid data.");
			} //if-4 ends
			else 
			{ //else-4
				$checkProfileId = $wpdb->get_var("SELECT count(adminProfileId) FROM ".$wp_abAdminProfileDetails." WHERE isDeleted=0 AND adminProfileId=".sanitize_text_field($stringValueArray['adminProfileId']));
				
				if($checkProfileId <= 0)				
				{ //if-6
					return wp_send_json_error("Data not found. Please try again");
				} //if-6 ends
				else
				{ //else-6
					$generalProfileUpdateWhere = array( 'adminProfileId' => "".sanitize_text_field($stringValueArray['adminProfileId'])."" );
					
					$generalProfileUpdateArray = array(
						"name" => "".apbud_validateTextContent($stringValueArray['name'])."",
						"address" => "".apbud_validateTextarea($stringValueArray['address'])."",
						"emailId" => "".sanitize_email($stringValueArray['emailId'])."",
						"mobileNo" => "".apbud_validateTextContent($stringValueArray['mobileNo'])."",
						"officePhoneNo" => "".apbud_validateTextContent($stringValueArray['officePhoneNo'])."",
						"websiteLink" => "".esc_url($stringValueArray['websiteLink'])."",
						"facebookLink" => "".esc_url($stringValueArray['facebookLink'])."",
						"twitterLink" => "".esc_url($stringValueArray['twitterLink'])."",
						"timeZoneValue" => "".$stringValueArray['timeZoneValue']."",
						"priorDaysToBook" => "".absint($stringValueArray['priorDaysToBook'])."",
						"priorMonthsToBook" => "".absint($stringValueArray['priorMonthsToBook'])."",
						"modifiedDate" => "".AB_CURRENT_TIMEZONE."",
						"modifiedBy" => "".$user."",
						"ipAddress" => "".$ip."",
					);
				
					$updProfile = $wpdb->update($wp_abAdminProfileDetails, $generalProfileUpdateArray, $generalProfileUpdateWhere);		
					if(!$updProfile)
					{ //if-3
						return wp_send_json_error("Profile details not updated");
					} //if-3 ends
					else
					{ //else-3
						update_option('timezone_string', $stringValueArray['timeZoneValue'], 'yes');
											
						return wp_send_json_success("Profile details updated successfully");
					} //else-3 ends
				} //else-6 ends
			} //else-4 ends
		} //if-5 ends

	} //if-1 ends
	else
	{ //else-1
		return wp_send_json_error("Wrong Nonce");	
	} //else-1 ends
	die();
}

//Treatments
add_action('wp_ajax_apbud_add_treatments', 'apbud_add_treatments');
add_action('wp_ajax_nopriv_apbud_add_treatments', 'apbud_add_treatments');

function apbud_add_treatments() 
{
	global $wpdb;
	$checkNonce = check_ajax_referer( 'apbud_add_treatments', 'ajax_nonce' );
	if($checkNonce)
	{ //if-1
		extract($_POST,EXTR_SKIP);
		parse_str($_POST['stringValues'], $treatmentValueArray);
		
		$wp_abServiceMst = $wpdb->prefix .'abServiceMst';	
		$user = get_current_user_id();
		$ip = apbud_getIp();		
		
		if($crudAction == 'addTreatments' || $crudAction == 'updateTreatments')
		{ //if-6
			if(sanitize_text_field($treatmentValueArray['serviceName']) == '')
			{
				return wp_send_json_error("Please enter treatment name.");
			}
				
			$exists = $wpdb->get_var("SELECT count(serviceName) FROM ".$wp_abServiceMst." WHERE isDeleted=0 AND serviceName = '".sanitize_text_field($treatmentValueArray['serviceName'])."'");
			
			if($exists > 0 && $crudAction == 'addTreatments')
			{ //if-4
				return wp_send_json_error("Service already exists.");
			} //if-4 ends
			else
			{ //else-4
				if($crudAction == 'addTreatments')
				{ //if-2
					$serviceArray = array(
						"serviceName" => "".apbud_validateTextContent($treatmentValueArray['serviceName'])."",
						"serviceDescription" => "".apbud_validateTextarea($treatmentValueArray['serviceDesc'])."",
						"createdDate" => "".AB_CURRENT_TIMEZONE."",
						"createdBy" => "".$user."",
						"ipAddress" => "".$ip."",
					);
				
					$insTreatment = $wpdb->insert($wp_abServiceMst, $serviceArray);		
					if(!$insTreatment)
					{ //if-3
						return wp_send_json_error("Service Not Saved.");
					} //if-3 ends
					else
					{ //else-3
						return wp_send_json_success("Service Saved Successfully.");
					} //else-3 ends
				} //if-2 ends
				else if($crudAction == 'updateTreatments')
				{ //else-if-2
					$serviceId = sanitize_text_field($treatmentValueArray['rowId']);
					
					if($serviceId == '' || is_numeric($serviceId) == false)
					{ //if-8
						return wp_send_json_error("Invalid Data");	
					} //if-8 ends
					else
					{ //else-8
						$checkServiceData = $wpdb->get_var("SELECT count(serviceId) FROM ".$wp_abServiceMst." WHERE isDeleted=0 AND serviceId=".$serviceId);	
						if($checkServiceData > 0)						
						{ //if-9
							$whereServiceArray = array(
								'serviceId' => $serviceId
							);
							
							$updateServiceArray = array(
								"serviceName" => "".apbud_validateTextContent($treatmentValueArray['serviceName'])."",
								"serviceDescription" => "".apbud_validateTextarea($treatmentValueArray['serviceDesc'])."",
								"modifiedDate" => "".AB_CURRENT_TIMEZONE."",
								"modifiedBy" => "".$user."",
								"ipAddress" => "".$ip."",
							);
							
							$updService = $wpdb->update($wp_abServiceMst, $updateServiceArray, $whereServiceArray);		
							if(!$updService)
							{ //if-5
								return wp_send_json_error("Service not updated");
							} //if-5 ends
							else
							{ //else-5
								return wp_send_json_success("Service updated successfully");
							} //else-5 ends
						} //if-9 ends
						else
						{ //else-9
							return wp_send_json_error("Service Details Not Found");
						} //else-9 ends
					} //else-8 ends				
				} //else-if-2 ends
			} //else-4 ends
		} //if-6 ends
		else if($crudAction == 'deleteTreatments')
		{ //else-if-6
			$deleteArray = array(
				'serviceId' => "".$_POST['stringValues'].""
			);
			
			$deleteValueArray = array(
				'isDeleted' => 1
			);
			
			$deleteTreatment = $wpdb->update($wp_abServiceMst, $deleteValueArray, $deleteArray);
			if(!$deleteTreatment)
			{ //if-7
				return wp_send_json_error("Service Not Deleted.");
			} //if-7 ends
			else
			{ //else-7
				return wp_send_json_success("Service Deleted Successfully.");
			} //else-7 ends
		} //else-if-6 ends
	} //if-1 ends
	else
	{ //else-1
		return wp_send_json_error("Wrong Nonce");	
	} //else-1 ends
	die();
}

//Fetch treatment data using id
add_action('wp_ajax_apbud_get_treatments', 'apbud_get_treatments');
add_action('wp_ajax_nopriv_apbud_get_treatments', 'apbud_get_treatments');

function apbud_get_treatments()
{
	global $wpdb;
	$checkNonce = check_ajax_referer( 'apbud_get_treatments', 'ajax_nonce' );
	if($checkNonce)
	{ //if-1
		
		$wp_abServiceMst = $wpdb->prefix .'abServiceMst';
		$getTreatment = $wpdb->get_results("SELECT serviceId, serviceName, serviceDescription FROM ".$wp_abServiceMst." WHERE isDeleted=0 AND serviceId=".sanitize_text_field($_POST['editId']), ARRAY_A);
		
		if($getTreatment)
		{
			echo json_encode($getTreatment[0]);		
		}
		else
		{
			return wp_send_json_error("No data found. Please try again");
		}
	} //if-1 ends
	else
	{ //else-1
		return wp_send_json_error("Wrong Nonce");	
	} //else-1 ends
	die();
}

//Holidays
add_action( 'wp_ajax_apbud_add_holidays', 'apbud_add_holidays' );
add_action( 'wp_ajax_nopriv_apbud_add_holidays', 'apbud_add_holidays' );

function apbud_add_holidays() 
{
	global $wpdb;
	
	$checkNonce = check_ajax_referer( 'apbud_add_holidays', 'ajax_nonce' );
	if($checkNonce)
	{ //if-1
		extract($_POST,EXTR_SKIP);
		parse_str($_POST['stringValues'], $holidayValueArray);
				
		$wp_abHolidayMst = $wpdb->prefix .'abHolidayMst';
		$user = get_current_user_id();
		$ip = apbud_getIp();
		
		
		if($crudAction == 'addHoliday' || $crudAction == 'updateHoliday')
		{ //if-2
			if($holidayValueArray['holidayName'] == '')
			{
				return wp_send_json_error("Please enter Holiday Name");
			}
			if($holidayValueArray['holidayDate'] == '')
			{
				return wp_send_json_error("Please select Date");
			}
				
			$exists = $wpdb->get_var("Select count(*) from ".$wp_abHolidayMst." where isDeleted=0 AND (holidayDate='".apbud_changeDateFormatWithoutTime($holidayValueArray['holidayDate'])."' OR holidayName='".sanitize_text_field($holidayValueArray['holidayName'])."')");
			
			if($exists > 0)
			{ //if-4
				return wp_send_json_error("Holiday already exists.");				
			} //if-4 ends
			else
			{ //else-4
				if($crudAction == 'addHoliday')
				{ //if-2
					$holidayArray = array(
						"holidayName" => "".apbud_validateTextContent($holidayValueArray['holidayName'])."",
						"holidayDate" => "".apbud_changeDateFormatWithoutTime($holidayValueArray['holidayDate'])."",
						"createdDate" => "".AB_CURRENT_TIMEZONE."",
						"createdBy" => "".$user."",
						"ipAddress" => "".$ip."",
					);
				
					$insHoliday = $wpdb->insert($wp_abHolidayMst, $holidayArray);		
					if(!$insHoliday)
					{ //if-3
						return wp_send_json_error("Holiday Not Saved.");
					} //if-3 ends
					else
					{ //else-3
						return wp_send_json_success("Holiday Saved Successfully.".displayHolidayDetails()."");
					} //else-3 ends
				} //if-2 ends
			} //else-4 ends
		} //if-2 ends
		else if($crudAction == 'deleteHoliday')
		{ //else-if-2
		
			$deleteArray = array(
				'holidayId' => "".sanitize_text_field($_POST['stringValues']).""
			);
			
			$deleteValueArray = array(
				'isDeleted' => 1
			);
			
			$deleteTreatment = $wpdb->update($wp_abHolidayMst, $deleteValueArray, $deleteArray);
			if(!$deleteTreatment)
			{ //if-3
				return wp_send_json_error("Holiday Not Deleted.");
			} //if-3 ends
			else
			{ //else-3
				return wp_send_json_success("Holiday Deleted Successfully.");
			} //else-3 ends
		} //else-if-2 ends
	} //if-1 ends
	else
	{ //else-1
		return wp_send_json_error("Wrong Nonce");	
	} //else-1 ends
	die();
}

/*******************************************************************************************************************************************************/

//Display Holidays using AJAX
function displayHolidayDetails()
{
?>
<table class="wp-list-table widefat">
    <thead>
        <tr>
            <th scope="col" id="name" class="manage-column column-name column-primary" width="25%">
                <strong><span>Holiday Name</span></strong>
            </th>
            <th scope="col" id="description" class="manage-column column-description sortable desc" width="50%">
                <strong><span>Holiday Date</span></strong>
            </th>
            <th scope="col" id="description" class="manage-column column-description sortable desc" width="15%">
                <strong><span>Delete</span></strong>
            </th>
        </tr>
    </thead>
    <tbody id="the-list" data-wp-lists="list:tag">
        <?php
        global $wpdb;
		$wp_abHolidayMst = $wpdb->prefix .'abHolidayMst';
        $total_query_holiday = "SELECT COUNT(*) FROM ".$wp_abHolidayMst." WHERE isDeleted=0";
        $total_holiday = $wpdb->get_var( $total_query_holiday );
        $items_per_page_holiday = 5;
        $page_holiday = isset( $_GET['paged'] ) ? abs( (int) $_GET['paged'] ) : 1;
        $offset_holiday = ( $page_holiday * $items_per_page_holiday ) - $items_per_page_holiday;
        $holidayResult = $wpdb->get_results( "SELECT holidayId, holidayName, holidayDate FROM ".$wp_abHolidayMst." WHERE isDeleted=0 ORDER BY holidayId DESC LIMIT ".$offset_holiday.", ".$items_per_page_holiday."", ARRAY_A );
        $totalHolidayPage = ceil($total_holiday / $items_per_page_holiday);
        
        foreach($holidayResult as $trRes){
        ?>
        <tr id="tag-1" onClick="Drow(this)" class="search-fade"> 
            <td class="name column-name has-row-actions column-primary" data-colname="Name">
                <span><?php echo apbud_stripTextContent($trRes['holidayName']); ?></span>
            </td>
            <td class="description column-description" data-colname="Description">
                <p><?php echo apbud_changeDateToReadableFormatWithoutTime($trRes['holidayDate']); ?></p>
            </td>
            <td class="view" data-colname="Delete">
                <span class="view"><a href="#" class="deleteHoliday delete-link" data-holid="<?php echo $trRes['holidayId']; ?>">Delete</a></span>
            </td>
        </tr>
        <?php }?>
    </tbody>
</table><?php echo "##&&##"; ?> 
<?php	
}

/*******************************************************************************************************************************************************/

//Time Slots
add_action( 'wp_ajax_apbud_add_timeSlots', 'apbud_add_timeSlots' );
add_action( 'wp_ajax_nopriv_apbud_add_timeSlots', 'apbud_add_timeSlots' );

function apbud_add_timeSlots() 
{
	global $wpdb;
	$sucess=0;
	$checkNonce = check_ajax_referer( 'admin_timeSlot', 'ajax_nonce' );
	if($checkNonce)
	{ //if-1
	
		$arrayValue=array();
		parse_str($_POST["dataString"]);
		$wp_abTimeSlotMst = $wpdb->prefix .'abTimeSlotMst';
		$wp_abSlotMappingDetails = $wpdb->prefix .'abSlotMappingDetails';
		$user = get_current_user_id();
		$ip = apbud_getIp();
		$currDate = date("Y-m-d H:i:s");
	/************************************************For Multiple Record *****************************************/
		if($countRows)
		{//if-2
			
			for($i=1;$i<=$countRows;$i++)
			{//for-0 starts
				$name=${"slotName_".$i};
				$arrayValue[$i]=$name;
			}//for-0 ends
			$countValueArray = array_count_values($arrayValue);
			foreach($countValueArray as $countValue)
			{//for-0.1 starts
				if($countValue > 1)
				{//if-2.1 starts
					return wp_send_json_error("Sorry! Slot Name already exists");
				}//if-2.1 ends
			}//for-0.1 ends
			for($i=1;$i<=$countRows;$i++)
			{//for-1 starts
				$slotId=${"slotId_".$i};
				$slotName=${"slotName_".$i};
				$startTime=${"start_time_slot_".$i};
				$endTime=${"end_time_slot_".$i};
				$slotMaxApppointments=${"slot_max_appointments_".$i};				
				
				if(apbud_validateTextContent($slotName) == "")
				{//if-3 starts
					return wp_send_json_error("Please enter Slot Name");
				}//if-3 ends
				if($startTime == '')
				{//if-4 starts
					return wp_send_json_error("Please select Start Time");
				}//if-4 ends
				if($endTime == '')
				{//if-5 starts
					return wp_send_json_error("Please select End Time");
				}//if-5 ends
				if(absint($slotMaxApppointments) == '')
				{//if-6 starts
					return wp_send_json_error("Please enter Maximum no of Appointments");
				}//if-6 ends
			
				$slotId = sanitize_text_field($slotId);
				
				$exists = $wpdb->get_results("Select slotId from ".$wp_abTimeSlotMst." where isDeleted=0 AND slotId='".$slotId."'",ARRAY_A);
						
				if($exists)
				{ //if-7 starts
											
					$done = $wpdb->query("update ".$wp_abTimeSlotMst." set slotName='".apbud_validateTextContent($slotName)."',slotStartTime='".$startTime."', slotEndTime='".$endTime."',maxAppointmentsPerSlot=".absint($slotMaxApppointments).",modifiedBy='".$user."',modifiedDate='".$currDate."',ipAddress='".$ip."' where slotId=".$exists[0]['slotId']." and 
not exists (select slotName from (select * from ".$wp_abTimeSlotMst.") as timeSlotTable where SlotName='".apbud_validateTextContent($slotName)."' and slotId <>".$exists[0]['slotId']." and isDeleted=0)");
					
					if($done==FALSE )
					{//if-8 starts	
						return wp_send_json_error("Error while Update");
					}
					else 
					{
						$sucess++;
					}//if-8 ends
				} 
				else
				{ //Single Record Insert code	
					$insertArray1 = array(
						'slotName'=>apbud_validateTextContent($slotName),
						'slotStartTime'=>$startTime,
						'slotEndTime'=>$endTime,
						'maxAppointmentsPerSlot'=>absint($slotMaxApppointments),
						'createdBy' => $user,
						'createdDate' => $currDate,
						"ipAddress" => "".$ip."",
					);
				
					$done = $wpdb->insert($wp_abTimeSlotMst, $insertArray1);
					$lastId = $wpdb->insert_id;
					
					if($done==FALSE )
					{//if-9 starts	
						return wp_send_json_error("Error while Insert");
						die();
					}
					else 
					{
						if($lastId)
						{
							$insert_per  = "INSERT into ".$wp_abSlotMappingDetails." (`workingDay`,`slotId`,`createdBy`,createdDate,ipAddress,isDeleted) values('1',".$lastId." ,".$user.",'".$currDate."','".$ip."','1'), ('2',".$lastId." ,".$user.",'".$currDate."','".$ip."','1'),
('3',".$lastId." ,".$user.",'".$currDate."','".$ip."','1'),('4',".$lastId." ,".$user.",'".$currDate."','".$ip."','1'),
('5',".$lastId." ,".$user.",'".$currDate."','".$ip."','1'),('6',".$lastId." ,".$user.",'".$currDate."','".$ip."','1'),
('0',".$lastId." ,".$user.",'".$currDate."','".$ip."','1')";
								  
						   $insert_per_res = $wpdb->query($insert_per);
						   if($insert_per_res)
						   {
								$sucess++;
						   }
						   else
						   {
								return wp_send_json_error("Error while Insert");
						   }
						}
						else
						{
							$sucess++;
						}
					}//if-9 ends
				}//if-7 ends
			}//for-1 ends
			if($sucess == $countRows)
			{//if-8 starts
				return wp_send_json_success("Time slot saved Successfully");
			}//if-8 ends
				
		}//if-2 ends
	} //if-1 ends
	else
	{ //else-1
		return wp_send_json_error("Wrong Nonce");	
	} //else-1 ends
	die();
}


/*******************************************************************************************************************************************************/
//Delete Time Slots
add_action( 'wp_ajax_apbud_delete_timeSlot', 'apbud_delete_timeSlot' );
add_action( 'wp_ajax_nopriv_apbud_delete_timeSlot', 'apbud_delete_timeSlot' );

function apbud_delete_timeSlot() 
{
	global $wpdb;
	$sucess=0;
	$checkNonce = check_ajax_referer( 'admin_delete_timeSlot', 'ajax_nonce' );
	if($checkNonce)
	{ //if-1
		parse_str($_POST["dataString"]);
		$wp_abTimeSlotMst = $wpdb->prefix .'abTimeSlotMst';
		$wp_abSlotMappingDetails = $wpdb->prefix .'abSlotMappingDetails';
	
		$rowId= sanitize_text_field($rowId);
		
		if(!empty($rowId))
		{//if-2 starts
			$checkResult = $wpdb->query("SELECT ts.slotId FROM ".$wp_abTimeSlotMst." ts where not exists (select slotId from ".$wp_abSlotMappingDetails." smd where smd.slotId=ts.slotId AND smd.isDeleted=0) AND ts.slotId=".$rowId." AND ts.isDeleted=0");
			if(!empty($checkResult))
			{
				$result = $wpdb->query( "Update ".$wp_abTimeSlotMst." SET isDeleted=1 WHERE slotId=".$rowId);
				if($result==FALSE )
				{//if-8 starts	
					return wp_send_json_error("Error while Delete");
				}
				else 
				{
					return wp_send_json_success("Time Slot deleted successfully");
				}//if-8 ends
			}
			else
			{
				return wp_send_json_error("Sorry! Time Slot can not be deleted as Working Days exists");
			}
		}//if-2 ends
		
	}//if-1 ends
	die();
}

/*******************************************************************************************************************************************************/
//Working Days

add_action( 'wp_ajax_apbud_admin_add_workingDays1', 'apbud_admin_add_workingDays1' );
add_action( 'wp_ajax_nopriv_apbud_admin_add_workingDays1', 'apbud_admin_add_workingDays1' );

function apbud_admin_add_workingDays1() 
{
	$checkNonce = check_ajax_referer( 'admin_workingDays', 'ajax_nonce' );
	
	if($checkNonce)
	{ //if-1
		global $wpdb;
		$one = 1;
		$zero = 0;
		$ip = apbud_getIp();
		$user = get_current_user_id();
		$crTime = date("Y-m-d H:i:s");
		$wp_abTimeSlotMst = $wpdb->prefix .'abTimeSlotMst';
		$wp_abSlotMappingDetails = $wpdb->prefix .'abSlotMappingDetails';
	
		parse_str($_POST["dataString"]);
		
		$slotList=$wpdb->get_results("Select slotId from ".$wp_abTimeSlotMst." where isDeleted=0", ARRAY_A);
		$countSlots = count($slotList);
		
		for($i=0;$i<$countSlots;$i++)
		{//for-1 starts
		   if($chk[($slotList[$i]['slotId'])]!='' && isset($chk[($slotList[$i]['slotId'])]))
		   {//if-1 starts check for module
				 for($k=0;$k<7;$k++)
				 {//for-3 starts
						if($chk[($slotList[$i]['slotId'])][$k]!='' && isset($chk[($slotList[$i]['slotId'])][$k]))
						{//if-3 starts
							$arrayData = array(
								"modifiedBy" => "".$user."",
								"modifiedDate" => "".$crTime."",
								"ipAddress" => "".$ip."",
								"isDeleted" => "".$zero.""
							);
						}
						else
						{
							$arrayData = array(
								"modifiedBy" => "".$user."",
								"modifiedDate" => "".$crTime."",
								"ipAddress" => "".$ip."",
								"isDeleted" => "".$one.""
							);
						}//if-3 ends						
						
						//Add the WHERE clauses
						$where_clause = array(
							"workingDay" => "".$k."",
							"slotId" => $slotList[$i]['slotId'],
							);
						$updated1 = $wpdb->update($wp_abSlotMappingDetails, $arrayData, $where_clause );
									
						if(!$updated1)
						{
							wp_send_json_error("Error while Update.");
						}
				 }//for-3 ends
			}//if-1 ends
			else
			{//else-1 starts
				 for($k=0;$k<7;$k++)
				 {//for-4 starts
						$arrayData = array(
							"modifiedBy" => "".$_SESSION['userId']."",
							"modifiedDate" => "".$crTime."",
							"ipAddress" => "".$ip."",
							"isDeleted" => "".$one.""
						);
					$where_clause = array(
						"workingDay" => "".$k."",
						"slotId" => $slotList[$i]['slotId'],
						);
					$updated2 = $wpdb->update($wp_abSlotMappingDetails, $arrayData, $where_clause );
									
					if(!$updated2)
					{
						return  wp_send_json_error("Error while Update.");
					}
				  }//for-4 ends
							  
			 }//else-1 ends   
		}//for-1 ends
		
		return wp_send_json_success("Working Days saved Successfully");	
		
	}//if-1 ends
	else
	{ //else-1
		return wp_send_json_error("Wrong Nonce");	
	} //else-1 ends
	die();
}

/************************* Code for Datepicker ****************************************/

/* Get Time Slot From Selected Date */
add_action( 'wp_ajax_apbud_getTimeSlot', 'apbud_getTimeSlot' );
add_action( 'wp_ajax_nopriv_apbud_getTimeSlot', 'apbud_getTimeSlot' );

function apbud_getTimeSlot() 
{
	global $wpdb;
	
	$checkNonce = check_ajax_referer( 'apbud_getTimeSlot', 'ajax_nonce' );
	if($checkNonce)
	{ //if-1
		extract($_POST,EXTR_SKIP);
		
		$abSlotMappingDetails = $wpdb->prefix . "abSlotMappingDetails";
		$abTimeSlotMst = $wpdb->prefix . "abTimeSlotMst";
		$abAppointmentMst = $wpdb->prefix . "abAppointmentMst";
		
		if($appointmentDay == '' || is_numeric($appointmentDay) == false)
		{ //if-2
			echo '<select class="control" name="appointmentSlotMappingId" id="appointmentSlotMappingId">
    <option value="">--No Time Slot Found--</option></select>';
			die();
		} //if-2 ends
		else
		{ //else-2
			
			$getTimeSlotResult = $wpdb->get_results("SELECT am.appointmentDate, count(am.appointmentSlotMappingId) AS tapp, smd.workingDay, smd.slotId, smd.slotMappingId, ts.slotName, ts.maxAppointmentsPerSlot, DATE_FORMAT(ts.slotStartTime, '%H:%i') as slotStartTime, DATE_FORMAT(ts.slotEndTime, '%H:%i') as slotEndTime
from ".$abSlotMappingDetails." smd
inner join ".$abTimeSlotMst." ts on ts.slotId = smd.slotId and ts.isDeleted=0
left join ".$abAppointmentMst." am on am.appointmentSlotMappingId = smd.slotMappingId and am.appointmentDate = '".apbud_changeDateFormatWithoutTime($appointmentDate)."'
where smd.workingDay=".apbud_validateTextContent($appointmentDay)." and smd.isDeleted=0
group by am.appointmentDate, ts.slotId, smd.workingDay
having tapp < ts.maxAppointmentsPerSlot", ARRAY_A);

			if(!$getTimeSlotResult)
			{
				echo '<select class="control" name="appointmentSlotMappingId" id="appointmentSlotMappingId">
    <option value="">--No Time Slot Found--</option></select>';
				die();
			}
			else
			{
				return wp_send_json_success( getTimeSlotSelect($getTimeSlotResult) );
			}
		} //else-2 ends
	} //if-1 ends
	else
	{ //else-1
		return wp_send_json_error("Wrong Nonce");	
	} //else-1 ends
	die();
}


function getTimeSlotSelect($getTimeSlotResult)
{
?>
<select class="control" name="appointmentSlotMappingId" id="appointmentSlotMappingId">
    <option value="">--Select Time Slot--</option>
    <?php
    foreach($getTimeSlotResult as $slots) { //foreach-1
    ?>
    <option value="<?php esc_attr_e($slots["slotMappingId"]); ?>"><?php esc_attr_e($slots["slotName"]."&nbsp;&nbsp; (".$slots["slotStartTime"]." - ".$slots["slotEndTime"].") "); ?></option>
    <?php } //foreach-1 ends?>
</select><?php echo "##&&##"; ?> 			
<?php		
}

/************************* End code for DatePicker **************************************/

//Add Appointment
add_action( 'wp_ajax_apbud_add_appointments', 'apbud_add_appointments' );
add_action( 'wp_ajax_nopriv_apbud_add_appointments', 'apbud_add_appointments' );

function apbud_add_appointments() 
{
	global $wpdb;
	$checkNonce = check_ajax_referer( 'apbud_add_appointments', 'ajax_nonce' );
	
	if($checkNonce)
	{ //if-1
		parse_str($_POST['dataString'], $dataStringArray);

		$abAppointmentMst = $wpdb->prefix . "abAppointmentMst";
		$abAdminProfileDetails = $wpdb->prefix .'abAdminProfileDetails';
		$abHolidayMst = $wpdb->prefix .'abHolidayMst';
		$abSlotMappingDetails = $wpdb->prefix . "abSlotMappingDetails";
		$abTimeSlotMst = $wpdb->prefix . "abTimeSlotMst";
		
		$user = get_current_user_id();
		$ip = apbud_getIp();		
		
		if($dataStringArray['appointmentDate'] == '')
		{
			return wp_send_json_error("Please select Appointment Date.");
		}
		if($dataStringArray['serviceId'] == '')
		{
			return wp_send_json_error("Please select Service.");
		}
		if($dataStringArray['appointmentSlotMappingId'] == '')
		{
			return wp_send_json_error("Please select Time Slot.");
		}
		if($dataStringArray['personName'] == '')
		{
			return wp_send_json_error("Please enter Full Name.");
		}
		if($dataStringArray['personEmailId'] == '')
		{
			return wp_send_json_error("Please enter EmailId .");
		}
		if($dataStringArray['personMobileNo'] == '')
		{
			return wp_send_json_error("Please enter Mobile Number.");
		}
		
		$holidayResult = $wpdb->get_results("SELECT holidayName FROM ".$abHolidayMst." where holidayDate='".apbud_changeDateFormatWithoutTime($dataStringArray['appointmentDate'])."'");
		if(!empty($holidayResult))
		{
			return wp_send_json_error("Sorry !! You can not book appointment because it's holiday.");
		}
		
		$earlierDateResults = $wpdb->get_results("SELECT priorDaysToBook,priorMonthsToBook FROM ".$abAdminProfileDetails." where isDeleted=0",ARRAY_A);
		
		if(!empty($earlierDateResults[0]['priorDaysToBook']) && !empty($earlierDateResults[0]['priorMonthsToBook']))
		{
			$priorDays = $earlierDateResults[0]['priorDaysToBook'];
			$earlierDateTimeStamp = strtotime("+".$priorDays." days");
			$priorMonths = ($earlierDateResults[0]['priorMonthsToBook'] * 30) ;
			$priorMonthsTimeStamp = strtotime("+".$priorMonths." days", $earlierDateTimeStamp);
			
			$selectedDateTimestamp = strtotime($dataStringArray['appointmentDate']);
			
			if($selectedDateTimestamp < $earlierDateTimeStamp)
			{
				return wp_send_json_error("Sorry!You can not select earlier Date");
			}
			else if($selectedDateTimestamp > $priorMonthsTimeStamp)
			{
				return wp_send_json_error("Sorry!You can not select future Date");
			}
		}
			
		$exists = $wpdb->get_var("SELECT count(appointmentId) FROM ".$abAppointmentMst." WHERE isDeleted=0 AND personEmailId = '".sanitize_email($dataStringArray['personEmailId'])."' AND personMobileNo = '".apbud_validateTextContent($dataStringArray['personMobileNo'])."' AND appointmentDate = '".apbud_changeDateFormatWithoutTime($dataStringArray['appointmentDate'])."'");
		
		$getTimeSlotRes = $wpdb->get_results("SELECT count(am.appointmentSlotMappingId) AS tapp, ts.maxAppointmentsPerSlot, smd.slotMappingId
from ".$abSlotMappingDetails." smd
inner join ".$abTimeSlotMst." ts on ts.slotId = smd.slotId and ts.isDeleted=0
left join ".$abAppointmentMst." am on am.appointmentSlotMappingId = smd.slotMappingId and am.appointmentDate = '".apbud_changeDateFormatWithoutTime($dataStringArray['appointmentDate'])."'
where smd.isDeleted=0 and smd.workingDay=DATE_FORMAT('".apbud_changeDateFormatWithoutTime($dataStringArray['appointmentDate'])."', '%w') and smd.slotMappingId = '".$dataStringArray['appointmentSlotMappingId']."'
group by am.appointmentDate, ts.slotId
having tapp < ts.maxAppointmentsPerSlot", ARRAY_A);		
		
		if($exists > 0)
		{ //if-5
			return wp_send_json_error("Sorry !! You have already booked appointment for ".$dataStringArray['appointmentDate']);
		}
		else if(!$getTimeSlotRes)
		{
			return wp_send_json_error("You have selected wrong time slot.");
		} 
		else
		{ 	
			$appointmentArray = array(
				"personName" => "".apbud_validateTextContent($dataStringArray['personName'])."",
				"personEmailId" => "".sanitize_email($dataStringArray['personEmailId'])."",
				"personMobileNo" => "".apbud_validateTextContent($dataStringArray['personMobileNo'])."",
				"personAddress" => "".apbud_validateTextarea($dataStringArray['personAddress'])."",
				"serviceId" => "".sanitize_text_field($dataStringArray['serviceId'])."",
				"appointmentDate" => "".apbud_changeDateFormatWithoutTime($dataStringArray['appointmentDate'])."",
				"appointmentSlotMappingId" => "".sanitize_text_field($dataStringArray['appointmentSlotMappingId'])."",
				"remarks" => "".apbud_validateTextarea($dataStringArray['remarks'])."",
				"createdDate" => "".AB_CURRENT_TIMEZONE."",
				"createdBy" => "".$user."",
				"ipAddress" => "".$ip.""
			);
			
			$appointmentResult = $wpdb->insert($abAppointmentMst, $appointmentArray);	
			
			if(!$appointmentResult)
			{ //if-3
				return wp_send_json_error("Error! while Insert");
			} //if-3 ends
			else
			{ //else-3
				return wp_send_json_success("Appointment scheduled successfully.");
			} //else-3 ends
		}//if-5 ends
	}//if-1 ends
	else
	{ //else-1
		return wp_send_json_error("Wrong Nonce");	
	} //else-1 ends
	die();
}

//Fetch Appointment
add_action( 'wp_ajax_apbud_admin_fetch_appointments', 'apbud_admin_fetch_appointments' );
add_action( 'wp_ajax_nopriv_apbud_admin_fetch_appointments', 'apbud_admin_fetch_appointments' );

function apbud_admin_fetch_appointments() 
{
	global $wpdb;
	$checkNonce = check_ajax_referer( 'apbud_admin_fetch_appointments', 'ajax_nonce' );
	
	if($checkNonce)
	{ //if-1
		parse_str($_POST['dataString'], $dataStringArray);

		$abAppointmentMst = $wpdb->prefix . "abAppointmentMst";
		$abSlotMappingDetails = $wpdb->prefix . "abSlotMappingDetails";
		$abTimeSlotMst = $wpdb->prefix . "abTimeSlotMst";
		$abServiceMst = $wpdb->prefix . "abServiceMst"; 
		$abHolidayMst = $wpdb->prefix . "abHolidayMst";
		
		if($dataStringArray['type']=='fetch')
		{
			$appointmentEvents = array();

			$getAppointments = $wpdb->get_results("SELECT am.appointmentDate,am.personName,am.personEmailId,am.personMobileNo,DATE_FORMAT(ts.slotStartTime, '%H:%i') as slotStartTime,DATE_FORMAT(ts.slotEndTime, '%H:%i') as slotEndTime,sm.serviceName FROM ".$abAppointmentMst." am
inner join ".$abSlotMappingDetails." smd on smd.slotMappingId=am.appointmentSlotMappingId AND smd.isDeleted=0 inner join ".$abTimeSlotMst." ts on ts.slotId=smd.slotId AND ts.isDeleted=0 inner join ".$abServiceMst." sm on sm.serviceId = am.serviceId
WHERE am.isDeleted=0",ARRAY_A);

			$holidayDisplayData = $wpdb->get_results("SELECT holidayDate,holidayName from ".$abHolidayMst." where isDeleted=0",ARRAY_A);
			
			if($getAppointments)
			{
				foreach($getAppointments as $appointment)
				{
					$appointmentArr = array();
					$appointmentArr['title']=strtoupper($appointment['personName'])."  -  [".$appointment['slotStartTime']." - ".$appointment['slotEndTime']."]";
					$appointmentArr['start']=$appointment['appointmentDate'];
					$appointmentArr['personName']=$appointment['personName'];
					$appointmentArr['slotStartTime']=$appointment['slotStartTime'];
					$appointmentArr['slotEndTime']=$appointment['slotEndTime'];
					$appointmentArr['personEmailId']=$appointment['personEmailId'];
					$appointmentArr['personMobileNo']=$appointment['personMobileNo'];
					$appointmentArr['serviceName']=$appointment['serviceName'];
					
					array_push($appointmentEvents, $appointmentArr);
				}
			}
			
			if($holidayDisplayData)
			{//if-4 
				 foreach($holidayDisplayData as $holidayData)
				 {//foreach-2 
				 	 if(!empty($holidayData['holidayName']))
						{//if-5 
							$holidayArr = array();
							$holidayArr['title']=$holidayData['holidayName'];
							$holidayArr['start']=$holidayData['holidayDate'];
							$holidayArr['description']=$holidayData['holidayName'];							
							$holidayArr['color']='#CB4630';
  						    array_push($appointmentEvents, $holidayArr);
						}//if-5 ends
				 }//foreach-2 ends
			}//if-4 ends
			
			echo json_encode($appointmentEvents);
		}
		
	}else
	{
		return wp_send_json_error("Wrong Nonce");	
	}//if-1 ends
	die();
}
<?php
	// Exit if accessed directly
	if ( ! defined( 'ABSPATH' ) ) exit;
?>
<div class="card" style="max-width: 100%; padding-top: 20px; padding-bottom: 30px;" id="service-div">
    <div class="wp-clearfix">
        <form action="" method="post" name="holidayForm" id="holidayForm">
            <h2 class="title">Add new holiday</h2>
            <div class="services-col-left">
                <div class="form-wrap">
                    <div class="form-field">
                        <label for="holidayFormLabelName">Holiday Name</label>
                        <input name="holidayName" class="regular-text alphaNumeric" type="text" maxlength="50" placeholder="Holiday Name" autocomplete="off">
                        <p>The name is how it appears on your site.</p>
                    </div>
                    <br />
                    <div class="form-field">
                        <label for="holidayFormLabelDate">Holiday Date</label>
                        <input name="holidayDate" id="holidayDate" class="regular-text datedropper" type="text" placeholder="Holiday Date" autocomplete="off" maxlength="12">
                    </div>  
                    <div class="form-field">
                    	<?php submit_button('Save Holidays'); ?>
                    </div>                              
                </div>
                <!-- /.form-wrap -->
            </div>
            <!-- /.services-col-left -->
        </form> 
                
        <div class="services-col-right">
        	<div id="displayHoliday">
            <div class="box-loader" style="display: none;">
            	 <img src="<?php echo apbud_IMAGES . '/ajax-loader3.gif'; ?>" />
            </div>
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
            </table>
            </div>
            <!-- /.displayHoliday -->
            <div class="tablenav bottom">
                <div class="alignleft actions bulkactions">
                   <label for="current-page-selector" class="displaying-num">
                        <ul class="defaultPagiantion">
                          <li>
                          <?php for($ho=1; $ho<=$totalHolidayPage; $ho++) { ?>
                            <a href="<?php echo bloginfo('url'); ?>/wp-admin/admin.php?page=appointment-buddy-settings&paged=<?php echo $ho; ?>" class="linkNumber"><?php echo $ho; ?></a>
                          <?php } ?>
                          </li>
                        </ul>
                    </label>
                </div>
                <br class="clear">
            </div> 
            
        </div>
        <!-- /.services-col-right -->
    </div>
    <!-- /.wp-clearfix --> 
</div>
<?php

add_action('admin_footer', 'apbud_add_holiday_script');

function apbud_add_holiday_script() 
{ 
?>
<script>
jQuery(document).ready(function() {
	
	//To validate and add holiday
	jQuery("#holidayForm").validate({
		rules:
		{
			holidayName:"required",
			holidayDate:"required"
		}, 
		messages:
		{
			holidayName:"Please enter Holiday Name",
			holidayDate:"Please select Date"
		},
		submitHandler: function()
		{
			var dataString = {
				stringValues: jQuery('#holidayForm').serialize() ,
				action: 'apbud_add_holidays',
				ajax_nonce: '<?php echo wp_create_nonce('apbud_add_holidays'); ?>',
				crudAction:'addHoliday'
			}
			
			jQuery('.services-col-right .box-loader').fadeIn(300);	
				
			jQuery.ajax({
				type: "post",
				url:'<?php echo apbud_AJAX_URL; ?>',
				data: dataString,
				dataType: 'html',
				success: function(response)
				{ 
					try
					{					
						if(jQuery.parseJSON(response) != null)
						{
							abAlertMessage(response);
							jQuery('html, body').animate({scrollTop : 0}, 800);
						}
					}
					catch (error)
					{
						var ress = response.split("##&&##");
						jQuery("#displayHoliday").html(ress[0]);
						abAlertMessage(ress[1]);
						jQuery('html, body').animate({scrollTop : 0}, 800);
						jQuery('#holidayForm').trigger("reset");
					}
					jQuery('.services-col-right .box-loader').fadeOut(300);
				}
			});
		} //submitHandler ends
	});
	
	//To Delete holiday
	jQuery(document).on('click', ".deleteHoliday", (function(e)
	{					
		e.preventDefault();		
        var rowId = jQuery(this).data("holid");
		var newthis = jQuery(this); 
		
		var dataString = {
				stringValues : rowId ,
				action : 'apbud_add_holidays' ,
				ajax_nonce : '<?php echo wp_create_nonce( 'apbud_add_holidays' ); ?>',
				crudAction:'deleteHoliday'
			}
			
		swal({
		  title: "Are you sure?",
		  text: "You will not be able to recover!",
		  type: "warning",
		  showCancelButton: true,
		  confirmButtonColor: "#DD6B55",
		  confirmButtonText: "Yes, delete it!",
		  closeOnConfirm: true
		},
		function() 
		{			
			jQuery.ajax({
				type:"POST",
				dataType:"html",
				url:'<?php echo apbud_AJAX_URL; ?>',
				data:dataString,
				success: function(response)
				{
					abSweetAlertMessage(response, newthis);
				}	
			});
			
			return false;
		});
    }));
	
	//Pagination
	ajaxPagination('#displayHoliday');
});
</script>
<?php } ?>
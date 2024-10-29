<?php
	// Exit if accessed directly
	if ( ! defined( 'ABSPATH' ) ) exit;
?>
<div class="card" style="max-width: 100%; padding-top: 20px; padding-bottom: 30px;" id="service-div">
    <div class="wp-clearfix">
        <form action="" method="post" name="treatmentsForm" id="treatmentsForm">
            <h2 class="title">Add new Service</h2>
            <input type="hidden" name="rowId" value="" id="rowId" />
            <?php
			
			?>
            <div class="col-mid">
                <div class="form-wrap">
                	<div class="form-field">
                        <label for="serviceName">Service Name</label>
                        <input class="regular-text alphaNumeric" type="text" name="serviceName" id="serviceName" maxlength="50" placeholder="Service Name" autocomplete="off" value="">
                    </div>
                    <div class="form-field">
                        <label for="serviceDesc">Service Description</label>
                        <textarea class="regular-text" rows="25" name="serviceDesc" id="serviceDesc" placeholder="Service Description.........."></textarea>
                        <p>Sample description here...</p>
                    </div>  
                    <div class="form-field">
                    	<?php submit_button('Save Service'); ?>
                    </div>                              
                </div>
                <!-- /.form-wrap -->
            </div>
            <!-- /.services-col-left -->
        </form> 
    </div>
    <!-- /.wp-clearfix --> 
</div>
<?php
add_action('admin_footer', 'apbud_add_treatments_script');

function apbud_add_treatments_script() { ?>
<script>
jQuery(document).ready(function() {
	
	//To validate form and add Treatments
	jQuery("#treatmentsForm").validate({
		rules:
		{
			serviceName:"required",			
		}, 
		messages:
		{
			serviceName:"Please enter Service name",
		},
		submitHandler: function()
		{
			if(jQuery("#rowId").val() == '')
			{
				var dataString = {
					stringValues : jQuery('#treatmentsForm').serialize() ,
					action : 'apbud_add_treatments' ,
					ajax_nonce : '<?php echo wp_create_nonce( 'apbud_add_treatments' ); ?>',
					crudAction:'addTreatments'
				}
			}
			else
			{
				var dataString = {
					stringValues : jQuery('#treatmentsForm').serialize() ,
					action : 'apbud_add_treatments' ,
					ajax_nonce : '<?php echo wp_create_nonce( 'apbud_add_treatments' ); ?>',
					crudAction:'updateTreatments'
				}	
			}			
									
			jQuery.ajax({
				type:"POST",
				dataType:"html",
				url:'<?php echo apbud_AJAX_URL; ?>',
				data:dataString,
				success: function(response)
				{
					var responseResult = jQuery.parseJSON(response);
					if(responseResult.success === false)
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
							tabapi.switch(4);
						}, 2000);
					}
				}	
			});
	
		} //submitHandler ends
	});
	
});
</script>
<?php } ?> 
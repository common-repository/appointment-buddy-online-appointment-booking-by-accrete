<?php
	// Exit if accessed directly
	if ( ! defined( 'ABSPATH' ) ) exit;
?>
<div class="card" style="max-width: 100%; padding-top: 20px; padding-bottom: 30px;" id="service-div">
    <div class="wp-clearfix">
        <div class="">
            <div id="displayTreametents">
            <table class="wp-list-table widefat">
                <thead>
                    <tr>
                        <th scope="col" id="name" class="manage-column column-name column-primary" width="25%">
                            <strong><span>Service Name</span></strong>
                        </th>
                        <th scope="col" id="description" class="manage-column column-description sortable desc" width="50%">
                            <strong><span>Service Description</span></strong>
                        </th>
                        <th scope="col" id="description" class="manage-column column-description sortable desc" width="10%">
                            <strong><span>Edit</span></strong>
                        </th>
                        <th scope="col" id="description" class="manage-column column-description sortable desc" width="15%">
                            <strong><span>Delete</span></strong>
                        </th>
                    </tr>
                </thead>
                <tbody id="the-list" data-wp-lists="list:tag">
                	<?php
					global $wpdb;
					$wp_abServiceMst = $wpdb->prefix .'abServiceMst';
					$total_query = "SELECT COUNT(*) FROM ".$wp_abServiceMst." WHERE isDeleted=0";
					$total = $wpdb->get_var( $total_query );
					$items_per_page = 5;
					$page = isset( $_GET['paged'] ) ? abs( (int) $_GET['paged'] ) : 1;
					$offset = ( $page * $items_per_page ) - $items_per_page;
					$treatmentResult= $wpdb->get_results( "SELECT serviceId, serviceName, serviceDescription FROM ".$wp_abServiceMst." WHERE isDeleted=0 ORDER BY serviceId DESC LIMIT ".$offset.", ".$items_per_page."", ARRAY_A );
					$totalPage = ceil($total / $items_per_page);
					
					foreach($treatmentResult as $trRes){
					?>
                    <tr id="tag-1" onClick="Drow(this)" class="search-fade"> 
                        <td class="name column-name has-row-actions column-primary" data-colname="Name">
                            <span><?php echo apbud_stripTextContent($trRes['serviceName']); ?></span>
                        </td>
                        <td class="description column-description" data-colname="Description">
                            <p><?php $content = apbud_stripTextContent($trRes['serviceDescription']); echo substr($content, 0, 55)."..."; ?></p>
                        </td>
                        <td class="edit" data-colname="Edit">
                            <span class="edit"><a href="" class="updateTreatments edit-link" data-serid="<?php echo $trRes['serviceId']; ?>">Edit</a></span>
                        </td>
                        <td class="view" data-colname="Delete">
                            <span class="delete1"><a href="" class="deleteTreatments delete-link" data-serid="<?php echo $trRes['serviceId']; ?>">Delete</a></span>
                        </td>
                    </tr>
                    <?php }?>
                </tbody>
            </table>
            </div>
            <!-- /.displayTreametents -->
            <div class="tablenav bottom">
                <div class="alignleft actions bulkactions">
                	<label for="current-page-selector" class="displaying-num">
                        <ul class="defaultPagiantion">
                          <li>
                          <?php for($tr=1; $tr<=$totalPage; $tr++) { ?>
                            <a href="<?php echo bloginfo('url'); ?>/wp-admin/admin.php?page=appointment-buddy-settings&paged=<?php echo $tr; ?>" class="linkNumber"><?php echo $tr; ?></a>
                          <?php } ?>
                          </li>
                        </ul>
                    </label>
                </div>
                <br class="clear">
            </div> 
            <!-- /.tablenav bottom -->
        </div>
        <!-- /.services-col-right -->
        <div class="modal-box" id="view-treatment">
        </div>
    </div>
    <!-- /.wp-clearfix --> 
</div>
<?php
add_action('admin_footer', 'apbud_list_treatments_script');

function apbud_list_treatments_script() { ?>
<script>
jQuery(document).ready(function() {
	
	//To Delete service
	jQuery(document).on('click', ".deleteTreatments", (function(e)
	{					
		e.preventDefault();			
        var rowId = jQuery(this).data("serid");
		var newthis = jQuery(this); 
		
		var dataStringDeleteTreatment = {
				stringValues : rowId ,
				action : 'apbud_add_treatments' ,
				ajax_nonce : '<?php echo wp_create_nonce( 'apbud_add_treatments' ); ?>',
				crudAction:'deleteTreatments'
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
				data:dataStringDeleteTreatment,
				success: function(response)
				{					
					abSweetAlertMessage(response, newthis);
				}	
			});
			
			return false;
		});
    }));
	
	//To Update service
	jQuery(document).on('click', ".updateTreatments", (function(e)
	{					
		var rowId = jQuery(this).data("serid");
		var newthis = jQuery(this);
		
		var dataString = {
			editId : rowId ,
			action : 'apbud_get_treatments' ,
			ajax_nonce : '<?php echo wp_create_nonce( 'apbud_get_treatments' ); ?>'
		} 
		jQuery.ajax({
			type:"POST",
			dataType:"html",
			url:'<?php echo apbud_AJAX_URL; ?>',
			data:dataString,
			success: function(response)
			{
				var treatmentData = jQuery.parseJSON(response);
				
				var treatDesc = treatmentData.serviceDescription;
				treatDesc = treatDesc.replace(/\\/g, '');
				jQuery('#treatmentsForm').find('input[name="rowId"]').val(treatmentData.serviceId);
				jQuery('#treatmentsForm').find('input[name="serviceName"]').val(treatmentData.serviceName);
				jQuery('#treatmentsForm').find('textarea[name="serviceDesc"]').val(treatDesc);
				tabapi.switch(3);
			}	
		});		
		
		e.preventDefault();
		
    }));
	
	//Pagination
	ajaxPagination('#displayTreametents');
});
</script>
<?php } ?> 
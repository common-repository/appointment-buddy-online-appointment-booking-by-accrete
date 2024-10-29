jQuery(document).ready(function()
{
	
	//allow to insert only numeric digits with some brackets in input box
	jQuery(document).on('keypress keydown', ".numeric",function(e) {
		var regex = new RegExp("^[0-9.()+\t\b -]+$");
		var str = String.fromCharCode(!e.keyCode ? e.which : e.keyCode);
		if ( regex.test(str) || (e.keyCode>=37 && e.keyCode<=40) || (e.keyCode>=96 && e.keyCode<=105)  || e.keyCode==46  || e.keyCode==9 || e.keyCode==173) {
			return true;
		}
	
		e.preventDefault();
		return false;
	});
	
	//allow to insert only numeric digits in input box
	jQuery(document).on('keypress keydown', ".onlyNumber",function(e) {
		var regex = new RegExp("^[0-9\t\b -]+$");
		var str = String.fromCharCode(!e.keyCode ? e.which : e.keyCode);
		if ( regex.test(str) || (e.keyCode>=37 && e.keyCode<=40) || (e.keyCode>=96 && e.keyCode<=105)  || e.keyCode==46  || e.keyCode==9) {
			return true;
		}
	
		e.preventDefault();
		return false;
	});
	
	//allow to insert only alphanumeric digits in input box
	jQuery(document).on('keypress keydown', ".alphaNumeric", function(e) {
		var regex = new RegExp("^[a-zA-Z0-9._()/+\t\b -]+$");
		var str = String.fromCharCode(!e.keyCode ? e.which : e.keyCode);
		if (regex.test(str) || (e.keyCode>=37 && e.keyCode<=40) || (e.keyCode>=96 && e.keyCode<=105)  || e.keyCode==46  || e.keyCode==9) {
			return true;
		}
	
		e.preventDefault();
		return false;
	});
	
	
	// OPEN LAST OPENED TAB
	
	var lastOpenTab = window.localStorage['lastOpenTab'];

	tabapi = jQuery('.tabbed-content').tabbedContent({
		currentClass: 'current',
		history: false,
		onSwitch: function(tab, api){
			window.localStorage['lastOpenTab'] = tab;
		},
		onInit: function(api){
			if (window.localStorage['lastOpenTab'] != undefined) {
				api.switch(lastOpenTab);
			}
		}
	}).data('api');


	// TIMEPICKER & DATEPICKER
	attachDateTimePicker();


	// AUTO HIDE NOTICE
	autohideNotice();


	// SLOT MACHINE
	updateSlotId();

	jQuery('.js-add-slot-btn').on('click', function(e){
		addSlot();
		updateSlotId();
		e.preventDefault();
	});
	
}); // document.ready


// TIME SLOT FUNCTIONS

function addSlot() {

	var slotCount = jQuery('tr.slot-row').length;
	jQuery('.time-slot-table > tbody').append('<tr class="slot-row" data-slot-id=""> <td><strong></strong></td><td><input type="text" name="slotName" id="slotName" class="inputTimeSlots alphaNumeric" data-msg="Please select Slot Name" maxlength="20" autocomplete="off"/></td><td><input type="text" class="timedropper inputTimeSlots" name="start_time_slot_" data-msg="Please select Start Time" autocomplete="off"></td><td><input type="text" class="timedropper inputTimeSlots endTimeSlot" name="end_time_slot_"  data-msg="Please select End Time" autocomplete="off"></td><td><input type="text" maxlength=2 class="inputTimeSlots numeric" name="slot_max_appointments_" data-msg="Please enter Maximum no of Appointments" autocomplete="off"></td><td><a class="button js-remove-slot-btn delete" href=""><span class="dashicons dashicons-trash"></span></a></td></tr>');

}

function removeSlot(e) {

	jQuery(e.target).parents('tr.slot-row').fadeOut(1000).remove();

}

function updateSlotId() {

	jQuery('.slot-row').each(function(index, value){
		jQuery(this).attr('data-slot-id', (index+1));
		jQuery(this).find('td > strong').text(index+1);
		jQuery(this).find('input[name^="slotName"]').attr('name', 'slotName_'+(index+1));
		jQuery(this).find('input[name^="slotId"]').attr('name', 'slotId_'+(index+1));
		jQuery(this).find('input[name^="start_time_slot_"]').attr('name', 'start_time_slot_'+(index+1));
		jQuery(this).find('input[name^="end_time_slot_"]').attr('name', 'end_time_slot_'+(index+1));
		jQuery(this).find('input[name^="slot_max_appointments_"]').attr('name', 'slot_max_appointments_'+(index+1));
		jQuery(this).find('div[id^="error_start_time_slot_"]').attr('id', 'error_start_time_slot_'+(index+1));
	});

	attachDateTimePicker();

}

function attachDateTimePicker() {
	
	jQuery('.timedropper').datetimepicker({
		timepicker: true,
		datepicker: false,
		format: 'H:i',
		step: 30
	});

	jQuery('.datedropper').datetimepicker({
		timepicker: false,
		datepicker: true,
		format: 'd-m-Y',
	});

}

function autohideNotice() {
	if( jQuery('.notice').length>0 ){
		setTimeout(function(){
			jQuery('.notice').fadeOut(1000, function(){
				jQuery(this).remove();
			});
		}, 3000);
	}	
}

//Common function to display message after ajax
function abAlertMessage(resp)
{
	var responseResult = jQuery.parseJSON(resp);
	
	if(responseResult.success === false)
	{
		jQuery(".ab-wrap").prepend('<div class="notice notice-error is-dismissible"><p>'+responseResult.data+'</p></div>');
	}
	else if(responseResult.success === true)
	{
		jQuery(".ab-wrap").prepend('<div class="notice notice-success is-dismissible"><p>'+responseResult.data+'</p></div>');	
	}					
	autohideNotice();	
}

function abSweetAlertMessage(resp, newthis)
{
	var responseResult = jQuery.parseJSON(resp);

	if(responseResult.success === false)
	{
		swal({title:responseResult.data, type:"error"});
	}
	else if(responseResult.success === true)
	{
		swal({title:responseResult.data, type:"success"});
		newthis.closest(".search-fade").fadeOut(1000);
	}
}

function abAlertMessageList(responseResult)
{
	if(responseResult == '1')
	{
		jQuery("#appointment-widget").html('<div class="notice notice-success is-dismissible"><p>Appointment Successfully Deleted</p></div>');	
	}
	else if(responseResult == '0')
	{
		jQuery("#appointment-widget").html('<div class="notice notice-error is-dismissible"><p>Appointment Successfully Deleted</p></div>');	
	}					
	autohideNotice();	
}

//to get row id
var i;
function Drow(ii)
{
	i = ii.rowIndex;
}

function ajaxPagination(tblId)
{
	//Pagination
	jQuery(document).on('click', ".displaying-num a", (function(e)
	{
		e.preventDefault();
		var link = jQuery(this).attr('href');
		jQuery(tblId).fadeOut(500, function(){
			jQuery(this).load(link + ' ' + tblId, function() {
				jQuery(this).fadeIn(500);
			});
		});	
	}));
}
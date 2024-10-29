jQuery(document).ready(function()
{
	
	//allow to insert only numeric digits in input box
	jQuery(document).on('keypress keydown', ".numeric",function(e) {
		var regex = new RegExp("^[0-9.()+\t\b -]+$");
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

	
	/****************************************************************************************************************************************/
	
	// TIMEPICKER & DATEPICKER
	attachDateTimePicker();


	// AUTO HIDE NOTICE
	autohideNotice();
}); // document.ready

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
		jQuery(".booking-widget").prepend('<div class="notice notice-error is-dismissible"><p>'+responseResult.data+'</p></div>');
	}
	else if(responseResult.success === true)
	{
		jQuery(".booking-widget").prepend('<div class="notice notice-success is-dismissible"><p>'+responseResult.data+'</p></div>');	
	}					
	autohideNotice();	
}


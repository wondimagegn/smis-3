// on dom ready
$(document).ready(function(){
// class exists
if($('.confirm_delete').length) {
        // add click handler
	$('.confirm_delete').click(function(){
		// ask for confirmation
		var result = confirm('Are you sure you want to delete this?');
		
		// show loading image
		$('#busy_indicator').show();
		$('#flashMessage').fadeOut();
		
		// get parent row
		var row = $(this).parents('tr');
		
		// do ajax request
		if(result) {
			$.ajax({
				type:"POST",
				url:$(this).attr('href'),
				data:"ajax=1",
				dataType: "json",
				success:function(response){
					// hide loading image
					$('#busy_indicator').hide();
					
					// hide table row on success
					if(response.success == true) {
						row.fadeOut();
					}
					
					// show respsonse message
					if( response.msg ) {
						$('#ajax_msg').html( response.msg ).show();
					} else {
						$('#ajax_msg').html( "<p id='flashMessage' class='flash_bad'>An unexpected error has occured, please refresh and try again</p>" ).show();
					}
				}
			});
		}
	return false;
	});
}
});


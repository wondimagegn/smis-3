$(document).ready(function() {
	
	$('#select-all:checkbox').change(function() {
		checked = $(this).attr('checked');
		disabled = $(this).attr('disabled');
		//alert(this.disabled);
		if(this.checked ==false) {
		   
		    $(this).parents('table').children('tbody').children('tr').children('td').children   ('input:checkbox').each(function() {
		        
			        $(this).attr('checked', false);
    		});
    		
    		 
		} else if( this.checked ==true ) {
		        
		         $(this).parents('table').children('tbody').children('tr').children('td').children   ('input:checkbox').each(function() {
		            if(this.disabled == true) {
			            $(this).attr('checked', false);
    		        } else {
    		             $(this).attr('checked', true);
    		        }
    		});
		}
		
	});


});



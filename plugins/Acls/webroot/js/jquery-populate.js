$(document).ready(function() {
/*if($.cookie("selectedCountry") == null){
    
	$("#country_id").val(65).attr('selected',true);
	$('#region').load('students/get_regions/65');
}else{

   $("#country_id").val( $.cookie("selectedCountry")).attr('selected',true);
   $('#region').load('students/get_regions/'+$.cookie("selectedCountry"));
}
*/
/*$('#country_id').change(function() {
  
var country_data = $(this).val();
$.cookie("selectedCountry", country_data);
var country_select = $.cookie("selectedCountry");

   $('#region').load('students/get_regions/'+$(this).val());
});
*/
//$("#country_id").val(65).attr('selected',true);

$("#country_id").val(68).attr('selected',true);

$('#region').load('/students/get_regions/68');
$('#city_id').load('/students/get_cities/'+$(this).val());

$("#country_id").change(function() {

   $('#region').load('/students/get_regions/'+$(this).val());
   $('#city_id').load('/students/get_cities/'+$(this).val());
   
   $("#region").change(function() {

   $('#city_id').load('/students/get_cities/'+$(this).val());
});
});
   $("#region").change(function() {

   $('#city_id').load('/students/get_cities/'+$(this).val());
});
   /*
     $('#college_id').change(function () { 
       
         $('#department_assignment').load('/users/getDepartment/'+$(this).val());

    });
    
    */
});

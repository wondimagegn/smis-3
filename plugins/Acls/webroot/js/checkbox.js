$(document).ready(function() {
    if($.cookie("selectedCountry") == null){
      /* default to ethiopia */
        $("#country_id").val( 65 ).attr('selected',true);
        $('#region_id').load('students/get_regions/65');
    } else {
       $("#country_id").val( $.cookie("selectedCountry") ).attr('selected',true);
       $('#region_id').load('students/get_regions/'+$.cookie("selectedCountry"));
    }

    $('#country_id').change(function() {
  /* load last selected country using a cookie, in case page was refreshed...this also makes sure the regions dropdown is populated with the last selected country's regions */
        var country_data = $(this).val();
        $.cookie("selectedCountry", country_data);
        var country_select = $.cookie("selectedCountry");

        $('#region_id').load('students/get_regions/'+$(this).val());
    });
});

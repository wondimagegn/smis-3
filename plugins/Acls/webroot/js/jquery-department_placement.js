/*$(document).ready(function() {
     //alert('fuck');
    $('#department_id').change(function() {
       // alert('fuck');
       success: function(data){
            $('#departmen_id').val(data);
        }
    });
    
});
*/
/*function onSelectChange(){
		var dropdown = document.getElementById("department_id");
		var index = dropdown.selectedIndex;
		var ddVal = dropdown.options[index].value;
		var ddText = dropdown.options[index].text;
		
		if(ddVal != 0) {
			output = "You Selected " + ddText;
		}
		
		document.getElementById("summery").innerHTML =  $('#summery').load('/reservedPlaces/get_summery/'+ddVal);
}
*/

$(document).ready(function() {
         
        $('#academicyear').change(function() {
        
          $("#summery_student_result_category").empty().html('<img src="/img/busy.gif" class="displayed" />');
         
           $("#summery_student_result_category").empty().html('<img src="/img/busy.gif" class="displayed" />');
           $('#summery_student_result_category').load('/reservedPlaces/get_summeries/'+$(this).val());
          
       });
       
       $('#participating_academicyear').change(function () {
            $('#quotaparticiptingdepartment').load('/participatingDepartments/participating_departments/'+
            $(this).val());
       });
     
      /************Others College Checkbox*******************/
       $("#extra").css("display","none");
        // Add onclick handler to checkbox w/id checkme
       $("#other_college").click(function(){
        
        // If checked
        if ($("#other_college").is(":checked"))
        {
            //show the hidden div
            $("#extra").show("fast");
        }
        else
        {      
            //otherwise, hide it 
            $("#extra").hide("fast");
           
        }
     });
       $("#college_id").change(function() {

           $('#department_id').load('/participatingDepartments/participating_departments/'+$(this).val());
       
       });
   /*************************************************************/
    /************Result Placement Criteria Setting*******************/
       $("#prepartory_result").css("display","none");
        // Add onclick handler to checkbox w/id checkme
       $("#fresh_first_semster_result").click(function(){
                
                // If checked
                if ($("#other_college").is(":checked"))
                {
                    //show the hidden div
                    $("#prepartory_result").show("fast");
                }
                else
                {      
                    //otherwise, hide it 
                    $("#prepartory_result").hide("fast");
                   
                }
     });
   /******************Participating Department*****************************/
   
     $('#college_id').change(function() {
          //var that = jQuery(this).busy();

          $('#otherscollegestudnetcount').load('/participatingDepartments/get_summeries/'+$(this).val()); 
          // document.getElementById("summery").innerHTML="wonde";   
          
        
       });    
	/******************Department Year Level for excluded published course exams*****************************/
	$('#ajax_department').change(function() {
	
        $('#ajax_year_level').empty().html('<img src="/img/busy.gif" class="displayed" />');
		$('#ajax_year_level').load('/excludedPublishedCourseExams/get_year_level/'+$(this).val());
       });
	   
	/******************Department Year Level for class period course constraints*****************************/
	$('#ajax_department_class_period_course_constraints').change(function() {
	
        $('#ajax_year_level_class_period_course_constraints').empty().html('<img src="/img/busy.gif" class="displayed" />');
		$('#ajax_year_level_class_period_course_constraints').load('/classPeriodCourseConstraints/get_year_level/'+$(this).val());
       });
	/******************get periods from week day for class period course constraints**************************/
	$('#ajax_weekday').change(function() {
	
        $('#ajax_periods').empty().html('<img src="/img/busy.gif" class="displayed" />');
		$('#ajax_periods').load('/classPeriodCourseConstraints/get_periods/'+$(this).val());
       });
	/******************Department Year Level for add number of session in publishedcourse*****************************/
	$('#ajax_department_published_course').change(function() {
	
        $('#ajax_year_level_published_course').empty().html('<img src="/img/busy.gif" class="displayed" />');
		$('#ajax_year_level_published_course').load('/publishedCourses/get_year_level/'+$(this).val());
       });
	/******************PublishedCourse corse type session for add number of session in publishedcourse******************/
	$('#ajax_course').change(function() {
	
        $('#ajax_course_type_session').empty().html('<img src="/img/busy.gif" class="displayed" />');
		$('#ajax_course_type_session').load('/publishedCourses/get_course_type_session/'+$(this).val());
       }); 
   /******************Get year level of a given department in section controller******************/
	$('#ajax_department_id_section').change(function() {
	
        $('#ajax_year_level_section').empty().html('<img src="/img/busy.gif" class="displayed" />');
		$('#ajax_year_level_section').load('/sections/get_year_level/'+$(this).val());
       }); 
   	/******************Department Year Level for class room course constraints*****************************/
	$('#ajax_department_class_room_course_constraints').change(function() {
	
        $('#ajax_year_level_class_room_course_constraints').empty().html('<img src="/img/busy.gif" class="displayed" />');
		$('#ajax_year_level_class_room_course_constraints').load('/classRoomCourseConstraints/get_year_level/'+$(this).val());
       });
      /************Department Year Level for Instructor class period course constraints**********************/
	$('#ajax_department_instructor_class_period_course_constraints').change(function() {
	
        $('#ajax_year_level_instructor_class_period_course_constraints').empty().html('<img src="/img/busy.gif" class="displayed" />');
		$('#ajax_year_level_instructor_class_period_course_constraints').load('/instructorClassPeriodCourseConstraints/get_year_level/'+$(this).val());
       });
	/******************Department Year Level for course exam gap constraints**********************/
	$('#ajax_department_course_exam_gap_constraints').change(function() {
	
        $('#ajax_year_level_course_exam_gap_constraints').empty().html('<img src="/img/busy.gif" class="displayed" />');
		$('#ajax_year_level_course_exam_gap_constraints').load('/courseExamGapConstraints/get_year_level/'+$(this).val());
       });
   /******************Department Year Level for course exam constraints**********************/
	$('#ajax_department_course_exam_constraints').change(function() {
	
        $('#ajax_year_level_course_exam_constraints').empty().html('<img src="/img/busy.gif" class="displayed" />');
		$('#ajax_year_level_course_exam_constraints').load('/courseExamConstraints/get_year_level/'+$(this).val());
       });
   /******************Department Year Level for  Exam Room Course Constraints*********************/
	$('#ajax_department_exam_room_course_constraints').change(function() {
	
        $('#ajax_year_level_exam_room_course_constraints').empty().html('<img src="/img/busy.gif" class="displayed" />');
		$('#ajax_year_level_exam_room_course_constraints').load('/examRoomCourseConstraints/get_year_level/'+$(this).val());

       });
});

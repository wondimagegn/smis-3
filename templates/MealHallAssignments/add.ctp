<script type="text/javascript">
    var image = new Image();
    image.src = '/img/busy.gif';
    function confirmMealHallAssignment() {
    	if($('#id_meal_hall').val() != "") {
    		return confirm('Are you sure you want to assign selected students in selected meal hall?');
    	}
    }
    //Get departments
    function getCollege() {
        //serialize form data
        var campus = $("#ajax_campus_id").val();
        $("#ajax_college_id").attr('disabled', true);
        $("#ajax_college_id").empty().html('<img src="/img/busy.gif" class="displayed" >');
        $("#ajax_department_id").empty();
        $("#ajax_year_level_id").empty();
        //get form action
        var formUrl = '/meal_hall_assignments/get_colleges/'+campus;
        $.ajax({
            type: 'get',
            url: formUrl,
            data: campus,
         success: function(data,textStatus,xhr){
            $("#ajax_college_id").attr('disabled', false);
            $("#ajax_college_id").empty();
            $("#ajax_college_id").append(data);
         },
         error: function(xhr,textStatus,error){
                alert(textStatus);
         }
        });
        return false;
    }
//Get departments
function getDepartment() {
        //serialize form data
        var college = $("#ajax_college_id").val();
        $("#ajax_department_id").attr('disabled', true);
        $("#ajax_department_id").empty().html('<img src="/img/busy.gif" class="displayed" >');
        $("#ajax_year_level_id").attr('disabled', true);
        $("#ajax_year_level_id").empty();
        //get form action
        var formUrl = '/meal_hall_assignments/get_departments/'+college;
        $.ajax({
        type: 'get',
        url: formUrl,
        data: college,
        success: function(data,textStatus,xhr){
        $("#ajax_department_id").attr('disabled', false);
        $("#ajax_department_id").empty();
        $("#ajax_department_id").append(data);
        },
        error: function(xhr,textStatus,error){
        alert(textStatus);
        }
        });

        //get list of year levels from college
        var subUrl = '/meal_hall_assignments/get_year_levels/'+college;
        $.ajax({
        type: 'get',
        url: subUrl,
        data: college,
        success: function(data,textStatus,xhr){
        $("#ajax_year_level_id").attr('disabled', false);
        $("#ajax_year_level_id").empty();
        $("#ajax_year_level_id").append(data);
        },
        error: function(xhr,textStatus,error){
        alert(textStatus);
        }
        });
        return false;
    }
         //Get year level if department is selected
    function getDepartmentYearLevel() {
        //serialize form data
        var dept = $("#ajax_department_id").val()+'~'+$("#ajax_college_id").val();
        $("#ajax_year_level_id").attr('disabled', true);
        $("#ajax_year_level_id").empty().html('<img src="/img/busy.gif" class="displayed" >');
        //get form action
         var formUrl = 
         '/meal_hall_assignments/get_department_year_levels/'
         +dept;
         $.ajax({
                type: 'get',
                url: formUrl,
                data: dept,
                success: function(data,textStatus,xhr){
                    $("#ajax_year_level_id").attr('disabled', false);
                    $("#ajax_year_level_id").empty();
                    $("#ajax_year_level_id").append(data);
                },
                error: function(xhr,textStatus,error){
                        alert(textStatus);
                }
            });
           return false;
         } 
</script>
<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
              
<div class="mealHallAssignments form">
<?php echo $this->Form->create('MealHallAssignment');?>
<div class="smallheading"><?php echo __('Add Meal Hall Assignment'); ?></div>
<table cellpadding="0" cellspacing="0">
	<?php
		echo '<tr><td class="font"> Program</td>'; 
        echo '<td>'. $this->Form->input('program_id',array('label'=>false,'disabled'=>true, 'style'=>'width:150px')).'</td>'; 
		echo '<td class="font"> Program Type</td>'; 
        echo '<td>'. $this->Form->input('program_type_id',array('label'=>false,'selected'=>isset($selected_program_type)?$selected_program_type:"",'style'=>'width:150px','empty'=>"--Select Program Type--")).'</td>'; 
        echo '<td class="font"> Campus</td>'; 
        echo '<td>'. $this->Form->input('campus_id',array('label'=>false,'id'=>'ajax_campus_id', 'onchange'=>'getCollege()','selected'=>isset($selected_campus)?$selected_campus:"",'options'=>$campuses,'style'=>'width:150px','empty'=>"--Select Campus--")).'</td></tr>'; 
        echo '<tr><td class="font"> College</td>'; 
		echo '<td>'. $this->Form->input('college_id',array('label'=>false,'id'=>'ajax_college_id', 'onchange'=>'getDepartment()','style'=>'width:150px','selected'=>isset($selected_college)?$selected_college:"",'empty'=>"--Select College--")).'</td>'; 
		echo '<td class="font"> Department</td>'; 
		echo '<td>'. $this->Form->input('department_id',array('label'=>false,'id'=>'ajax_department_id', 'onchange'=>'getDepartmentYearLevel()','style'=>'width:150px','selected'=>isset($selected_department)?$selected_department:"",'options'=>$departments,'empty'=>'All')).'</td>';
		echo '<td class="font"> Year Level</td>'; 
        echo '<td>'. $this->Form->input('year_level_id',array('label'=>false, 'id'=>'ajax_year_level_id','selected'=>isset($selected_year_level)?$selected_year_level:"",'options'=>$yearLevels,'style'=>'width:150px','empty'=>'--Select Year Level--')).'</td></tr>';  
        echo '<tr><td colspan="6">'.$this->Form->input('academic_year',array('label' => 'Academic Year','id'=>'academicyear','type'=>'select','style'=>'width:150px','options'=>$current_and_next_acyear,'selected'=>isset($selected_academicyear)?$selected_academicyear:"",'empty'=>"--Select Academic Year--")).'</td></tr>';
        echo '<tr><td colspan="6">'. $this->Form->Submit('Search',array('name'=>'search','class'=>'tiny radius button bg-blue','div'=>false)).'</td></tr>'; 
		
	?> 
</table>
<?php if(!empty($admitted_unassigned_students) || !empty($non_admitted_unassigned_students)) { ?>
<table style="border: #CCC double 2px"><tr><td width="50%">
<table style="border: #CCC dashed 1px">
 <?php echo '<tr><td colspan="2" class="smallheading">Meal Hall  unassigned students Lists</td></tr>';
 	   echo '<tr><td> S.N<u>o</u></td>';
 	   echo '<td> Select/Unselect All <br/>'.$this->Form->checkbox(null, array('id' => 'select-all','checked'=>'', 'name'=>'select_all')).'</td></tr>';
 	   $count = 1;
 	foreach($admitted_unassigned_students as $admitted_unassigned_student){
		echo "<tr><td>".$count++."</td>";
 		echo "<td>".$this->Form->checkbox('Student.Selected.'.$admitted_unassigned_student['Student']['id'],
            array('class'=>'checkbox1')). "&nbsp;&nbsp;&nbsp;".$admitted_unassigned_student['Student']['full_name'].' ( ID:'.$admitted_unassigned_student['Student']['studentnumber'].')'."</td></tr>";
 	}
 	
 	foreach($non_admitted_unassigned_students as $non_admitted_unassigned_student){
 		 echo "<tr><td>".$count++."</td>";
 		 echo "<td>".$this->Form->checkbox('AcceptedStudent.Selected.'.$non_admitted_unassigned_student['AcceptedStudent']['id'],array('class'=>'checkbox1')). "&nbsp;&nbsp;&nbsp;".$non_admitted_unassigned_student['AcceptedStudent']['full_name'].' ( ID:'.$non_admitted_unassigned_student['AcceptedStudent']['studentnumber'].')'."</td></tr>";
 		 
 	
 	}
 ?>
</table></td>

<td width="50%"><table style="border: #CCC dashed 1px">
<?php 	
		echo '<tr><td class="smallheading"> Assign to Meal Hall:</td></tr>';
		echo '<tr><td class="font">'.$this->Form->input('meal_hall',array('label' => false, 'id'=>'id_meal_hall','type'=>'select', 'options'=>$mealHalls, 'empty'=>"--Select Meal Hall--")).'</td></tr>';
       echo '<tr><td>'. $this->Form->Submit('Assign',array('name'=>'assign','class'=>'tiny radius button bg-blue','div'=>false,'onClick'=>'return confirmMealHallAssignment()')).'</td></tr>';
?>
</table>
</td></tr></table>
<?php } else if(empty($beforesearch)){
	    echo "<div class='info-box info-message'> <span></span> There is no unassigned student in the selected criteria</div>";
} ?>
	<!-- <?php
		echo $this->Form->input('meal_hall_id');
		echo $this->Form->input('student_id');
		echo $this->Form->input('accepted_student_id');
		echo $this->Form->input('academic_year');
	?> -->
<?php echo $this->Form->end();?>
</div>
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row -->
      </div> <!-- end of box-body -->
</div><!-- end of box -->

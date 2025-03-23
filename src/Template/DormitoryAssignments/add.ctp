
<?php ?>
<script type="text/javascript">
var image = new Image();
image.src = '/img/busy.gif';
function confirmDormitoriesAssignment() {
	if($('#id_doritory_block').val() != "") {
		return confirm('Are you sure you want to assign selected students in selected dormitory block?');
	}
}
//Get departments
function getDepartment() {
            //serialize form data
            var college = $("#ajax_college_id").val();
$("#ajax_department_id").attr('disabled', true);
$("#ajax_department_id").empty().html('<img src="/img/busy.gif" class="displayed" >');
$("#ajax_year_level_id").empty();
//get form action
            var formUrl = '/dormitory_assignments/get_departments/'+college;
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
            return false;
 }
 //Get year level
function getYearLevel() {
            //serialize form data
            var dept = $("#ajax_department_id").val();
$("#ajax_year_level_id").attr('disabled', true);
$("#ajax_year_level_id").empty().html('<img src="/img/busy.gif" class="displayed" >');
//get form action
            var formUrl = '/dormitory_assignments/get_year_levels/'+dept;
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

  //Get section
function getSection() {
    
    $("#ajax_department_id").attr('disabled', true);
    $("#ajax_college_id").attr('disabled', true);
    $("#ajax_section_id").attr('disabled',true);
    $("#ajax_section_id").empty().html('<img src="/img/busy.gif" class="displayed" >');
    var formUrl = '/dormitory_assignments/get_section';
   
    $.ajax({
        type: 'post',
        url: formUrl,
        data: $("#DormitoryAssignmentForm").serialize(),
        success: function(data,textStatus,xhr){
           
            $("#ajax_department_id").attr('disabled', false);
            $("#ajax_college_id").attr('disabled', false);
            
            $("#ajax_section_id").attr('disabled', false);
            $("#ajax_section_id").empty();
            $("#ajax_section_id").append(data);
        },
        error: function(xhr,textStatus,error){
                alert(textStatus);
        }
    });
   
    return false;
 }
</script>
<div class="box">
     <div class="box-body">
       <div class="row">
        <div class="large-12 columns">

    <div class="dormitoryAssignments form">
    <?php echo $this->Form->create('DormitoryAssignment',array('id'=>'DormitoryAssignmentForm'));?>
    <div class="smallheading"><?php echo __('Students Dormitory Assignment'); ?></div>
    <?php if(isset($programs)) {?>
    <table cellpadding="0" cellspacing="0">
    	<?php
    		echo '<tr><td class="font"> Program</td>'; 
            echo '<td>'. $this->Form->input('program_id',array('label'=>false,'style'=>'width:150px', 'selected'=>isset($selected_program)?$selected_program:"",'empty'=>"--Select Program--")).'</td>'; 
    		echo '<td class="font"> Program Type</td>'; 
            echo '<td>'. $this->Form->input('program_type_id',array('label'=>false, 'selected'=>isset($selected_program_type)?$selected_program_type:"",'style'=>'width:150px','empty'=>"--Select Program Type--")).'</td>'; 
            echo '<td class="font"> Gender</td>'; 
            echo '<td>'. $this->Form->input('gender',array('label'=>false, 'selected'=>isset($selected_gender)?$selected_gender:"",'style'=>'width:150px','options'=>array('male'=>'Male','female'=>'Female') ,'empty'=>"--Select Gender--")).'</td></tr>'; 
            echo '<tr><td class="font"> College</td>'; 
    		echo '<td>'. $this->Form->input('college_id',array('label'=>false,'id'=>'ajax_college_id', 'onchange'=>'getDepartment()','style'=>'width:150px','selected'=>isset($selected_college)?$selected_college:"",'empty'=>"--Select College--")).'</td>'; 
    		echo '<td class="font"> Department</td>'; 
    		echo '<td>'. $this->Form->input('department_id',array('label'=>false,'id'=>'ajax_department_id', 'onchange'=>'getYearLevel()','style'=>'width:150px','selected'=>isset($selected_department)?$selected_department:"",'options'=>$departments,'empty'=>'--Select Department--')).'</td>';
    		echo '<td class="font"> Year Level</td>'; 
            echo '<td>'. $this->Form->input('year_level_id',array('label'=>false, 'id'=>'ajax_year_level_id','onchange'=>'getSection()','selected'=>isset($selected_year_level)?$selected_year_level:"",'options'=>$yearLevels,'style'=>'width:150px','empty'=>'--Select Year Level--')).'</td></tr>';  
             echo '<tr><td class="font">Section</td><td>'. $this->Form->input('section_id',array('label'=>false,'id'=>'ajax_section_id','options'=>$sections,'selected'=>isset($selected_section_id)?$selected_section_id:"",'style'=>'width:150px','empty'=>'All')).'</td></tr>';  

            echo '<tr><td colspan="6">'. $this->Form->Submit('Search',array('name'=>'search','class'=>'tiny radius button bg-blue','div'=>false)).'</td></tr>'; 
    		
    	?> 
    </table>
    <?php
    	}
     if(!empty($admitted_unassigned_students) || !empty($non_admitted_unassigned_students)) { ?>
    <table style="border: #CCC double 2px"><tr><td width="50%">
    <table style="border: #CCC dashed 1px">
     <?php echo '<tr><td colspan="2" class="smallheading">Dormitory unassigned students Lists</td></tr>';
     	   echo '<tr><td> S.N<u>o</u></td>';
     	   echo '<td> Select/Unselect All <br/>'.$this->Form->checkbox(null, array('id' => 'select-all','checked'=>'', 'name'=>'select_all')).'</td></tr>';
     	$count =1;
     	foreach($admitted_unassigned_students as $admitted_unassigned_student){
            $hasFxInLastStatus='';
    		$style='';
            if($admitted_unassigned_student['Student']['fxinlaststatus']=="Yes"){
                $hasFxInLastStatus=" has Fx in last registration ";
                $style='class="rejected"';
            } 
            echo "<tr $style ><td>".$count++."</td>";
     		echo "<td>".$this->Form->checkbox('Student.Selected.'.$admitted_unassigned_student['Student']['id'],array('class'=>'checkbox1')). "&nbsp;&nbsp;&nbsp;".$admitted_unassigned_student['Student']['full_name'].' ( ID:'.$admitted_unassigned_student['Student']['studentnumber'].')'.$hasFxInLastStatus."</td></tr>";
     	}
     	
     	foreach($non_admitted_unassigned_students as $non_admitted_unassigned_student){
     		 echo "<tr><td>".$count++."</td>";
     		 echo "<td>".$this->Form->checkbox('AcceptedStudent.Selected.'.$non_admitted_unassigned_student['AcceptedStudent']['id'],
                array('class'=>'checkbox1')). "&nbsp;&nbsp;&nbsp;".$non_admitted_unassigned_student['AcceptedStudent']['full_name'].' ( ID:'.$non_admitted_unassigned_student['AcceptedStudent']['studentnumber'].')'."</td></tr>";
     		 /*echo "<tr><td class='font'>".$this->Form->input('AcceptedStudent.Selected.'.$non_admitted_unassigned_student['AcceptedStudent']['id'],array('type'=>'checkbox','value'=>$non_admitted_unassigned_student['AcceptedStudent']['id'], 'label'=>$non_admitted_unassigned_student['AcceptedStudent']['full_name'].' ( ID:'.isset($non_admitted_unassigned_student['AcceptedStudent']['studentnumber'])?$non_admitted_unassigned_student['AcceptedStudent']['studentnumber']:"---".')'))."</td></tr>";*/
     	}
     ?>
    </table></td>

    <td width="50%"><table style="border: #CCC dashed 1px">
    <?php 	
    		echo '<tr><td class="smallheading"> Assign to Dormitory Block:</td></tr>';
    		echo '<tr><td class="font">'.$this->Form->input('dormitory_block',array('label' => false, 'id'=>'id_doritory_block','type'=>'select', 'options'=>$fine_formatted_dormitories, 'empty'=>"--Select Dormitory Block--")).'</td></tr>';
           echo '<tr><td>'. $this->Form->Submit('Assign',array('name'=>'assign','class'=>'tiny radius button bg-blue','div'=>false, 'onClick'=>'return confirmDormitoriesAssignment()')).'</td></tr>';
    ?>
    </table>
    </td></tr></table>
    <?php } else if(empty($beforesearch)){
    	    echo "<div class='info-box info-message'> <span></span> There is no unassigned student in the selected criteria</div>";
    } ?>

    <?php echo $this->Form->end();?> 
    </div>
</div>
</div>
</div>
</div>
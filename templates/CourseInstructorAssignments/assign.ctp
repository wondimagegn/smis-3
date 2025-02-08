<?php ?>
<script type="text/javascript">
//Sub Cat Combo 1
    function updateDepartmentCombo(id,published_course_id,isprimary, course_split_section_id) {
            //serialize form data
            var formData = $("#ajax_college_"+id).val();
$("#ajax_department_"+id).empty();
$("#ajax_department_"+id).attr('disabled', true);
$("#ajax_instructor_"+id).attr('disabled', true);
$("#ajax_instructor_"+id).empty();
//get form action
            var formUrl = '/course_instructor_assignments/get_department/'+formData;
            $.ajax({
                type: 'get',
                url: formUrl,
                data: formData,
                success: function(data,textStatus,xhr){
$("#ajax_department_"+id).attr('disabled', false);
$("#ajax_department_"+id).empty();
$("#ajax_department_"+id).append(data);

},
                error: function(xhr,textStatus,error){
                        alert(textStatus);
                }
});
return false;
}
//update instructor combo
function updateInstructorCombo(id,published_course_id,isprimary,course_split_section_id) {
            //serialize form data
            var subCat = $("#ajax_department_"+id).val() +'~'+$("#course_type_"+id).val()+'~'+published_course_id + '~'+isprimary +'~'+course_split_section_id;
$("#ajax_instructor_"+id).attr('disabled', true);
$("#ajax_instructor_"+id).empty();
//get form action
			
            var formUrl = '/course_instructor_assignments/assign_instructor/'+subCat;
            $.ajax({
                type: 'get',
                url: formUrl,
                data: subCat,
                success: function(data,textStatus,xhr){
$("#ajax_instructor_"+id).attr('disabled', false);
$("#ajax_instructor_"+id).empty();
$("#ajax_instructor_"+id).append(data);
                },
                error: function(xhr,textStatus,error){
                        alert(textStatus);
                }
            });
            return false;
        }
 //reset department combo
 function resetDepartmentCombo(id) {
            //serialize form data
            var subCat = $("#ajax_college_"+id).val();
$("#ajax_instructor_"+id).attr('disabled', true);
$("#ajax_instructor_"+id).empty();
$("#ajax_department_"+id).attr('disabled', false);
$("#ajax_department_"+id).empty();
//get form action
            var formUrl = '/course_instructor_assignments/reset_department/'+subCat;
            $.ajax({
                type: 'get',
                url: formUrl,
                data: subCat,
                success: function(data,textStatus,xhr){
$("#ajax_department_"+id).attr('disabled', false);
$("#ajax_department_"+id).empty();
$("#ajax_department_"+id).append(data);
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
             
<div class="courseInstructorAssignments form">
<?php echo $this->Form->create('CourseInstructorAssignment');  ?>

<p class="smallheading"><?php echo __('Instructor Assignment For Course'); ?> .</p>
<table cellspacing="0" cellpadding="0" class="fs14">
		<tr>
			<td style="width:12%">Academic Year:</td>
			<td style="width:38%"><?php
			
			echo $this->Form->input('academicyear',array('label' =>false,'type'=>'select',
		'options'=>$acyear_array_data,'empty'=>"--Select Academic Year--",'id' => 'AcadamicYear', 'label' => false, 'class' => 'fs14', 'style' => 'width:200px'));?> </td>
			<td style="width:12%">Semester:</td>
			<td style="width:38%"><?php echo $this->Form->input('semester', array('id' => 'Semester', 'class' => 'fs14', 'type' => 'select', 'style' => 'width:200px', 'label' => false, 'options' => array('I' => 'I', 'II' => 'II', 'III' => 'III'),'empty'=>'--select semester--')); ?></td>
			
		</tr>
		<tr>
			
		    <td style="width:12%">Program:</td>
			<td style="width:38%"><?php 
			 echo $this->Form->input('program_id',array('empty'=>"--Select Program--",
			 'label' => false, 'class' => 'fs14','style'=>'width:200px'));
			
			?></td>
			<td style="width:12%">Program Type:</td>
			<td style="width:38%"><?php 
			echo $this->Form->input('program_type_id',array('empty'=>"--Select Program Type--",
			'class' => 'fs14',  'style' => 'width:200px', 'label' => false));
			
			 ?></td>    
			
		</tr>
		<tr>
			<?php if ($role_id != ROLE_COLLEGE) { ?>
		    <td style="width:12%">YearLevel:</td>
			<td style="width:20%"><?php  echo $this->Form->input('year_level_id',array('empty'=>"--Select year Level--",'class' => 'fs14',  'style' => 'width:200px', 'label' => false)) ?></td>
			<td style="width:8%">&nbsp;</td>
			<td style="width:60%">&nbsp;</td>    
			<?php } ?>
		</tr>
		<tr>
		<td colspan='4'><?php echo $this->Form->Submit('Search',array('name'=>'search','class'=>'tiny radius button bg-blue','div'=>false)); 
		?></td>
		</tr>
</table>

<?php 
if (isset($sections_array)) { 
$index =0;
foreach($sections_array as $sak=>$sav){
$count = 1;
echo "<div class='smallheading'> Section : ".$sak."</div>";

echo '<table style="border: #CCC double 3px "><tr>';
echo '<th> No.</th>';
echo '<th> Course Title </th>';
echo '<th> Course Code </th>';
echo '<th> Credit </th>';
echo '<th> L T L </th></tr>';

asort($sav);
foreach($sav as $sk=>$sv){
$index =$index +1;
//debug($sk);
if(is_string($sk)){
echo '<tr><td colspan="5"><B><I> Split section name for this publish course: '.$sk .'</I></B></td></tr>';
echo '<tr><td class="font_color">'.$count++ .'</td>';
echo '<td class="font_color">'.$sv['course_title'].'</td>';
echo '<td class="font_color">'.$sv['course_code'].'</td>';
echo '<td class="font_color">'.$sv['credit'].'</td>';
echo '<td class="font_color">'.$sv['credit_detail'].'</td></tr>';

echo '<tr><td colspan="2">';
//for Primary Instructor
echo '<table style="border: #CCC dashed 2px">';
echo '<tr><td colspan="4" style="text-align: center;"><B>Primary Instructor </B></td></tr>';
echo '<tr><th style="border-right: #CCC solid 1px"> Full Name </th>';
echo '<th style="border-right: #CCC solid 1px"> Position </th>';
echo '<th style="border-right: #CCC solid 1px"> Assigned For </th>';
if($sv['grade_submitted'] == 0){
echo '<th style="border-right: #CCC solid 1px"> Action </th>'; 
}
echo '</tr>';
if(!empty($sv['assign_instructor'][1])){
foreach($sv['assign_instructor'][1] as $asvalue){
echo '<tr><td style="border-right: #CCC solid 1px">'. $asvalue['full_name'].'</td>';
echo '<td style="border-right: #CCC solid 1px">'.$asvalue['position'] .'</td>';
echo '<td style="border-right: #CCC solid 1px">'.$asvalue['course_type'] .'</td>';
if($sv['grade_submitted'] == 0){
echo '<td style="border-right: #CCC solid 1px">'. $this->Html->link(__('Delete'), array('controller'=>'course_instructor_assignments','action' => 'delete',$asvalue['CourseInstructorAssignment_id'],$sv['published_course_id']),null, sprintf(__('Are you sure you want to delete?'),$asvalue['CourseInstructorAssignment_id'],$sv['published_course_id'])).'</td>'; 
}
echo '</tr>';
}
}
$isprimary = 1;
$course_split_section_id = $sv['course_split_section_id'];
if(isset($thisdepartment)){
$reformat_departments=array();
$reformat_departments[$thisdepartment]=$departments[$thisdepartment];
unset($departments[$thisdepartment]);
foreach($departments as $id=>$name){
$reformat_departments[$id]=$name;
}
$departments = $reformat_departments;
}
if($sv['grade_submitted'] == 0){
echo '<tr><td>'.$this->Form->input('CourseInstructorAssignment.'.$index.'.type',
array('id'=>'course_type_'.$index,'label'=>'Type','type'=>'select','options'=>
$course_type_array[$sk],'onchange'=>'resetDepartmentCombo('.$index.')',
'selected'=>array_search(end($course_type_array[$sk]),$course_type_array[$sk]))).'</td>';
echo '<td>'. $this->Form->input('CourseInstructorAssignment.'.$index.'.college_id', array('id'=>'ajax_college_'.$index, 'onchange' => 'updateDepartmentCombo('.$index.','.$sv['published_course_id'].','.$isprimary.','.$course_split_section_id.')', 'type'=>'select','options'=>$colleges,'selected'=>isset($thiscollege)?$thiscollege:"")).'</td>'; 
echo '<td>'. $this->Form->input('CourseInstructorAssignment.'.$index.'.department_id',
array('id'=>'ajax_department_'.$index,'onchange' => 'updateInstructorCombo('.$index.','
.$sv['published_course_id'].','.$isprimary.','.$course_split_section_id.')',
'type'=>'select','options'=>$departments,'empty'=>'Select Dept.')).'</td></tr>'; 
}
echo '</table></td>';
//for Secondary Instructor
$index = $index +1;
echo '<td colspan="3"><table style="border: #CCC dashed 2px">';
echo '<tr><td colspan="4" style="text-align: center;"><B>Secondary Instructor </B></td></tr>';
echo '<tr><th style="border-right: #CCC solid 1px"> Full Name </th>';
echo '<th style="border-right: #CCC solid 1px"> Position </th>';
echo '<th style="border-right: #CCC solid 1px"> Assigned For</th>';
if($sv['grade_submitted'] == 0){
echo '<th style="border-right: #CCC solid 1px"> Action </th>'; 
}
echo '</tr>';
if(!empty($sv['assign_instructor'][0])){
foreach($sv['assign_instructor'][0] as $asvalue){
echo '<tr><td style="border-right: #CCC solid 1px">'. $asvalue['full_name'].'</td>';
echo '<td style="border-right: #CCC solid 1px">'.$asvalue['position'] .'</td>';
echo '<td style="border-right: #CCC solid 1px">'.$asvalue['course_type'] .'</td>';
if($sv['grade_submitted'] == 0){
echo '<td style="border-right: #CCC solid 1px">'. $this->Html->link(__('Delete'), array('controller'=>'course_instructor_assignments','action' => 'delete',$asvalue['CourseInstructorAssignment_id'],$sv['published_course_id']),null, sprintf(__('Are you sure you want to delete?'),$asvalue['CourseInstructorAssignment_id'],$sv['published_course_id'])).'</td>';
}
echo '</tr>';
}
}
$isprimary = 0;
$course_split_section_id = $sv['course_split_section_id'];
if(isset($thisdepartment)){
$reformat_departments=array();
$reformat_departments[$thisdepartment]=$departments[$thisdepartment];
unset($departments[$thisdepartment]);
foreach($departments as $id=>$name){
$reformat_departments[$id]=$name;
}
$departments = $reformat_departments;
}
if($sv['grade_submitted'] == 0){
echo '<tr><td>'.$this->Form->input('CourseInstructorAssignment.'.$index.'.type',
array('id'=>'course_type_'.$index,'label'=>'Type','type'=>'select','options'=>$course_type_array[$sk],'onchange'=>'resetDepartmentCombo('.$index.')','selected'=>array_search(end($course_type_array[$sk]),$course_type_array[$sk]))).'</td>';
echo '<td>'. $this->Form->input('CourseInstructorAssignment.'.$index.'.college_id',
array('id'=>'ajax_college_'.$index, 'onchange' => 'updateDepartmentCombo('.$index.','.$sv['published_course_id'].','.$isprimary.','.$course_split_section_id.')', 'type'=>'select','options'=>$colleges,'selected'=>isset($thiscollege)?$thiscollege:"")).'</td>'; 
echo '<td>'. $this->Form->input('CourseInstructorAssignment.'.$index.'.department_id',
array('id'=>'ajax_department_'.$index, 'onchange' => 'updateInstructorCombo('.$index.','.$sv['published_course_id'].','.$isprimary.','.$course_split_section_id.')', 'type'=>'select','options'=>$departments,'empty'=>'Select Dept.')).'</td></tr>'; 
}

echo '<tr><td colspan="4" id="ajax_instructor_'.($index).'"></td></tr>';
echo '</table>';
echo '</td></tr>';
} else {
echo '<tr><td class="font_color">' . $count++ .'</td>';
echo '<td class="font_color">' . $sv['course_title'] .'</td>';
echo '<td class="font_color">' . $sv['course_code'] .'</td>';
echo '<td class="font_color">' . $sv['credit'] .'</td>';
echo '<td class="font_color">' . $sv['credit_detail'] .'</td></tr>';
echo '<tr><td colspan="2">';
//for Primary Instructor
echo '<table style="border: #CCC dashed 2px">';
echo '<tr><td colspan="4" style="text-align: center;"><B>Primary Instructor </B></td></tr>';
echo '<tr><th style="border-right: #CCC solid 1px"> Full Name </th>';
echo '<th style="border-right: #CCC solid 1px"> Position </th>';
echo '<th style="border-right: #CCC solid 1px"> Assigned For</th>';
if($sv['grade_submitted'] == 0){
echo '<th style="border-right: #CCC solid 1px"> Action </th>';
}
echo '</tr>';
if(!empty($sv['assign_instructor'][1])){
foreach($sv['assign_instructor'][1] as $asvalue){
echo '<tr><td style="border-right: #CCC solid 1px">'. $asvalue['full_name'].'</td>';
echo '<td style="border-right: #CCC solid 1px">'.$asvalue['position'] .'</td>';
echo '<td style="border-right: #CCC solid 1px">'.$asvalue['course_type'] .'</td>';
if($sv['grade_submitted'] == 0){
echo '<td style="border-right: #CCC solid 1px">'. $this->Html->link(__('Delete'), array('controller'=>'course_instructor_assignments','action' => 'delete',$asvalue['CourseInstructorAssignment_id'],$sv['published_course_id']),null, sprintf(__('Are you sure you want to delete?'),	$asvalue['CourseInstructorAssignment_id'],$sv['published_course_id'])).'</td>';
echo '</tr>';
}
}
}
$isprimary =1;
$course_split_section_id = 0;
if(isset($thisdepartment)){
$reformat_departments=array();
$reformat_departments[$thisdepartment]=$departments[$thisdepartment];
unset($departments[$thisdepartment]);
foreach($departments as $id=>$name){
$reformat_departments[$id]=$name;
}
$departments = $reformat_departments;
}
if($sv['grade_submitted'] == 0){
echo '<tr><td>'.$this->Form->input('CourseInstructorAssignment.'.$index.'.type',array('id'=>'course_type_'.$index,'label'=>'Type','type'=>'select','options'=>$course_type_array[$sk],'onchange'=>'resetDepartmentCombo('.$index.')','selected'=>array_search(end($course_type_array[$sk]),$course_type_array[$sk])))."</td>";
echo '<td>'. $this->Form->input('CourseInstructorAssignment.'.$index.'.college_id',array('id'=>'ajax_college_'.$index,'onchange' => 'updateDepartmentCombo('.$index.','. $sv['published_course_id'].','.$isprimary.','.$course_split_section_id.')', 'type'=>'select','options'=>$colleges,'selected'=>isset($thiscollege)?$thiscollege:"")).'</td>'; 
echo '<td>'. $this->Form->input('CourseInstructorAssignment.'.$index.'.department_id',array('id'=>'ajax_department_'.$index, 'onchange' => 'updateInstructorCombo('.$index.','.$sv['published_course_id'].','.$isprimary.','.$course_split_section_id.')', 'type'=>'select','options'=>$departments,'empty'=>'Select Dept.')).'</td></tr>';	
}	
echo '<tr><td colspan="4" id="ajax_instructor_'.$index.'"></td></tr>';
echo '</table></td>';
//for Secondary Instructor
$index=$index+1;
echo '<td colspan="3"><table style="border: #CCC dashed 2px">';
echo '<tr><td colspan="4" style="text-align: center;"><B>Secondary Instructor </B></td></tr>';
echo '<tr><th style="border-right: #CCC solid 1px"> Full Name </th>';
echo '<th style="border-right: #CCC solid 1px"> Position </th>';
echo '<th style="border-right: #CCC solid 1px"> Assigned For</th>';
if($sv['grade_submitted'] == 0){
echo '<th style="border-right: #CCC solid 1px"> Action </th>'; 
}
echo '</tr>';
if(!empty($sv['assign_instructor'][0])){
foreach($sv['assign_instructor'][0] as $asvalue){
echo '<tr><td style="border-right: #CCC solid 1px">'. $asvalue['full_name'].'</td>';
echo '<td style="border-right: #CCC solid 1px">'.$asvalue['position'] .'</td>';
echo '<td style="border-right: #CCC solid 1px">'.$asvalue['course_type'] .'</td>';
if($sv['grade_submitted'] == 0){
echo '<td style="border-right: #CCC solid 1px">'. $this->Html->link(__('Delete'), array('controller'=>'course_instructor_assignments','action' => 'delete',$asvalue['CourseInstructorAssignment_id'],$sv['published_course_id']),null, sprintf(__('Are you sure you want to delete?'),$asvalue['CourseInstructorAssignment_id'],$sv['published_course_id'])).'</td>'; 
}
echo '</tr>';
}
}
$isprimary =0;
$course_split_section_id = 0;
if(isset($thisdepartment)){
$reformat_departments=array();
$reformat_departments[$thisdepartment]=$departments[$thisdepartment];
unset($departments[$thisdepartment]);
foreach($departments as $id=>$name){
$reformat_departments[$id]=$name;
}
$departments = $reformat_departments;
}
if($sv['grade_submitted'] == 0){
echo '<tr><td>'.$this->Form->input('CourseInstructorAssignment.'.$index.'.type',
array('id'=>'course_type_'.$index,'label'=>'Type','type'=>'select','options'=>$course_type_array[$sk],'onchange'=>'resetDepartmentCombo('.$index.')','selected'=>array_search(end($course_type_array[$sk]),$course_type_array[$sk]))).'</td>';
echo '<td>'. $this->Form->input('CourseInstructorAssignment.'.$index.'.college_id',
array('id'=>'ajax_college_'.$index, 'onchange' => 'updateDepartmentCombo('.$index.','.$sv['published_course_id'].','.$isprimary.','.$course_split_section_id.')', 'type'=>'select','options'=>$colleges,'selected'=>isset($thiscollege)?$thiscollege:"")).'</td>'; 
echo '<td>'. $this->Form->input('CourseInstructorAssignment.'.$index.'.department_id',
array('id'=>'ajax_department_'.$index, 'onchange' => 'updateInstructorCombo('.$index.','.$sv['published_course_id'].','.$isprimary.','.$course_split_section_id.')', 'type'=>'select','options'=>$departments,'empty'=>'Select Dept.')).'</td></tr>'; 
}	
echo '<tr><td colspan="4" id="ajax_instructor_'.$index.'"></td></tr>';

echo '</table></td>';
echo '</tr>';
}
//break;
}
echo '</table>';
}
}
echo $this->Form->end();
?>
</div>
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->

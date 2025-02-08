<?php 
$gradeList['NG']='NG';
$gradeList['Fx']='Fx';
?>
<script type="text/javascript">

function toggleView(obj) {
	if($('#c'+obj.id).css("display") == 'none')
		$('#i'+obj.id).attr("src", '/img/minus2.gif');
	else
		$('#i'+obj.id).attr("src", '/img/plus2.gif');
	$('#c'+obj.id).toggle("slow");
}
function toggleViewFullId(id) {
	if($('#'+id).css("display") == 'none') {
		$('#'+id+'Img').attr("src", '/img/minus2.gif');
		$('#'+id+'Txt').empty();
		$('#'+id+'Txt').append('Hide Filter');
		}
	else {
		$('#'+id+'Img').attr("src", '/img/plus2.gif');
		$('#'+id+'Txt').empty();
		$('#'+id+'Txt').append('Display Filter');
		}
	$('#'+id).toggle("slow");
}

</script>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
            
<div class="examResults index">
<?php echo $this->Form->create('ExamGradeChange');?>
<div class="smallheading"><?php echo __('Cancel auto grade conversion.');?></div>
<div onclick="toggleViewFullId('ListPublishedCourse')"><?php 
	if (!empty($examGradeChanges)) {
		echo $this->Html->image('plus2.gif', array('id' => 'ListPublishedCourseImg')); 
		?><span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListPublishedCourseTxt">Display Filter</span><?php
		}
	else {
		echo $this->Html->image('minus2.gif', array('id' => 'ListPublishedCourseImg')); 
		?><span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListPublishedCourseTxt">Hide Filter</span><?php
		}
?></div>
<div id="ListPublishedCourse" style="display:<?php echo (!empty($examGradeChanges) ? 'none' : 'display'); ?>">
<table cellspacing="0" cellpadding="0" class="fs14">
	<tr>
		<td style="width:15%">Academic Year:</td>
		<td style="width:25%"><?php echo $this->Form->input('acadamic_year', array('id' => 'AcadamicYear', 'label' => false, 'class' => 'fs14', 'style' => 'width:125px', 'type' => 'select', 'options' => $acyear_list, 'default' => (isset($selected_acadamic_year) ? $selected_acadamic_year : (isset($academic_year) && !empty($academic_year) ? $academic_year : $defaultacademicyear)))); ?></td>
		<td style="width:15%">Semester:</td>
		<td style="width:25%"><?php echo $this->Form->input('semester', array('id' => 'Semester', 'class' => 'fs14', 'type' => 'select', 'style' => 'width:125px', 'label' => false, 'options' => array('I' => 'I', 'II' => 'II', 'III' => 'III'))); ?></td>
	</tr>
	<tr>
		<td style="width:15%">Program:</td>
		<td style="width:25%"><?php echo $this->Form->input('program_id', array('id' => 'Program', 
		'class' => 'fs14', 'label' => false, 'type' => 'select', 'options' => $programs, 
		'default' => isset($program_id) ? $program_id :"" )); ?></td>
		<td style="width:15%">Program Type:</td>
		<td style="width:25%"><?php echo $this->Form->input('program_type_id', array('id' => 'ProgramType', 'class' => 'fs14', 'label' => false, 'type' => 'select','style'=>'width:150px','options' => $program_types)); ?></td>
	</tr>

   <tr>
		<td style="width:15%">Department:</td>
		<td style="width:25%">
         <?php 
	        if (!empty($departments)) {
	            echo $this->Form->input('department_id',array('label'=>false,'style'=>'width:150px')); 
	        } else if (!empty($colleges)) {
	           echo $this->Form->input('college_id',array('label'=>false));    
	        }
          ?>
        </td>
		
		<td style="width:15%">Grade</td>
		<td style="width:25%">
		<?php 
		 echo $this->Form->input('grade', array('label' => false,'type'=>'select','options'=>$gradeList,'empty'=>'select'));
		?>
		</td>
	</tr>

	
	<tr>
		<td colspan="4">
		<?php echo $this->Form->submit(__('List Published Courses'), array('name' => 'listPublishedCourses', 'div' => false,'class'=>'tiny radius button bg-blue')); ?>
		</td>
	</tr>
</table>
</div>
<?php
if(isset($examGradeChanges) && !empty($examGradeChanges)) {
$st_count=0;
foreach($examGradeChanges as $td=>$studList){
	$tableHeadDetail=explode('~',$td);
?>
<table>
	<tr>
		<td colspan="8">
			<table style="margin:0px; border:dashed 2px #CCCCCC">
				<tr class="fs13">
					<td style="font-weight:bold; width:4%">College:</td>
					<td style="width:30%"><?php echo $tableHeadDetail[0]; ?></td>
					<td style="font-weight:bold; width:5%">Department:</td>
					<td style="width:20%"><?php echo $tableHeadDetail[1]; ?></td>
					<td style="font-weight:bold; width:5%">Program:</td>
					<td style="width:13%"><?php echo $tableHeadDetail[2]; ?></td>
					<td style="font-weight:bold; width:13%">Program Type:</td>
					<td style="width:10%"><?php echo $tableHeadDetail[3]; ?></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
	  <th style="width:10%"><?php  echo $this->Form->input('select_all', array('type' => 'checkbox', 'id' => 'select-all', 'div' => false, 'label' => false)); ?>
	    Select All</th>
	    <th style="width:15%">Student Name</th>
		<th style="width:10%">ID</th>
		<th style="width:25%">Course</th>
		<th style="width:10%">Previous Grade</th>
		<th style="width:10%">Converted Grade</th>
		<th style="width:15%">Conversion Date</th>
		
	</tr>
	<?php
	foreach($studList as $key => $grade_change) {
	?>
	  <tr>
	    <td>
	    <?php 
	      echo $this->Form->input('ExamGradeChange.'.$st_count.'.gp', array('type' => 'checkbox', 'label' => false, 'id' => 'ExamGradeChange'.$st_count,'class'=>'checkbox1'));
	      
	      echo $this->Form->input('ExamGradeChange.'.$st_count.'.id', array('type' => 'hidden', 'value' => $grade_change['ExamGradeChange']['id']));
	      
	      
	      
	      ?>
	    </td>
	    
		<td><?php echo $grade_change['Student']['first_name'].' '.$grade_change['Student']['middle_name'].' '.$grade_change['Student']['last_name']; ?></td>
		<td><?php echo $grade_change['Student']['studentnumber']; ?></td>
		<td><?php echo $grade_change['Course']['course_title'].' ('.$grade_change['Course']['course_code'].')'; ?></td>
		<td><?php echo $grade_change['ExamGrade']['grade']; ?></td>
		<td><?php echo $grade_change['ExamGradeChange']['grade']; ?></td>
		<td><?php echo $this->Format->humanize_date($grade_change['ExamGradeChange']['created']); ?></td>
	</tr>
	
	<?php 
	$st_count++;
	} ?>
	</table>
<?php
	}
	 echo $this->Form->submit(__('Cancel Auto Converted Grade'), array('name' => 'cancelAutoGrade', 'div' => false,'class'=>'tiny radius button bg-blue')); 
 }
?>

<?php echo $this->Form->end(); ?>
</div>
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row -->
      </div> <!-- end of box-body -->
</div><!-- end of box -->

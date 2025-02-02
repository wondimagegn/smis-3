<?php ?>
<script type="text/javascript">
function validate(evt) {
  var theEvent = evt || window.event;
  var key = theEvent.keyCode || theEvent.which;
  key = String.fromCharCode( key );
  var regex = /[0-9]|\./;
  if(!regex.test(key) ) {
    theEvent.returnValue = false;
    if(theEvent.preventDefault) theEvent.preventDefault();
  }
}

function check_uncheck(id) {
	var checked = ($('#'+id).attr("checked") == 'checked' ? true : false);
	for(i = 1; i <= number_of_students; i++) {
		$('#Student'+i).attr("checked", checked);
	}
}
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

//this toggles the visibility of our parent permission fields depending on the current selected value of the underAge field
function toggleFields(id)
{
	//alert($("#"+id).val());
    if ($("#"+id).val() == 'attrition_rate') {
	
     	$(".notVisibleOnAttritionRate ").hide();
    } else if($("#"+id).val() == 'top_students') {
       // $("#parentPermission").hide();
	$(".notVisibleOnAttritionRate ").show();

    } else if ($("#"+id).val() == 'grade_change_statistics') {
	$(".gradeChangeState").hide();

    }
}

</script>
<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
            	
<div class="form">
<?php echo $this->Form->create('ContinuousAssessment');?>
<p class="fs16">
             <strong> Important Note: </strong> 
               This tool will help you to view some predefined  continouse assessement reports. By providing some criteria you can find report.
</p>
<div onclick="toggleViewFullId('ListPublishedCourse')"><?php 
	if (!empty($instructor_lists) || !empty($instructor_lists) 
|| !empty($instructor_lists)) {
		echo $this->Html->image('plus2.gif', array('id' => 'ListPublishedCourseImg')); 
		?><span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListPublishedCourseTxt">Display Filter</span><?php
	}
	else {
		echo $this->Html->image('minus2.gif', array('id' => 'ListPublishedCourseImg')); 
		?><span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListPublishedCourseTxt">Hide Filter</span><?php
	}
?>
</div>
<div id="ListPublishedCourse"  style="display:<?php echo (!empty($instructor_lists) ? 'none' : 'display'); ?>">

<table cellspacing="0" cellpadding="0" class="fs13">
	
	<tr>
		<td style="width:10%">Department:</td>
		<td style="width:25%"><?php echo $this->Form->input('department_id', array('id' => 'ProgramType', 'class' => 'fs14','style' => 'width:300px', 'label' => false, 'type' => 'select', 'options' => $departments, 'default' => $default_department_id)); ?></td>
		<td style="width:11%">Year Level:</td>
		<td style="width:53%"><?php echo $this->Form->input('year_level_id', array('id' => 'YearLevel', 'class' => 'fs13', 'label' => false, 'type' => 'select', 'options' => $yearLevels, 'default' => $default_year_level_id)); ?></td>
	</tr>

	<tr>
		<td style="width:15%">Academic Year:</td>
		<td style="width:20%"><?php echo $this->Form->input('acadamic_year', array('id' => 'AcadamicYear', 'label' => false, 'class' => 'fs14', 'style' => 'width:125px', 'type' => 'select', 'options' => $acyear_array_data, 'default' => (isset($academic_year_selected) ? $academic_year_selected : $defaultacademicyear))); ?></td>
		<td style="width:15%">Semester:</td>
		<td style="width:50%"><?php echo $this->Form->input('semester', array('id' => 'Semester', 'class' => 'fs14', 'type' => 'select', 'style' => 'width:125px', 'label' => false, 'options' => array('I' => 'I', 'II' => 'II', 'III' => 'III'), 'default' => (isset($semester_selected) ? $semester_selected : false))); ?></td>
	</tr>

	<tr>
		<td style="width:10%">Program:</td>
		<td style="width:25%"><?php echo $this->Form->input('program_id', array('id' => 'Program', 'class' => 'fs14', 'label' => false, 'type' => 'select', 'options' => $programs, 'default' => $default_program_id)); ?></td>
		<td style="width:12%">Program Type:</td>
		<td style="width:53%"><?php echo $this->Form->input('program_type_id', array('id' => 'ProgramType', 'class' => 'fs14', 'label' => false, 'type' => 'select', 'options' => $program_types, 'default' => $default_program_type_id)); ?></td>
	</tr>

	<tr class="gradeChangeState">
		<td> Number Continouse Assessement:</td>
		<td>
				<?php echo $this->Form->input('numberofassessement', array('id' => 'Top', 
		'class' => 'fs13', 'label' => false,'onkeypress'=>'validate(event)' )); ?>
		</td>
			<td> Gender:</td>
		<td><?php echo $this->Form->input('gender', array('id' => 'Gender', 
		'class' => 'fs14', 'type' => 'select', 'style' => 'width:125px', 'label' => false,
		 'options' => array('all' => 'All', 'female' => 'Female', 'male' => 'Male'))); ?></td>
   </tr>
	<tr>
		<td colspan="6">
		<?php echo $this->Form->submit(__('Get Report', true), array('name' => 'getReport', 'div' => false,'class'=>'tiny radius button bg-blue')); ?>
		</td>
	</tr>
	
</table>
</div>
<?php 	
echo $this->Form->end(); ?>
</div>

<?php 
	
  if(isset($instructor_lists) && !empty($instructor_lists)) {
	  //debug($instructor_lists);
      echo $this->element('continous_assessement/instructor_list');
  } else {
echo '<div id="flashMessage" class="info-box info-message"><span></span>There is no report in the selected criteria</div>';
  }
?>

	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->

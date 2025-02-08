<?php echo $this->Form->create('Student');?>
<div class="box" ng-app="idReport" >
     <div class="box-body" ng-controller="reportCntrl">
       <div class="row">
	  <div class="large-12 columns">
		 
<div class="form">

<p class="fs16">
             <strong> Important Note: </strong> 
               This tool will help you to get some predefined reports about ID printing. By providing some criteria you can find report.
</p>
<div onclick="toggleViewFullId('ListPublishedCourse')"><?php 
	if (!empty($attrationRate) || !empty($student_lists) 
|| !empty($student_lists) || !empty($resultBy)) {
		echo $this->Html->image('plus2.gif', array('id' => 'ListPublishedCourseImg')); 
		?><span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListPublishedCourseTxt">Display Filter</span><?php
	}
	else {
		echo $this->Html->image('minus2.gif', array('id' => 'ListPublishedCourseImg')); 
		?><span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListPublishedCourseTxt">Hide Filter</span><?php
	}
?>
</div>
<div id="ListPublishedCourse" 
style="display:<?php echo (!empty($idNotPrintedStudentList['IDPrintingList']) || !empty($idNotPrintedStudentList['distributionIDPrintingCount']) || !empty($distributionIDPrintingCount['distributionIDPrintingCount'])  ? 'none' : 'display'); ?>">

<table cellspacing="0" cellpadding="0" class="fs13" >
	<tr>
		<td> Report Type:</td>
		<td><?php echo $this->Form->input('report_type', array('label'=> false, 'type' => 'select', 'style' => 'width:200px', 'div' => false,'empty'=>'Select Report Type','onchange' => 'toggleFields("reportType")', 'options' => $report_type_options,'id'=>'reportType')); ?> <span style="font-size:11px"></span></td>

		 <?php 
		 if(isset($this->data['Student']['report_type'])
&& $this->data['Student']['report_type']=='IDPrintingCount') { ?>
		<td>Printed Count:</td>
		<td><?php echo $this->Form->input('printed_count', array('label'=> false, 'type' => 'number', 'style' => 'width:200px', 'div' => false)); ?> 
<span style="font-size:11px"></span></td>

	     <?php } else { ?>
          <td class="notVisibleOnPrintCount" style="display:none">Printed Count:</td>
		  <td class="notVisibleOnPrintCount" style="display:none;"><?php echo $this->Form->input('printed_count', array('label'=> false, 'type' => 'number', 'style' => 'width:200px', 'div' => false)); ?> 
<span style="font-size:11px"></span></td>
	     <?php } ?>
	</tr>
	
	<tr>
		<td style="width:10%">Department:</td>
		<td style="width:25%"><?php echo $this->Form->input('department_id', array('id' => 'ProgramType', 'class' => 'fs14','style' => 'width:300px', 'label' => false, 'type' => 'select', 'options' => $departments, 'default' => $default_department_id)); ?></td>
		<td style="width:11%">Year Level:</td>
		<td style="width:53%"><?php echo $this->Form->input('year_level_id', array('id' => 'YearLevel', 'class' => 'fs13', 'label' => false, 'type' => 'select', 'options' => $yearLevels, 'default' => $default_year_level_id)); ?></td>
	</tr>


	<tr>
		<td style="width:15%">Academic Year:</td>
		<td style="width:20%"><?php echo $this->Form->input('acadamic_year', array('id' => 'AcadamicYear', 'label' => false, 'class' => 'fs14', 'style' => 'width:125px', 'type' => 'select', 'options' => $acyear_array_data, 'default' => (isset($academic_year_selected) ? $academic_year_selected : $defaultacademicyear))); ?></td>

		<td> Gender:</td>
		<td><?php echo $this->Form->input('gender', array('id' => 'Gender', 
		'class' => 'fs14', 'type' => 'select', 'style' => 'width:125px', 'label' => false,
		 'options' => array('all' => 'All', 'female' => 'Female', 'male' => 'Male'))); ?></td>
		
	</tr>

	<tr>
		<td style="width:10%">Program:</td>
		<td style="width:25%"><?php echo $this->Form->input('program_id', array('id' => 'Program', 'class' => 'fs14', 'label' => false, 'type' => 'select', 'options' => $programs, 'default' => $default_program_id)); ?></td>
		<td style="width:12%">Program Type:</td>
		<td style="width:53%"><?php echo $this->Form->input('program_type_id', array('id' => 'ProgramType', 'class' => 'fs14', 'label' => false, 'type' => 'select', 'options' => $program_types, 'default' => $default_program_type_id)); ?></td>
	</tr>


	<tr>
		<td colspan="2">
		<?php echo $this->Form->submit(__('Get Report', true), array('name' => 'getReport', 'div' => false,'class'=>'tiny radius button bg-blue')); ?>
		</td>

		<td colspan="2" >
		<?php echo $this->Form->submit(__('Export Report Excel', true), array('name' => 'getReportExcel', 'div' => false,'class'=>'tiny radius button bg-blue','onclick'=>'')); ?>
		</td>

	</tr>
	
</table>
</div>

<?php 	

echo $this->Form->end(); ?>
</div>

<?php 
  if(isset($distributionIDPrintingCount) && !empty($distributionIDPrintingCount)) {
      echo $this->element('reports/id_printing_stats');
  } else if (isset($idNotPrintedStudentList['IDPrintingList']) && !empty($idNotPrintedStudentList['IDPrintingList'])) {
       echo $this->element('reports/id_not_issued_student_list');
  } else {
      echo '<div id="flashMessage" class="info-box info-message"><span></span>There is no report in the selected criteria</div>';
  }
?>            
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row -->
	

    </div> <!-- end of box-body -->
	
</div><!-- end of box -->

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

//this toggles the visibility of our parent permission fields depending on the current selected value of the underAge field
function toggleFields(id)
{
   
    if ($("#"+id).val() == 'IDPrintingCount') {
     	$(".notVisibleOnPrintCount ").show();
    } else if ($("#"+id).val() == 'NOTPrinttedIDCount') {
		 $(".notVisibleOnPrintCount").hide();
    } else {
        $(".notVisibleOnPrintCount").hide();
	
    }
  
}

</script>

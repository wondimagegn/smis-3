<?php ?>
<div class="box" ng-app="generalReport" >
     <div class="box-body" ng-controller="reportCntrl">
       <div class="row">
	  <div class="large-12 columns">
		 
<div class="form">
<?php echo $this->Form->create('Report');?>
<p class="fs16">
             <strong> Important Note: </strong> 
               This tool will help you to get some predefined reports. By providing some criteria you can find report.
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
style="display:<?php echo (!empty($student_lists)  || !empty($resultBy) || !empty($attrationRate) || !empty($dismissedList) || !empty($resultBy) ||!empty($gradeChangeLists) || !empty($gradeSubmissionDelay) || !empty($distributionStatistics) || !empty($top) || !empty($studentList) || 
!empty($distributionStatisticsStatus) ||
!empty($distributionStatsGraduate) || !empty($distributionStatsLetterGrade['distributionLetterGrade'])  ? 'none' : 'display'); ?>">

<table cellspacing="0" cellpadding="0" class="fs13" >
	<tr>
		<td> Report Type:</td>
		<td><?php echo $this->Form->input('report_type', array('label'=> false, 'type' => 'select', 'style' => 'width:200px', 'div' => false,'empty'=>'Select Report Type','onchange' => 'toggleFields("reportType")', 'options' => $report_type_options,'id'=>'reportType')); ?> <span style="font-size:11px"></span></td>
	     <?php if(isset($this->data['Report']['report_type'])
&& $this->data['Report']['report_type']=='top_students') { ?>
		<td 
style="width:11%">Top:</td>
		<td   style="width:53%;">
<?php echo $this->Form->input('top', array('id' => 'Top', 
		'class' => 'fs13','type'=>'number', 'label' => false,'onkeypress'=>'validate(event)' )); ?>

</td>

	     <?php } else { ?>
<td class="notVisibleOnAttritionRate" 
style="display:none;width:11%">Top:</td>
		<td  class="notVisibleOnAttritionRate" style="display:none;width:53%;">
<?php echo $this->Form->input('top', array('id' => 'Top', 
		'class' => 'fs13', 'label' => false,'onkeypress'=>'validate(event)' )); ?>

</td>

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
		<td> Region:</td>
		<td><?php echo $this->Form->input('region_id', array('label'=> false, 'type' => 'select', 'style' => 'width:200px', 'div' => false, 'default' => $default_region_id, 'options' => $regions)); ?> 
<span style="font-size:11px"></span></td>

		<td> Gender:</td>
		<td><?php echo $this->Form->input('gender', array('id' => 'Gender', 
		'class' => 'fs14', 'type' => 'select', 'style' => 'width:125px', 'label' => false,
		 'options' => array('all' => 'All', 'female' => 'Female', 'male' => 'Male'))); ?></td>
   </tr>
	<tr class="academicStatus notVisibleOnAttritionRate"  style="<?php isset($showFromToBlock) && !empty($showFromToBlock) ? 'display:block;':'display:none;' ?>">
		<td style="width:10%">Academic Status</td>
		<td style="width:25%">
<?php echo $this->Form->input('academic_status_id',array('label'=>false,'empty'=>'All')); ?>
        </td>
		<td style="width:10%">SGPA/CGPA</td>
		<td style="width:25%">
<?php echo $this->Form->input('gpa', array('label' => false,
		 'options' => array('sgpa' => 'SGPA', 'cgpa' => 'CGPA'))); ?>
        </td>
	</tr>
    <tr class="academicStatus notVisibleOnAttritionRate"  style="<?php isset($showFromToBlock) && !empty($showFromToBlock) ? 'display:block;':'display:none;' ?>">
		<td style="width:10%">From</td>
		<td style="width:25%">
<?php echo $this->Form->input('from', array(
		'class' => 'fs13','maxlength'=>1, 'label' => false,'type'=>'number','min'=>0,'max'=>4)); ?>
        </td>
		<td style="width:10%">To</td>
		<td style="width:25%">
<?php echo $this->Form->input('to', 
array('id' => 'Top', 'class' => 'fs13','type'=>'number','maxlength'=>1, 'label' => false,'min'=>0,'max'=>4)); ?>
        </td>
	</tr>
         <?php 

if(isset($this->data['Report']['report_type'])
&& ($this->data['Report']['report_type']=='distributionStatsGender' || $this->data['Report']['report_type']=='distributionStatsGenderAndRegion' 
|| $this->data['Report']['report_type']=='distributionStatsStatus' || $this->data['Report']['report_type']=='distributionStatsGraduate' )) { ?>
        
         <tr>
		
		<td class="visibleOnDistribution" colspan="2">Graph Type:</td>
		<td  class="visibleOnDistribution" colspan="2">
                 <?php echo $this->Form->input('graph_type', array('label'=> false, 'type' => 'select', 'div' => false, 'options' =>$graph_type)); ?>
		</td>
	</tr>
	<?php } else { ?>
               <tr>

		<td class="visibleOnDistribution" colspan="2" style="display:none;">Graph Type:</td>
		<td  class="visibleOnDistribution" colspan="2" style="display:none">
                 <?php echo $this->Form->input('graph_type', array('label'=> false, 'type' => 'select', 'div' => false, 'options' =>$graph_type)); ?>
		</td>
	</tr>
         
	<?php } ?>

	

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
  if(isset($attrationRate) && !empty($attrationRate)) {
      echo $this->element('reports/attration_rate_stats');
  } else if(isset($top) && !empty($top)) {
	 echo $this->element('reports/top_student');
  } else if(isset($dismissedList) && !empty($dismissedList)) {
	 echo $this->element('reports/dismissed_list');
  } else if(isset($resultBy) && !empty($resultBy)) { 

	 echo $this->element('reports/result_list');
  } else if (isset($gradeChangeLists) && !empty($gradeChangeLists)) {
	 echo $this->element('reports/grade_change_list');
  } else if (isset($gradeSubmissionDelay) && !empty($gradeSubmissionDelay)) {
	 echo $this->element('reports/grade_submission_delay_list');
  } else if(isset($studentList) && !empty($studentList)) {
  	 echo $this->element('reports/list_bygrade_student');

  } else if (isset($distributionStatistics) && !empty($distributionStatistics)) {
      if(!empty($distributionStatistics)) {
      	
         echo $this->element('reports/distribution_stat');
      } 
  } else if (!empty($distributionStatisticsStatus['distributionByStatusYearLevel'])) {
  	 echo $this->element('reports/distribution_status_stat');
  }  else if (!empty($distributionStatisticsStatus['distributionByStatusYearLevel'])) {
  	 echo $this->element('reports/distribution_status_stat');
  } else if (!empty($distributionStatsLetterGrade['distributionLetterGrade'])) {
  	 echo $this->element('reports/distribution_stats_letter_grade');
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
   
    if ($("#"+id).val() == 'attrition_rate') {
	
     	$(".notVisibleOnAttritionRate ").hide();
    } else if(($('#'+id).val()=='distributionStatsGender' || $('#'+id).val()=='distributionStatsGenderAndRegion'
|| $('#'+id).val()=='distributionStatsStatus' || $('#'+id).val()=='distributionStatsGraduate')) { 
	
	$('.visibleOnDistribution').show();
	$('.academicStatus').hide();

    } else if($("#"+id).val() == 'top_students') {
       
		$(".notVisibleOnAttritionRate ").show();

    } else if ($("#"+id).val() == 'grade_change_statistics') {
		$(".gradeChangeState").hide();

    } else if ($("#"+id).val() == 'academic_status_range') {
		 $(".academicStatus").show();
    } else {
        $(".academicStatus").hide();
	$(".notVisibleOnAttritionRate ").hide();
        $('.visibleOnDistribution').hide();
    }
  
}

</script>

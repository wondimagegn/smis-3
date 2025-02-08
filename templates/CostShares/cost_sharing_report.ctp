<?php ?>
<div class="box" >
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
		 
<div class="form">
<?php echo $this->Form->create('Costshare');?>
<p class="fs16">
             <strong> Important Note: </strong> 
               This tool will help you to get some predefined reports. By providing some criteria you can find report.
</p>
<div onclick="toggleViewFullId('ListCostShare')"><?php 
	if (!empty($attrationRate) || !empty($student_lists) 
|| !empty($student_lists) || !empty($resultBy)) {
		echo $this->Html->image('plus2.gif', array('id' => 'ListCostShareImg')); 
		?><span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListCostShareTxt">Display Filter</span><?php
	}
	else {
		echo $this->Html->image('minus2.gif', array('id' => 'ListCostShareImg')); 
		?><span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListCostShareTxt">Hide Filter</span><?php
	}
?>
</div>
<div id="ListCostShare" 
style="display:<?php echo (!empty($costSharingForMoE)    ? 'none' : 'display'); ?>">

<table cellspacing="0" cellpadding="0" class="fs13" >
    <tr>
		<td> Report Type:</td>
		<td><?php echo $this->Form->input('Report.report_type', array('label'=> false, 'type' => 'select', 'style' => 'width:200px', 'div' => false,'empty'=>'Select Report Type','onchange' => 'toggleFields("reportType")', 'options' => $report_type_options,'id'=>'reportType')); ?> <span style="font-size:11px"></span></td>

		
	</tr>
	
	<tr>
		<td style="width:10%">Department:</td>
		<td style="width:25%"><?php echo $this->Form->input('Report.department_id', array('id' => 'ProgramType', 'class' => 'fs14', 'label' => false, 'type' => 'select', 'options' => $departments)); ?></td>
		 <td style="width:25%"> <span id="AdmissionAY" style="display: none;">Admission AY</span> <span id="GraduatedAY">Graduated AY</span></td>
		 <td>
		 <?php echo $this->Form->input('Report.graduated_academic_year', array('id' => 'AcadamicYear', 'label' => false, 'class' => 'fs14', 'style' => 'width:125px', 'type' => 'select', 'options' => $acyear_array_data, 'default' => (isset($academic_year_selected) ? $academic_year_selected : $defaultacademicyear))); ?>
		 </td>

	    </tr>
		<tr>
			<td style="width:10%">Program:</td>
			<td style="width:25%"><?php echo $this->Form->input('Report.program_id', array('id' => 'Program', 'class' => 'fs14', 'label' => false, 'type' => 'select', 'options' => $programs)); ?></td>
			<td style="width:12%">Program Type:</td>
			<td style="width:53%"><?php echo $this->Form->input('Report.program_type_id', array('id' => 'ProgramType', 'class' => 'fs14', 'label' => false, 'type' => 'select', 'options' => $program_types)); ?></td>
		</tr>
	  

	<tr>
		<td style="width:10%">Name:</td>
		<td style="width:25%"><?php echo $this->Form->input('Report.name', array('id' => 'name', 'class' => 'fs14', 'label' => false)); ?></td>
		<td style="width:10%">Student ID:</td>
		<td style="width:25%"><?php echo $this->Form->input('Report.studentnumber', array('id' => 'studentnumber', 'class' => 'fs14', 'label' => false)); ?></td>
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
  if(isset($costSharingForMoE['StudentList']) && !empty($costSharingForMoE['StudentList'])) {
      echo $this->element('reports/cost_sharing_government_report');
  } else if(isset($costSharingForInternal['StudentList']) &&
  	!empty($costSharingForInternal['StudentList'])){
  	echo $this->element('reports/cost_sharing_internal_report');
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
   
    if ($("#"+id).val() == 'completedCostSharingAgreemnt')
    {
     	$("#AdmissionAY ").hide();
     	$("#GraduatedAY").show();
    }else if($("#"+id).val() == 'incompleteCostSharing'){
    	
    	$("#AdmissionAY").show();
    	$("#GraduatedAY").hide();
    } else {
        $("#AdmissionAY").hide();
        $("#GraduatedAY").show();
	 } 
}

</script>

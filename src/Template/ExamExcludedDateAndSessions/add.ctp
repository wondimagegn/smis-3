<?php ?>
<script type="text/javascript">
var image = new Image();
image.src = '/img/busy.gif';
//Get Class Period from a given week day
function getclassperiod() {
            //serialize form data
            var subCat = $("#ajax_exam_period").val();
$("#exam_periods_details").attr('disabled', true);
$("#exam_periods_details").empty().html('<img src="/img/busy.gif" class="displayed" >');
//get form action
            var formUrl = '/exam_excluded_date_and_sessions/get_exam_periods_details/'+subCat;
            $.ajax({
                type: 'get',
                url: formUrl,
                data: subCat,
                success: function(data,textStatus,xhr){
$("#exam_periods_details").attr('disabled', false);
$("#exam_periods_details").empty();
$("#exam_periods_details").append(data);
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
            
<div class="examExcludedDateAndSessions form">
<?php echo $this->Form->create('ExamExcludedDateAndSession');?>
<div class="smallheading"><?php echo __('Add Exam Excluded Date And Session'); ?></div>
<div class="font"><?php echo 'Colege/Institute: '.$college_name ?></div>
<table cellpadding="0" cellspacing="0">
	<?php 
		echo '<tr><td class="font"> Academic Year</td>';
		echo '<td>'.$this->Form->input('academic_year',array('label' => false, 'type'=>'select','options'=>$acyear_array_data,'empty'=>"--Select Academic Year--", 'selected'=>isset($selected_academicyear)?$selected_academicyear:"", 'style'=>'width:200PX')).'</td>';
		echo '<td class="font"> Semester</td>';
		echo '<td >'.$this->Form->input('semester',array('label'=>false,'options'=>array('I'=>'I','II'=>'II','III'=>'III'),'type'=>'select', 'selected'=>isset($selected_semester)?$selected_semester:"",'empty'=>'--Select Semester--', 'style'=>'width:200PX')).'</td></tr>'; 
		echo '<tr><td colspan="4">'. $this->Form->Submit('Search',array('name'=>'search','class'=>'tiny radius button bg-blue','div'=>false)).'</td></tr>'; 
	?>
</table>
<table cellpadding="0" cellspacing="0">
<?php 
if(!empty($examPeriods)){
$data_array = array();
foreach($examPeriods as $examPeriod){
	$data_array[$examPeriod['ExamPeriod']['id']] = 'From '.$this->Format->short_date($examPeriod['ExamPeriod']['start_date']) .' to '.$this->Format->short_date($examPeriod['ExamPeriod']['end_date']).', '.$examPeriod['Program']['name'].', '.$examPeriod['ProgramType']['name'].', '.$examPeriod['YearLevel']['name'].' Year';
}

//$attributes=array('legend'=>false,'label'=>false,'separator'=>'<br/>');
	echo "<tr><td class='smallheading'> Exam Periods </td></tr>";
	//echo "<tr><td>". $this->Form->radio('Exam_Period',$data_array,$attributes)."</td></tr>";
	echo "<tr><td>". $this->Form->input('Exam_Period',array('id'=>'ajax_exam_period','onchange'=>'getclassperiod()','select'=>'checkbox','options'=>$data_array,'empty'=>'---Select Exam Period---','selected'=>isset($selected_exam_period)?$selected_exam_period:""))."</td></tr>";
}
?>
</table>
<div id="exam_periods_details">
<?php
if(!empty($date_array)){
?>
		<table style='border: #CCC solid 1px'>
		<tr><th style='border-right: #CCC solid 1px'>No.</th>
		<th style='border-right: #CCC solid 1px'>Date</th>
		<th style='border-right: #CCC solid 1px'>1st Session(Morning)</th>
		<th style='border-right: #CCC solid 1px'>2nd Session(Afternoon)</th>
		<th style='border-right: #CCC solid 1px'>3rd Session(Evening)</th></tr>
		<?php
		$count = 1;
	foreach($date_array as $dak=>$dav){
		echo "<tr><td style='border-right: #CCC solid 1px'>".$count++."</td>
			<td style='border-right: #CCC solid 1px'>".$this->Format->short_date($dav).' ('.date("l",strtotime($dav)).')'."</td>";
		if(isset($excluded_session_by_date[$dav][1])){
			echo "<td style='border-right: #CCC solid 1px'> Excluded. </td>";
		} else {
			echo "<td style='border-right: #CCC solid 1px'>".$this->Form->input('ExamPeriod.Selected.'.$dak.'-1',array('type'=>'checkbox','value'=>$dak.'-1','label'=>false))."</td>";
		}
		if(isset($excluded_session_by_date[$dav][2])){
			echo "<td style='border-right: #CCC solid 1px'> Excluded. </td>";
		} else {
			echo "<td style='border-right: #CCC solid 1px'>".$this->Form->input('ExamPeriod.Selected.'.$dak.'-2',array('type'=>'checkbox','value'=>$dak.'-2','label'=>false))."</td>";
		}
		if(isset($excluded_session_by_date[$dav][3])){
			echo "<td style='border-right: #CCC solid 1px'> Excluded. </td></tr>";
		} else {
			echo "<td style='border-right: #CCC solid 1px'>".$this->Form->input('ExamPeriod.Selected.'.$dak.'-3',array('type'=>'checkbox','value'=>$dak.'-3','label'=>false))."</td></tr>";
		}
	}
	?> </table>
	<?php echo $this->Form->Submit('Submit', array('name'=>'submit','div'=>false));?>
<?php
	if(isset($examExcludedDateAndSessions)) {
		?><div class="smallheading">Already Recorded Exam Excluded Dates and Sessions</div>
		<table style='border: #CCC solid 1px'>
		<tr><th style='border-right: #CCC solid 1px'>S.N<u>o</u>.</th>
			<th style='border-right: #CCC solid 1px'>Excluded Date</th>
			<th style='border-right: #CCC solid 1px'>Session</th>
			<th style='border-right: #CCC solid 1px'>Action</th></tr>
		<?php
		$count = 1;
		foreach($examExcludedDateAndSessions as $eedsk=>$eedsv){
			$session_name =null;
			if($eedsv['ExamExcludedDateAndSession']['session']==1){
				$session_name = "1st";
			} else if($eedsv['ExamExcludedDateAndSession']['session']==2){
				$session_name = "2nd";
			} else if($eedsv['ExamExcludedDateAndSession']['session']==3){
				$session_name = "3rd";
			}
			echo "<tr><td style='border-right: #CCC solid 1px'>".$count++."</td>
				<td style='border-right: #CCC solid 1px'>".$this->Format->short_date($eedsv['ExamExcludedDateAndSession']['excluded_date']).' ('.date("l",strtotime($eedsv['ExamExcludedDateAndSession']['excluded_date'])).')'."</td>
				<td style='border-right: #CCC solid 1px'>".$session_name.
			"</td><td style='border-right: #CCC solid 1px'>".
			$this->Html->link(__('Delete'), array('action' => 'delete', $eedsv['ExamExcludedDateAndSession']['id'],"fromadd"),null, sprintf(__('Are you sure you want to delete # %s?'), $eedsv['ExamExcludedDateAndSession']['id'],"fromadd")).
			"</td></tr>";
		}
		?></table><?php
	} 
}
?>
</div>
<!--
	<?php
		echo $this->Form->input('exam_period_id');
		echo $this->Form->input('excluded_date');
		echo $this->Form->input('session');
	?>
<?php echo $this->Form->Submit('Submit', array('name'=>'submit','div'=>false));?>
-->
<?php $this->Form->end(); ?>
</div>
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->

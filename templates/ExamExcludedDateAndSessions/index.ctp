<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
            
<div class="examExcludedDateAndSessions index">
<?php echo $this->Form->create('ExamExcludedDateAndSession');?>
	<div class="smallheading"><?php echo __('Exam Excluded Date And Sessions');?></div>
	<div class="font"><?php echo 'Colege/Institute: '.$college_name ?></div>
	<table cellpadding="0" cellspacing="0">
	<?php 
		echo '<tr><td class="font"> Academic Year</td>';
       echo '<td>'.$this->Form->input('academic_year',array('label'=>false ,'style'=>'width:200PX', 'type'=>'select','options'=>$acyear_array_data,'empty'=>"--Select Academic Year--")).'</td>';
       echo '<td class="font"> Semester</td>';
       echo '<td >'.$this->Form->input('semester',array('label'=>false, 'style'=>'width:200PX','options'=>array('I'=>'I','II'=>'II',
            'III'=>'III'))).'</td></tr>'; 
        echo '<tr><td class="font"> Programr</td>';
        echo '<td>'. $this->Form->input('program_id', array('label'=>false, 'style'=>'width:200PX')).'</td>'; 
        echo '<td class="font"> Program Type</td>';
        echo '<td>'. $this->Form->input('program_type_id', array('label'=>false, 'style'=>'width:200PX')).'</td></tr>'; 
        echo '<tr><td colspan="4">'. $this->Form->end(array('label'=>'Search',
'class'=>'tiny radius button bg-blue')).'</td></tr>'; 
       ?>
      </table>
<?php 
if(!empty($examExcludedDateAndSession_array)){
	foreach($examPeriods as $epk=>$examPeriod){
?>
<div class="smallheading"><?php echo $examPeriod['YearLevel']['name'] .' Year Exam Periods from '. $this->Format->short_date($examPeriod['ExamPeriod']['start_date']).' to '.$this->Format->short_date($examPeriod['ExamPeriod']['end_date']).' Exam Excluded Date and Sessions are:'; ?></div>

	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo 'S.N<u>o</u>';?></th>
			<th><?php echo 'Excluded Date';?></th>
			<th><?php echo 'Session';?></th>
			<th class="actions"><?php echo __('Actions');?></th>
	</tr>
	<?php
	$count=1;
	foreach ($examExcludedDateAndSession_array[$epk] as $examExcludedDateAndSession){
	?>
	<tr>
		<td><?php echo $count++; ?>&nbsp;</td>
		<td><?php echo $this->Format->short_date($examExcludedDateAndSession['ExamExcludedDateAndSession']['excluded_date']).' ('.date("l",strtotime($examExcludedDateAndSession['ExamExcludedDateAndSession']['excluded_date'])).')'; ?>&nbsp;</td>
		<td><?php echo $examExcludedDateAndSession['ExamExcludedDateAndSession']['session']; ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('Delete'), array('action' => 'delete', $examExcludedDateAndSession['ExamExcludedDateAndSession']['id']), null, sprintf(__('Are you sure you want to delete?'), $examExcludedDateAndSession['ExamExcludedDateAndSession']['id'])); ?>
		</td>
	</tr>
<?php }?>
	</table>

<?php }
} else if(empty($examExcludedDateAndSession_array) && !($isbeforesearch)){
    echo "<div class='info-box info-message'><span></span>There is no exam excluded date and session in the selected search criteria.</div>";
}
//$this->Form->end();
?>
</div>
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->

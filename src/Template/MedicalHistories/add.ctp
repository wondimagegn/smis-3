<?php ?>
<script>
function confirmCardNumberchange() {
	if($('#id_details').val() != "" && $('#id_record_type').val() != "") {
		return confirm('Are you sure you want to Add this medical record?');
	}
}
</script>
<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
            <div class="medicalHistories form">
<?php echo $this->Form->create('MedicalHistory');?>
	<div class="smallheading"><?php echo __('Add Student Medical Histories');?></div>
	<div class="font"><?php echo __('Search parameters');?> </div>
	<table cellpadding="0" cellspacing="0">
	<?php 
        echo '<tr><td class="font">'.$this->Form->input('studentnumber',array('label' => 'Student ID')).'</td>';
        echo '<td class="font">'.$this->Form->input('card_number',array('label' => 'Card Number')).'</td></tr>';
        		
		echo '<tr><td colspan="2">'.$this->Form->Submit('Search',array('name'=>'search','div'=>false,'class'=>'tiny radius button bg-blue')).'</td></tr>'; 
	?>
	</table>
<?php if(isset($students)) { 
?>
	<table cellpadding="0" cellspacing="0" style="border: #CCC solid 1px">
	<?php 
		echo '<tr><td colspan="10" style="text-align:center"><div class="smallheading" > Student Biodata</div></td></tr>';
	?>
	<tr>
		<th style="border-right: #CCC solid 1px"><?php echo ('ID');?></th>
		<th style="border-right: #CCC solid 1px"><?php echo ('Name');?></th>
		<th style="border-right: #CCC solid 1px"><?php echo ('Card Number');?></th>
		<th style="border-right: #CCC solid 1px"><?php echo ('Gender');?></th>
		<th style="border-right: #CCC solid 1px"><?php echo ('BirthDay');?></th>
		<th style="border-right: #CCC solid 1px"><?php echo ('Program');?></th>
		<th style="border-right: #CCC solid 1px"><?php echo ('ProgramType');?></th>
		<th style="border-right: #CCC solid 1px"><?php echo ('College');?></th>
		<th style="border-right: #CCC solid 1px"><?php echo ('Department');?></th>
	    <th style="border-right: #CCC solid 1px" class="actions"><?php echo __('Actions');?></th> 
	</tr>
	<tr>
		<td style="border-right: #CCC solid 1px">
			<?php echo $this->Html->link($students['Student']['studentnumber'], array('controller' => 'students', 'action' => 'view', $students['Student']['id'])); ?>
		</td>
		<td style="border-right: #CCC solid 1px">
			<?php echo $this->Html->link($students['Student']['full_name'], array('controller' => 'students', 'action' => 'view', $students['Student']['id'])); ?>
		</td>
		<td style="border-right: #CCC solid 1px"><?php echo !empty($students['Student']['card_number'])?$students['Student']['card_number']:"---"; ?>&nbsp;</td>
		<td style="border-right: #CCC solid 1px"><?php echo $students['Student']['gender']; ?>&nbsp;</td>
		<td style="border-right: #CCC solid 1px"><?php echo !empty($students['Student']['birthdate'])?'<u>'.$students['Student']['birthdate'].'</u>':"---"; ?>&nbsp;</td>
		<td style="border-right: #CCC solid 1px"><?php echo $students['Program']['name']; ?>&nbsp;</td>
		<td style="border-right: #CCC solid 1px"><?php echo $students['ProgramType']['name']; ?>&nbsp;</td>
		<td style="border-right: #CCC solid 1px"><?php echo $students['College']['name']; ?>&nbsp;</td>
		<td style="border-right: #CCC solid 1px"><?php echo $students['Department']['name']; ?>&nbsp;</td>
		
		<td style="border-right: #CCC solid 1px" class="actions">
			<?php echo $this->Html->link(__('View'), array('controller'=>'students','action' => 'view', $students['Student']['id'])); ?>
		</td> 
	</tr>
<?php
?>
	</table>
<?php 
}
?>
	<table cellpadding="0" cellspacing="0">
<?php 
	if(isset($student_id)){
		if(0){
			$status = 'Dismissal';
		?>
			<tr><td colspan="2"><div class="error-box error-message"><font color=RED><u>Beaware:</u></font><br/> - The Student status is: <?php echo $status; ?></div></td></tr>	
		<?php
		}
		echo $this->Form->hidden('student_id',array('value'=>$student_id));
		echo '<tr><td class="font">Recod Type</td><td>'.$this->Form->input('record_type',array('id'=>'id_record_type','label'=>false,'type'=>'select', 'options'=>array('chef complaint'=>'Chef Complaint', 'laboratory instruction'=>'Laboratory Instruction', 'laboratory result'=>'Laboratory Result', 'prescriptions'=>'Prescriptions', 'other'=>'Other'), 'empty'=>'---Please Select Record Type---'
)).'</td></tr>';
		echo '<tr><td class="font"> Details </td><td>'.$this->Form->input('details',array('id'=>'id_details','label'=>false,'cols'=>'80','rows'=>'10')).'</td></tr>';
		echo '<tr><td colspan="2">'.$this->Form->Submit('Submit',array('name'=>'submit','class'=>'tiny radius button bg-blue','div'=>false, 'onClick' => 'return confirmCardNumberchange()')).'</td></tr>'; 
	}
?>
	</table>
	<?php if(isset($medicalHistories)) { 
?>
	<table cellpadding="0" cellspacing="0" style="border: #CCC solid 1px">
	<?php 
		echo '<tr><td colspan="6" style="text-align:center"><div class="smallheading" > Student Medical Histories</div></td></tr>';
	?>
	<tr>
		<th style="border-right: #CCC solid 1px"><?php echo ('S.No');?></th>
		<th style="border-right: #CCC solid 1px"><?php echo ('Record Type');?></th>
		<!--<th style="border-right: #CCC solid 1px"><?php echo ('Details');?></th>-->
		<th style="border-right: #CCC solid 1px"><?php echo ('Created Date');?></th>
		<th style="border-right: #CCC solid 1px"><?php echo ('Modified Date');?></th>
	    <th style="border-right: #CCC solid 1px" class="actions"><?php echo __('Actions');?></th> 
	</tr>
	<?php 
		if(!empty($medicalHistories)){
			$count = 1;
			foreach($medicalHistories as $medicalHistory) {?>
			<tr>
				<td style="border-right: #CCC solid 1px"><?php echo $count++; ?>&nbsp;</td>
				<td style="border-right: #CCC solid 1px"><?php echo $medicalHistory['MedicalHistory']['record_type']; ?>&nbsp;</td>
				<!--<td style="border-right: #CCC solid 1px"><?php echo $medicalHistory['MedicalHistory']['details']; ?>&nbsp;</td>-->
				<td style="border-right: #CCC solid 1px"><?php echo $this->Format->humanize_date($medicalHistory['MedicalHistory']['created']); ?>&nbsp;</td>
				<td style="border-right: #CCC solid 1px"><?php echo $this->Format->humanize_date($medicalHistory['MedicalHistory']['modified']); ?>&nbsp;</td>
				<td style="border-right: #CCC solid 1px" class="actions">
					<?php
					$deadline = date('Y-m-d H:i:s', mktime(
					substr($medicalHistory['MedicalHistory']['created'], 11, 2)+24,
					substr($medicalHistory['MedicalHistory']['created'], 14, 2),
					substr($medicalHistory['MedicalHistory']['created'], 17, 2),
					substr($medicalHistory['MedicalHistory']['created'], 5, 2),
					substr($medicalHistory['MedicalHistory']['created'], 8, 2),
					substr($medicalHistory['MedicalHistory']['created'], 0, 4)
					));
					if($user_id == $medicalHistory['MedicalHistory']['user_id'] && $deadline >= date('Y-m-d H:i:s')){
						echo $this->Html->link(__('Edit'), array('action' => 'edit', $medicalHistory['MedicalHistory']['id'])); 
					}
					?>
				</td> 
			</tr>
			<tr><th style="border-right: #CCC solid 1px"><?php echo ('Details');?></th>
				<td class="fs13" colspan="4" style="border-right: #CCC solid 1px"><?php echo nl2br($medicalHistory['MedicalHistory']['details']); ?>&nbsp;</td>
			</tr>
<?php
		}
	} else {
		echo "<div class='info-box info-message'><span></span>Student doen't have a medical History.'</div>";
	}
?>
	</table>
<?php 
}
?>
<?php echo $this->Form->end();?>
</div>
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->

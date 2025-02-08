<script>
var card_number = "<?php echo !empty($students['Student']['card_number']) ? $students['Student']['card_number'] : ""; ?>";
function confirmCardNumberchange() {
	if(card_number != "") {
		if($('#new_card_number').val() != "") {
			return confirm('Are you sure you want to change student card number?');
		}
	}
}
</script>
<div class="students form">
<?php echo $this->Form->create('Student');?>
	<div class="smallheading"><?php echo __('Manage Student Medical Card Number');?></div>
	
	<table cellpadding="0" cellspacing="0">
	<?php 
        echo '<tr><td class="font">'.$this->Form->input('studentnumber',array('label' => 'Student ID')).'</td></tr>';
        		
		echo '<tr><td colspan="2">'.$this->Form->Submit('Search',array('name'=>'search','class'=>'tiny radius button bg-blue','div'=>false)).'</td></tr>'; 
	?>
		</table>
<?php if(isset($students)) { 
?>
	<table cellpadding="0" cellspacing="0">
	<?php 
		echo $this->Form->hidden('id',array('value'=>$students['Student']['id']));
		echo '<tr><td class="font" colspan="10">'.$this->Form->input('Student.card_number',array('label' => 'Card Name', 'id' => 'new_card_number')).'</td></tr>';
		echo '<tr><td colspan="10">'.$this->Form->Submit('Submit',array('name'=>'submit','class'=>'tiny radius button bg-blue','div'=>false, 'onClick' => 'return confirmCardNumberchange()')).'</td></tr>'; 
	?>
	<tr>
		<th><?php echo ('ID');?></th>
		<th><?php echo ('Name');?></th>
		<th><?php echo ('Card Number');?></th>
		<th><?php echo ('Gender');?></th>
		<th><?php echo ('BirthDay');?></th>
		<th><?php echo ('Program');?></th>
		<th><?php echo ('ProgramType');?></th>
		<th><?php echo ('College');?></th>
		<th><?php echo ('Department');?></th>
	    <th class="actions"><?php echo __('Actions');?></th> 
	</tr>
	<tr>
		<td>
			<?php echo $this->Html->link($students['Student']['studentnumber'], array('controller' => 'students', 'action' => 'view', $students['Student']['id'])); ?>
		</td>
		<td>
			<?php echo $this->Html->link($students['Student']['full_name'], array('controller' => 'students', 'action' => 'view', $students['Student']['id'])); ?>
		</td>
		<td><?php echo !empty($students['Student']['card_number'])?$students['Student']['card_number']:"---"; ?>&nbsp;</td>
		<td><?php echo $students['Student']['gender']; ?>&nbsp;</td>
		<td><?php echo !empty($students['Student']['birthdate'])?'<u>'.$students['Student']['birthdate'].'</u>':"---"; ?>&nbsp;</td>
		<td><?php echo $students['Program']['name']; ?>&nbsp;</td>
		<td><?php echo $students['ProgramType']['name']; ?>&nbsp;</td>
		<td><?php echo $students['College']['name']; ?>&nbsp;</td>
		<td><?php echo $students['Department']['name']; ?>&nbsp;</td>
		
		<td class="actions">
			<?php echo $this->Html->link(__('View'), array('action' => 'view', $students['Student']['id'])); ?>
			
		</td> 
	</tr>
<?php
?>
	</table>
<?php 
}
echo $this->Form->end(); ?>
</div>

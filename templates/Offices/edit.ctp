<div class="offices form">
<?php echo $this->Form->create('Office');?>
	
		<div class="smallheading"><?php echo __('Add Clearance Office'); ?></div>
	<?php
	    echo '<table><tr><td><table>';
	    echo $this->Form->input('id');
		echo '<tr><td>'.$this->Form->input('staff_id',array('label'=>'Official')).'</td></tr>';
		echo '<tr><td>'.$this->Form->input('name').'</td></tr></table>';
		echo '</td><td><table>';
		
		echo '<tr><td>'.$this->Form->input('telephone').'</td></tr>';
		echo '<tr><td>'.$this->Form->input('alternative_telephone').'</td></tr>';
		echo '<tr><td>'.$this->Form->input('email').'</td></tr>';
		echo '<tr><td>'.$this->Form->input('alternative_email').'</td></tr>';
		echo '<tr><td>'.$this->Form->input('address').'</td></tr>';
		echo '</table>';
	    echo '</td></tr>';
	    echo '</table>';
	?>
	
<?php echo $this->Form->end(__('Submit'));?>
</div>


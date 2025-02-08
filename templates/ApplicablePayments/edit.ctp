<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
             
<div class="applicablePayments form">
<?php echo $this->Form->create('ApplicablePayment');?>
	<fieldset>
		<legend><?php echo __('Edit Applicable Payment'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('student_id');
		echo $this->Form->input('tutition_fee');
		echo $this->Form->input('meal');
		echo $this->Form->input('accomodation');
		echo $this->Form->input('health');
		echo $this->Form->input('sponsor_type');
		echo $this->Form->input('sponsor_name');
		echo $this->Form->input('sponsor_address');
	?>
	</fieldset>
<?php echo $this->Form->end(array('label'=>__('Submit'),'class'=>'tiny radius button bg-blue'));?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('Delete'), array('action' => 'delete', $this->Form->value('ApplicablePayment.id')), null, sprintf(__('Are you sure you want to delete # %s?'), $this->Form->value('ApplicablePayment.id'))); ?></li>
		<li><?php echo $this->Html->link(__('List Applicable Payments'), array('action' => 'index'));?></li>
		<li><?php echo $this->Html->link(__('List Students'), array('controller' => 'students', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Student'), array('controller' => 'students', 'action' => 'add')); ?> </li>
	</ul>
</div>
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->

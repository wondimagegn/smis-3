<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
             
<div class="costSharingPayments form">
<?php echo $this->Form->create('CostSharingPayment');?>
	<fieldset>
		<legend><?php echo __('Edit Cost Sharing Payment'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('reference_number');
		echo $this->Form->input('amount');
		echo $this->Form->input('payment_type');
		echo $this->Form->input('student_id');
	?>
	</fieldset>
<?php echo $this->Form->end(array('label'=>__('Submit'),'class'=>'tiny radius button bg-blue'));?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('Delete'), array('action' => 'delete', $this->Form->value('CostSharingPayment.id')), null, sprintf(__('Are you sure you want to delete # %s?'), $this->Form->value('CostSharingPayment.id'))); ?></li>
		<li><?php echo $this->Html->link(__('List Cost Sharing Payments'), array('action' => 'index'));?></li>
		<li><?php echo $this->Html->link(__('List Students'), array('controller' => 'students', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Student'), array('controller' => 'students', 'action' => 'add')); ?> </li>
	</ul>
</div>
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->

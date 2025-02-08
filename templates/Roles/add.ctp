<?php ?>
<div class="box"> 
     <div class="box-body">
       <div class="row">
	   <?php echo $this->Form->create('Role');?>
	    <div class="large-12 columns">
              <h5 class="box-title">
              <?php echo __('Add Role'); ?>
	      </h5>

		
<table class="display">	
        <tr>
	<td> <?php echo $this->Form->input('parent_id',
array('empty'=>'none','options'=>$roles)); ?> </td>
	</tr>
	<tr>
	<td> <?php echo $this->Form->input('name'); ?> </td>
	</tr>
	<tr>
	<td> <?php echo $this->Form->input('description'); ?> </td>
	</tr>
	<tr>
	<td> <?php echo $this->Form->end(
array('class'=>'tiny radius button bg-blue','label'=>'Submit')); ?> </td>
	</tr>	
</table>


             </div>
        </div>
      </div>
</div>

<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
            
<div class="regions form">
<?php echo $this->Form->create('Region');?>
		<div class="smallheading"><?php echo __('Add Region'); ?></div>
	<?php
		echo $this->Form->input('name');
		echo $this->Form->input('short');
		echo $this->Form->input('description');
		echo $this->Form->input('country_id');
	?>
<?php echo $this->Form->end(__('Submit'));?>
</div>
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->

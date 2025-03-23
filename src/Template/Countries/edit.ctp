<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
             
<div class="countries form">
<?php echo $this->Form->create('Country');?>
	
		<div class="smallheading"><?php echo __('Edit Country'); ?></div>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('name');
		echo $this->Form->input('code');
	?>
	
<?php echo $this->Form->end(array('label'=>__('Submit'),'class'=>'tiny radius button bg-blue');?>
</div>
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->

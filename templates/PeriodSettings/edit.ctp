<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
            
<div class="periodSettings form">
<?php echo $this->Form->create('PeriodSetting');?>
<div class="smallheading"><?php echo __('Edit Period Setting'); ?></div>

	<?php
		echo "<div class='font'>".$college_name."</div>";
		echo $this->Form->input('id');
		//echo $this->Form->input('college_id');
		echo $this->Form->input('period',array('readonly'=>true));
		echo $this->Form->input('hour');
	?>
<?php echo $this->Form->end(array('label'=>__('Submit'),
'class'=>'tiny radius button bg-blue'));?>
</div>

	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->

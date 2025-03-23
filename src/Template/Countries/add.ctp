<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
             
<div class="countries form">
<?php echo $this->Form->create('Country');?>
	<div class="smallheading"> <?php echo __('Add Country'); ?> </div>
	<?php
		echo $this->Form->input('name');
		echo $this->Form->input('code',array('after'=>'E.g ET,NL,DE'));
	?>
<?php echo $this->Form->end(__('Submit'));?>
</div>
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->

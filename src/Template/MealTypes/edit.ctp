<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
            
<div class="mealTypes form">
<?php echo $this->Form->create('MealType');?>
<div class="smallheading"><?php echo __('Edit Meal Type'); ?></div>
	<?php
echo $this->Form->input('id');
		echo $this->Form->input('meal_name');
	?>

<?php echo $this->Form->end(array('label'=>__('Submit'),
'class'=>'tiny radius button bg-blue'));?>
</div>
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->

<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
            
<div class="mealHalls form">
<?php echo $this->Form->create('MealHall');?>
		<div class="smallheading"><?php echo __('Edit Meal Hall'); ?></div>
	<table>
	<?php
		echo $this->Form->input('id');
		echo "<tr><td>".$this->Form->input('campus_id', array('disabled'=>true))."</td>";
		echo "<td>".$this->Form->input('name')."</td></tr>";
		echo "<tr><td colspan='2'>".$this->Form->end(array('label'=>__('Submit'),'class'=>'tiny radius button bg-blue'))."</td></tr>"; 
	?>
	</table>
</div>

	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->

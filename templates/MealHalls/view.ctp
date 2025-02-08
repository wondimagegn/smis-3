<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
             <div class="mealHalls view">
<h2><?php echo __('Meal Hall');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Id'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $mealHall['MealHall']['id']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Campus'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Html->link($mealHall['Campus']['name'], array('controller' => 'campuses', 'action' => 'view', $mealHall['Campus']['id'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Name'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $mealHall['MealHall']['name']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Created'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $mealHall['MealHall']['created']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Modified'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $mealHall['MealHall']['modified']; ?>
			&nbsp;
		</dd>
	</dl>
</div>
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->

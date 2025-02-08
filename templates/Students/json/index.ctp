<?php ?>
<div class="box">
     <div class="box-header bg-transparent">
  	 <h2 class="box-title"> <?php echo __('View Students');?></h2>
     </div>
     <div class="box-body">
	<?php echo $this->DataTable->render('Student'); ?>
     </div>
</div>

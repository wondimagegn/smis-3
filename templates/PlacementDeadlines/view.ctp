<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
       
<div class="placementDeadlines view">
<h2><?php echo __('Placement Deadline'); ?></h2>
	<dl>
		
		<dt><?php echo __('Deadline'); ?></dt>
		<dd>
			<?php echo h($placementDeadline['PlacementDeadline']['deadline']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Admission Level'); ?></dt>
		<dd>
			<?php echo h($programs[$placementDeadline['PlacementDeadline']['program_id']]); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Admission Type'); ?></dt>
		<dd>
			<?php echo h($programTypes[$placementDeadline['PlacementDeadline']['program_type_id']]); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Academic Year'); ?></dt>
		<dd>
			<?php echo h($placementDeadline['PlacementDeadline']['academic_year']); ?>
			&nbsp;
		</dd>
		
		<dt><?php echo __('Applied For'); ?></dt>
		<dd>
			<?php echo h($allUnits[$placementDeadline['PlacementDeadline']['applied_for']]); ?>
			&nbsp;
		</dd>
		
	</dl>
</div>


	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->


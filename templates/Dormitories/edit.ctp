<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
            
<div class="dormitories form">
<?php echo $this->Form->create('Dormitory');?>
	<div class='smallheading'><?php echo __('Edit Dormitory'); ?></div>
	<table>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->hidden('dormitory_block_id');
		echo "<tr><td>".$this->Form->input('dorm_number')."</td>";
		echo "<td>".$this->Form->input('floor',array('type'=>'select','options'=>$floor_data))."</td></tr>";
		echo "<tr><td>".$this->Form->input('capacity')."</td>";
		echo "<td>".$this->Form->input('available')."</td></tr>";
		echo "<tr><td colspan='2'>".$this->Form->end(array('label'=>__('Submit'),'class'=>'tiny radius button bg-blue'))."</td></tr>";
	?>
	</table>
</div>


	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->

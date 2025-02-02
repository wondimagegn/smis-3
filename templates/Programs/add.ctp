<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
             
<div class="programs form">
<?php echo $this->Form->create('Program');?>
<table><tbody>	
		<tr><td><div class="smallheading"><?php echo __('Add Program'); ?></div> </td></tr>
	
	<?php
		echo "<tr><td>".$this->Form->input('name')."</td></tr>";
		echo "<tr><td>".$this->Form->input('description')."</td></tr>";
		echo "<tr><td>".$this->Form->end(array('label'=>__('Add'),'class'=>'tiny radius button bg-blue'))."</td></tr>";
	?>
</tbody>
</table>
</div>

	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->

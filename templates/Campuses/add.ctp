<?php ?>
<div class="box">
     <div class="box-header bg-transparent">
  	 <h2 class="box-title">
	<?php echo __('Add Campus'); ?>
	 </h2>
     </div>
     <div class="box-body">
       <div class="row">
           <div class="large-12 columns">
            <?php echo $this->Form->create('Campus');?>
		<table><tbody>
		
	<?php
		echo "<tr><td>";
		echo $this->Form->input('name', array('style' => 'width:300px'));
		echo "</td></tr>";
	        echo "<tr><td>";
		echo $this->Form->input('description');
		echo "</td></tr>";
	?>
	
<?php 
echo "<tr><td>";
echo $this->Form->end(array('label'=>__('Submit'),'class'=>'tiny radius button bg-blue'));
echo "</td></tr>";
?>
</tbody></table>
           </div>
        </div>
     </div>
</div>

<?php ?>
<div class="box">
     <div class="box-header bg-transparent">
  	 <h2 class="box-title">
	<?php echo __('Edit Announcement'); ?>
	 </h2>
     </div>
     <div class="box-body">
       <div class="row">
           <div class="large-12 columns">
            <?php 
            echo $this->Form->create('Announcement');
            echo $this->Form->input('id');
             ?>            
		<table><tbody>
	<?php
	
		echo "<tr><td colspan='3'>";
		echo $this->Form->input('headline');
		echo "</td></tr>";
		echo "<tr><td colspan='3'>";
		echo $this->Form->input('story');
		echo "</td></tr>";
		echo "<tr><td>";
		echo $this->Form->input('is_published');
		echo "</td>";
		echo "<td>";
		echo $this->Form->input('annucement_start');
		echo "</td>";
		echo "<td>";
		echo $this->Form->input('annucement_end');
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

<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
             
<div class="quotas form">
<?php echo $this->Form->create('Quota');?>
	<fieldset>
		<legend><?php echo __('Edit Privilaged Quota'); ?></legend>
		<table>
		<tbody>
	<?php
	    
		echo $this->Form->input('id');
		echo $this->Form->hidden('college_id',array('value'=>$college_id));
		echo '<tr><td class="font">'.$college_name.'</td></tr>';
		echo '<tr><td>'.$this->Form->input('academicyear').'</td></tr>';
		echo '<tr><td>'.$this->Form->input('female',array('label'=>'Female')).'</td></tr>';
		
		//echo $this->Form->input('developing_regions_id');
       echo  '<tr><td class="font">List of regions.Please check the  developing regions you
want to give privilaged quota.</td></tr>';
	   echo '<tr><td>'.$this->Form->input('developing_regions_id', 
array('type' => 'select', 'multiple' => 'checkbox',
'div'=>'input select','selected'=>isset($selected)?$selected:'')).'</td></tr>';
       echo '<tr><td>'.$this->Form->input('regions',array('label'=>'Developing Region Quotas')).'</td></tr>';
		echo '<tr><td>'.$this->Form->end(__('Submit')).'</td></tr>';
		
	?>
	</tbody>
	</table>
	</fieldset>
	
<?php echo $this->Form->end(__('Submit'));?>
</div>
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->

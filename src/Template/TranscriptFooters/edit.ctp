<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
            
<div class="transcriptFooters form">
<?php 
echo $this->Form->create('TranscriptFooter');
echo $this->Form->input('id');
?>
<div class="smallheading"><?php echo __('Edit Transcript Footer'); ?></div>
<table>
	<tr>
		<td style="width:10%">Footer Line 1:</td>
		<td style="width:90%"><?php echo $this->Form->input('line1', array('label' => false, 'style' => 'width:700px; height:50px')); ?></td>
	</tr>
	<tr>
		<td>Footer Line 2:</td>
		<td><?php echo $this->Form->input('line2', array('label' => false, 'style' => 'width:700px; height:50px')); ?></td>
	</tr>
	<tr>
		<td>Footer Line 3:</td>
		<td><?php echo $this->Form->input('line3', array('label' => false, 'style' => 'width:700px; height:50px')); ?></td>
	</tr>
	<tr>
		<td>Program:</td>
		<td><?php echo $this->Form->input('program_id', array('label' => false)); ?></td>
	</tr>
	<tr>
		<td>Admission Year:</td>
		<td><?php echo $this->Form->input('academic_year', array('label' => false, 'options' => $acyear_array_data, 'after' => ' when the application of the footer starts')); ?></td>
	</tr>
</table>
<?php echo $this->Form->end(array('label'=>__('Submit'),
'class'=>'tiny radius button bg-blue'));?>
</div>
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->

<?php ?>
<div class="box">
     <div class="box-header bg-transparent">
  	  <h6 class="box-title">
	     <?php echo __('Add Note'); ?>
	     </h6>
     </div>
     <div class="box-body">
       <div class="row">
	   
	   <?php echo $this->Form->create('Note');?>
	  <div class="large-6 columns">
	  	
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('title');
		echo $this->Form->input('content',array('label'=>"Description"));
		echo $this->Form->input('college_id');
		echo "<br/>";
		$from = date('Y') - Configure::read('Calendar.yearsInPast');
        $to = date('Y') + Configure::read('Calendar.yearsAhead');
        $format = Configure::read('Calendar.dateFormat');
	?>
	</div>
          <div class="large-6 columns">
	<?php
		
		echo $this->Form->input('department_id');
		echo "<br/>";
		echo $this->Form->input('published_date',array('dateFormat'=>$format,'minYear'=>$from,'maxYear'=>$to));
		echo "<br/>";
		echo $this->Form->input('start_date');
		echo "<br/>";
		echo $this->Form->input('end_date');
		echo "<br/>";
		echo $this->Form->input('user_id');
	?>
	  </div>

	<div class="large-12 columns">
	
<?php echo $this->Form->end(array('label'=>'Submit','class'=>'tiny radius button bg-blue'));
?>
	  </div>
	</div>
      </div>
</div>

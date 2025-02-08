<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
             
<div class="studentStatusPatterns form">
<?php echo $this->Form->create('StudentStatusPattern');?>
	
		<div class="smallheading"><?php echo __('Edit Student Status Pattern'); ?></div>
		<p class="fs16">
                    <strong> Important Note: </strong> 
                  
                     
                      Status pattern is useful to display students academic status  based on 
                      their program types on their grade report.
                    
      </p>

	<?php
	    echo $this->Form->input('id');
	    
	    echo '<table>';
		
		echo '<tr><td>'.$this->Form->input('program_id').'</td></tr>';
		echo '<tr><td>'.$this->Form->input('program_type_id').'</td></tr>';
		
		echo "<tr><td>".
		$this->Form->input('acadamic_year',array(
            'label' => 'Academic Year','type'=>'select','options'=>$acyear_array_data))
		."</td></tr>";
		echo "<tr><td>".$this->Form->input('application_date')."</td></tr>";
		echo '<tr><td>'.$this->Form->input('description').'</td></tr>';
		echo "<tr><td>".$this->Form->input('pattern',
		array('options'=>array('1'=>1,'2'=>2,
            '3'=>3,'4'=>4,'5'=>5)))."</td></tr>";
		
	
	    echo '</table>';
	
	?>
	
<?php echo $this->Form->end(array('label'=>__('Submit'),
'class'=>'tiny radius button bg-blue'));?>
</div>

	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->

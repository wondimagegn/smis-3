<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
	     <h6 class="box-title">
		<?php echo __('Add Academic Rule'); ?>
	     </h6>
	  </div>
	  <div class="large-12 columns">
		<?php echo $this->Form->create('AcademicRule');?>
<div style="padding-bottom:20px;"></div>
	<fieldset>
		<legend class="smallheading"><?php echo __('Add Academic Rule'); ?></legend>
	<?php
		
		echo $this->Form->input('name',array('options'=>array('SGPA'=>'Semester Grade Point Average ','CGPA'=>'Commulative Grade Point Average',
            'TWW'=>'Two Consecutive Warning','PFW'=>'Probation Followed By Warning'),'empty'=>'--select rule name--'));
		echo $this->Form->input('from');
		echo $this->Form->input('to');
		//echo $this->Form->input('AcademicStand');
		if (!empty($academicStandsDetail)) {
		    echo "<table>";
		    echo "<tr><th>Name</th>";
		    echo "<th>Year Level</th>";
		    echo "<th>Semester</th>";
		    echo "<th>Applay To </th></tr>";
		    foreach ($academicStandsDetail as $academicstandkey=>$academicstandvalue) {
		        echo "<tr><td>".$academicstandvalue['AcademicStand']['name']."</td>";
		        echo "<td>".$academicstandvalue['YearLevel']['name']."</td>";
		        echo "<td>".$academicstandvalue['AcademicStand']['semester'].'</td>';
		       
		       echo "<td>".$this->Form->checkbox("AcademicStand.approve.".$academicstandvalue['AcademicStand']['id'])."</td></tr>";                                        
		    }
		    echo "</table>";
		}
	?>
	</fieldset>
<?php echo $this->Form->end(array('label'=>__('Submit'),
'class'=>'tiny radius button bg-blue'));?>
	  </div>
	</div>
      </div>
</div>

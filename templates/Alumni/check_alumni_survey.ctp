<?php echo $this->Form->create('Alumnus'); ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  	<div class="large-12 columns">
	  	  <table cellpadding="0" cellspacing="0">
					<?php 	
							echo '<tr><td class="smallheading">Search Alumni Survey</td></tr>';
				
							echo '<tr><td class="font">'.$this->Form->input('Alumnus.studentID',array('label' => 'Student Number/ID')).'</td></tr>';
						   echo '<tr><td>'. $this->Form->Submit('Check',array('name'=>'check','class'=>'tiny radius button bg-blue','div'=>false)).'</td></tr>';
					?>
			</table>
			<div>
			<?php 
			if (isset($alumniDetail) && !empty($alumniDetail)) {
				echo $this->element('alumni_survey_form');
			}
			?>

	    	</div>

		  </div>
	  	</div>
	   </div>
	  </div>
</div>

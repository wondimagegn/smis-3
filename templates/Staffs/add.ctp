<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
            
<div class="staffs form">
<?php 
echo $this->Form->create('Staff',array('type'=>'file',
'novalidate' => true,'enctype' => 'multipart/form-data'));
?>

		<div class="smallheading"><?php echo __('Add Staff'); ?></div>
	<?php
		 $options=array('male'=>'Male','female'=>'Female');
		$attributes=array('legend'=>false,'label'=>false);
	    echo '<table>';
	    echo '<tr><td style="width:50%">';
	    echo '<table>';
	    echo '<tr><td class="fs16" style="font-weight:bold">Basic </td></td>';
	    echo '<tr><td>'.$this->Form->input('title_id').'</td></tr>';
		echo '<tr><td>'.$this->Form->input('position_id').'</td></tr>';
		  echo '<tr><td>'.$this->Form->input('education').'</td></tr>';
        echo '<tr><td>'.$this->Form->input('servicewing').'</td></tr>';
		
		echo '<tr><td>'.$this->Form->input('first_name').'</td></tr>';
		echo '<tr><td>'.$this->Form->input('middle_name').'</td></tr>';
		echo '<tr><td>'.$this->Form->input('last_name').'</td></tr>';
       echo '<tr><td>'.$this->Form->input('college_id').'</td></tr>';
       	echo '<tr><td>'.$this->Form->input('department_id').'</td></tr>';
       	
        $from = date('Y') - Configure::read('Calendar.birthdayInPast');
        $to = date('Y') + Configure::read('Calendar.birthdayAhead');
        $format = Configure::read('Calendar.dateFormat');
        
		echo '<tr><td>'.$this->Form->input('birthdate',array('label'=>'Birth date','dateFormat'=>$format,'minYear'=>$from,'maxYear'=>$to,'style'=>'width:80px;')).'</td></tr>';
 
		echo '<tr><td> Gender '. $this->Form->input('gender',array('options'=>$options,'type'=>'radio','legend'=>false,'separator'=>'','label'=>false)).'</td></tr>';
		  echo '<tr><td> Profile Picture:'.$this->Form->input('Attachment.0.file', array('type' => 'file')).'</td></tr>';
		echo '</table>';
		echo '</td>';
		echo '<td>';
		 echo '<table>';
	    echo '<tr><td class="fs16" style="font-weight:bold">Address </td></td>';
	    echo '<tr><td>'.$this->Form->input('phone_mobile').'</td></tr>';
		echo '<tr><td>'.$this->Form->input('phone_office').'</td></tr>';
		echo '<tr><td>'.$this->Form->input('phone_home').'</td></tr>';
		
		echo '<tr><td>'.$this->Form->input('email').'</td></tr>';
		echo '<tr><td>'.$this->Form->input('alternative_email').'</td></tr>';
		 echo '<tr><td>'.$this->Form->input('country_id',
	     	array('empty'=>false,
'type'=>'select','options'=>$countries,'empty'=>"--Select Country",
			'selected'=>isset($this->request->data['Staff']['country_id'])? $this->request->data['Staff']['country_id']:68
	     	)).'</td></tr>';

		
		echo '</table>';
		echo '</td></tr>';
	    echo '</table>';
		
		
	?>
<?php echo $this->Form->end(array('label'=>__('Submit'),
'class'=>'tiny radius button bg-blue'));?>
</div>
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row -->
      </div> <!-- end of box-body -->
</div><!-- end of box -->

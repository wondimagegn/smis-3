<div class="row">
<div class="large-12 columns">

<?php echo $this->Form->create('StaffStudy',array('action'=>'add_staff_study', "method"=>"POST",'enctype' => 'multipart/form-data'));
	
echo '<h6>Please provide the study attended with university commitement </h6>';

echo $this->Form->hidden('StaffStudy.staff_id',array('value'=>$staff_profile['Staff']['id']));

echo $this->Form->hidden('StaffStudy.id');
?>

<?php 

echo '<table>';
	    echo '<tr><td style="width:50%">';
	    echo '<table>';
	    echo '<tr><td class="fs16" style="font-weight:bold">Study </td></td>';
	    echo '<tr><td>'.$this->Form->input('StaffStudy.education').'</td></tr>';
		echo '<tr><td>'.$this->Form->input('StaffStudy.country_id',array('empty'=>false,'value'=>68)).'</td></tr>';
		echo '<tr><td>'.$this->Form->input('StaffStudy.university_joined',array('label'=>'University Attended')).'</td></tr>';
		echo '<tr><td>'.$this->Form->input('StaffStudy.specialization').'</td></tr>';
		echo '</table>';
		echo '</td>';
		
		echo '<td style="width:50%">';

		 echo '<table>';
	    echo '<tr><td class="fs16" style="font-weight:bold">Date </td></td>';
	    echo '<tr><td>'.$this->Form->input('StaffStudy.leave_date').'</td></tr>';
		echo '<tr><td>'.$this->Form->input('StaffStudy.return_date').'</td></tr>';

		echo '</table>';

    echo '<table>';
	    echo '<tr><td class="fs16" style="font-weight:bold">Commitement </td></td>';
	    echo '<tr><td>'.$this->Form->input('StaffStudy.committement_signed').'</td></tr>';
		 echo '<tr><td> Attach Commitement :'.$this->Form->input('Attachment.0.file', array('type' => 'file','label'=>false)).'</td></tr>';

		echo '</table>';


		echo '</td>';

		
		echo '</tr>';

		echo '</table>';

	
echo $this->Form->end('Add Study');

?>
</div>
</div>
<a class="close-reveal-modal">&#215;</a>

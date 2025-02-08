<?php
if (!empty($sections_organized_by_acy)) { 
	//debug($sections_organized_by_acy);
	//echo $this->Form->select('Section.assigned_section', $sections_organized_by_acy, array(/* 'multiple' => 'checkbox', */ 'required'));
	echo $this->Form->input('Section.assigned_section', array( 'label' => 'Target Section: ', 'type' => 'select', 'options' => $sections_organized_by_acy, 'empty' => '[ Select Section ]', 'required', 'style' => 'width: 45%;')); 
	echo '<hr>' . $this->Form->Submit('Add to Selected Section', array('id' => 'Add_To_Section_Button', 'class' => 'tiny radius button bg-blue')); 
}  else { ?>
	<div class='info-box info-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style='margin-right: 15px;'></span>No Active Section found with the given search criteria or the student already assigned to a given section in the given filter.</div>
	<?php
} ?>

<script type="text/javascript">

	var form_being_submitted = false;

	$('#Add_To_Section_Button').click(function() {

		var assignedSectionSelection = $('#SectionAssignedSection').val();
		
		if (assignedSectionSelection == '') {
			$('#SectionAssignedSection').focus();
			$('#SectionAssignedSection').setAttribute('title', 'Please select target section to add the student');
			return false;
		}

		if (form_being_submitted) {
			alert('Adding to Selected Section, please wait a moment...');
			$('#Add_To_Section_Button').attr('disabled', true);
			return false;
		}

		if (!form_being_submitted && assignedSectionSelection != '') {
			$('#Add_To_Section_Button').val('Adding to Selected Section...');
			form_being_submitted = true;
			return true;
		} else {
			return false;
		}

	});

	if (window.history.replaceState) {
		window.history.replaceState(null, null, window.location.href);
	}

</script>
<div class="graduationStatuses view">
<div class="smallheading"><?php echo __('Graduation Status');?></div>
<table class="fs12">
	<tr>
		<td style="width:15%"><?php echo __('Program'); ?></td>
		<td style="width:85%"><?php echo $graduationStatus['Program']['name']; ?></td>
	</tr>
	<tr>
		<td><?php echo __('CGPA'); ?></td>
		<td><?php echo $graduationStatus['GraduationStatus']['cgpa']; ?></td>
	</tr>
	<tr>
		<td><?php echo __('Status'); ?></td>
		<td><?php echo $graduationStatus['GraduationStatus']['status']; ?></td>
	</tr>
	<tr>
		<td><?php echo __('Academic Year'); ?></td>
		<td><?php echo $graduationStatus['GraduationStatus']['academic_year']; ?></td>
	</tr>
	<tr>
		<td><?php echo __('Applicable For Current Student'); ?></td>
		<td><?php echo $graduationStatus['GraduationStatus']['applicable_for_current_student'] == 1 ? 'Yes' : 'No'; ?></td>
	</tr>
	<tr>
		<td><?php echo __('Created'); ?></td>
		<td><?php echo $this->Format->humanize_date($graduationStatus['GraduationStatus']['created']); ?></td>
	</tr>
	<tr>
		<td><?php echo __('Modified'); ?></td>
		<td><?php echo $this->Format->humanize_date($graduationStatus['GraduationStatus']['modified']); ?></td>
	</tr>
</table>
</div>

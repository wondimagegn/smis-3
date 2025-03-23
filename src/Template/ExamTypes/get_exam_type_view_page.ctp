<?php
if(count($exam_types) > 0) {
?>
<table cellspacing="0" cellpadding="0" id="exam_setup">
	<tr>
		<th style="width:25%">Exam Type</th>
		<th style="width:15%">In Percent</th>
		<th style="width:10%">Order</th>
		<th style="width:15%">Mandatory</th>
		<th style="width:20%">Date Created</th>
		<th style="width:15%">Date Modified</th>
	</tr>
<?php
foreach($exam_types as $key => $exam_type) {
?>
	<tr>
		<td><?php echo $exam_type['ExamType']['exam_name']; ?></td>
		<td><?php echo $exam_type['ExamType']['percent'].'%'; ?></td>
		<td><?php echo ($exam_type['ExamType']['order'] != 0 ? $exam_type['ExamType']['order'] : '---' ); ?></td>
		<td><?php echo ($exam_type['ExamType']['mandatory'] == 1 ? 'Yes' : 'No'); ?></td>
		<td><?php echo $this->Format->humanize_date($exam_type['ExamType']['created']); ?></td>
		<td><?php echo $this->Format->humanize_date($exam_type['ExamType']['modified']); ?></td>
	</tr>
<?php
	}
?>
</table>
	<?php
	}
else
	echo '<p>There is not exam setup for the selected acadamic year, semester and course.</p>';
?>

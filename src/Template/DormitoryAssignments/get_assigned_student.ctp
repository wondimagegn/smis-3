<?php ?>
<div class="row">
<div class="large-12 columns">
	<h1>
	Room Mates
	</h1>
	<table>
	<tr>
		<th>Name</th><th>Department</th>
	</tr>
	<?php foreach($assignedStudents as $k=>$list) { ?>
		
		<tr>
		<td><?php echo $list['Student']['full_name'];?></td>
		<td><?php  echo $list['Student']['Department']['name'];?></td>
	</tr>
	<?php } ?> 
	</table>
</div>
</div>
<a class="close-reveal-modal">&#215;</a>

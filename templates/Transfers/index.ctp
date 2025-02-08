<div style="height:200px">
</div>
<?php 
/* if ($role_id==ROLE_STUDENT) {
   //debug($autoplacedresult);
   if (!empty($autoplacedresult['AcceptedStudent']['department_id']) && 
   !empty($autoplacedresult['AcceptedStudent']['minute_number'])) {
        echo "<div class='smallheading' style='padding-bottom:200px'><p> 
        Dear ".ucwords(strtolower($autoplacedresult['AcceptedStudent']['full_name'])).",<br /> <br /> 
        Congratulations you are placed to department of ".$placed_department_name.".<br /> <br /> From 
        ".$autoplacedresult['College']['name']."</p></div> ";
   } else {
     echo "<div class='smallheading'> Please wait. The placement result will come soon.</div>";
 
   
   }
   
 } else {
?>
<div class="preferences index">
    <?php if (isset($department_name) && isset($current_acyear)) {
    ?>
	<h2><?php echo __(' List of students of placed to '.$department_name.' in '.$current_acyear.' Academic Year');?></h2>
	<?php } elseif(isset($college_name) && isset($current_acyear)){
	 ?>
	 <h2><?php echo __('Accepted Students of '.$college_name.' in '.$current_acyear.' Academic Year');?></h2>
	 <?
	}
	
?>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo 'No.'; ?></th>
			<th><?php echo 'Full Name';?></th>
			<th><?php echo 'academicyear';?></th>
			<th><?php echo'college';?></th>
			<th><?php echo 'program';?></th>
			
			<th class="actions"><?php echo __('Actions');?></th>
	</tr>
	<?php
	$i = 0;
	$count=1;
	foreach ($recentAcceptedStudents as $recentAcceptedStudent):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
	   <td><?php echo $count++;?></td>
		<td>
			<?php echo $this->Html->link($recentAcceptedStudent['AcceptedStudent']['full_name'], array('controller' => 'AcceptedStudents', 'action' => 'view', $recentAcceptedStudent['AcceptedStudent']['id'])); ?>
		</td>
		<td><?php echo $recentAcceptedStudent['AcceptedStudent']['academicyear']; ?>&nbsp;</td>
		
		<td><?php echo $recentAcceptedStudent['College']['name']; ?>&nbsp;</td>
		<td><?php echo $recentAcceptedStudent['Program']['name']; ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View'), array('controller'=>'accepted_students','action' => 'view', $recentAcceptedStudent['AcceptedStudent']['id'])); ?>
			<?php echo $this->Html->link(__('Edit'), array('controller'=>'accepted_students','action' => 'edit', $recentAcceptedStudent['AcceptedStudent']['id'])); ?>
		
		</td>
	</tr>
<?php endforeach; ?>
	</table>
	
</div>

<?php 
}

*/
?>


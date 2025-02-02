
<?php 
/*
This file should be in app/views/elements/export_xls.ctp
Thanks to Marco Tulio Santos for this simple XLS Report
*/
header ("Expires: " . gmdate("D,d M YH:i:s") . " GMT");
header ("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
header ("Cache-Control: no-cache, must-revalidate");
header ("Pragma: no-cache");
header ("Content-type: application/vnd.ms-excel");
header ("Content-Disposition: attachment; filename=".$filename.".xls" );
header ("Content-Description: Exported as XLS" );
?>

<div class="dormitoryAssignments index">
<div class="smallheading"><?php echo __('List of Dormitory assigned students');?></div>
	
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo 'S.No';?></th>
			<th><?php echo 'Name';?></th>
			<th><?php echo 'ID';?></th>
			<th><?php echo 'Dorm';?></th>
			<th><?php echo 'floor';?></th>
			<th><?php echo 'Block';?></th>
			<th><?php echo 'Campus';?></th>
			<th><?php echo 'Assignment Date';?></th>
			<th><?php echo 'Leave Date';?></th>
			<th><?php echo 'Received';?></th>
			<th><?php echo 'Received_date';?></th>
			
	</tr>
	<?php
	$i = 0;
	$start = $this->Paginator->counter('%start%');
	foreach ($dormitoryAssignments as $dormitoryAssignment):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
		
		$floor = null;
		if($dormitoryAssignment['Dormitory']['floor'] ==1){
			$floor = "Ground Floor";
		} else if($dormitoryAssignment['Dormitory']['floor']==2){
			$floor = ($dormitoryAssignment['Dormitory']['floor']-1)."st Floor";
		} else if($dormitoryAssignment['Dormitory']['floor']==3){
			$floor = ($dormitoryAssignment['Dormitory']['floor']-1)."nd Floor";
		} else if($dormitoryAssignment['Dormitory']['floor']==4){
			$floor = ($dormitoryAssignment['Dormitory']['floor']-1)."rd Floor";
		} else {
			$floor = ($dormitoryAssignment['Dormitory']['floor']-1)."th Floor";
		}
		
		$received = null;
		if($dormitoryAssignment['DormitoryAssignment']['received'] == 0){
			$received = "No";
		} else {
			$received = "Yes";
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $start++; ?>&nbsp;</td>
		<?php if(!empty($dormitoryAssignment['Student']['id'])) {?>
			<td>
				<?php echo $this->Html->link($dormitoryAssignment['Student']['full_name'], array('controller' => 'students', 'action' => 'view', $dormitoryAssignment['Student']['id'])); ?>
			</td>
			<td>
				<?php echo $this->Html->link($dormitoryAssignment['Student']['studentnumber'], array('controller' => 'students', 'action' => 'view', $dormitoryAssignment['Student']['id'])); ?>
			</td>
		<?php } else if(!empty($dormitoryAssignment['AcceptedStudent']['id'])) {?>
			<td><?php echo $dormitoryAssignment['AcceptedStudent']['full_name']; ?>&nbsp;</td>
			<td><?php echo $dormitoryAssignment['AcceptedStudent']['studentnumber']; ?>&nbsp;</td>
		
		<?php }?>
		<td>
			<?php echo $this->Html->link($dormitoryAssignment['Dormitory']['dorm_number'], array('controller' => 'dormitories', 'action' => 'view', $dormitoryAssignment['Dormitory']['id'])); ?>
		</td>
		<td><?php echo $floor; ?>&nbsp;</td>
		<td><?php echo $dormitoryAssignment['Dormitory']['DormitoryBlock']['block_name']; ?>&nbsp;</td>
		<td><?php echo $dormitoryAssignment['Dormitory']['DormitoryBlock']['Campus']['name']; ?>&nbsp;</td>
		
		<td><?php echo $this->Format->short_date($dormitoryAssignment['DormitoryAssignment']['created']); ?>&nbsp;</td>
		<td><?php echo $dormitoryAssignment['DormitoryAssignment']['leave_date']; ?>&nbsp;</td>
		<td><?php echo $received; ?>&nbsp;</td>
		<td><?php echo $dormitoryAssignment['DormitoryAssignment']['received_date']; ?>&nbsp;</td>
		
	</tr>
<?php endforeach; ?>
	</table>
</div>

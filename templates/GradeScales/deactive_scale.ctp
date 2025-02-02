 <?php 
echo $this->Form->create('GradeScale');

?>
<div>
<h2><?php echo __('Grade Scales');?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
	        <th><?php echo __('Select');?></th>
			<th><?php echo __('S.No');?></th>
			<th><?php echo __('Name');?></th>
		   <th><?php echo __('Grade Type');?></th>
			<th><?php echo __('Program');?></th>
			<th><?php echo __('Freshman');?></th>
			<th><?php echo __('Active');?></th>
			<th><?php echo __('created');?></th>
			<th><?php echo __('modified');?></th>
			<th><?php echo __('Action'); ?></th>
	</tr>
	<?php
	$i = 0;
	$count=1;
	foreach ($gradeScales as $gradeScale):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
		
	?>
	<tr<?php echo $class;?>>
	    <td><?php echo $this->Form->checkbox('GradeScale.selected.'.$gradeScale['GradeScale']['id']);?> </td>
		<td><?php echo $count++;?> </td>
		<td><?php echo $gradeScale['GradeScale']['name']; ?>&nbsp;</td>
		
		<td>
			<?php echo $this->Html->link($gradeScale['GradeScaleDetail'][0]['Grade']['GradeType']['type'], array('controller' => 'grade_types', 'action' => 'view', 
			$gradeScale['GradeScaleDetail'][0]['Grade']['GradeType']['id'])); ?>
		</td>
		
		<td>
			<?php echo $this->Html->link($gradeScale['Program']['name'], array('controller' => 'programs', 'action' => 'view', $gradeScale['Program']['id'])); ?>
		</td>
		<td><?php echo (($gradeScale['GradeScale']['own']==1) ? 'Yes' : 'No'); ?>&nbsp;
		
		</td>
		
		<td><?php 
		    echo (($gradeScale['GradeScale']['active']==1) ? 'Yes' : 'No'); 
		
		?>&nbsp;
		
		</td>
		<td><?php echo $gradeScale['GradeScale']['created']; ?>&nbsp;</td>
		<td><?php echo $gradeScale['GradeScale']['modified']; ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View'), array('action' => 'view', $gradeScale['GradeScale']['id'])); ?>
			<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $gradeScale['GradeScale']['id'])); ?>
			
		</td>
	</tr>
<?php endforeach; ?>
	</table>
	  <table>
            <tr>
                <td style='padding:0'> <?php 
                 // echo $this->Form->submit('Unpublished Selected',array('name'=>'unpublishselected','div'=>'false'));?></td>
                  
                   <td style='padding:0'> <?php 
                  echo $this->Form->submit('Deactivate Selected',array('name'=>'deactivateselected','div'=>'false'));?></td>
              
            </tr>
           
      </table>
</div>

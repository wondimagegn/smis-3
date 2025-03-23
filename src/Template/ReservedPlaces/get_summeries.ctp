<table><tbody>
<?php if(!empty($total_students_college_academicyear)) { ?>
<tr>
<td colspan="2" style="font-weight:bold" class="fs14">Auto placement options</td>
</tr>
<tr>
<td colspan="2"><?php //echo $this->Form->input('AcceptedStudent.high_proprity_for_high_result', array('label' => false, 'checked' => 'checked', 'type' => 'checkbox')); ?>
<!-- Give high priority to students who has high result but lost their top prferences than students who has low result and their top preference is not yet considered.
-->
<?php 
echo $this->Form->input('AcceptedStudent.priority',array('label'=>'Auto placement options','type'=>'radio','required','separator'=>' ',
		        'options' => array('high_proprity_for_high_result' => 'Give high priority to students who has high result but lost their top preferences than students who has low result and their top preference is not yet considered. Dont use this option if there is any quota defined. Remove the quota and rerun to get the expected placement result.', 'first_consider_first' => 'Do not consider student second and other preference before considering their first preference. (Considering student second preference before their first preference happen when there is privilage quota and the system unable to get enough students for the given result category for medium-level wanted departments')));
		        ?>
</td>
</tr>
<tr>
<td colspan="2" style="padding-bottom:20px"><?php //echo $this->Form->input('AcceptedStudent.first_consider_first', array('label' => false, 'checked' => 'checked', 'type' => 'checkbox')); ?> <!-- Do not consider student second and other preference before considering their first preference. (Considering student second preference before their first preference happen when there is privilage quota and the system unable to get enough students for the given result category for medium-level wanted departments) CHECKING ON THIS OPTION IS RECOMENDED.-->
</td>
</tr>
<tr>
<td colspan="2">
<?php echo '<div class="font">'.$college_name.' has a total of '.$total_students_college_academicyear.' elegible students for placement who are admitted in '.$selectedAcademicYear.' academic year </div>';?>
<table><tbody>
<tr><th style="width:30%"><?php echo __('Placement Result Category')?></th><th><?php echo __('Number of Students') ?></th></tr>
<?php 
    foreach($summeryresultcategorystudent as $k =>$v){
        echo '<tr><td>'.$k.'</td><td>'.$v.'</td></tr>';
    }
?>
</tbody>
<?php 
} else {
    echo '<div class="error-box error-message"><span></span>There is no student for the selected academic year.</div>';
}

 ?>
</table>
</td>
</tr>
<tr>
<td colspan="2">

<table>
<tbody>
<?php
if(!empty($generalsummery)){ 
echo '<tr><td colspan="5"><div class="font">'.__('Departments placement quota for '.$selectedAcademicYear.' academic year').'</div></td></tr>'; ?>
<tr>
<th style="width:25%"><?php echo __('Department')?></th><th style="width:20%"><?php echo __('Department Capacity')?></th>
<th style="width:17%"><?php echo __('Female Quota')?></th> <th style="width:17%"><?php echo __('Regions Quota')?></th>
<th style="width:21%"><?php echo __('Disability Quota')?></th>
</tr>
<?php
    foreach($generalsummery as $k=>$v){
        echo '<tr><td>'.$v['Department']['name'].'</td><td>'.$v['ParticipatingDepartment']['number'].'</td><td>'.$v['ParticipatingDepartment']['female'].'</td><td>'.$v['ParticipatingDepartment']['regions'].'</td><td>'.$v['ParticipatingDepartment']['disability'].'</td></tr>';
    }
}
?>
</tbody></table>
</td>
</tr>
<tr>
<td colspan=2>
<table><tbody>
<?php if(!empty($reservedmodifiedmatrix)) {
foreach($reservedmodifiedmatrix as $departmentname=>$v)
{break;}
//debug(count($v));
echo '<tr><td colspan="'.(count($v)+2).'"><div class="font" style="font-weight:bold">'.__('Reserved places for department by result category for '.$selectedAcademicYear.' academic year placement.').'</div></td></tr>'; ?>
<?php 
echo '<tr><th>Department</th>';
foreach($reservedmodifiedmatrix as $departmentname=>$v){
    foreach($v as $key=>$value){
        echo '<th>'.$key.')</th>';
    }
    echo '<th>Total</th>';
    break;
}
?>
</tr>
<?php 
    foreach($reservedmodifiedmatrix as $key=>$value){
        echo '<tr><td>'.$key.'</td>';
                 $total=0;
        foreach($value as $k=>$v){
           
            foreach($v as $f=> $m){
            echo '<td>'.$m.'</td>';
            $total=$total+$m;
            }
        }
        
        echo '<td>'.$total.'</td></tr>';
        
      
    }
?>
</tbody>
<?php } ?>
</table></td>
</tr>
<tr><td colspan=2>
<table><tbody>
<?php if(!empty($acceptedStudents)) { ?>
    <tr><th colspan="5"> <?php 
echo '<div class="warning-box warning-message"><span></span>'.__('<p>List of students for '.$selectedAcademicYear.
' academic year who are legible for placement but didn\'t fill their department placement preferences.</p> <p style="font-weight:normal; font-weight:11px">
Please complete their preferences. Otherwise the system will assign 
the students to the department randomly based on available space.</p>',true).'</div>'; ?></th></tr>
	
	<tr>
            <th><?php echo ('Full Name');?></th>
			<th><?php echo ('Sex');?></th>
			<th><?php echo ('Student Number');?></th>
			
			
			<th><?php echo ('Placement Type');?></th>
			<th><?php echo ('Actions');?></th>
			
	</tr>
	<?php
	$i = 0;

	foreach ($acceptedStudents as $acceptedStudent):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
      
        <td><?php echo $acceptedStudent['AcceptedStudent']['full_name']; ?>&nbsp;</td>
		<td><?php echo $acceptedStudent['AcceptedStudent']['sex']; ?>&nbsp;</td>
		<td><?php echo $acceptedStudent['AcceptedStudent']['studentnumber']; ?>&nbsp;</td>
			
		<td><?php echo $acceptedStudent['AcceptedStudent']['placementtype']; ?>&nbsp;</td>
		<td>
		<?php echo $this->Html->link(__('Add Preference'), array('controller'=>'preferences','action' => 'add', $acceptedStudent['AcceptedStudent']['id'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
<?php } ?>
</tbody>
	</table>
</td></tr>
</tbody>
</table>

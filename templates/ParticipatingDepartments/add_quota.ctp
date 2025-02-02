<?php //echo $this->Html->script('jquery-1.6.2.min'); ?>  
<?php //echo $this->Html->script('jquery-department_placement');?>
<script>
function toggleView(id) {
	if($('#c'+id).css("display") == 'none')
		$('#i'+id).attr("src", '/img/minus2.gif');
	else
		$('#i'+id).attr("src", '/img/plus2.gif');
	$('#c'+id).toggle("slow");
}
</script>
<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
             
<h3>Department's Quota in the Auto Student Placement</h3>
<div class="quotas form">
<?php 
   // debug($check_auto_placement_already_run_not_allow_adding_or_edit);
   if (!isset($hide_search)) {
     echo $this->Form->create('ParticipatingDepartment', array('action' => 'add_quota')); 
?>
<table class="small_padding fs16" ><tr> 
	    <tr>
	        <td style='width:15%'> Academic Year:
	        </td>
	        <td style='width:75%'>
	          <?php 
	              
			        echo $this->Form->input('ParticipatingDepartment.academic_year',array('id'=>'academicyear',
                    'label' =>false,'type'=>'select','options'=>$acyear_array_data,
                    'empty'=>"--Select Academic Year--",'selected'=>isset($selected)?$selected:'')); ?>
            </td>
	    </tr>
	<tr><td colspan=2><?php echo $this->Form->Submit('Continue',array('div'=>false,
 'name'=>'quotaacademicyear','class'=>'tiny radius button bg-blue')); ?> </td>	
    </tr>

</table>
<?php } ?>

<div id="quotaparticiptingdepartment"></div>
<?php 
    if (isset($check_auto_placement_already_run_not_allow_adding_or_edit)
       &&$check_auto_placement_already_run_not_allow_adding_or_edit>0) {
          echo '<div class="info-box info-message"><span></span>Participating Department quota for '.$selectedAcademicYear.' academic year for student auto placement to department for '.$college_name.'. You have already run the auto placement, you can not add or edit quota now.</div>';
       }
       ?>
<?php if(!empty($departments)) { ?>
<?php 
if (isset($total_students_for_placement)) {
     echo "<table>";
     echo "<tbody>";
     //If it is preparatory result
     	echo "<tr><td style='font-weight:bold; width:20%'>Total number of not placed students: ".number_format($total_students_for_placement, 0, '.', ',')."</td></tr>";
     if(isset($total_students_with_fm_result)) {
     	echo "<tr><td style='font-weight:bold; width:20%'>Total number of students who can participate in the placement: ".number_format($total_students_with_fm_result, 0, '.', ',')."</td></tr>";
     	if($total_students_for_placement > $total_students_with_fm_result) {
     		echo "<tr><td class='fs13'><strong>Important Note:</strong> ".
     		number_format(($total_students_for_placement - $total_students_with_fm_result), 0, '.', ',')." students are either their registered and add course grade is not fully submitted OR they are dismissed. As a result, you can make department quota based on only ".$total_students_with_fm_result." students. If student semester status is generated while you are working on the placement, you will be required to adjust department quota otherwise they will be handled using manual placement tool.</td></tr>";
     	}
     }
     echo "</tbody></table>";
}
?>
<table width="50%"><tbody>

<tr><th colspan=3> <?php echo __('Total Number of privileged students ') ?></th></tr>
   <tr><th><?php echo "Region"; ?></th><th>
    <?php echo "Female"; ?></th><th><?php echo "Disability" ?></th></tr>

   
    <tr><td><?php echo $quota_sum['region'] ?></td><td>
    <?php echo $quota_sum['female'] ?></td><td><?php echo $quota_sum['disable'] ?></td></tr>
</tbody></table>

<p onclick="toggleView('all_stat')" class="fs15" style="font-weight:bold; padding-bottom:0px; margin-bottom:0px"><?php echo $this->Html->image('plus2.gif', array('id' => 'iall_stat')); ; ?> All student preference stat</p>
<div id="call_stat" style="display:none">
<table>
	<tr>
		<th style="width:25%">Department</th>
		<?php
		for($i = 1; $i <= count($stat['all']); $i++) {
			echo '<th style="width:'.(75/count($stat['all'])).'%">'.$i.'</th>';
		}
		?>
	</tr>
	<?php
	foreach($stat['all'] as $stat_dep) {
		?>
		<tr>
			<td><?php echo $stat_dep['department_name']; ?></td>
			<?php
			$preference_sum = 0;
			for($i = 1; $i <= count($stat['all']); $i++) {
			?>
				<td>
					<table style="width:100%" cellpadding="0" cellspacing="0">
						<?php
						foreach($stat_dep['count'][$i] as $k => $v) {
							if(strcasecmp($k, '~total~') != 0) {
								echo '<tr>

										<td style="width:50%; background:transparent">'.$k.':</td>';
								echo '<td style="width:50%; text-align:right; background:transparent">'.$v.'</td>

										</tr>';
							}
						}
					?>
						<tr>
							<td style="background:transparent">Total:</td>
							<td style="text-align:right; background:transparent" colspan="2"><?php echo $stat_dep['count'][$i]['~total~']; ?></td>
						</tr>
					</table>
				</td>
			<?php
			}
			?>
		</tr>
		<?php
	}
	?>
</table>
</div>


<p onclick="toggleView('female_stat')" class="fs15" style="font-weight:bold; padding-bottom:0px; margin-bottom:0px"><?php echo $this->Html->image('plus2.gif', array('id' => 'ifemale_stat')); ; ?> Female students preference stat</p>

<div id="cfemale_stat" style="display:none">
<table>
	<tr>
		<th style="width:25%">Department</th>
		<?php
		for($i = 1; $i <= count($stat['female']); $i++) {
			echo '<th style="width:'.(75/count($stat['female'])).'%">'.$i.'</th>';
		}
		?>
	</tr>
	<?php
	foreach($stat['female'] as $stat_dep) {
		?>
		<tr>
			<td><?php echo $stat_dep['department_name']; ?></td>
			<?php
			$preference_sum = 0;
			for($i = 1; $i <= count($stat['female']); $i++) {
			?>
				<td>
					<table style="width:100%" cellpadding="0" cellspacing="0">
						<?php
						foreach($stat_dep['count'][$i] as $k => $v) {
							if(strcasecmp($k, '~total~') != 0) {
								echo '<tr>

										<td style="width:50%; background:transparent">'.$k.':</td>';
								echo '<td style="width:50%; text-align:right; background:transparent">'.$v.'</td>

										</tr>';
							}
						}
					?>
						<tr>
							<td style="background:transparent">Total:</td>
							<td style="text-align:right; background:transparent" colspan="2"><?php echo $stat_dep['count'][$i]['~total~']; ?></td>
						</tr>
					</table>
				</td>
			<?php
			}
			?>
		</tr>
		<?php
	}
	?>
</table>
</div>


<p onclick="toggleView('region_stat')" class="fs15" style="font-weight:bold; padding-bottom:0px; margin-bottom:0px"><?php echo $this->Html->image('plus2.gif', array('id' => 'iregion_stat')); ; ?> Developing regions preference stat</p>
<div id="cregion_stat" style="display:none">
<table>
	<tr>
		<th style="width:25%">Department</th>
		<?php
		for($i = 1; $i <= count($stat['region']); $i++) {
			echo '<th style="width:'.(75/count($stat['region'])).'%">'.$i.'</th>';
		}
		?>
	</tr>
	<?php
	foreach($stat['region'] as $stat_dep) {
		?>
		<tr>
			<td><?php echo $stat_dep['department_name']; ?></td>
			<?php
			$preference_sum = 0;
			for($i = 1; $i <= count($stat['region']); $i++) {
			?>
				<td>
					<table style="width:100%" cellpadding="0" cellspacing="0">
						<?php
						foreach($stat_dep['count'][$i] as $k => $v) {
							if(strcasecmp($k, '~total~') != 0) {
								echo '<tr>

										<td style="width:50%; background:transparent">'.$k.':</td>';
								echo '<td style="width:50%; text-align:right; background:transparent">'.$v.'</td>

										</tr>';
							}
						}
					?>
						<tr>
							<td style="background:transparent">Total:</td>
							<td style="text-align:right; background:transparent" colspan="2"><?php echo $stat_dep['count'][$i]['~total~']; ?></td>
						</tr>
					</table>
				</td>
			<?php
			}
			?>
		</tr>
		<?php
	}
	?>
</table>
</div>


<p onclick="toggleView('disable_stat')" class="fs15" style="font-weight:bold; padding-bottom:0px; margin-bottom:0px"><?php echo $this->Html->image('plus2.gif', array('id' => 'idisable_stat')); ; ?> Disable students preference stat</p>
<div id="cdisable_stat" style="display:none">
<table>
	<tr>
		<th style="width:25%">Department</th>
		<?php
		for($i = 1; $i <= count($stat['disable']); $i++) {
			echo '<th style="width:'.(75/count($stat['disable'])).'%">'.$i.'</th>';
		}
		?>
	</tr>
	<?php
	foreach($stat['disable'] as $stat_dep) {
		?>
		<tr>
			<td><?php echo $stat_dep['department_name']; ?></td>
			<?php
			$preference_sum = 0;
			for($i = 1; $i <= count($stat['disable']); $i++) {
			?>
				<td>
					<table style="width:100%" cellpadding="0" cellspacing="0">
						<?php
						foreach($stat_dep['count'][$i] as $k => $v) {
							if(strcasecmp($k, '~total~') != 0) {
								echo '<tr>

										<td style="width:50%; background:transparent">'.$k.':</td>';
								echo '<td style="width:50%; text-align:right; background:transparent">'.$v.'</td>

										</tr>';
							}
						}
					?>
						<tr>
							<td style="background:transparent">Total:</td>
							<td style="text-align:right; background:transparent" colspan="2"><?php echo $stat_dep['count'][$i]['~total~']; ?></td>
						</tr>
					</table>
				</td>
			<?php
			}
			?>
		</tr>
		<?php
	}
	?>
</table>
</div>
<table>
<tbody>
<?php echo $this->Form->create('ParticipatingDepartment',array('action'=>'add_quota'));?>

<tr><td colspan="6"><?php
if(isset($already_added_capacity) && $already_added_capacity>0){
 __('<strong>Edit or Re-adjust Departments Quota</strong>');
 } else {
 __('<strong>Add Departments Quota</strong>');
 }
  ?></td></tr>

<?php 
    $count=0;
    echo '<tr>';
    echo '<td>Department</td>';
    echo '<td>Department Capacity</td><td>Femal Quota (if exist)</td><td>Region Quota (if exist)</td><td>Disability Quota (if exist)</td>';
    echo '<td>Action</td>';
    echo '</tr>';
    echo $this->Form->hidden('ParticipatingDepartment.academic_year',array('value'=>$selectedAcademicYear));
    foreach($departments as $key=>$value){
       
       if (isset($check_auto_placement_already_run_not_allow_adding_or_edit)
       && $check_auto_placement_already_run_not_allow_adding_or_edit>0) {
              
            echo '<tr><td style="vertical-align:bottom; width:20%">'.$value['Department']['name'].'</td><td>';
            echo $value['ParticipatingDepartment']['number'];
            echo '</td><td>';
            echo $value['ParticipatingDepartment']['female'];
            echo '</td><td>';
            echo $value['ParticipatingDepartment']['regions'];
            echo '</td><td>';   
            echo $value['ParticipatingDepartment']['disability'];
            echo '</td>';
            echo '<td>&nbsp;</td>';
            echo '</tr>';
    
       } else {
       
       
        echo $this->Form->hidden('ParticipatingDepartment.'.$count.'.id',array('value'
        =>$value['ParticipatingDepartment']['id']));
         echo $this->Form->hidden('ParticipatingDepartment.'.$count.'.department_id',array('value'
        =>$value['ParticipatingDepartment']['department_id']));
        echo '<tr><td style="vertical-align:bottom; width:20%">'.$value['Department']['name'].'</td><td>';
        echo $this->Form->input('ParticipatingDepartment.'.$count.'.number',
        array('value'=>(empty($this->request->data['ParticipatingDepartment'][$count]['number'])?$value['ParticipatingDepartment']['number'] : $this->request->data['ParticipatingDepartment'][$count]['number']),
        'label'=>'Department Capacity', 'style' => 'width:150px','label'=>false)).'</td><td>';
         echo $this->Form->input('ParticipatingDepartment.'.$count.'.female',
        array('value'=> (empty($this->request->data['ParticipatingDepartment'][$count]['female'])?$value['ParticipatingDepartment']['female'] : $this->request->data['ParticipatingDepartment'][$count]['female']),
        'label'=>'Female', 'style' => 'width:150px','label'=>false)).'</td><td>';
        echo $this->Form->input('ParticipatingDepartment.'.$count.'.regions',
        array('value'=> (empty($this->request->data['ParticipatingDepartment'][$count]['regions'])?$value['ParticipatingDepartment']['regions'] : $this->request->data['ParticipatingDepartment'][$count]['regions']),
        'label'=>'Regions Quota', 'style' => 'width:150px','label'=>false)).'</td><td>';   
        
         echo $this->Form->input('ParticipatingDepartment.'.$count.'.disability',
        array('value'=> (empty($this->request->data['ParticipatingDepartment'][$count]['disability']) ? $value['ParticipatingDepartment']['disability']:$this->request->data['ParticipatingDepartment'][$count]['disability']),
        'label'=>'Disability Quota', 'style' => 'width:150px','label'=>false)).'</td>';
        echo '<td>';
          if (!empty($value['ParticipatingDepartment']['id'])) {
		              
	          $tmp_academic_year=str_replace('/', "-",
	          $value['ParticipatingDepartment']['academic_year']);
	          
		      $action_participating_id='add_quota~participating_departments~'.$tmp_academic_year;
		                          
		   }
		   if(!empty($action_participating_id)) {
		                    
		                    echo $this->Html->link(__('Delete'), 
		                    array('action' => 'delete', $value['ParticipatingDepartment']['id'],
		                     $action_participating_id), null, 
		                    sprintf(__('Are you sure you want to delete # %s?'), 
		                    $value['Department']['name'])); 
		                    
		   }
        echo '</td>';
        echo '</tr>';
        
          /*

          

		  */
                   
       } 
        $count++;
      
    }
?>
<tr>
	<td colspan="5">&nbsp;</td>
</tr>
<tr>
<td colspan="6" class="font">Please select region/s that will be considered as developing region and entitled to privilaged quota, if there is any.</td>
</tr>
<tr>
<td colspan="5">
<?php

$selected_region_ids=array();
if(isset($selected_regions)){
  $selected_region_ids=explode(',',$selected_regions);
}
if (isset($check_auto_placement_already_run_not_allow_adding_or_edit)
&& $check_auto_placement_already_run_not_allow_adding_or_edit>0) {
  echo $this->Form->input('ParticipatingDepartment.developing_regions_id', 
array('type' => 'select', 'multiple' => 'checkbox',
'div'=>'input select','disabled'=>true,'value'=>(empty($this->request->data['ParticipatingDepartment']['developing_regions_id']) ? $selected_region_ids:$this->request->data['ParticipatingDepartment']['developing_regions_id'])));  

} else {
  echo $this->Form->input('ParticipatingDepartment.developing_regions_id', 
array('type' => 'select', 'multiple' => 'checkbox',
'div'=>'input select','value'=>(empty($this->request->data['ParticipatingDepartment']['developing_regions_id']) ? $selected_region_ids:$this->request->data['ParticipatingDepartment']['developing_regions_id'])));
}

?>
</td>
</tr>
</tbody>
</table>

<?php 
 if (isset($check_auto_placement_already_run_not_allow_adding_or_edit)
       && $check_auto_placement_already_run_not_allow_adding_or_edit>0) {
       
       } else {
         echo $this->Form->Submit('Save Quota',array('div'=>false,
 'name'=>'quota','class'=>'tiny radius button bg-blue'));       
       }
?>
<?php } ?>
</div>


	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->

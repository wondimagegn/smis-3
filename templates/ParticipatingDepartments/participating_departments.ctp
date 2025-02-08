<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
             
<div class="participatingDepartments form">
<table>
<tbody>
<?php echo $this->Form->create('ParticipatingDepartment',array('action'=>'add_quota'));?>

<tr><td class='headerfont' colspan="4"><?php echo __('Add Departments Quota') ?></td></tr>
<?php 
    //debug($departments);
    foreach($departments as $key=>$value){
        echo $this->Form->hidden('ParticipatingDepartment.'.$key.'.id',array('value'
        =>$value['ParticipatingDepartment']['id']));
        echo '<tr><td>'.$value['Department']['name'].'</td><td>'.$this->Form->input('ParticipatingDepartment.'.$key.'.number',array('value'
        =>$value['ParticipatingDepartment']['number'],'label'=>'Department Capacity')).'</td>
        <td>'.$this->Form->input('ParticipatingDepartment.'.$key.'.female',array('value'
        =>$value['ParticipatingDepartment']['female'],'label'=>'Female Quota')).'</td>
        <td>'.$this->Form->input('ParticipatingDepartment.'.$key.'.regions',array('value'
        =>$value['ParticipatingDepartment']['female'],'label'=>'Regions Quota')).'
        </td></tr>';
       
    }
?>
<tr>
<td class="font">List of regions.Please check the  developing regions you
want to give privilaged quota
</td>
<td>
<?php 
echo $this->Form->input('ParticipatingDepartment.developing_regions_id', 
array('type' => 'select', 'multiple' => 'checkbox',
'div'=>'input select'));
?>
</td>
</tr>
</tbody>
</table>
<?php echo $this->Form->Submit('Submit',
array('class'=>'tiny radius button bg-blue'));?>
</div>
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->

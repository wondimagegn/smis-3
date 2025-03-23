<?php  
echo $this->Form->create('Preference');

?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
             
<div class="preferences index">
	
	<?php 
	if($role_id!=ROLE_STUDENT ){

?>
     
<table cellspacing="0" cellpadding="0" class="fs13">
	<tr>
		<td style="width:11%">Academic Year</td>

		<td style="width:25%;">
		<?php 		
			echo $this->Form->input('Preference.academicyear',array('id'=>'Academicyear',
            'label' => 'Academic Year','label'=>false,'type'=>'select','onchange'=>'updateDepartmentListOnChangeofOtherField()',
'options'=>$acyear_array_data,
'empty'=>'--Select Academic Year--'
));
?>
		</td>

		<td style="width:11%">Preference Order:</td>
		<td style="width:25%"><?php 
		
		echo $this->Form->input('Preference.preferences_order',array('options'=>array('1'=>'1','2'=>'2','3'=>'3','4'=>'4','5'=>'5','6'=>'6','7'=>'7'),
			'label'=>false,
			'style'=>'width:80px;')
			);
		?></td>

	</tr>
	
	<tr>
		<td>Department:</td>
		<td><?php echo $this->Form->input('department_id', array('id' => 'Department', 'class' => 'fs13', 'label' => false, 'type' => 'select', 'options' => $departments)); ?></td>
		
		<td>Limit:</td>
		<td>
                   <?php echo $this->Form->input('limit', array('id' => 'Limit', 'class' => 'fs13', 'label' => false)); ?>
<?php echo $this->Form->hidden('page', array('value'=>1)); ?>
                </td>
	</tr>
	
	<tr>
		<td colspan="6">
		<?php echo $this->Form->submit(__('List Students Preference', true), array('name' => 'listStudentsPreference','id'=>'Search','class'=>'tiny radius button bg-blue', 'div' => false)); ?>
		</td>
	</tr>
</table>

<?php 

}
?>
	
	<div id="preference_list">
	   <?php 
	   if($role_id==ROLE_STUDENT){
	   echo '<div class="smallheading info-box info-message"><span></span>Important Note: Please fill or change your

	   departmental placement preference before the deadline. <br/> Deadline: '.
	   $this->Format->humanize_date($preference_deadline['PreferenceDeadline']['deadline']).'</div>';
	   }
	   ?>
	</div>
	
<?php if(isset($preferences)&& !empty($preferences)) { ?>
<h3><?php echo __('List of Student Department Placement Preferences');?></h3>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th>S.N<u>o</u></th>
			<th><?php echo $this->Paginator->sort('full_name');?></th>
			<th><?php echo $this->Paginator->sort('Academic Year', 'academicyear');?></th>
			<th><?php echo $this->Paginator->sort('department_id');?></th>
			<th><?php echo $this->Paginator->sort('preferences_order');?></th>
			
			<th class="actions"><?php echo __('Actions');?></th>
	</tr>
	<?php
	$i = 0;
	$start = $this->Paginator->counter('%start%');
	foreach ($preferences as $preference):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $start++; ?></td>
		<td>
			<?php echo $this->Html->link($preference['AcceptedStudent']['full_name'], array('controller' => 'AcceptedStudents', 'action' => 'view', $preference['AcceptedStudent']['id'])); ?>
		</td>
		<td><?php echo $preference['Preference']['academicyear']; ?>&nbsp;</td>
		<td>
			<?php echo $this->Html->link($preference['Department']['name'], array('controller' => 'departments', 'action' => 'view', $preference['Department']['id'])); ?>
		</td>
		<td><?php echo $preference['Preference']['preferences_order']; ?>&nbsp;</td>
		<td class="actions">
			
			<?php echo $this->Html->link(__('Edit'), array('action' => 'edit_preference', $preference['Preference']['accepted_student_id'])); ?>
			<?php echo $this->Html->link(__('Delete'), array('action' => 'delete', $preference['Preference']['id']), null, sprintf(__('Are you sure you want to delete  %s preferences ?'),$preference['AcceptedStudent']['full_name'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
	</table>
	<p>
	<?php
	echo $this->Paginator->counter(array(
	'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%')
	));
	?>	</p>

	<div class="paging">
		<?php echo $this->Paginator->prev('<< ' . __('previous'), array(), null, array('class'=>'disabled'));?>
	 | 	<?php echo $this->Paginator->numbers();?>
 |
		<?php echo $this->Paginator->next(__('next') . ' >>', array(), null, array('class' => 'disabled'));?>
	</div>
<?php }  ?>
</div>
<?php 
 //echo $this->Js->writeBuffer(); // Write cached scripts
 
 ?>
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row -->
   </div> <!-- end of box-body -->
</div> <!-- end of box -->



<script>
function updateDepartmentListOnChangeofOtherField() 
{
          
			var formData='';
			var academic_year= $("#Academicyear").val().replace("/", "-");
            if(typeof academic_year!="undefined") 
            {
                 formData = academic_year;
		    } else {
              return false;
		    }
			
           
            $("#Department").attr('disabled', true);
			$("#Search").attr('disabled',true);
			//get  participating department
            var formUrl = '/participatingDepartments/getParticipatingDepartment/'+formData;
           
            $.ajax({
                type: 'get',
                url: formUrl,
                data: formData,
                success: function(data,textStatus,xhr){
				        $("#Academicyear").attr('disabled', false);
					    $("#Department").attr('disabled', false);
                        $("#Search").attr('disabled',false);
			
						$("#Department").empty();
					    $("#Department").append(data);
                    
					},
                error: function(xhr,textStatus,error){
                        alert(textStatus);
                }
			});
         	
			return false;
		
 }

</script>


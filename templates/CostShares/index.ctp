<style type="text/css">
	

</style>
<?php 
echo $this->Form->Create('CostShare',array('action'=>'search'));
?>
<script type='text/javascript'>
function updateSection(id) {
            //serialize form data
            var formData = $("#department_id_"+id).val();
			$("#section_id_"+id).attr('disabled', true);
			$("#department_id_"+id).attr('disabled',true);	
					//get form action
			var formUrl = '/sections/get_sections_by_dept/'+formData;
			$.ajax({
				type: 'get',
				url: formUrl,
				data: formData,
				success: function(data,textStatus,xhr){
						$("#section_id_"+id).attr('disabled', false);
					    $("#department_id_"+id).attr('disabled',false);	
						$("#section_id_"+id).empty();
						$("#section_id_"+id).append(data);
						
				},
				error: function(xhr,textStatus,error){
						alert(textStatus);
				}
			});
			return false;
 }
 
function updateCollegeSection(id) {
            //serialize form data
            var formData = $("#college_id_"+id).val();
			$("#college_id_"+id).attr('disabled', true);
			$("#section_id_"+id).attr('disabled', true);
			//get form action
			var formUrl = '/sections/get_sections_of_college/'+formData;
			$.ajax({
				type: 'get',
				url: formUrl,
				data: formData,
				success: function(data,textStatus,xhr){
						
						$("#section_id_"+id).attr('disabled', false);
                       $("#college_id_"+id).attr('disabled', false);
						$("#section_id_"+id).empty();
						$("#section_id_"+id).append(data);
						
				},
				error: function(xhr,textStatus,error){
						alert(textStatus);
				}
			});

			return false;
      
}

</script>
<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
             
<?php  if ($role_id != ROLE_STUDENT) { ?>
 	<div class="smallheading"><?php echo __('CostShare  search');?></div>
	<?php

	   
	    echo '<table><tr><td>';
        echo '<table class="fs13 small_padding">';	
        ?>
       <tr>
			<td style="width:13%">Academic Year:</td>
			<td style="width:37%"><?php echo $this->Form->input('Search.academic_year',array('options'=>$acyear_array_data,'label'=>false));?></td>
        <?php 
        if (isset($college_ids) && !empty($college_ids)) {
        ?>
            <td style="width:13%">College:</td>
        <?php 
            echo '<td  style="width:37%">'.$this->Form->input('Search.college_id',array('empty'=>' ',
        'id'=>'college_id_1','onchange'=>'updateCollegeSection(1)','label'=>false,
        'style'=>'width:250px')).'</td>';
        
        } else {
        ?>
          <td style="width:13%">Department:</td>
        <?php 
           echo '<td style="width:37%">'.$this->Form->input('Search.department_id',array('empty'=>' ',
        'id'=>'department_id_1','onchange'=>'updateSection(1)','label'=>false,
        'style'=>'width:250px')).'</td>';
        
        }
        ?>
		</tr>
		
		<tr>
			<td style="width:13%">Student Name:</td>
			<td style="width:37%"><?php echo $this->Form->input('Search.name',array('empty'=>' ','label'=>false));?></td>
            <td style="width:13%">Section:</td>
			<td style="width:37%"><?php 
		           echo $this->Form->input('Search.section_id',array('empty'=>'','id'=>'section_id_1',
		           'label'=>false));
			?></td>
		</tr>
		<tr>
			<td style="width:13%">Completed:</td>
			<td style="width:37%">
			<?php echo $this->Form->input('Search.completion',array('options'=>array('yes'=>'Yes','no'=>'No'),'label'=>false));?>
			</td>
           
		</tr>
		<?php 
      
  		echo '</table>';
		echo '</td><td>';
		
		echo '</td></tr>';
		echo '</table>';
		
		echo $this->Form->submit('Search',
array('class'=>'tiny radius button bg-blue'));
	}
	?>
<?php echo $this->Form->end();?>

<div class="costShares index">
<?php if (!empty($costShares)) { ?>
	<div class="smallheading"><?php echo __('Cost Shares');?></div>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th>S.N<u>o</u></th>
			<th><?php echo $this->Paginator->sort('student_id');?></th>
			<th><?php echo $this->Paginator->sort('academic_year');?></th>
			<th><?php echo $this->Paginator->sort('sharing_cycle');?></th>

			<th><?php echo $this->Paginator->sort('education_fee');?></th>
			<th><?php echo $this->Paginator->sort('accomodation_fee');?></th>
			<th><?php echo $this->Paginator->sort('cafeteria_fee');?></th>
			<th><?php echo $this->Paginator->sort('medical_fee');?></th>
			<th><?php echo $this->Paginator->sort('cost_sharing_sign_date');?></th>
		     <?php 
		 if ($role_id != ROLE_STUDENT ) {
		?>	
			<th class="actions"><?php echo __('Actions');?></th>
	
	    <?php 
	        }
	    ?>
	</tr>
	<?php
	$i = 0;
	$start = $this->Paginator->counter('%start%');
	foreach ($costShares as $costShare):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>

	<tr <?php echo $class;?>>
		<td><?php echo $start++; ?>&nbsp;</td>
		<td class='jsView' data-animation="fade" data-reveal-id="myModal" data-reveal-ajax="/students/get_modal_box/<?php echo $costShare['Student']['id'];?>" >
			<?php 
			echo $costShare['Student']['full_name'];

			?>
		</td>
		<td><?php echo $costShare['CostShare']['academic_year']; ?>&nbsp;</td>
		<td><?php echo $costShare['CostShare']['sharing_cycle']; ?>&nbsp;</td>

		<td><?php echo $costShare['CostShare']['education_fee']; ?>&nbsp;</td>
		<td><?php echo $costShare['CostShare']['accomodation_fee']; ?>&nbsp;</td>
		<td><?php echo $costShare['CostShare']['cafeteria_fee']; ?>&nbsp;</td>
		<td><?php echo $costShare['CostShare']['medical_fee']; ?>&nbsp;</td>
		<td><?php echo $costShare['CostShare']['cost_sharing_sign_date']; ?>&nbsp;</td>
		<?php 
		 if ($role_id != ROLE_STUDENT ) {
		?>
		<td class="actions">
			<?php
			
		   echo $this->Html->link(__('Edit'), array('action' => 'edit', $costShare['CostShare']['id'])); 
			
			?>
		</td>
	    <?php } ?>
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
<?php } ?>

<?php if (!empty($studentsWithoutCostShares)) {

 ?>
	<div class="smallheading"><?php echo __('List of students whose costsharing not maintained  '.$this->request->data['Search']['academic_year']);?></div>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th>S.N<u>o</u></th>
			<th><?php echo $this->Paginator->sort('student_id');?></th>
			
			<th><?php echo $this->Paginator->sort('Student.studentnumber');?></th>
			<th><?php echo $this->Paginator->sort('Student.gender');?></th>

			<th><?php echo $this->Paginator->sort('Student.department_id');?></th>

			<th><?php echo $this->Paginator->sort('Student.program_id');?></th>

			
	</tr>
	<?php
	$i = 0;
	$start = $this->Paginator->counter('%start%');
	foreach ($studentsWithoutCostShares as $studentsWithoutCostShare):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $start++; ?>&nbsp;</td>
		<td>
			<?php echo $this->Html->link($studentsWithoutCostShare['Student']['full_name'], array('controller' => 'students', 'action' => 'view', $studentsWithoutCostShare['Student']['id'])); ?>
		</td>
		<td><?php echo $studentsWithoutCostShare['Student']['studentnumber']; ?>&nbsp;</td>
		<td><?php echo $studentsWithoutCostShare['Student']['gender']; ?>&nbsp;</td>

		<td><?php echo $studentsWithoutCostShare['Department']['name']; ?>&nbsp;</td>

		<td><?php echo $studentsWithoutCostShare['Program']['name']; ?>&nbsp;</td>
		
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
<?php } ?>
</div>
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->

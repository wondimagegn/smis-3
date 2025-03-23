<?php echo $this->Form->create('TakenProperty');?>
<script type='text/javascript'>
function updateDepartmentCollege(id) {
           
            //serialize form data
            var formData = $("#college_id_"+id).val();
			$("#college_id_"+id).attr('disabled', true);
			$("#department_id_"+id).attr('disabled', true);
			
			//get form action
            var formUrl = '/departments/get_department_combo/'+formData;
            $.ajax({
                type: 'get',
                url: formUrl,
                data: formData,
                success: function(data,textStatus,xhr){
						$("#department_id_"+id).attr('disabled', false);
						$("#college_id_"+id).attr('disabled', false);
						$("#department_id_"+id).empty();
						$("#department_id_"+id).append('<option style="width:200px"></option>');
						
						$("#department_id_"+id).append(data);
					},
                error: function(xhr,textStatus,error){
                        alert(textStatus);
                }
			});
			
			return false;
      
}
</script>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
            
<div class="takenProperties index ">
 <div class="smallheading">View Student Taken Properties </div>
 <table cellpadding="0" cellspacing="0"><tbody>
 <?php 
 $from = date('Y') - 1;
 $to = date('Y') ;
                 
 if ($role_id == ROLE_CLEARANCE || $role_id == ROLE_ACCOMODATION) {        
         echo '<tr><td>'.$this->Form->input('Search.college_id',array('label'=>'College','empty'=>'','style'=>'width:200px','id'=>'college_id_1','onchange'=>'updateDepartmentCollege(1)')).
         '</td>'; 
         echo '<td>'.$this->Form->input('Search.department_id',
         array('label'=>'Department','empty'=>' ','style'=>'width:200px','id'=>'department_id_1')).'</td></tr>';  
		  echo '<tr><td>'.$this->Form->input('Search.studentnumber',array('label'=>'Student Number')).'</td>';
		  echo '<td>'.$this->Form->input('Search.taken_date',array('label'=>'Taken From',
		  'maxYear'=>$to,'type'=>'date')).'</td></tr>';
    } else if ($role_id == ROLE_COLLEGE) {
         echo '<tr><td>&nbsp;</td>'; 
         echo '<td>'.$this->Form->input('Search.department_id',
         array('label'=>'Department','empty'=>' ','style'=>'width:200px')).'</td></tr>';  
		  echo '<tr><td>'.$this->Form->input('Search.studentnumber',array('label'=>'Student Number')).'</td>';
		  echo '<td>'.$this->Form->input('Search.taken_date',array('label'=>'Taken Date','maxYear'=>$to,
		  'type'=>'date')).'</td></tr>';
    } else if ($role_id == ROLE_DEPARTMENT) {
       echo '<tr><td>'.$this->Form->input('Search.studentnumber',array('label'=>'Student Number')).'</td>';
       echo '<td>'.$this->Form->input('Search.taken_date',array('label'=>'Taken Date','maxYear'=>$to,
       'type'=>'date')).'</td></tr>';
    }		
    ?>
<tr><td colspan=2><?php  echo $this->Form->submit('Search',array('name'=>'search','div'=>'false'));  ?> 
</td></tr>
</tbody>
</table> 
</div>

<div class="takenProperties index">
<?php if (!empty($takenProperties)) { ?>
	<div class="smallheading"><?php echo __('List of taken properties.');?></div>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th>S.N<u>o</u></th>
			<th><?php echo $this->Paginator->sort('Property');?></th>
			<th><?php echo $this->Paginator->sort('Student','student_id');?></th>
			<th><?php echo $this->Paginator->sort('office_id');?></th>
			<th><?php echo $this->Paginator->sort('taken_date');?></th>
			<th><?php echo $this->Paginator->sort('returned');?></th>
			<th><?php echo $this->Paginator->sort('return_date');?></th>
			<th><?php echo $this->Paginator->sort('remark');?></th>
			<th class="actions"><?php echo __('Actions');?></th>
	</tr>
	<?php
	$i = 0;
	$start = $this->Paginator->counter('%start%');
	foreach ($takenProperties as $takenProperty):
		$class = null;
		$not_return=null;
		if ($i++ % 2 == 0) {
			 $class = ' class="altrow"';
		}
		
		if ($takenProperty['TakenProperty']['returned']==0 && $takenProperty['TakenProperty']['return_date'] < date('Y-m-d')) {
		       $not_return='style="color:red !important;"';
		} 
	?>
	<tr<?php echo $class;?>  >
		<td <?php  echo $not_return;?> ><?php echo $start++; ?>&nbsp;</td>
		<td <?php  echo $not_return;?> ><?php echo $takenProperty['TakenProperty']['name']; ?>&nbsp;</td>
		
		<td <?php  echo $not_return;?> >
			<?php echo $this->Html->link($takenProperty['Student']['full_name'], array('controller' => 'students', 'action' => 'view', $takenProperty['Student']['id'])); ?>
			
		</td>
		
		<td <?php  echo $not_return;?>>
			<?php echo $this->Html->link($takenProperty['Office']['name'], array('controller' => 'offices', 'action' => 'view', $takenProperty['Office']['id'])); ?>
		</td>
		
		<td <?php  echo $not_return;?>>
		<?php 
		    echo $this->Format->humanize_date($takenProperty['TakenProperty']['taken_date']); 
		?>
		&nbsp;
		
		</td>
		<td <?php  echo $not_return;?>>
		<?php 
		    echo $takenProperty['TakenProperty']['returned'] == 1 ? 'Returned':'Not returned';
		?>
		
		&nbsp;</td>
		<td <?php  echo $not_return;?> ><?php 
		    //debug($takenProperty['TakenProperty']['return_date']);
		    if (!empty($takenProperty['TakenProperty']['return_date'])) {
		        echo $this->Format->humanize_date($takenProperty['TakenProperty']['return_date']); 
		    }
		//echo $takenProperty['TakenProperty']['return_date']; 
		 //echo $this->Form->input('TakenProperty.'.$start.'.return_date',array('label'=>false,'div'=>false)); 
		 ?>
		
		&nbsp;</td>
		<td <?php  echo $not_return;?>><?php echo $takenProperty['TakenProperty']['remark']; ?>&nbsp;</td>
		<td class="actions">
			<?php //echo $this->Html->link(__('Edit'), array('action' => 'edit', $takenProperty['TakenProperty']['id'])); ?>
			<?php echo $this->Html->link(__('Delete'), array('action' => 'delete', $takenProperty['TakenProperty']['id']), null, sprintf(__('Are you sure you want to delete # %s?'), $takenProperty['TakenProperty']['name'])); ?>
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
<?php 

}

?>
</div>
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->

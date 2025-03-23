
<?php echo $this->Form->create('ApplicablePayment');?>
<script type='text/javascript'>
//Sub cat combo
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
						$("#department_id_"+id).append('<option></option>');
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
            
<div class="payments index">
<table class="fs13 small_padding" style="margin-bottom:0px">
    <tr><td class="smallheading" colspan="4">View Applicable payments </td></tr>
	<tr>
		<td style="width:13%"> From:</td>
		<td style="width:37%"><?php echo $this->Form->input('paid_date_from', array('label' => false, 'type' => 'date', 'dateFormat' => 'MDY', 'minYear' =>date('Y')-Configure::read('Calendar.applicationStartYear'), 'maxYear' => date('Y'), 'orderYear' => 'desc','default'=>false)); ?></td>
		<td style="width:13%">To:</td>
		<td style="width:37%"><?php echo $this->Form->input('paid_date_to', array('label' => false, 'type' => 'date', 'dateFormat' => 'MDY', 'minYear' => Configure::read('Calendar.applicationStartYear'), 'maxYear' => date('Y'), 'orderYear' => 'desc')); ?></td>
	</tr>
	
	<tr>
		<td style="width:13%"> College:</td>
		<td style="width:37%"><?php echo $this->Form->input('college_id',array('label'=>false,
		'options'=>$colleges,'empty'=>' ',
		'style'=>'width:250px','id'=>'college_id_1','onchange'=>'updateDepartmentCollege(1)')); ?></td>
		
		<td style="width:13%">Department:</td>
		<?php if (!empty($departments)) { ?>
		<td style="width:37%"><?php echo $this->Form->input('department_id',array('label'=>false,
		'empty'=>' ','options'=>$departments,'empty'=>' ','style'=>'width:250px','id'=>'department_id_1')); ?></td>
	    <?php } else { ?>  
	      <td style="width:37%"><?php echo $this->Form->input('department_id',array('label'=>false,
		'empty'=>' ','options'=>array(),'style'=>'width:250px','id'=>'department_id_1')); ?></td>
	    <?php } ?>
	</tr>
   <tr>
		<td style="width:13%"> Student Number:</td>
		<td style="width:37%"><?php echo $this->Form->input('studentnumber',array('label'=>false)); ?></td>
		<td style="width:13%"> Sponsor Type:</td>
		<td style="width:37%"><?php echo $this->Form->input('sponsor_type',array('label'=>false)); ?></td>
	</tr>
   
	<tr>
			<td colspan="4"><?php echo $this->Form->submit(__('View Applicable Payment'), array('name' => 'viewPayment', 'id' => 'ViewPaymentButton','class'=>'tiny radius button bg-blue', 'div' => false)); ?></td>
	</tr>
	</table>
</div>
<div class="applicablePayments index">
<?php 
    if (!empty($applicablePayments)) {
?>
	<h2><?php echo __('Applicable Payments');?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th>S.N<u>o</u></th>
			<th><?php echo $this->Paginator->sort('student_id','Full name');?></th>
			<th><?php echo $this->Paginator->sort('academic_year','Academic Year');?></th>
			<th><?php echo $this->Paginator->sort('semester','Semester');?></th>
			<th><?php echo $this->Paginator->sort('department_id','Department');?></th>
			<th><?php echo $this->Paginator->sort('Program/ProgramType','program_id');?></th>
			<th><?php echo $this->Paginator->sort('tutition_fee');?></th>
			<th><?php echo $this->Paginator->sort('meal');?></th>
			<th><?php echo $this->Paginator->sort('accomodation');?></th>
			<th><?php echo $this->Paginator->sort('health');?></th>
			<th><?php echo $this->Paginator->sort('sponsor_type');?></th>
			<th><?php echo $this->Paginator->sort('sponsor_name');?></th>
			<th class="actions" ><?php echo __('Actions');?></th>
	</tr>
	<?php
	$i = 0;
	$start = $this->Paginator->counter('%start%');
	
	foreach ($applicablePayments as $applicablePayment):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $start++; ?>&nbsp;</td>
		<td  class='jsView' data-animation="fade" data-reveal-id="myModal" data-reveal-ajax="/students/get_modal_box/<?php echo $applicablePayment['Student']['id'];?>">
			<?php echo $applicablePayment['Student']['full_name'];  ?>
		</td>
		
		<td>
			<?php echo $applicablePayment['ApplicablePayment']['academic_year']; ?>
		</td>
		<td>
			<?php echo $applicablePayment['ApplicablePayment']['semester']; ?>
		</td>
		
		<td>
			<?php echo $applicablePayment['Student']['Department']['name']; ?>
		</td>
		
		<td>
			<?php echo $applicablePayment['Student']['Program']['name'].'/'.$applicablePayment['Student']['ProgramType']['name']; ?>
		</td>
		<td><?php 
		    if ($applicablePayment['ApplicablePayment']['tutition_fee']==1) {
		        echo 'Yes';
		    } else {
		        echo 'No';
		        
		    }
		    
		  ?>&nbsp;</td>
		<td><?php 
		
		    if ($applicablePayment['ApplicablePayment']['meal']==1) {
		         echo 'Yes';
		    } else {
		         echo 'No'; 
		    }
		
		?>&nbsp;</td>
		<td><?php 
		    if ($applicablePayment['ApplicablePayment']['accomodation']==1) {
		         echo 'Yes';
		    } else {
		         echo 'No'; 
		    }
		    
		    
		  ?>&nbsp;</td>
		<td><?php 
		
		     if ($applicablePayment['ApplicablePayment']['health']==1) {
		         echo 'Yes';
		    } else {
		         echo 'No'; 
		    }
		     ?>&nbsp;</td>
		<td><?php echo $applicablePayment['ApplicablePayment']['sponsor_type']; ?>&nbsp;</td>
		<td><?php echo $applicablePayment['ApplicablePayment']['sponsor_name']; ?>&nbsp;</td>
		
		<td class="actions">
			<?php echo $this->Html->link(__('View'), array('action' => 'view', $applicablePayment['ApplicablePayment']['id'])); ?>
			<?php //echo $this->Html->link(__('Edit'), array('action' => 'edit', $applicablePayment['ApplicablePayment']['id'])); ?>
			<?php echo $this->Html->link(__('Delete'), array('action' => 'delete', $applicablePayment['ApplicablePayment']['id']), null, sprintf(__('Are you sure you want to delete # %s?'), $applicablePayment['ApplicablePayment']['id'])); ?>
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

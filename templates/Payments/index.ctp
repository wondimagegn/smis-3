<?php echo $this->Form->create('Payment');?>
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
<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
             
<div class="payments index">
<table class="fs13 small_padding" style="margin-bottom:0px">
    <tr><td class="smallheading" colspan="4">View payments </td></tr>
	<tr>
		<td style="width:13%"> From:</td>
		<td style="width:37%"><?php echo $this->Form->input('paid_date_from', array('label' => false, 'type' => 'date', 'dateFormat' => 'MDY', 'minYear' => Configure::read('Calendar.applicationStartYear'), 'maxYear' => date('Y'), 'orderYear' => 'desc','default'=>false)); ?></td>
		<td style="width:13%">To:</td>
		<td style="width:37%"><?php echo $this->Form->input('paid_date_to', array('label' => false, 'type' => 'date', 'dateFormat' => 'MDY', 'minYear' => Configure::read('Calendar.applicationStartYear'), 'maxYear' => date('Y'), 'orderYear' => 'desc')); ?></td>
	</tr>
	
	<tr>
		<td style="width:13%"> College:</td>
		<td style="width:37%"><?php echo $this->Form->input('college_id',array('label'=>false,
		'options'=>$colleges,
		'style'=>'width:250px','id'=>'college_id_1','onchange'=>'updateDepartmentCollege(1)')); ?></td>
		<td style="width:13%">Department:</td>
		<?php if (!empty($departments)) { ?>
		<td style="width:37%"><?php echo $this->Form->input('department_id',array('label'=>false,
		'empty'=>' ','options'=>$departments,'empty'=>' ','style'=>'width:250px','id'=>'department_id_1')); ?></td>
	    <?php } else { ?>
	        	<td style="width:37%"><?php echo $this->Form->input('department_id',array('label'=>false,
		'empty'=>' ','options'=>array(),'empty'=>' ','style'=>'width:250px','id'=>'department_id_1')); ?></td>

             	    
	    <?php } ?>
	
	</tr>
   <tr>
		<td style="width:13%"> Reference Number:</td>
		<td style="width:37%"><?php echo $this->Form->input('reference_number',array('label'=>false,'required'=>false)); ?></td>
		
	</tr>
   
	<tr>
			<td colspan="4"><?php echo $this->Form->submit(__('View Payment'), array('name' => 'viewPayment', 'id' => 'ViewPaymentButton', 'div' => false)); ?></td>
	</tr>
	</table>
</div>
<div class="payments index">
<?php 
    if (isset($payments) && !empty($payments)) {
    
?>
	<h2><?php echo __('Payments');?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th>S.N<u>o</u></th>
			<th><?php echo $this->Paginator->sort('student_id');?></th>
			<th><?php echo $this->Paginator->sort('academic_year');?></th>
			<th><?php echo $this->Paginator->sort('semester');?></th>
			<th><?php echo $this->Paginator->sort('reference_number');?></th>
			<th><?php echo $this->Paginator->sort('fee_amount');?></th>
			<th><?php echo $this->Paginator->sort('tutition_fee');?></th>
			<th><?php echo $this->Paginator->sort('meal');?></th>
			<th><?php echo $this->Paginator->sort('accomodation');?></th>
			<th><?php echo $this->Paginator->sort('health');?></th>
			<th><?php echo $this->Paginator->sort('payment_date');?></th>
			
			<th class="actions"><?php echo __('Actions');?></th>
	</tr>
	<?php
	$i = 0;
	$start = $this->Paginator->counter('%start%');
	foreach ($payments as $payment):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $start++; ?>&nbsp;</td>
		<td>
			<?php echo $this->Html->link($payment['Student']['full_name'], array('controller' => 'students', 'action' => 'view', $payment['Student']['id'])); ?>
		</td>
		<td><?php echo $payment['Payment']['academic_year']; ?>&nbsp;</td>
		<td><?php echo $payment['Payment']['semester']; ?>&nbsp;</td>
		<td><?php echo $payment['Payment']['reference_number']; ?>&nbsp;</td>
		<td><?php echo $payment['Payment']['fee_amount']; ?>&nbsp;</td>
		<td>
		  <?php 
		        if ($payment['Payment']['tutition_fee']==1) {
		            echo 'Yes';
		        } else {
		            echo 'No';
		            
		        }
		        
		        
		  ?>
		  
		  &nbsp;</td>
		<td><?php 
		       if ($payment['Payment']['meal']==1) {
		            echo 'Yes';
		        } else {
		            echo 'No';
		            
		        }
		       
		    
		    ?>&nbsp;</td>
		<td><?php 
		
		     if ($payment['Payment']['accomodation']==1) {
		            echo 'Yes';
		        } else {
		            echo 'No';
		            
		        }
		?>&nbsp;</td>
		<td><?php 
		     if ($payment['Payment']['health']==1) {
		            echo 'Yes';
		        } else {
		            echo 'No';
		            
		        }
		
		 ?>&nbsp;</td>
		<td><?php echo $payment['Payment']['payment_date']; ?>&nbsp;</td>
		
		<td class="actions">
			
			<?php echo $this->Html->link(__('Delete'), array('action' => 'delete', $payment['Payment']['id']), null, sprintf(__('Are you sure you want to delete # %s?'), $payment['Payment']['id'])); ?>
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

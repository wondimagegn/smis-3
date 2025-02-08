<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
             
<div class="courseExemptions index">
<?php echo $this->Form->create('CourseExemption');?>
<p class="smallheading">View Exemption .</p>
<table cellspacing="0" cellpadding="0" class="fs14">
		<tr>
		    <td style="width:13%"> Program:</td>
		    <td style="width:37%"><?php echo $this->Form->input('Search.program_id',array('label'=>false,
		    'style'=>'width:250px','id'=>'program_id_1','empty'=>' ')); ?></td>
		    <td style="width:13%">Department:</td>
		    <td style="width:37%"><?php echo $this->Form->input('Search.department_id',array('label'=>false,
		    'empty'=>' ','style'=>'width:250px','id'=>'department_id_1')); ?></td>
	    </tr>
	    <tr>
		    <td style="width:13%"> Program Type:</td>
		    <td style="width:37%"><?php echo $this->Form->input('Search.program_type_id',array('label'=>false,
		    'style'=>'width:250px','empty'=>' ')); ?></td>
		    <td style="width:13%">Year Approved:</td>
		    <td style="width:37%"><?php 
		    
		     echo $this->Form->year('Search.year_approved',
		     isset($this->request->data['Search']['year_approved']['year']) ? $this->request->data['Search']['year_approved']['year'] : date('Y') , array('empty' => false, 'label' => false, 'div' => false, 'style' => 'width:100px', 'class' => 'fs14'));
		     
		   // echo $this->Form->input('Search.year_approved',array('label'=>false)); ?></td>
	    </tr>
	    
        <tr>
		  	<td style="width:13%"> Type:</td>
			<td style="width:37%"><?php 
			echo $this->Form->input('Search.accepted', array('type' => 'checkbox', 'label' => 'Accepted', 'div' => false, 'checked' => (!isset($this->request->data) || $this->request->data['Search']['accepted'] == 1 ? 'checked' : false))).'<br/>';
			echo $this->Form->input('Search.rejected', array('type' => 'checkbox', 'label' => 'Rejected', 'div' => false, 'checked' => (!isset($this->request->data) || $this->request->data['Search']['rejected'] == 1 ? 'checked' : false))).'<br/>';
			echo $this->Form->input('Search.notprocessed', array('type' => 'checkbox', 'label' => 'Not Processed', 'div' => false, 'checked' => (!isset($this->request->data) || $this->request->data['Search']['notprocessed'] == 1 ? 'checked' : false))).'<br/>';
			
			?></td>		
			<td style="width:13%">&nbsp;</td>
			<td style="width:37%">&nbsp;</td>
		</tr>
		
		<tr>
		<td colspan='4'><?php echo $this->Form->submit(__('View Exemption'), array('name' => 'viewExemption', 'div' => false)); ?></td>
		</tr>
</table>
<?php if (!empty($courseExemptions)) { ?>
<?php //debug($courseSubstitutionRequests); ?>
	<h2><?php echo __('Course Exemption Requests .');?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('S.No','id');?></th>
			<th><?php echo $this->Paginator->sort('Request Date','request_date');?></th>

			<th><?php echo $this->Paginator->sort('taken_course_title');?></th>
			<th><?php echo $this->Paginator->sort('taken_course_code');?></th>
			<th><?php echo $this->Paginator->sort('course_taken_credit');?></th>
			<th><?php echo $this->Paginator->sort('department_accept_reject');?></th>
			
			<th><?php echo $this->Paginator->sort('registrar_confirm_deny');?></th>
			
			<th><?php echo $this->Paginator->sort('department_approve_by');?></th>
			
			<th><?php echo $this->Paginator->sort('registrar_approve_by');?></th>
			<th><?php echo $this->Paginator->sort('Course Code-Course Title-Credit','course_id');?></th>
			<th><?php echo $this->Paginator->sort('student_id');?></th>
			<th class="actions"><?php echo __('Actions');?></th>
	</tr>
	<?php
	$i = 0;
	$start=$this->Paginator->counter('%start%');
	foreach ($courseExemptions as $courseExemption):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $start++; ?>&nbsp;</td>
		<td><?php echo $courseExemption['CourseExemption']['request_date']; ?>&nbsp;</td>

		<td><?php echo $courseExemption['CourseExemption']['taken_course_title']; ?>&nbsp;</td>
		<td><?php echo $courseExemption['CourseExemption']['taken_course_code']; ?>&nbsp;</td>
		<td><?php echo $courseExemption['CourseExemption']['course_taken_credit']; ?>&nbsp;</td>
		<td>
		<?php 
		    if ($courseExemption['CourseExemption']['department_accept_reject']==1) {
		         echo 'Accepted';
		    }  else {
		        // echo 'Waiting Decision';
		        
		        if (is_null($courseExemption['CourseExemption']['department_accept_reject'])) {
		             echo 'Waiting Decision';
		        } else if ($courseExemption['CourseExemption']['department_accept_reject']==0) {
		            echo 'Rejected';
		        }
		    
		    }
		    ?>
		&nbsp;</td>
		
		<td>
		<?php 
			 if ($courseExemption['CourseExemption']['registrar_confirm_deny']==1) {
		         echo 'Accepted';
		    }  else {
		    
		        if (is_null($courseExemption['CourseExemption']['registrar_confirm_deny'])) {
		             echo '--';
		        } else if ($courseExemption['CourseExemption']['registrar_confirm_deny']==0) {
		            echo 'Rejected';
		        }
		    }
		
		?>&nbsp;</td>
		
		<td><?php echo $courseExemption['CourseExemption']['department_approve_by']; ?>&nbsp;</td>
	
		<td><?php echo $courseExemption['CourseExemption']['registrar_approve_by']; ?>&nbsp;</td>
		
		<td>
		
			<?php echo $this->Html->link($courseExemption['Course']['course_code_title'].'-'.$courseExemption['Course']['credit'], array('controller' => 'courses', 'action' => 'view', $courseExemption['Course']['id'])); ?>
		</td>
		<td>
			<?php echo $this->Html->link($courseExemption['Student']['full_name'], array('controller' => 'students', 'action' => 'student_academic_profile', $courseExemption['Student']['id'])); ?>
		</td>
		<td class="actions">
			<?php 
			echo $this->Html->link(__('View'), array('action' => 'view', $courseExemption['CourseExemption']['id'])); 
			if ($role_id == ROLE_DEPARTMENT) {
			  if (is_null($courseExemption['CourseExemption']['department_accept_reject'])) {
		            echo $this->Html->link(__('Accpet/Reject Exemption'), array('action' => 'approve_request', $courseExemption['CourseExemption']['id']));
		        }
			
			
			
			}
			
			if ($role_id == ROLE_REGISTRAR) {
			 
		        if (is_null($courseExemption['CourseExemption']['registrar_confirm_deny'])) {
		            echo $this->Html->link(__('Accpet/Reject Exemption'), array('action' => 'approve_request', $courseExemption['CourseExemption']['id']));
		        }
			
			
			}
			
			?>
			
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

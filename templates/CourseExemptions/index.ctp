<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
             
<div class="courseExemptions index" id="exemption_request">
<?php if (!empty($courseExemptions)) { ?>
	<div class="smallheading"><?php echo __('List of course exemptions request');?></div>
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
			<th><?php echo $this->Paginator->sort('Course Code-Course Title - Credit','course_id');?></th>
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
		<td><?php 
		
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
		   ?>&nbsp;</td>
		
		<td>
		<?php 
		 
		 
		    if ($courseExemption['CourseExemption']['registrar_confirm_deny']==1) {
		         echo 'Accepted';
		    }  else {
		    
		        if (is_null($courseExemption['CourseExemption']['department_accept_reject'])
		        || is_null($courseExemption['CourseExemption']['registrar_confirm_deny'])) {
		             echo '--';
		        } else if ($courseExemption['CourseExemption']['registrar_confirm_deny']==0) {
		            echo 'Rejected';
		        }
		    }
		
		?>&nbsp;
		
		
		</td>
		
		<td><?php echo $courseExemption['CourseExemption']['department_approve_by']; ?>&nbsp;</td>
	
		<td><?php echo $courseExemption['CourseExemption']['registrar_approve_by']; ?>&nbsp;</td>
		
		<td>
		
			<?php 
			if(isset($courseExemption['Course']['course_code_title'])) {
			echo $this->Html->link($courseExemption['Course']['course_code_title'].'-'.$courseExemption['Course']['credit'], array('controller' => 'courses', 'action' => 'view', $courseExemption['Course']['id'])); 
			}
			
			?>
		</td>
		<td>
			<?php echo $this->Html->link($courseExemption['Student']['full_name'], array('controller' => 'students', 'action' => 'view', $courseExemption['Student']['id'])); ?>
		</td>
		
		<td class="actions">
			<?php 
			echo $this->Html->link(__('View'), array('action' => 'view', $courseExemption['CourseExemption']['id'])); 
			if ($role_id == ROLE_STUDENT) {
			   echo $this->Html->link(__('Cancel Request'), array('action' => 'delete', $courseExemption['CourseExemption']['id']), null, sprintf(__('Are you sure you want to cancel # %s? course exemption request.'),  $courseExemption['Course']['course_code_title'])); 
			}
			?>
			<?php 
			if ($role_id != ROLE_STUDENT) {
			echo $this->Html->link(__('Accpet/Reject Exemption'), array('action' => 'approve_request', $courseExemption['CourseExemption']['id']));
			}
			 ?>
			
		</td>
	</tr>
<?php endforeach; ?>
	</table>
	
	<p>
	<?php
	
	 $this->Paginator->options(array(
            'update' => '#exemption_request',
            'evalScripts' => true,
            'before' => $this->Js->get("#busy-indicator")->effect('fadeIn', array('buffer' => false)),
            'complete' => $this->Js->get("#busy-indicator")->effect('fadeOut', array('buffer' => false)),
    ));
	echo $this->Paginator->counter(array(
	'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%')
	));
	?>	</p>

	<div class="paging">
		<?php echo $this->Paginator->prev('<< ' . __('previous'),array()
    , null, array('class'=>'disabled'));?>
	 | 	<?php echo $this->Paginator->numbers();?>
 |
		<?php echo $this->Paginator->next(__('next') . ' >>',array(), null, array('class' => 'disabled'));?>
	</div>
<?php } ?>
</div>
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->

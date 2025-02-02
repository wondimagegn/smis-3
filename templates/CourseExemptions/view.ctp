<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
            
<div class="courseExemptions view">
<h2><?php echo __('Course Exemption');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Request Date'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Format->humanize_date($courseExemption['CourseExemption']['request_date']); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Reason'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $courseExemption['CourseExemption']['reason']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Taken Course Title'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $courseExemption['CourseExemption']['taken_course_title']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Taken Course Code'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $courseExemption['CourseExemption']['taken_course_code']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Course Taken Credit'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $courseExemption['CourseExemption']['course_taken_credit']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Department Accept Reject'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php  echo $courseExemption['CourseExemption']['department_accept_reject']==1 ? 'Accepted': (!empty($courseExemption['CourseExemption']['department_approve_by']) ? 'Denied':'Waiting Department Approval'); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Department Reason'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $courseExemption['CourseExemption']['department_reason']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Registrar Confirm Deny'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $courseExemption['CourseExemption']['registrar_confirm_deny']==1 ? 'Accepted': (!empty($courseExemption['CourseExemption']['registrar_approve_by']) ? 'Denied':'Waiting Registrar Confirmation');  ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Registrar Reason'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $courseExemption['CourseExemption']['registrar_reason']; ?>
			&nbsp;
		</dd>
		
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Department Approve By'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $courseExemption['CourseExemption']['department_approve_by']; ?>
			&nbsp;
		</dd>
	
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Registrar Approve By'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $courseExemption['CourseExemption']['registrar_approve_by']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Course'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Html->link($courseExemption['Course']['course_code_title'].'-'.$courseExemption['Course']['credit'], array('controller' => 'courses', 'action' => 'view', $courseExemption['Course']['id'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Student'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Html->link($courseExemption['Student']['full_name'], array('controller' => 'students', 'action' => 'view', $courseExemption['Student']['id'])); ?>
			&nbsp;
		</dd>
		<?php if (!empty($courseExemption['Attachment']) && count($courseExemption['Attachment'])>0) { ?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Attachment'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php 
			 
			 echo 'PDF file uploaded on: '.$this->Format->humanize_date($courseExemption['Attachment'][0]['created']). '<br/> '; 
			  echo "<a href=".$this->Media->url($courseExemption['Attachment'][0]['dirname'].DS.$courseExemption['Attachment'][0]['basename'],true)." target=_blank'>View Attachment</a>";
		    ?>
		 </dd>
		 <dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Preview'); ?></dt>
	 <dd <?php if ($i++ % 2 == 0) echo $class;?> >
		    
		 </dd>
		</dl>
		<div>
		<?php 
		 echo $this->Media->embedAsObject($courseExemption['Attachment'][0]['dirname'].DS.$courseExemption['Attachment'][0]['basename'],array('width'=>860,'height'=>'500'));
		 
		 ?>
		</div>
		<?php } ?>
</div>
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->

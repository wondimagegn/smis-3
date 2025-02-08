<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
		<div class="large-12 columns">
                     <h2 class="box-title">
			Academic Calendars
		      </h2>
		</div>
		<div class="large-12 columns">
		  <dl style="float:left"><?php $i = 0; $class = ' class="altrow"';?>
	    <dt><div class="smallheading"><?php echo __('Academic Calendar');?></div></dt>
		
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Academic Year'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $academicCalendar['AcademicCalendar']['academic_year']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Semester'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $academicCalendar['AcademicCalendar']['semester']; ?>
			&nbsp;
		</dd>
		
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Program'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Html->link($academicCalendar['Program']['name'], array('controller' => 'programs', 'action' => 'view', $academicCalendar['Program']['id'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Program Type'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Html->link($academicCalendar['ProgramType']['name'], array('controller' => 'program_types', 'action' => 'view', $academicCalendar['ProgramType']['id'])); ?>
			&nbsp;
		</dd>
		
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Course Registration Start Date'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Format->humanize_date($academicCalendar['AcademicCalendar']['course_registration_start_date']); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Course Registration End Date'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Format->humanize_date($academicCalendar['AcademicCalendar']['course_registration_end_date']); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Course Add Start Date'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Format->humanize_date($academicCalendar['AcademicCalendar']['course_add_start_date']); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Course Add End Date'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Format->humanize_date($academicCalendar['AcademicCalendar']['course_add_end_date']); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Course Drop Start Date'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Format->humanize_date($academicCalendar['AcademicCalendar']['course_drop_start_date']); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Course Drop End Date'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Format->humanize_date($academicCalendar['AcademicCalendar']['course_drop_end_date']); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Grade Submission Start Date'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Format->humanize_date($academicCalendar['AcademicCalendar']['grade_submission_start_date']); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Grade Submission End Date'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Format->humanize_date($academicCalendar['AcademicCalendar']['grade_submission_end_date']); ?>
			&nbsp;
		</dd>
		
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Fx Grade Submission End Date'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Format->humanize_date($academicCalendar['AcademicCalendar']['grade_fx_submission_end_date']); ?>
			&nbsp;
		</dd>
		
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Senate Meeting Date'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Format->humanize_date($academicCalendar['AcademicCalendar']['senate_meeting_date']); ?>
			&nbsp;
		</dd>
		
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Graduation Date'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Format->humanize_date($academicCalendar['AcademicCalendar']['graduation_date']); ?>
			&nbsp;
		</dd>
		
		
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Online Admission Start Date'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Format->humanize_date($academicCalendar['AcademicCalendar']['online_admission_start_date']); ?>
			&nbsp;
		</dd>
		
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Online Admission End Date'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Format->humanize_date($academicCalendar['AcademicCalendar']['online_admission_end_date']); ?>
			&nbsp;
		</dd>
		
		
		
		<dt> <div class="smallheading"> <?php echo __('Year Level'); ?></div></dt>
		<br/>
		<?php 
		echo "<ul>";
		foreach ($academicCalendar['AcademicCalendar']['year_level_id'] as $year_level_id=>$year_level_name) {
		?>
		 <li<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $year_level_name; ?>
			&nbsp;
		</li>
		<?php 
		}
		echo "</ul>";
		?>
		
	    
	</dl>
	<dl style="float:left;width:35%">
	
	   <dt><div class="smallheading"><?php echo __('College and Department which has this calendar');?></div></dt>
	    <?php 
	  echo "<ul>";
	  foreach($colleges as $college_id=>$college_name){

  
         //check
          if (isset($this->request->data['AcademicCalendar']['college_id']) && !empty($this->request->data['AcademicCalendar']['college_id']) && in_array($college_id,$this->request->data['AcademicCalendar']['college_id'])) {
            echo '<li>'.$college_name.'<ul>';         
         
         } else {
              echo '<li>'.$college_name.'<ul>';  
         }

         if (!empty($college_department[$college_id])){
              foreach($college_department[$college_id] as $department_id=>$department_name){
             
               if (in_array($department_id,$academicCalendar['AcademicCalendar']['department_id'])) {
                 echo '<li>'.$department_name.'</li>';

                } else {
                   echo '<li>'.$department_name.'</li>';
                }
             }
         }
         echo "</ul></li>";
         
         }
         echo "</ul>";
         ?>
</dl>
		 </div>
	</div>
      </div>
</div>

<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
             
<div class="courseInstructorAssignments form">
<?php echo $this->Form->create('CourseInstructorAssignment');?>    
<div style="padding-bottom:20px"></div>
	<fieldset>
		<legend class="smallheading"><?php echo __('Add Course Instructor Assigment'); ?></legend>
	<?php
		echo $this->Form->input('academic_year');
		echo $this->Form->input('semester');
		echo $this->Form->input('section_id');
		echo $this->Form->input('staff_id');
		echo $this->Form->input('course_id');
		echo $this->Form->input('type');

	?>
	<?php 
	if (!empty($publishedCourses)) {
		    echo "<table>";
		    echo "<tr><th>Course Title</th>";
		    echo "<th>Couse Code</th>";
		    echo "<th>Semester</th>";
		    echo "<th>Credit</th>";
		    
		    echo "<th>Assign To </th></tr>";
		    foreach ($publishedCourses as $pk=>$pv) {
		        echo "<tr><td>".$pv['Course']['course_title']."</td>";
		        echo "<td>".$pv['Course']['course_code']."</td>";
		         echo "<td>".$pv['PublishedCourse']['semester']."</td>";
		        echo "<td>".$pv['Course']['course_detail_hours'].'</td>';
		       
		       echo "<td>".$this->Form->checkbox("CourseInstructorAssigment.assign.".$pv['Course']['id'])."</td></tr>";    
                                     
                 
                                          
		    }
		    echo "</table>";
		}
		?>
	</fieldset>
<?php echo $this->Form->end(array('label'=>__('Assign To Selected Courses'),'class'=>'tiny radius button bg-blue'));?>
</div>
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->

<?php ?>
<div class="box">
<div class="box-body">
<div class="row">
<div class="large-12 columns">
<article>
    <div class="row">
        <div class="medium-4 columns">
            <address>
				 <strong><?php echo $continouseExamSetup['0']['PublishedCourse']['CourseInstructorAssignment'][0]['Staff']['Title']['title'];?></strong>
                <strong><?php echo $continouseExamSetup['0']['PublishedCourse']['CourseInstructorAssignment'][0]['Staff']['full_name'];?></strong>
				<br>Position:<?php echo $continouseExamSetup['0']['PublishedCourse']['CourseInstructorAssignment'][0]['Staff']['Position']['position'];?>
                <br>Department: <?php echo $continouseExamSetup['0']['PublishedCourse']['CourseInstructorAssignment'][0]['Staff']['Department']['name'];?>
                <br>Email: <i><?php echo $continouseExamSetup['0']['PublishedCourse']['CourseInstructorAssignment'][0]['Staff']['email'];?></i>
                <br>Phone: <?php echo $continouseExamSetup['0']['PublishedCourse']['CourseInstructorAssignment'][0]['Staff']['phone_mobile'];?>

            </address>
        </div>
		<div class="medium-4 columns">
            <address>
				<br>Course Title:<?php echo $continouseExamSetup['0']['PublishedCourse']['Course']['course_title'];;?>
                <br>Course Code: <?php echo $continouseExamSetup['0']['PublishedCourse']['Course']['course_code'];?>
                <br><?php echo $continouseExamSetup['0']['PublishedCourse']['Course']['Curriculum']['type_credit'].':'; ?> 
<i><?php echo $continouseExamSetup['0']['PublishedCourse']['Course']['credit'];?></i>
              
<br> Year Level: 
<i><?php echo $continouseExamSetup['0']['PublishedCourse']['YearLevel']['name'];?></i>
              
            </address>
        </div>
	</div>
	<div class="row">
		<div class="medium-12 columns">
		  <table class="inventory">
                    <thead>
                        <tr>
                            <th>Exam Type
                            </th>
                            <th>Mandatory
                            </th>
                            <th>Percent
                            </th>
                           
                            <th>Result Entry Completed
                            </th>
                             <th>Result Entry Date
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                      <?php foreach($continouseExamSetup as $k=>$v) { ?>
<tr>
                            <td> 
<?php echo $v['ExamType']['exam_name'];?>
                            </td>
                            <td>
<?php echo $v['ExamType']['mandatory']==1 ? 'Yes':'No';?>
                            </td>
                            <td>
<?php echo $v['ExamType']['percent'];?>
                            </td>
                           
                            <td>

<span>+<b  class="counter-up"><?php echo (count($v['ExamResult'])/$total_registered)*100 ?> </b>%</span>
                      </td>
                          <td>
<?php echo $this->Format->humanize_date($v['ExamResult'][0]['created']);?>
                            </td>
                        </tr>


					  <?php } ?>
                    </tbody>
                </table>
			</div>
	</div>
</article>

</div> <!-- end of columns 12 -->

</div> <!-- end of row -->


</div> <!-- end of box-body -->
</div><!-- end of box -->

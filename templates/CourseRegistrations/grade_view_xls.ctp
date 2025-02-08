<?php 
/*
This file should be in app/views/elements/export_xls.ctp
Thanks to Marco Tulio Santos for this simple XLS Report
*/
header ("Expires: " . gmdate("D,d M YH:i:s") . " GMT");
header ("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
header ("Cache-Control: no-cache, must-revalidate");
header ("Pragma: no-cache");
header ("Content-type: application/vnd.ms-excel");
header ("Content-Disposition: attachment; filename=".$filename.".xls" );
header ("Content-Description: Exported as XLS" );
?>

<style>
table.grade_list tr td{
	padding:0px;
	vertical-align:middle;
}
</style>

<table class="fs13">
	<tr>
	  <td colspan="8" style="text-align:center">
			<?php 
			if(isset($university['University']['name'])) {
				echo $university['University']['name'];

			} else {
				echo '----';

			}
		

			?>
             </td>
	</tr>
	<tr>
	  <td colspan="8" style="text-align:center">
			
  OFFICE OF THE REGISTRAR
             </td>
	</tr>

   	<tr>
	    <td colspan="8" style="text-align:center">
		Roster	
             </td>
	</tr>

    <tr>
	    <td colspan="4">
		COLLEGE/INSTITUTE: 
             </td>
             <td colspan="4">
		<?php 
			if(isset($department['Department']['College']['name'])) {
				echo $department['Department']['College']['name'];
			}
		?>
             </td>
	</tr>

    <tr>
	    <td>
		<?php 
			if(isset($department['Department']['name'])) {
				echo $department['Department']['name'];
			}
		?>
             </td>
             <td>
	         
             </td>
	     <td>
		 <?php 
			if(isset($courseDetail['Course']['YearLevel']['name'])) {
				echo $courseDetail['Course']['YearLevel']['name'];
			}
		?>
	     </td>
	     
	     <td>
			 <?php 
			if(isset($courseDetail['Course']['course_title'])) {
				echo $courseDetail['Course']['course_title'];
			}
		?>
	     </td>
	</tr>

    <tr>
	    <td>
		Department
             </td>
             <td>
		Section
             </td>
	     <td>
		CLASS YEAR
	     </td>
	    
	     <td>
		COURSE TITLE
	     </td>
	</tr>


	<tr>
	    <td>
		<?php 
			if(isset($courseDetail['Course']['course_code'])) {
				echo $courseDetail['Course']['course_code'];
			}
		?>
             </td>
             
               <td>
		   <?php 
			if(isset($publish_course_detail_info['Course']['Curriculum']['type_credit']) && $publish_course_detail['Course']['Curriculum']['type_credit']=="ECTS Credit Point") {
				echo 'ECTS Credit Point';
			} else {
				echo 'ECTS Credit Point';
			}
		?>
             </td>
	    
              <td>
		  <?php 
			echo $courseDetail['Course']['semester'];
		?>
	     </td>

	    <td>
		  <?php 
		  
		?>
	     </td>
             <td>
	             <?php 
			echo $academicYear;
		?>
	    </td>
	</tr>

	 <tr>
	    <td>
		Course N<u>o</u>
             </td>
             <td>
		   <?php 
			if(isset($courseDetail['Course']['Curriculum']['type_credit'])
&& $courseDetail['Course']['Curriculum']['type_credit']=="ECTS Credit Point") {
				echo 'ECTS Credit Point';
			} else {
				echo 'Credit';
			}
		?>
             </td>
	     <td>
		SEMESTER
	     </td>
	    
	     <td>
		INSTRUCTOR
	     </td>
	     <td>
		 ACADEMIC YEAR
	     </td>	
	</tr>
</table>

<table class="grade_list">
	   <tr>
			<th>&nbsp;</th>
			<th>Student ID</th>
			<th>Name</th>
			<th>Sex</th>
			<th>Grade</th>
	  </tr>
	 <?php if(!empty($studentExamGradeList)) {
			$count=1;
		 ?>
			<?php foreach($studentExamGradeList as $K=>$V) { ?>
			    <tr>
					<td><?php echo $count++; ?></td>
					<td><?php echo $V['Student']['studentnumber']; ?></td>
					<td>
                  <?php
					echo $V['Student']['full_name']; ?>
                   </td>

				  <td><?php
					echo $V['Student']['gender']; ?></td>
					<td>
				  <?php 
					if(!empty($V['ExamGrade'])) {
						echo $V['ExamGrade'][0]['grade'];
					} else {
						echo '--';
					}

				?>
	
					</td>
				</tr>
			<?php } ?>
		<?php
			}
		?>
	
</table>

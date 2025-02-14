<?php ?>
<style>
.bordering {
border-left:1px #cccccc solid;
border-right:1px #cccccc solid;
}
.bordering2 {
	border-left:1px #000000 solid;
	border-right:1px #000000 solid;
	border-top:1px #000000 solid;
	border-bottom:1px #000000 solid;
}
.courses_table tr td, .courses_table tr th {
padding:1px
}
</style>

<?php 
if (isset($gradeSubmissionDelay) && !empty($gradeSubmissionDelay)) {

    foreach($gradeSubmissionDelay as $program=>$programType) {
       foreach ($programType as $programTypeName=> $statDetail) {
?>
        <p class="fs16">
                List of Instructors who has failed to submit Grade on due date  for 
                <?php echo $this->data['Report']['acadamic_year']; ?> AY, Semester
                 <?php  echo $this->data['Report']['semester']; ?> <br/>
                <strong> Program : </strong>   <?php 
                      echo $program;
                    ?>
                    <br/>
                <strong> Program Type: </strong>  <?php 
                      echo $programTypeName;
                         
                    ?>
        </p>
       <table style="width:100%">
  
            <tr>
		        <th  class="bordering2" style="vertical-align:bottom; width:3%">S.N<u>o</u>
		        </th>
		        <th class="bordering2" style="vertical-align:bottom; width:20%">Instructor's Name</th>
		        <th class="bordering2" style="vertical-align:bottom; width:20%">College/Institute name</th>
		        <th class="bordering2"  style="vertical-align:bottom; width:20%">Department Name</th>
		        <th class="bordering2"  style="vertical-align:bottom; width:28%">Course</th>
		        <th class="bordering2"  style="vertical-align:bottom; width:9%">Delayed</th>
		    </tr>
		    
		    <?php 
		      $count=0;
		      foreach ($statDetail as $instcollege=>$instdept) {
		        
		       ?>
		      <?php foreach ($instdept as $instdepartment=>$instcourses) { 
		        
		      ?>
		      <?php foreach ($instcourses as $instfullname=>$courses) {
		            foreach($courses as $coursetitle=>$delays) {
		     
		        $count++; ?>
		               <tr>
		                   <td  class="bordering2">
		                       <?php echo $count; ?>
		                    </td>
		                    <td  class="bordering2">
		                       <?php echo $instfullname; ?>
		                    </td>
		                    <td  class="bordering2">
		                      <?php echo $instcollege; ?>
		                    </td>
		                    <td  class="bordering2">
		                        <?php echo $instdepartment; ?>
		                    </td>
		                    
		                      <td  class="bordering2">
		                        <?php echo $coursetitle; ?>
		                    </td>
		                    
		                     <td  class="bordering2">
		                        <?php echo $delays['noDaysDelayed']; ?>
		                    </td>
		                   
		               </tr>
		               
		                <?php } ?>
		            <?php } ?>
		        <?php } ?>
		    <?php } ?>
        </table>
    <?php   
      }
   }   
} 
?>

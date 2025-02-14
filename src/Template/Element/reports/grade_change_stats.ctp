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
if (isset($gradeChangeStat) && !empty($gradeChangeStat)) {
    foreach($gradeChangeStat as $program=>$statDetail) {
         $detail=explode('~',$program);
?>
        <p class="fs16">
                List of Instructors who has grade change  for 
                <?php echo $this->data['Report']['acadamic_year']; ?> AY, Semester
                 <?php  echo $this->data['Report']['semester']; ?> <br/>
                <strong> Department : </strong>   <?php 
                      echo $detail[0];
                    ?>
                    <br/>
                <strong> Program: </strong>  <?php 
                      echo $detail[1];
                         
                    ?>
                <br/>
                  <strong> Program Type </strong>  <?php 
                      echo $detail[2];
                         
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
                        <th class="bordering2"  style="vertical-align:bottom; width:28%">Section</th>
		        <th class="bordering2"  style="vertical-align:bottom; width:9%">Number of Change</th>
		    </tr>
		    
		    <?php 
		      $count=0;
		      foreach ($statDetail as $sk=>$stvv) 
                      {
                       foreach($stvv as $stv){
		        $count++; 
                      ?>
		               <tr>
		                   <td  class="bordering2">
		                       <?php echo $count; ?>
		                    </td>
		                    <td  class="bordering2">
		                       <?php echo $stv['Staff']['Title']['title'].' '.$stv['Staff']['full_name']; ?>
		                    </td>
		                    <td  class="bordering2">
		                            <?php echo $stv['Staff']['College']['name']; ?>
		                    </td>
		                    <td  class="bordering2">
		                        <?php echo $stv['Staff']['Department']['name']; ?>
		                    </td>
		                    
		                    <td  class="bordering2">
		                         <?php echo $stv['PublishedCourse']['Course']['course_title']; ?>
		                    </td>
                                    <td  class="bordering2">
		                         <?php echo $stv['PublishedCourse']['Section']['name'].'('.$stv['PublishedCourse']['Program']['name'].'-'.$stv['PublishedCourse']['ProgramType']['name'].')'; ?>
		                    </td>
		                    
		                    <td  class="bordering2">
		                        <?php  echo $stv['PublishedCourse']['numberofgradechange']; ?>
		                    </td>
		                   
		               </tr>
		               
		 <?php 
                   }
                }

 ?>
        </table>
    <?php   
   }   
} 
?>

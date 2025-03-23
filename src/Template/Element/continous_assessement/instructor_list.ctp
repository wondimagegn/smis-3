<?php 

if (isset($instructor_lists) && !empty($instructor_lists)) {
?>
	<p class="fs16">
           Number  of continous assessement created for  <?php echo $this->request->data['ContinuousAssessment']['acadamic_year']; ?> A/Y, and Semester <?php  echo $this->request->data['ContinuousAssessment']['semester']; ?>  <br/>      
    </p>
    <table>
                    <tr>
                        <td class="bordering2"> S.N<u>o</u> </td> 
                        <td class="bordering2"> Instructor Name </td> 
                        <td class="bordering2"> Course </td> 
					    <td class="bordering2"> Department </td> 
						 <td class="bordering2"> Number of Assessement </td> 
					    <td class="bordering2"> Action </td> 
                    </tr>     
<?php 	

	$count=0;
	foreach($instructor_lists as $d=>$numberofassessements){
		$detail=explode('~',$d);
		$p_id=explode('p_id',$detail[3]);
		
?>
	        <tr>
                 <td class="bordering2" > <?php echo ++$count; ?> </td> 
                 <td class="bordering2" > <?php echo $detail[1]; ?>  </td> 
                 <td class="bordering2" > <?php echo $detail[2]; ?> </td> 
                 <td class="bordering2" > <?php echo $detail[0]; ?> </td> 
                 <td class="bordering2" > <?php echo $numberofassessements;?> </td> 
				 <td class="bordering2" > 
						<?php
					  if($numberofassessements>0){
					    echo $this->Html->link(__('View Detail'), array('action' => 'view_continouse_assessement_setup', $p_id[1]));
					  }
	
 ?>
				</td> 
            </tr>
<?php 
	}
?>
			</table>
<?php 

}
?>

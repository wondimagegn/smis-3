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


<?php 

if (isset($getActiveTeacherByDegree['teachersStatisticsByDegree']) 
  && !empty($getActiveTeacherByDegree['teachersStatisticsByDegree'])) {
  
     debug($getActiveTeacherByDegree);
    ?>
      <p class="fs16">
            <?php echo $headerLabel; ?>
      </p>

<table style="width:100%">
  
    <tr>
    		<th rowspan="2" class="bordering2" style="vertical-align:bottom; width:2%">S.N<u>o</u>
    		</th>
		
		    <th rowspan="2" class="bordering2" style="vertical-align:bottom; width:15%">College/School/Center</th>
		    <th rowspan="2" class="bordering2"  style="vertical-align:bottom; width:8%">Department</th>
         <th rowspan="2" class="bordering2"  style="vertical-align:bottom; width:8%">Gender</th>
         
         <?php
       
          foreach ($educations as $k=>$value) { ?>
              <th colspan="3"  class="bordering2" class="bordering2"><?php echo $value;?></th>
        <?php } ?>
    </tr>
    <tr>
     <?php foreach ($educations as $k=>$value) { ?>
              <th style="width:5%" class="bordering2">Ethiopian</th>
              <th style="width:5%" class="bordering2">Foreigner</th>
              <th style="width:5%" class="bordering2">Total</th>
             
     <?php } ?>
     </tr>
    
   <?php 
   $count=0;
     foreach($getActiveTeacherByDegree['teachersStatisticsByDegree'] as 
      $college=>$departmentList) {
        ?>
          <tr>
              <td class="bordering2" rowspan="<?php echo  $getActiveTeacherByDegree['collegeRowSpan'][$college]+count($departmentList)+1;?>">
              <?php echo ++$count;?>
              </td>

               <td class="bordering2" rowspan="<?php echo  $getActiveTeacherByDegree['collegeRowSpan'][$college]+count($departmentList)+1;?>">
              <?php echo $college;?>
              </td>
          </tr>
        
             <?php foreach($departmentList as $deptname=>$genderList) { 
              ?>
                 <tr>
                        <td class="bordering2" rowspan="<?php echo count($genderList)+2 ?>"><?php echo $deptname; ?></td>
                  </tr>

                  <?php 
                 
                  $sumByDegree=array();
                  foreach ($genderList as $gk => $degreelist) {
                    ?>
                     <tr>
                      <td class="bordering2"><?php echo ucwords($gk); ?></td>
                       <?php

                        foreach($degreelist as $pk=>$ppv) { 
                            $sumByDegree[$pk]['Ethiopian']+=$ppv['Ethiopian'];
                             $sumByDegree[$pk]['Foreigner']+=$ppv['Foreigner'];
                        ?>
                        <td class="bordering2"><?php echo $ppv['Ethiopian']; ?></td>
                         <td class="bordering2"><?php echo $ppv['Foreigner']; ?></td>

                         <td class="bordering2"><?php echo $ppv['Foreigner']+$ppv['Ethiopian']; ?></td>

                       <?php } ?>


                 </tr>

                    <?php 
                  
                  } ?>

                  <tr>
                      <td class="bordering2">Total</td>
                         <?php 
                      debug($sumByDegree);
                       
                          foreach ($sumByDegree as $d => $dv) {
                            ?>
                            <td class="bordering2"><?php echo $dv['Ethiopian'];

                          ?></td>
                          <td class="bordering2"><?php echo $dv['Foreigner'];
                          ?></td>
                          <td class="bordering2"><?php echo $dv['Foreigner']+$dv['Ethiopian'];
                          ?></td>
                            <?php
                           
                          }
                        ?>
                    
                 </tr>




            <?php } ?>
   <?php } ?>
</table>
<?php 
  }
?>
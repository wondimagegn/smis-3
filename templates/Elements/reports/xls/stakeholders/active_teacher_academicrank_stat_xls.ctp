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

if (isset($getActiveTeacherByAcademicRank['teachersStatisticsByAcademicRank']) 
  && !empty($getActiveTeacherByAcademicRank['teachersStatisticsByAcademicRank'])) {
  
    ?>
      <p class="fs16">
            <?php echo $headerLabel; ?>
      </p>

<table style="width:100%">
  
    <tr>
    		<th rowspan="3" class="bordering2" style="vertical-align:bottom; width:2%">S.N<u>o</u>
    		</th>
		
		    <th rowspan="3" class="bordering2" style="vertical-align:bottom; width:15%">College/School/Center</th>
		    <th rowspan="3" class="bordering2"  style="vertical-align:bottom; width:8%">Department</th>
         <th rowspan="3" class="bordering2"  style="vertical-align:bottom; width:8%">Degree</th>
        <th colspan="<?php echo count($educations)*3;?>"  class="bordering2" class="bordering2" style="text-align: center;">Academic Rank</th>
       
    </tr>
    <tr>
      <?php
          foreach ($positions as $k=>$value) { ?>
              <th colspan="3"  class="bordering2" class="bordering2"><?php echo $value;?></th>
        <?php } ?>
    
     </tr>
     <tr>
        <?php foreach ($positions as $k=>$value) { ?>
              <th style="width:5%" class="bordering2">Male</th>
              <th style="width:5%" class="bordering2">Female</th>
              <th style="width:5%" class="bordering2">Total</th>
     <?php } ?>

     </tr>
      <?php 
   $count=0;
     foreach($getActiveTeacherByAcademicRank['teachersStatisticsByAcademicRank'] as 
      $college=>$departmentList) {
        ?>
          <tr>
              <td class="bordering2" rowspan="<?php echo  $getActiveTeacherByAcademicRank['collegeRowSpan'][$college]+count($departmentList)+1;?>">
              <?php echo ++$count;?>
              </td>

               <td class="bordering2" rowspan="<?php echo  $getActiveTeacherByAcademicRank['collegeRowSpan'][$college]+count($departmentList)+1;?>">
              <?php echo $college;?>
              </td>
          </tr>

          <?php foreach($departmentList as $deptname=>$degreeLists) { 
            debug($degreeLists);
              ?>
                 <tr>
                        <td class="bordering2" rowspan="<?php echo count($degreeLists)+1; ?>"><?php echo $deptname; ?></td>
                       
                  </tr>
                  <?php foreach ($degreeLists as $dk => $rankLists) {
                    debug($rankLists);
                    ?>
                     <tr>
                        <td class="bordering2"><?php echo $dk; ?></td>
                       <?php foreach($rankLists as $rk=>$rv) { ?>
                        <td class="bordering2"><?php echo $rv['male']; ?></td>
                        <td class="bordering2"><?php echo $rv['female']; ?></td>
                        <td class="bordering2"><?php echo $rv['female']+$rv['male']; ?></td>
                         <?php  } ?>
                      </tr>

                    <?php 
                  
                  } ?>

                
            <?php } ?>
   <?php } ?>
    
</table>
<?php 
  }
?>
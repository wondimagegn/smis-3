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

if (isset($getTeachersOnStudyLeave['teachersOnStudyLeave']) 
  && !empty($getTeachersOnStudyLeave['teachersOnStudyLeave'])) {
  
     debug($getTeachersOnStudyLeave);
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
              <th style="width:5%" class="bordering2">Inside Ethiopia</th>
              <th style="width:5%" class="bordering2">Outside Ethiopia</th>
              <th style="width:5%" class="bordering2">Total</th>
             
     <?php } ?>
     </tr>
    
   <?php 
   $count=0;
     foreach($getTeachersOnStudyLeave['teachersOnStudyLeave'] as 
      $college=>$departmentList) {
        ?>
          <tr>
              <td class="bordering2" rowspan="<?php echo  $getTeachersOnStudyLeave['collegeRowSpan'][$college]+count($departmentList)+1;?>">
              <?php echo ++$count;?>
              </td>

               <td class="bordering2" rowspan="<?php echo  $getTeachersOnStudyLeave['collegeRowSpan'][$college]+count($departmentList)+1;?>">
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
                            $sumByDegree[$pk]['In Ethiopia']+=$ppv['In Ethiopia'];
                             $sumByDegree[$pk]['Outside Ethiopia']+=$ppv['Outside Ethiopia'];
                        ?>
                        <td class="bordering2"><?php echo $ppv['In Ethiopia']; ?></td>
                         <td class="bordering2"><?php echo $ppv['Outside Ethiopia']; ?></td>

                         <td class="bordering2"><?php echo $ppv['Outside Ethiopia']+$ppv['In Ethiopia']; ?></td>

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
                            <td class="bordering2"><?php echo $dv['In Ethiopia'];

                          ?></td>
                          <td class="bordering2"><?php echo $dv['Outside Ethiopia'];
                          ?></td>
                          <td class="bordering2"><?php echo $dv['Outside Ethiopia']+$dv['In Ethiopia'];
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
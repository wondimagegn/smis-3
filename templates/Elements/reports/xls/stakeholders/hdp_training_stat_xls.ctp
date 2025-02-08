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

if (isset($getStaffCompletedHDPStatistics) 
  && !empty($getStaffCompletedHDPStatistics)) {
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
         
         
         <?php
          
          foreach ($completed as $k=>$value) { ?>
    
    <th colspan="3"  class="bordering2" class="bordering2"><?php echo $value;?></th>
     <?php } ?>
    </tr>
    <tr>
     <?php foreach ($completed as $k=>$value) { ?>
       <th style="width:5%" class="bordering2">M</th>
    <th style="width:5%" class="bordering2">F</th>
    <th style="width:5%" class="bordering2">Total</th>
   
     <?php } ?>
     </tr>
    
   <?php 
   $count=0;
   foreach($getStaffCompletedHDPStatistics as $college=>$departmentList) { ?>
          <tr>
              <td class="bordering2" rowspan="<?php echo  count($departmentList)+1;?>">
              <?php echo ++$count;?>
              </td>

               <td class="bordering2" rowspan="<?php echo  count($departmentList)+1;?>">
              <?php echo $college;?>
              </td>
          </tr>
        
             <?php 
             foreach($departmentList as $deptname=>$yearList) {
              debug($yearList);
              ?>
                 <tr>
                        <td class="bordering2"><?php echo $deptname; ?></td>
                        <td class="bordering2"><?php echo $yearList[0]['male']; ?></td>
                        <td class="bordering2"><?php echo $yearList[0]['female']; ?></td>
                        <td class="bordering2"><?php echo $yearList[0]['male']+$yearList[0]['female']; ?></td>

                        <td class="bordering2"><?php echo $yearList[1]['male']; ?></td>
                        <td class="bordering2"><?php echo $yearList[1]['female']; ?></td>
                        <td class="bordering2"><?php echo $yearList[1]['male']+$yearList[1]['female']; ?></td>

                  </tr>
                    <?php 
                  
                  } ?>
         
   <?php } ?>
</table>
<?php 
   }
?>
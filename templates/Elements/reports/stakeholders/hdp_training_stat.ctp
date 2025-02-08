<?php ?>
<div class="attrationView index">
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
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

if (isset($studentConstituencyByAgeGroup) 
  && !empty($studentConstituencyByAgeGroup)) {
    foreach($studentConstituencyByAgeGroup as 
      $program=>$statDetail) {
     
    ?>
      <p class="fs16">
             <?php echo str_replace('undergraduate/graduate ', $program, $headerLabel); ?>
      </p>

<table style="width:100%">
  
    <tr>
    		<th rowspan="2" class="bordering2" style="vertical-align:bottom; width:2%">S.N<u>o</u>
    		</th>
		
		    <th rowspan="2" class="bordering2" style="vertical-align:bottom; width:15%">Age</th>
		   
         
         <?php
           unset($program_types[0]);
          foreach ($program_types as $k=>$value) { ?>
    
    <th colspan="3"  class="bordering2" class="bordering2"><?php echo $value;?></th>
     <?php } ?>
    </tr>
    <tr>
     <?php foreach ($program_types as $k=>$value) { ?>
       <th style="width:5%" class="bordering2">M</th>
    <th style="width:5%" class="bordering2">F</th>
    <th style="width:5%" class="bordering2">Total</th>
   
     <?php } ?>
     </tr>
    
   <?php 
   $count=0;
    $sumByProgramType=array();
   foreach($statDetail as $ageKey=>$programTypeList) { ?>
          <tr>
              <td class="bordering2" >
              <?php echo ++$count;?>
              </td>

               <td class="bordering2" >
              <?php echo $ageKey;?>
              </td>

              <?php foreach($programTypeList as $pk=>$pvv) { 
                  $sumByProgramType[$pk]['male']+=$pvv['male'];
                  $sumByProgramType[$pk]['female']+=$pvv['female'];

                ?>
                    <td class="bordering2"><?php echo $pvv['male'];?> </td>
                    <td class="bordering2"><?php echo $pvv['female'];?> </td>
                    <td class="bordering2"><?php echo $pvv['female']+$pvv['male'];?> </td>
                
              <?php } ?>
          </tr>
        

                
            <?php } ?>

      <tr>
        <th class="bordering2" style="vertical-align:bottom; width:2%">
        </th>
    
       
         <th class="bordering2"  style="vertical-align:bottom; width:8%">Sum</th>
         
         <?php
           unset($program_types[0]);
           ?>
   
     <?php 

     foreach ($program_types as $k=>$pvalue) { ?>
       <th style="width:5%" class="bordering2"><?php echo 
       $sumByProgramType[$pvalue]['male'];?></th>
    <th style="width:5%" class="bordering2"><?php echo 
       $sumByProgramType[$pvalue]['female'];?></th>
    <th style="width:5%" class="bordering2"><?php echo  $sumByProgramType[$pvalue]['male']+$sumByProgramType[$pvalue]['female'] ;?></th>
   
     <?php } ?>
     </tr>

          
  
</table>
<?php 
  }
 }
?>
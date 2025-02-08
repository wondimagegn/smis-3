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
if (isset($attrationRate) && !empty($attrationRate)) {

$table_width = (count($yearLevel)*10) + (count($yearLevel)*10) + 86;

foreach($attrationRate as $program=>$statDetail) 
{
$program_detail=explode('~',$program);
?>
<p class="fs16">
    Student Attration rate of <?php echo $this->data['Report']['acadamic_year']; ?> AY, Semester
    <?php  echo $this->data['Report']['semester']; ?> <br/>
    <strong> Program : </strong>   <?php 
    echo $program_detail[0];
    ?>
    <br/>
    <strong> Program Type: </strong>  <?php 
    echo $program_detail[1];  
    ?>
</p>

<table style="width:100%">
  
    <tr>
		<th rowspan="2" class="bordering2" style="vertical-align:bottom; width:2%">S.N<u>o</u>
		</th>
		
		<th rowspan="2"  class="bordering2" style="vertical-align:bottom; width:15%">
College/School/Center</th>
		<th rowspan="2" class="bordering2"  style="vertical-align:bottom; width:8%">Department Name</th>
		<?php 
		$percent = 10;
		$last_percent = false;
		$total_percent = (count($yearLevel)*10) + (count($yearLevel)*10) + 86;
		if($total_percent > 100) {
			//$percent = (100 - 86) / (count($master_sheet['registered_courses']) + count($master_sheet['added_courses']));
		}
		else if($total_percent < 100) {
			$last_percent = 100 - $total_percent;
		}
		
		?>
		
		<?php foreach ($yearLevel as $k=>$value) { ?>
		
		<th colspan="4"  class="bordering2" style="text-align:center; width:<?php echo $percent; ?>%" 
		class="bordering2"><?php echo $value;?></th>
	   <?php } ?>
	   
	   	<th colspan="4" class="bordering2" style="text-align:center; width:15%" class="bordering2">Grand Total</th>	
    </tr>
    <tr>
       
		
	 <?php foreach ($yearLevel as $k=>$value) { ?>
		
		 <th style="width:5%" class="bordering2">M</th>
		<th style="width:5%" class="bordering2">F</th>
		<th style="width:5%" class="bordering2">Total</th>
		<th style="width:5%" class="bordering2">Rate(%)</th>
	   <?php } ?>
		
		<th style="width:5%" class="bordering2">M</th>
		<th style="width:5%" class="bordering2">F</th>
		<th style="width:5%" class="bordering2">Dept. Total</th>
		<th style="width:5%" class="bordering2">Dept. Rate(%)</th>
		
    </tr>
   
  
    <?php     
    $count = 0;
    foreach($statDetail as $college => $stat) {
    ?>
    <?php
     $grand_college_sum_female=0;
     $grand_college_sum_male=0;
     $grand_college_total=0;
     $grand_college_rate=0;         
     foreach ($stat as $department=>$deptyearLevel) 
     { 
              $count++;
              ?>
                   <tr>
                       <td class="bordering2">  <?php echo $count; ?> </td>
                       <td class="bordering2">  <?php echo $college; ?> </td>
                       <td class="bordering2">  <?php echo $department; ?> </td>
                       <?php 
		                $grand_total_female=0;
		                $grand_total_male=0;
		                $dept_total=0;
		                $dept_rate=0;
                         
                      foreach ($yearLevel as $yk=>$yvalue) {
                     
                      if (isset($deptyearLevel[$yvalue])) {
                       
                          
                       ?>
                         <td class="bordering2"> <?php 
                          if(isset($deptyearLevel[$yvalue]['male'])){
                              echo $deptyearLevel[$yvalue]['male'];
                           } else {
                              echo 0;
							}
                           ?> 
                         </td>
                         <td class="bordering2"> 
                         <?php 
                            if(isset($deptyearLevel[$yvalue]['female'])){
                         		echo $deptyearLevel[$yvalue]['female'];
                            } else {
							    echo 0;
					         }
                         ?> </td>
                         <td class="bordering2"> <?php 
                         
                         echo $deptyearLevel[$yvalue]['total'];?> </td>
                         
                         <td class="bordering2"> <?php 
                         if ($deptyearLevel[$yvalue]['total']>0) {
				                if(isset($deptyearLevel[$yvalue]['male']) 
&& isset($deptyearLevel[$yvalue]['female'])){
				                 echo number_format(
				                   ($deptyearLevel[$yvalue]['male']+$deptyearLevel[$yvalue]['female'])/
				                   $deptyearLevel[$yvalue]['total'],3, '.', '');
								} else {
                                  echo "0";
							   }

                         } else {
                            echo "0";
                         }
                         ?> 
                         
                         </td>  
                     <?php
                       if(isset($deptyearLevel[$yvalue]['female'])) {
                         $grand_total_female+=$deptyearLevel[$yvalue]['female'];
                       }
                        if(isset($deptyearLevel[$yvalue]['male'])) {
                         $grand_total_male+=$deptyearLevel[$yvalue]['male'];
                       }
                     // $grand_total_male +=$deptyearLevel[$yvalue]['male'];
                      $dept_total +=$deptyearLevel[$yvalue]['total']; 
                      
                        
                        }  else {
?>
                        <td class="bordering2">---</td>
                        <td class="bordering2">---</td>
                  			<td class="bordering2">---</td>
 							          <td class="bordering2">---</td>
                    <?php 
          					}
                  } //foreach 
                ?>
                    
                       <td class="bordering2"> 
                         
                         <?php echo $grand_total_male; ?> 
                      </td>
                      <td class="bordering2">
                         
                          <?php echo $grand_total_female; ?> 

                      </td>
                      <td class="bordering2"> 
                         
                         <?php echo $dept_total; ?> 
                      </td>
                     <td class="bordering2"> 
                        
                         <?php 
                            if ($dept_total>0) {
                           echo number_format(
                           ($grand_total_female+$grand_total_male)/$dept_total,3, '.', '');
                         
                            } else {
                              echo "0";
                            }
                         ?> 
                         
                      </td>  
                    
                   </tr>  
                   
                   <?php
                   }
                    $count=0;
                    ?>

  <?php 
  } 
?>
                    
</table>
<?php
 }
 ?>
 
<p class="fs16">
        <strong > Label </strong> <br/>
        <strong> M : </strong>  Male Dismissed <br/>
        <strong> F : </strong>  Female Dismissed <br/>
        <strong> Total : </strong> Total Registred <br/>
        <strong> Rate : </strong> Rate Dismissed <br/>
        <strong> - : </strong> No registration for that year <br/>
       
</p>


 <?php 
}
?>
</div>

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
if (isset($graduateRateToEntry['distributionGraduateEntry']) && 
!empty($graduateRateToEntry['distributionGraduateEntry'])){
?>
 
</p>
<?php 
foreach($graduateRateToEntry['distributionGraduateEntry'] as $programD=>$list) {
    $headerExplode=explode('~',$programD);
    debug($headerExplode);
?>
	   <p class="fs16">
		  <strong> College : </strong>   <?php 
		          echo $headerExplode[0];
		        ?>
		        <br/>
		   <strong> Department : </strong>   <?php 
		          echo $headerExplode[1];
		        ?>
		        <br/>
	 
		   <strong> Program : </strong>   <?php 
		          echo $headerExplode[2];
		        ?>
		        <br/>
		    <strong> Program Type: </strong>  <?php 
		          echo $headerExplode[3];
		          
		         
		        ?>
		        <br/>
  		
		  	<strong> Graduation Date : </strong>   <?php 
		          echo $this->Format->humanize_date($headerExplode[4]);
		        ?>
		        <br/>
                
	    </p>
		   
            <?php 

  
              ?>
                <table style="width:100%">
                	<tr>
                	   <td class="bordering2"> S.N<u>o</u> </td><td class="bordering2"> Gender </td> 
                	   <td class="bordering2"> Graduated </td> 
                       <td class="bordering2"> 
                        Admitted 
                        </td> 

                        <td class="bordering2"> 
                         Graduation Rate
                        </td> 
                    </tr> 

                <?php 
                   $count=1;
                   foreach ($list as $admittedAC=>$gval) {
                   ?>
                    <tr>
                     <th colspan="5" class="bordering2"> 
                     <?php echo "Admission AY: ".$admittedAC;?>
                     </th> 
                    </tr>   
                    <?php foreach($gval as $kk=>$kvv) { ?>  
                   <tr>
                        <td class="bordering"> 
                        <?php echo $count++;?> 
                        </td> 
                         <td class="bordering"> 
                        <?php echo $kk;?> 
                        </td> 
                        <td class="bordering"> 
                         <?php echo $kvv['graduated'];?> 
                        </td> 
                     	<td class="bordering"> 
                         <?php echo $kvv['admitted'];?> 
                        </td> 
                        <td class="bordering"> 
                         <?php echo number_format(($kvv['graduated']/$kvv['admitted'])*100,2,'.','');?> 

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


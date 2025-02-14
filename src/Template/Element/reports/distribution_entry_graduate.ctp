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
                         <?php echo number_format(($kvv['graduated']/$kvv['admitted'])*100,2,'.','').'%';?> 

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


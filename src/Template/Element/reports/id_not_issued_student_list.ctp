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
if (isset($idNotPrintedStudentList['IDPrintingList']) && 
!empty($idNotPrintedStudentList['IDPrintingList'])) {
?>
 <p class="fs16">

		    ID Card not issued list in <?php echo $this->data['Student']['acadamic_year']; ?> A/Y <br/>
</p>
<?php 
debug($idNotPrintedStudentList['IDPrintingList']);
foreach($idNotPrintedStudentList['IDPrintingList'] as $programD=>$list) {
    $headerExplode=explode('~',$programD);
?>
	   <p class="fs16">
		   <strong> Department : </strong>   <?php 
		          echo $headerExplode[0];
		        ?>
		        <br/>
	 
		   <strong> Program : </strong>   <?php 
		          echo $headerExplode[1];
		        ?>
		        <br/>
		    <strong> Program Type: </strong>  <?php 
		          echo $headerExplode[2];
		          
		         
		        ?>
		  <strong> Year : </strong>   <?php 
		          echo $headerExplode[3];
		        ?>
		        <br/>
                  <strong> Printed Count : </strong>   <?php 
		          echo $headerExplode[4];
		        ?>
		        <br/>
		  
	    </p>
		   
            <?php 

  
              ?>
                <table style="width:100%">
                   
                    <tr>
                        <td class="bordering2"> S.N<u>o</u> </td> 
                        <td class="bordering2"> ID </td> 
                        <td class="bordering2"> Sex </td> 
                        <td class="bordering2"> Full Name </td> 
		
                    </tr>     
                 <?php 
                    $count=0;
             
                    foreach ($list as $ko=>$val) {

		
                  ?>
 
                       <tr class='jsView' data-animation="fade" data-reveal-id="myModal" data-reveal-ajax="/students/get_modal_box/<?php echo $val['id'];?>">
                        <td class="bordering" > <?php echo ++$count; ?> </td> 
                        <td class="bordering" > <?php echo $val['studentnumber']; ?>  </td> 
                         <td class="bordering" > <?php echo $val['gender']; ?>  </td> 
                        <td class="bordering" > <?php echo $val['first_name'].' '.
$val['middle_name'].' '.$val['last_name']; ?> </td> 

 			
                    </tr>     
                  <?php 
                   
                 }
?>
    </table>
<?php 
}
                 ?>
            
          
        
  <?php 
 
}   
?>


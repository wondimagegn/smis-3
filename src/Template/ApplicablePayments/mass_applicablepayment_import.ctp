<?php 
echo $this->Form->create('ApplicablePayment', array('controller' => 'applicable_payments', 'action' => 'mass_applicablepayment_import', 'type' => 'file'));
?>
<div class="box">
     <div class="box-body pad-forty">
       <div class="row">
		   	 <div class="large-12 columns">
		   	 	   <h4><?php echo __('Upload Applicable Payment '); ?></h4>	
				 	
		   	 </div>
			 <div class="large-12 columns">
                  <p class="fs16">
<span class="rejected">Be-aware:</span> Before importing the excel ,make sure that the excel use the template below. Dont touch or edit the header of the template, it is required by the program.  <a href="/files/template/applicablepayment_template.xls">Download Import Template!</a>
</p>
			 </div>
			  <div class="large-12 columns">
<?php   
                     echo $this->Form->input('academic_year',array('id'=>'academicyear',
            'label' => 'Academic Year','type'=>'select','options'=>$acyear_list,
            'empty'=>"--Select Academic Year--",
            'required'=>true,
            'selected'=>isset($this->request->data['ApplicablePayment']['academic_year'])
            && !empty($this->request->data['ApplicablePayment']['academic_year']) ? 
            $this->request->data['ApplicablePayment']['academic_year']:'')); 
?>

            <?php 
            echo $this->Form->input('semester',
                    array('options'=>array('I'=>'I','II'=>'II',
            'III'=>'III')));
            ?>

				   <label><strong>Applicable Payment : </strong>

				   <?php    
						echo $this->Form->file('File',array('label'=>'Excel'));

				?>
				</label><br/>


     <?php 
    echo $this->Form->submit('Upload',array('class'=>'tiny radius button bg-blue'));

?>

	<?php
    if(isset($non_valide_rows)){
        
          echo "<ul style='color:red'>";
          foreach($non_valide_rows as $k=>$v){
                echo "<li>".$v."</li>";
          }
          echo "</ul>";
         
    }
   
 ?>
 
 
			  </div> <!-- end of columns 6 -->
			  
	   </div> <!-- end of row --->
   </div> <!-- end of box-body -->
</div><!-- end of box -->

<?php    
echo $this->Form->end();

?>



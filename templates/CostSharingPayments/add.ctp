<?php ?>
<script type='text/javascript'>
//Sub cat combo
function updateDepartmentCollege(id) {
           
            //serialize form data
            var formData = $("#college_id_"+id).val();
			$("#college_id_"+id).attr('disabled', true);
			$("#department_id_"+id).attr('disabled', true);
			$("#section_id_"+id).attr('disabled', true);
			$("#student_id_"+id).attr('disabled', true);
			$("#academic_year_id_"+id).attr('disabled', true);
			
			//get form action
            var formUrl = '/departments/get_department_combo/'+formData;
            $.ajax({
                type: 'get',
                url: formUrl,
                data: formData,
                success: function(data,textStatus,xhr){
						$("#department_id_"+id).attr('disabled', false);
						$("#college_id_"+id).attr('disabled', false);
						$("#academic_year_id_"+id).attr('disabled', false);
						$("#department_id_"+id).empty();
						$("#department_id_"+id).append(data);
							//student lists
							var subCat = $("#department_id_"+id).val();
							$("#section_id_"+id).attr('disabled', true);	
							
							//get form action
							var formUrl = '/sections/get_sections_by_dept/'+subCat;
							$.ajax({
								type: 'get',
								url: formUrl,
								data: subCat,
								success: function(data,textStatus,xhr){
										
										$("#section_id_"+id).attr('disabled', false);
			                            $("#student_id_"+id).attr('disabled', false);
			                            
										$("#section_id_"+id).empty();
										$("#section_id_"+id).append(data);
										
								},
								error: function(xhr,textStatus,error){
										alert(textStatus);
								}
							});
							
					},
                error: function(xhr,textStatus,error){
                        alert(textStatus);
                }
			});
			
			return false;
      
}

//Sub cat combo
function updateSection(id) {
           
            //serialize form data
            var formData = $("#department_id_"+id).val();
			$("#section_id_"+id).attr('disabled', true);
			$("#college_id_"+id).attr('disabled', true);
			$("#department_id_"+id).attr('disabled',true);	
			$("#academic_year_id_"+id).attr('disabled', true);
					//get form action
			var formUrl = '/sections/get_sections_by_dept/'+formData;
			$.ajax({
				type: 'get',
				url: formUrl,
				data: formData,
				success: function(data,textStatus,xhr){
						$("#section_id_"+id).attr('disabled', false);
						$("#college_id_"+id).attr('disabled', false);
			            $("#department_id_"+id).attr('disabled',false);	
			            $("#academic_year_id_"+id).attr('disabled', false);
						$("#section_id_"+id).empty();
						$("#section_id_"+id).append(data);
						
				},
				error: function(xhr,textStatus,error){
						alert(textStatus);
				}
			});
			return false;
 }
 //Sub cat combo
function updateSectionOFYear(id) {
           
            //serialize form data
            var formData = $("#academic_year_id_"+id).val();
            $("#academic_year_id_"+id).attr('disabled', true);
			$("#section_id_"+id).attr('disabled', true);
			$("#college_id_"+id).attr('disabled', true);
			$("#department_id_"+id).attr('disabled',true);	
					//get form action
			var formUrl = '/sections/get_sections_by_academic_year/'+formData+'/'+
			 $("#department_id_"+id).val();
			
			$.ajax({
				type: 'get',
				url: formUrl,
				data: formData,
				success: function(data,textStatus,xhr){
						$("#section_id_"+id).attr('disabled', false);
						 $("#academic_year_id_"+id).attr('disabled', false);
						$("#college_id_"+id).attr('disabled', false);
			            $("#department_id_"+id).attr('disabled',false);	
						$("#section_id_"+id).empty();
						$("#section_id_"+id).append(data);
						
				},
				error: function(xhr,textStatus,error){
						alert(textStatus);
				}
			});
			return false;
 }
//Sub cat combo
function updateStudent(id) {
           
            //serialize form data
            var formData = $("#section_id_"+id).val();
			$("#college_id_"+id).attr('disabled', true);
			$("#department_id_"+id).attr('disabled',true);	
			$("#section_id_"+id).attr('disabled', true);
			$("#student_id_"+id).attr('disabled', true);
			//get form action
            var formUrl = '/sections/get_section_students/'+formData;
            $.ajax({
                type: 'get',
                url: formUrl,
                data: formData,
                success: function(data,textStatus,xhr){
						$("#section_id_"+id).attr('disabled', false);
						$("#student_id_"+id).attr('disabled',false);
						$("#college_id_"+id).attr('disabled', false);
			            $("#department_id_"+id).attr('disabled',false);	
						$("#student_id_"+id).empty();
						$("#student_id_"+id).append(data);
							
				},
                error: function(xhr,textStatus,error){
                        alert(textStatus);
                }
			});
			
			return false;
      
}

  function updateCostSharingSummery (id) {
           //serialize form data
            var formData = $("#student_id_"+id).val();

			$("#college_id_"+id).attr('disabled', true);
			$("#section_id_"+id).attr('disabled', true);
			$("#department_id_"+id).attr('disabled', true);
			$("#summery_of_cost").attr('disabled',true);
			//get form action
            var formUrl = '/cost_shares/get_cost_share_summery/'+formData;
            $.ajax({
                type: 'get',
                url: formUrl,
                data: formData,
                success: function(data,textStatus,xhr){
						$("#section_id_"+id).attr('disabled', false);
						$("#department_id_"+id).attr('disabled', false);
						$("#college_id_"+id).attr('disabled', false);
						$("#summery_of_cost").empty();
						$("#summery_of_cost").append(data);
				},
                error: function(xhr,textStatus,error){
                        alert(textStatus);
                }
			});
			
			return false;
 }

</script>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
             
<div class="costShares form">
<?php echo $this->Form->create('CostSharingPayment',array('type'=>'file'));?>

<?php 
    if (!isset($studentIDs)) {

?>
<table cellpadding="0" cellspacing="0">
<?php 	
        echo '<tr><td class="smallheading">Add Cost Sharing Payment For Student</td></tr>';
        
		echo '<tr><td class="font">'.$this->Form->input('studentID',array('label' => 'Student ID')).'</td></tr>';
       echo '<tr><td>'. $this->Form->Submit('Continue',array('name'=>'continue','class'=>'tiny radius button bg-blue','div'=>false)).'</td></tr>';
?>
</table>
<?php 
}

?>

<?php 
        if (isset($studentIDs)) {
?>
	<div class="smallheading"><?php echo __('Add  Payment'); ?></div>
	<table>
	    <tr>
	     <td>
	        <table>
	          
	            <?php
	            '<tr><td class="smallheading">Add cost sharing payment</td></tr>';
		            echo $this->Form->hidden('student_id',array('value'=>$student_section_exam_status['StudentBasicInfo']['id']));
		           		
		echo '<tr><td>'.$this->Form->input('reference_number').'</td></tr>';
		echo '<tr><td>'.$this->Form->input('amount').'</td></tr>';
		echo '<tr><td>'.$this->Form->input('payment_type').'</td></tr>';
		
		echo '<tr><td>'.$this->element('attachments', array('plugin' => 'media')).'</td></tr>';
		            //
		         
	        ?>
	        </table>
	    
	    </td>
	    <td><?php 
	       echo $this->element('student_basic');
	       
	        if (!empty($cost_share_summery)) {
                $sum=0;
                //cost_sharing_sign_date
                echo '<table>';
                echo '<tr><td colspan=5 class="smallheading">The cost shares student has already incurred.</td></tr>';
                
                echo '<tr><th>Year</th><th>Education Fee</th><th>Accomodation Fee</th>
                <th>Cafeteria Fee</th><th>Medical Fee</th><th>Total</th></tr>';
                foreach ($cost_share_summery as $index=>$value) {
                    $total = $value['CostShare']['education_fee']+$value['CostShare']['accomodation_fee']+
                    $value['CostShare']['cafeteria_fee']+$value['CostShare']['medical_fee']; 
                    
                    echo '<tr>';//cafeteria_fee medical_fee
                    echo '<td>'.$value['CostShare']['academic_year'].'</td><td>'.$value['CostShare']['education_fee'].
                    '</td><td>'.$value['CostShare']['accomodation_fee'].'</td><td>'.$value['CostShare']['cafeteria_fee'].
                    '</td><td>'.$value['CostShare']['medical_fee'].'</td><td>'.$total.'</td>';
                    echo '</tr>';
                    $sum =$sum+$total;
                }
                echo '<tr><td>Grand Total</td><td colspan="5" style="text-align:middle">'.$sum.'</td></tr>';
                echo '</table>';
            } else {
                echo '<div class="info-box info-message"><span></span>There is no 
                cost sharing payment for '.$student_full_name.'.Either it is cost is not maintain or not applicable</div>';
            }
	      
	    ?></td>
	    
	    </tr>
	 <tr><td> <?php echo $this->Form->Submit('Save',array('name'=>'saveApplicablePayment','class'=>'tiny radius button bg-blue','div'=>false)); 
	 ?>
	 </td></tr>
	</table>
	
<?php 
}
?>
</div>


	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->

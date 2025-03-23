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
			
			//get form action
            var formUrl = '/departments/get_department_combo/'+formData;
            $.ajax({
                type: 'get',
                url: formUrl,
                data: formData,
                success: function(data,textStatus,xhr){
						$("#department_id_"+id).attr('disabled', false);
						$("#college_id_"+id).attr('disabled', false);
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

function updateCollegeSection(id) {
           
            //serialize form data
            var formData = $("#college_id_"+id).val();
			$("#college_id_"+id).attr('disabled', true);
			
			$("#section_id_"+id).attr('disabled', true);
			$("#student_id_"+id).attr('disabled', true);
	

			
			//get form action
			var formUrl = '/sections/get_sections_of_college/'+formData;
			$.ajax({
				type: 'get',
				url: formUrl,
				data: formData,
				success: function(data,textStatus,xhr){
						
						$("#section_id_"+id).attr('disabled', false);
                        $("#student_id_"+id).attr('disabled', false);
                        $("#college_id_"+id).attr('disabled', false);
                        
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
function updateSection(id) {
           
            //serialize form data
            var formData = $("#department_id_"+id).val();
			$("#section_id_"+id).attr('disabled', true);
			$("#college_id_"+id).attr('disabled', true);
			$("#department_id_"+id).attr('disabled',true);	
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

</script>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
             
<div class="costShares form">
<?php echo $this->Form->create('CostShare',array('type'=>'file'));?>
   <div class="smallheading"><?php echo __('Maintain Student Cost Share'); ?></div>
	<?php 
	echo '<table>';
	echo '<tr><td>';
	    echo '<table class="fs13 small_padding">';
	
		         $from = date('Y') - 1;
                 $to = date('Y') + 1;
 if (isset($college_ids) && !empty($college_ids)) {
       echo '<tr><td style="width:13%">College</td><td style="width:37%">'.$this->Form->input('college_id',array('label'=>false,'empty'=>'--select college--','id'=>'college_id_1',
 'onchange'=>'updateCollegeSection(1)','style'=>'width:250px')).'</td></tr>'; 
 
 } else {
             echo '<tr><td style="width:13%">College</td><td style="width:37%">'.$this->Form->input('college_id',
             array('label'=>false,'empty'=>'--select college--','id'=>'college_id_1',
         'onchange'=>'updateDepartmentCollege(1)','style'=>'width:250px')).'</td></tr>'; 
         
         echo '<tr><td style="width:13%">Department</td><td style="width:37%">'.$this->Form->input('department_id',
         array('id'=>'department_id_1',
         'onchange'=>'updateSection(1)','label'=>false,'style'=>'width:250px')).'</td></tr>';
 }               
 
 
 echo '<tr><td style="width:13%">Section</td><td style="width:37%">'.$this->Form->input('section_id',array('id'=>'section_id_1',
 'onchange'=>'updateStudent(1)','label'=>false,'style'=>'width:250px')).'</td></tr>'; 
		echo '<tr><td style="width:13%">Student</td><td style="width:37%">'.$this->Form->input('student_id',array('id'=>'student_id_1','label'=>false,'style'=>'width:250px')).'</td></tr>';
		echo '<tr><td style="width:13%">Academic Year</td><td style="width:37%">'.$this->Form->input('academic_year',array('options'=>$acyear_array_data,'default'=>$defaultacademicyear,'label'=>false)).'</td></tr>';
		echo '<tr><td style="width:13%">Sharing Cycle</td><td style="width:37%">'.$this->Form->input('sharing_cycle',array('id'=>'sharingCycle',
            'label' =>false,'type'=>'select','options'=>$sharing_cycles,
            'required'=>true,
            'selected'=>isset($this->request->data['CostShare']['sharing_cycle'])
            && !empty($this->request->data['CostShare']['sharing_cycle']) ? 
            $this->request->data['CostShare']['sharing_cycle']:'')).'</td></tr>';

		
    echo '</table></td><td>';
		echo '<table>';
		echo '<tr><td>'.$this->Form->input('education_fee').'</td></tr>';
		echo '<tr><td>'.$this->Form->input('accomodation_fee').'</td></tr>';
		echo '<tr><td>'.$this->Form->input('cafeteria_fee').'</td></tr>';
		echo '<tr><td>'.$this->Form->input('medical_fee').'</td></tr>';
		echo '<tr><td>'.$this->Form->input('cost_sharing_sign_date',array('format'=>$format,
		'minYear'=>$from,'maxYear'=>$to,'type'=>'date')).'</td></tr>';
		echo '<tr><td>'.$this->element('attachments', array('plugin' => 'media')).'</td></tr>';
	    echo '</table>';
	    echo '</td></tr>';
	    echo '</table>';
	?>

<?php echo $this->Form->end(array('label'=>__('Submit'),'class'=>'tiny radius button bg-blue'));?>
</div>

	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row -->
      </div> <!-- end of box-body -->
</div><!-- end of box -->

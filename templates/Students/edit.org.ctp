<?php  echo $this->Html->script('amharictyping'); ?> 
<script type="text/javascript">
var region=Array();		
var months = Array();
<?php
for($i = 1; $i <= 12; $i++) {
    ?>
    months[<?php echo $i -1; ?>] = new Array();
    months[<?php echo $i -1; ?>][0] = "<?php echo date('m', mktime(0, 0, 0, $i, 1, 2011)); ?>";
    months[<?php echo $i -1; ?>][1] = "<?php echo date('F', mktime(0, 0, 0, $i, 1, 2011)); ?>";
    <?php
}
?>


<?php
   
    foreach($regions as $region_id=>$region_name){
    ?>
        region["<?php echo $region_id; ?>"] = "<?php echo $region_name; ?>";
    <?php
    
        }
?>

 		function addRow(tableID,model,no_of_fields,all_fields,other) {
			
			 var elementArray = all_fields.split(',');
            
			var table = document.getElementById(tableID);

			var rowCount = table.rows.length;
			var row = table.insertRow(rowCount);

			var cell0 = row.insertCell(0);
			cell0.innerHTML = rowCount ;
			for(var i=1;i<=no_of_fields;i++) {
				var cell = row.insertCell(i);
				if (elementArray[i-1] == "region_id") {
				  
			            var element = document.createElement("select");
						var string='<option value="">--select region--- </option>';
						for (var f=1;f<region.length;f++) {
						   string += '<option value="'+f+'"> '+region[f]+'</option>';
						}		
						element.style.width='150px';
			            element.innerHTML = string;
			            
				} else if ( elementArray[i-1] == "exam_year") {
					/*
				        var element = document.createElement("select");
					    var d = new Date();
			            var full_year =d.getFullYear();
			            var string='<option value="">--select year--- </option>';
			            
			            for(var j=full_year-1;j>other;j--) {
			                
				            string += '<option value="'+j+'"> '+j+'</option>';
			            }
			
			            element.innerHTML = string;
				   */
                                   var element1 = document.createElement("select");
		               string = "";
			       for (var f=0;f<months.length;f++) {
				   string += '<option value="'+months[f][0]+'"> '+months[f][1]+'</option>';
				}
				element1.name = "data["+model+"]["+rowCount+"]["+elementArray[i-1]+"][month]";
				element1.innerHTML = string;
				cell.appendChild(element1);

				
        			var element3 = document.createElement("select");
			        var d = new Date();
		            var full_year =d.getFullYear();
		            string = "";
		            for(var j=full_year;j>=full_year-30;j--) {
			            string += '<option value="'+j+'"> '+j+'</option>';
		            }
		            element3.name = "data["+model+"]["+rowCount+"]["+elementArray[i-1]+"][year]";
		            element3.innerHTML = string;
		           cell.appendChild(element3);
		            continue;

				} else if (elementArray[i-1]=='grade') {
				    var element = document.createElement("input");
				    element.type = "text";
				    element.size = "4";
				} else if (elementArray[i-1] == 'mark') {
				    var element = document.createElement("input");
				    element.type = "text";
				    element.size = "5";
				} else if (elementArray[i-1]=='national_exam_taken') {
				
				   var element = document.createElement("input");
				   element.type = "checkbox";
				  
				} else if (elementArray[i-1]=='first_degree_taken'){
				var element = document.createElement("input");
				   element.type = "checkbox";
				
				} else if(elementArray[i-1]=='second_degree_taken'){
				var element = document.createElement("input");
				   element.type = "checkbox";
				
				} else if (elementArray[i-1] == 'cgpa_at_graduation') {
				    var element = document.createElement("input");
				    element.type = "text";
				    element.size = "5";
				
				} else if (elementArray[i-1] == 'date_graduated') {
			       var element1 = document.createElement("select");
		               string = "";
			       for (var f=0;f<months.length;f++) {
				   string += '<option value="'+months[f][0]+'"> '+months[f][1]+'</option>';
				}
				element1.name = "data["+model+"]["+rowCount+"]["+elementArray[i-1]+"][month]";
				element1.innerHTML = string;
				cell.appendChild(element1);

				var element2 = document.createElement("select");
		                string = "";
				for (var f=1;f<=31;f++) {
				   string += '<option value="'+(f < 10 ? '0'+f : f)+'"> '+f+'</option>';
				}
				element2.name = "data["+model+"]["+rowCount+"]["+elementArray[i-1]+"][day]";
				element2.innerHTML = string;
				cell.appendChild(element2);
        			var element3 = document.createElement("select");
			        var d = new Date();
		            var full_year =d.getFullYear();
		            string = "";
		            for(var j=full_year;j>=full_year-30;j--) {
			            string += '<option value="'+j+'"> '+j+'</option>';
		            }
		            element3.name = "data["+model+"]["+rowCount+"]["+elementArray[i-1]+"][year]";
		            element3.innerHTML = string;
		            cell.appendChild(element3);
		            continue;

			           
				
				} else {
				   var element = document.createElement("input");
				    element.type = "text";
				     element.size = "13";
				}
			
				
				element.name = "data["+model+"]["+rowCount+"]["+elementArray[i-1]+"]";
				cell.appendChild(element);
			}

		}
		
	   function deleteRow(tableID) {
		   
			try {
			    var table = document.getElementById(tableID);
			    var rowCount = table.rows.length;
			    if(rowCount !=2){
                    table.deleteRow(rowCount-1);
			    } else {
			
			        //alert('No more rows to delete');
			    }
			
			} catch(e) {
				alert(e);
			}
			
	 }
	  function updateRegionCity(id) {
            //serialize form data
            var formData = $("#country_id_"+id).val();
           
			$("#region_id_"+id).empty();
			$("#region_id_"+id).attr('disabled', true);
			$("#city_id_"+id).attr('disabled', true);
			//get form action
            var formUrl = '/students/get_regions/'+formData;
            $.ajax({
                type: 'get',
                url: formUrl,
                data: formData,
                success: function(data,textStatus,xhr){
						$("#region_id_"+id).attr('disabled', false);
						$("#region_id_"+id).empty();
						$("#region_id_"+id).append(data);
							//Items list
							var subCat = $("#region_id_"+id).val();
							$("#city_id_"+id).empty();
							//get form action
							var formUrl = '/students/get_cities/'+subCat;
							$.ajax({
								type: 'get',
								url: formUrl,
								data: subCat,
								success: function(data,textStatus,xhr){
										$("#city_id_"+id).attr('disabled', false);
										$("#city_id_"+id).empty();
										$("#city_id_"+id).append(data);
								},
								error: function(xhr,textStatus,error){
										alert(textStatus);
								}
							});
							//End of items list
				},
                error: function(xhr,textStatus,error){
                        alert(textStatus);
                }
			});
			
			return false;
		}
	 //Update city given region
	 
	function updateCity(id) {
            //serialize form data
            var subCat = $("#region_id_"+id).val();
			$("#city_id_"+id).attr('disabled', true);
			$("#city_id_"+id).empty();
			//get form action
            var formUrl = '/students/get_cities/'+subCat;
            $.ajax({
                type: 'get',
                url: formUrl,
                data: subCat,
                success: function(data,textStatus,xhr){
						$("#city_id_"+id).attr('disabled', false);
						$("#city_id_"+id).empty();
						$("#city_id_"+id).append(data);
                },
                error: function(xhr,textStatus,error){
                        alert(textStatus);
                }
            });
			
            return false;
        }
        
</script>
<?php echo $this->Form->create('Student',array('type'=>'file','novalidate' => true));?>
<div class="box">
     <div class="box-body pad-forty">
       <div class="row">
       	 <div class="large-12 columns">
       	 	   <h4><?php echo __('Update Student Details'); ?></h4>		
       	 </div>
	  <div class="large-12 columns">
               
         	 <!-- tabs -->
				<ul class="tabs" data-tab>
					<li class="tab-title active"><a href="#basic_data">Basic Information</a>
					</li>
					<li class="tab-title"><a href="#add_address">Add Address</a>
					</li>
					<li class="tab-title"><a href="#education_background">Educational Background</a>
					</li>
					
				</ul>
            <div class="tabs-content edumix-tab-horz">
                <div class="content active" id="basic_data">
                       <div class="row">
                       <div class="large-6 columns">     	
							 <?php 
								$errors = $this->Form->validationErrors;
							     echo $this->Form->hidden('id');
    echo $this->Form->hidden('program_id');
		        echo $this->Form->hidden('program_type_id');
		        echo $this->Form->input('Contact.0.id',array('type'=>'hidden'));
		
								if(count($errors['Student']) > 0) { 
			
								 $flatErrors = Set::flatten($errors['Student']);
								?>
								 <div class="errorSummary">
									<ul>
									<?php 

										foreach($flatErrors as $key => $value) { ?>
										<li><?php echo($value); ?></li>
									<?php } ?>
									</ul>
								</div>
							  <?php } ?>
							  </div>
							  </div>
                        <div class="row">
                  <div class="large-6 columns">
										<strong> Demographic Information</strong>
                        <div class="row">
		
<div class="large-12 columns">
					<?php 
         
				echo '<label> First name in English';
                             echo $this->Form->input('first_name',array(
		          'readOnly'=>true,'label'=>false,'div'=>false));
				echo '</label>';
?>
</div>
<div class="large-12 columns">
<?php 
echo '<label> Middle name in English';
		          echo $this->Form->input('middle_name',array('label'=>false,
		          'readOnly'=>true));
echo '</label>';
?>
</div>
<div class="large-12 columns">
<?php 
		echo '<label> Last name in English';
		          echo $this->Form->input('last_name',array('label'=>false,
		          'readOnly'=>true));
		      echo '</label>';  
?>
</div>
<div class="large-12 columns">
             <label> First name in Amharic 
<?php 		               
		          if (empty($this->request->data['Student']['amharic_first_name'])) {
		            
		             echo $this->Form->input('amharic_first_name',array('label'=>false,array('id'=>'AmharicText','onkeypress'=>"return AmharicPhoneticKeyPress(event,this);")));     
		          } else {
		                echo $this->request->data['Student']['amharic_first_name'].
"";
		          } 
?>
		</label>
</div>
<div class="large-12 columns">
		 <label> Middle name in  Amharic
<?php 
		          if (empty($this->request->data['Student']['amharic_middle_name'])) {
		               echo $this->Form->input('amharic_middle_name',array('label'=>false,
		               array('id'=>'AmharicTextMiddleName','onkeypress'=>"return AmharicPhoneticKeyPress(event,this);")));
		          } else {
		                echo $this->request->data['Student']['amharic_middle_name'];
		          } 
?>
	</label>
</div>		
<div class="large-12 columns">  
	 <label> Last name in  Amharic      
<?php 
		          if (empty($this->request->data['Student']['amharic_last_name'])) {
		              
		                echo $this->Form->input('amharic_last_name',array('label'=>false,
		                array('id'=>'AmharicTextLastName','onkeypress'=>"return AmharicPhoneticKeyPress(event,this);")));
		          } else {
		                echo $this->request->data['Student']['amharic_last_name'].
"";
		          } 
?>
	</label>
</div>
<div class="large-12 columns">
<?php 
/*
 echo $this->Html->link(__('Name Spelling Error Correction', true),
		           array('action' => 'correct_name', $this->data['Student']['id'])).'<br/>';
		          
		          echo $this->Html->link(__('Change Name By Court Decision', true),
		           array('action' => 'name_change', $this->data['Student']['id'])).'<br/>';
		           */
		           
		           echo $this->Html->link('Name Spelling Error Correction','#',array('data-animation'=>"fade",'data-reveal-id'=>'myModalCorrectName','data-reveal-ajax'=>'/students/correct_name/'.$this->data['Student']['id'])).'<br/>';
		           echo $this->Html->link('Change Name By Court Decision','#',array('data-animation'=>"fade",'data-reveal-id'=>'myModalChangeName','data-reveal-ajax'=>'/students/name_change/'.$this->data['Student']['id'])).'<br/>';

 
		   ?>

</div>
<div class="large-12 columns">
		<label> Estimated Graduation Date
<?php 
		        echo $this->Form->input('estimated_grad_date',array(
		         'minYear' => date('Y'),
		          'maxYear' =>date('Y')+Configure::read('Calendar.expectedGraduationInFuture'), 
		          'orderYear' => 'desc',
		           'label'=>false
		        ));
?>
	</label>
</div>
<div class="large-12 columns">
<?php 
		        echo $this->Form->input('gender',array('label'=>'Gender','type'=>'select',
		        'options' => array('female' => 'Female', 'male' => 'Male')));
?>
</div>

<div class="large-12 columns">
<?php 
		        echo $this->Form->input('lanaguage',array('label'=>'Primary Lanaguage'));
		?>
</div>
<div class="large-12 columns">
<?php 
		        echo $this->Form->input('ecardnumber',array('label'=>'Ecardnumber'));
		?>
</div>
<div class="large-12 columns">
   <?php 
		
echo $this->Form->input('emailVisible',array('label'=>'Publish email as public'));
?>
</div>
<div class="large-12 columns">
 <?php 
		        
echo $this->Form->input('email',array('label'=>'Email'));

?>
</div>

<div class="large-12 columns">
		<?php 
		         echo  $this->Form->input('email_alternative',array('label'=>'Alternative Email'));
		?>
</div>
<div class="large-12 columns">
   <?php 
		
echo $this->Form->input('phoneVisible',array('label'=>'Publish phone as public'));
?>
</div>
<div class="large-12 columns">
			<?php 
		         echo $this->Form->input('phone_home',array('label'=>'Home Phone'));
			?>
	</div>
<div class="large-12 columns">	
		<?php           
		          echo $this->Form->input('phone_mobile',array('label'=>'Mobile Phone'));
		?>
	</div>
<div class="large-12 columns">	   
		<?php    
		        echo $this->Form->input('birthdate',array('label'=>'Birth date',
		        'minYear' => date('Y')-Configure::read('Calendar.birthdayInPast'), 'maxYear' => date('Y')-14, 'orderYear' => 'desc'));
?>
</div>
		
			

</div>
                                     </div>
								      <div class="large-6 columns">
                                              <?php 


		          // save account information in the user table
		        echo '<table><tbody>';
		           $atLeastOneImage=true;
		           		
		       if(!empty($this->request->data['Attachment']))
               {
		            echo '<tr><td colspan=2><strong>Attachment</strong></td></tr>';
                    
                    foreach($this->request->data['Attachment'] as $ak=>$av){
                        $action_controller_id='edit~students~'.$av['foreign_key'];
                     
                       if(!empty($av['dirname']) && !empty($av['basename']) && strcasecmp($av['dirname'],'img')==0){
                          
                          // echo $media->embed($media->file($av['dirname'].DS.$av['basename']));
                           echo '<tr><td valign="top" align="right">'.$this->Media->embedAsObject($av['dirname'].DS.$av['basename'],array('width'=>144,'class'=>'profile-picture')).'</td>';
                           echo '<td>'.$this->Html->link(__('Delete Picture', true), 
                           array('controller'=>'attachments','action' => 'delete',
                            $av['id'],$action_controller_id), null, sprintf(__('Are you sure you want to delete picture ?', true))).'</td>';
                           echo '</tr>';
                           
                       } else {
                          $atLeastOneImage=false;
                       }
                      
				    }
				    if ($atLeastOneImage==false) {
				    echo '<tr><td valign="top" align="right"><img src="/img/noimage.jpg" width="144" class="profile-picture"></td></tr>';
				    echo '<tr><td>&nbsp;</td></tr>';
                    echo '<tr><td>'.$this->element('Media.attachments').'</td></tr>';
                     echo '<tr><td>&nbsp;</td></tr>';
				    }
                } else {
                   echo '<tr><td valign="top" align="right"><img src="/img/noimage.jpg" 
width="144" class="profile-picture"></td></tr>';
  				   echo '<tr><td>&nbsp;</td></tr>';
                   echo '<tr><td>'.$this->element('Media.attachments').'</td></tr>';
                   echo '<tr><td>&nbsp;</td></tr>';
                }
                
                
                if (!empty($this->data['User']['username'])) {
                    
		            echo '<tr><td colspan=2><strong>Access Information</strong></td></tr>';
                
		            echo '<tr><td> Username:'.$this->data['User']['username'].'</td></td>';
                }
           
		       echo '<tr><td> <table><tbody>';
		      
		        echo '<tr><td colspan=2><strong>Classification of Admission</strong></td></tr>';
               
			    echo "<tr><td> Program: ".$programs[$this->data['Student']['program_id']] ."</td></tr>";
			    
			     echo "<tr><td> Program Type: ".$programTypes[$this->data['Student']['program_type_id']] ."</td></tr>";
			     
				
		        echo "<tr><td> School/Center: ".$colleges[$this->data['Student']['college_id']]."</td></tr>";
               
                  if (!empty($this->data['Student']['department_id'])) {
                        echo "<tr><td> Department:".$departments[$this->data['Student']['department_id']].'</td></tr>';
                      
                  } else {
                        echo "<tr><td> Department:--- </td></tr>";      
                  }
                  echo '<tr><td>Admission Year: '.$this->Format->humanize_date($this->data['Student']['admissionyear']).'</td></tr>';
                  
		        echo '</tbody></table></td>';
		        
		         echo "</tr>";
		         
		        echo '</tbody></table>';
		        

							?>     
                                     </div>



                             </div>
               </div>
                <div class="content" id="add_address">
                               <?php		
                echo '<table cellspacing="0" cellpading="0"><tbody>';
                echo "<tr><td><table><tbody>";
               
                 echo '<tr><td colspan=2><strong>Address & Contact </strong></td></tr>';
                 
	            // save student primary address 
		       
		        echo '<tr><td>'.$this->Form->input('country_id',array('id'=>'country_id_2','onchange'=>'updateRegionCity(2)','label'=>'Country','error'=>false,'empty'=>false,
		        'style'=>'width:250px','value'=>68)).'</td></tr>';
		        echo '<tr><td>'.$this->Form->input('region_id',array('id' => 'region_id_2',
		        'onchange'=>'updateCity(2)','label' => 'Region/City','error' => false,'empty' => 'Select Country First','style'=>'width:250px')).'</td></tr>';
		        echo '<tr><td>'. $this->Form->input('city_id',array('label'=>'City','id'=>'city_id_2')).'</td></tr>';
		        echo '<tr><td>'. $this->Form->input('zone_subcity',array('label'=>'Zone/Subcity')).'</td></tr>';
		        echo '<tr><td>'.$this->Form->input('woreda').'</td></tr>';
		       	      
		        echo '<tr><td>'.$this->Form->input('kebele').'</td></tr>';
		        echo '<tr><td>'.$this->Form->input('house_number',array('label'=>'House Number')).'</td></tr>';
		        
		        echo '<tr><td>'.$this->Form->input('address1',array('label'=>'Address1')).'</td></tr>';
		        
		        echo "</tbody></table></td>";
		
		          // save account information in the user table
		        echo '<td><table><tbody>';
		      
		        echo '<tr><td colspan=2><strong>Primary Emergency Contact</strong></td></tr>';
           
		        echo '<tr><td>'.$this->Form->input('Contact.0.first_name',array('label'=>'First Name')).'</td></td>';
		        echo '<tr><td>'.$this->Form->input('Contact.0.middle_name',array('label'=>'Middle Name')).'</td></tr>';
		         echo '<tr><td>'.$this->Form->input('Contact.0.last_name',
            array('label'=>'Last Name')).'</td></tr>';
            echo '<tr><td>'.$this->Form->input('Contact.0.country_id',
            array('label'=>'Country','id'=>'country_id_1','value'=>68,'style'=>'width:250px','onchange'=>'updateRegionCity(1)')).'</td></tr>';
              echo '<tr><td>'.$this->Form->input('Contact.0.region_id',
            array('label'=>'Region','id'=>'region_id_1','onchange'=>'updateCity(1)',
            'style'=>'width:250px')).'</td></tr>';
              echo '<tr><td>'.$this->Form->input('Contact.0.city_id',
            array('label'=>'City','id'=>'city_id_1','style'=>'width:250px')).'</td></tr>';
           
            echo '<tr><td>'.$this->Form->input('Contact.0.email',
            array('label'=>'Email')).'</td></tr>';
              echo '<tr><td>'.$this->Form->input('Contact.0.alternative_email',
            array('label'=>'Alternative Email')).'</td></tr>';
            
             echo '<tr><td>'.$this->Form->input('Contact.0.phone_home',
            array('label'=>'Phone Home')).'</td></tr>';
            
             echo '<tr><td>'.$this->Form->input('Contact.0.phone_office',
            array('label'=>'Phone Office')).'</td></tr>';
            
             echo '<tr><td>'.$this->Form->input('Contact.0.phone_mobile',
            array('label'=>'Phone Mobile')).'</td></tr>';
            
             echo '<tr><td>'.$this->Form->input('Contact.0.address1',
            array('label'=>'Address')).'</td></tr>';
            echo '<tr><td>'.$this->Form->input('Contact.0.primary_contact',
            array('label'=>'Primary Contact')).'</td></tr>';
            
		        echo '</tbody></table></td></tr>';
		       
		        echo '</tbody></table>';
      
		?>
                            </div>
                <div class="content" id="education_background">
                                <?php
		  $fields=array('school_level'=>'1','name'=>'2','national_exam_taken'=>3,'town'=>4,'zone'=>5,'region_id'=>6);
		            $all_fields = "";
			        $sep = "";

			        foreach ($fields as $key => $tag) {
				        $all_fields.= $sep.$key;
				        $sep = ",";
			        }
			
          
            echo "<div class=\"AddTab\">\n";
             // save account information in the user table
		        echo '<table><tbody><tr><td> <div class="smallheading">Senior Secondary/Preparatory school attended </div> <table  id="high_school_education"><tbody>';
		     
                echo '<tr><th>No.</th><th>School Level</th><th>Name</th><th>National Exam Taken</th><th>Town</th><th>Zone</th><th>Region</th></tr>';
		           
		            if (!empty($this->data['HighSchoolEducationBackground'])) {
		              $count=1;
		              foreach ($this->data['HighSchoolEducationBackground'] as $bk=>$bv) {
		             
		               
		                echo "<tr>";
		                   if (!empty($bv['id'])) {
		                       echo $this->Form->hidden('HighSchoolEducationBackground.'.$bk.'.id');
		                   }
		                   echo "<td>".$count."</td><td>".
		                   $this->Form->input('HighSchoolEducationBackground.'.$bk.'.school_level',
		                array('label'=>false,'div'=>false,'size'=>13)).
		                '</td><td>'.
		                $this->Form->input('HighSchoolEducationBackground.'.$bk.'.name',
		            array('label'=>false,'div'=>false,'size'=>13)).'</td>';
		         
		           
		           echo '<td>';
		           echo $this->Form->input('HighSchoolEducationBackground.'.$bk.'.national_exam_taken',
					array('label'=>false,'div'=>false));
				  echo '</td>';
		          
		          echo '<td>'.
					$this->Form->input('HighSchoolEducationBackground.'.$bk.'.town',
		            array('label'=>false,'div'=>false,'size'=>13)).'</td><td>'.
		            $this->Form->input('HighSchoolEducationBackground.'.$bk.'.zone',
		            array('label'=>false,'div'=>false,'size'=>13)).'</td><td>'.
					$this->Form->input('HighSchoolEducationBackground.'.$bk.'.region_id',
		            array('options'=>$regions,'type'=>'select','label'=>false,'style'=>'width:150px')).
		            '</td></tr>';
		          
				        $count++;
		            }
		         } else {
		             //echo $this->Form->hidden('HighSchoolEducationBackground.0.id');
		            echo "<tr><td>1</td><td>".$this->Form->input('HighSchoolEducationBackground.0.school_level',
		            array(
		            'label'=>false,'div'=>false,'size'=>13)).'</td><td>'.
		            $this->Form->input('HighSchoolEducationBackground.0.name',
		            array(
		            'label'=>false,'div'=>false,'size'=>13)).'</td>';
		          
		             echo '<td>';
		           echo $this->Form->input('HighSchoolEducationBackground.0.national_exam_taken',
					array('label'=>false,'div'=>false));
				    echo '</td>';
		         
		            echo '<td>'.
					$this->Form->input('HighSchoolEducationBackground.0.town',
		            array(
		            'label'=>false,'div'=>false,'size'=>13)).'</td><td>'.
		            $this->Form->input('HighSchoolEducationBackground.0.zone',
		            array(
		           'label'=>false,'div'=>false,'size'=>13)).'</td><td>'.
					$this->Form->input('HighSchoolEducationBackground.0.region_id',
		            array('options'=>$regions,'type'=>'select','label'=>false,'empty'=>'--select region--','style'=>'width:150px')).'</td><td>'.'</td></tr>';
		         ?>
		         
		         <?php 
		         
		         }
		
                echo "</tbody></table>";
               
                echo '</td></tr>';
                 ?>
                <table><tr><td colspan=6>
		<INPUT type="button" value="Add Row" onclick="addRow('high_school_education','HighSchoolEducationBackground',6,'<?php echo $all_fields; ?>')" />
		<INPUT type="button" value="Delete Row" onclick="deleteRow('high_school_education')" />
	</td></tr></table>
                
                <?php 
                    $higher_fields=array('name'=>'1','field_of_study'=>'2',
                    'diploma_awarded'=>'3','date_graduated'=>'4','cgpa_at_graduation'=>'5',
                    'city'=>'6',
                    'first_degree_taken'=>7,
                    'second_degree_taken'=>8
                    );
		            $higher_all_fields = "";
			        $sepp = "";

			        foreach ($higher_fields as $key => $tag) {
				       $higher_all_fields.= $sepp.$key;
				        $sepp = ",";
			        }
		        echo '<tr><td><div class="smallheading">Higher Education Attended</div><table id="higher_education_background"><tbody>';
		       
                 echo '<tr><th>No.</th><th>Name of Institution/College</th>
                 <th>Field of study</th><th>Diploam Awared</th>
                 <th>Date graduated </th>
                 <th>CGPA at Graduation</th>
                 <th>City</th>
                 <th>First Degree</th>
                 <th>Second Degree</th>
                 </tr>';
		           
		       if (!empty($this->data['HigherEducationBackground'])) {
		            $count=1;
		            foreach ($this->data['HigherEducationBackground'] as $bk=>$bv) {
		                       echo $this->Form->hidden('HigherEducationBackground.'.$bk.'.id');
		            echo "<tr><td>".$count."</td><td>".$this->Form->input('HigherEducationBackground.'.$bk.'.name',
		            array('label'=>false,'div'=>false,'size'=>13)).'</td>';
		            
		         
		            
		            echo '<td>'.
		            $this->Form->input('HigherEducationBackground.'.$bk.'.name',
		            array('label'=>false,'div'=>false,'size'=>13)).'</td><td>'.
					$this->Form->input('HigherEducationBackground.'.$bk.'.field_of_study',
		            array('label'=>false,'div'=>false,'size'=>13)).'</td><td>'.
		            $this->Form->input('HigherEducationBackground.'.$bk.'.diploma_awarded',
		            array('label'=>false,'div'=>false,'size'=>13)).'</td><td>'.
					$this->Form->input('HigherEducationBackground.'.$bk.'.cgpa_at_graduation',
		            array('label'=>false,'div'=>false,'size'=>5)).'</td>';
		            
		            echo "<td>".$this->Form->input('HigherEducationBackground.'.$bk.'.city',
		                array('div'=>false,'label'=>false,'type'=>'text','size'=>13))."</td>";
		                
		                echo "<td>".$this->Form->input('HigherEducationBackground.'.$bk.'.first_degree_taken',
		                array('div'=>false,'label'=>false,'type'=>'checkbox'))."</td>";
		                
		                echo "<td>".$this->Form->input('HigherEducationBackground.'.$bk.'.second_degree_taken',
		                array('div'=>false,'label'=>false,'type'=>'checkbox'))."</td>";
		                
		            echo '</tr>';
				        $count++;
		            }
		         } else {
		             echo $this->Form->hidden('HigherEducationBackground.0.id');
		             echo "<tr><td>1</td><td>".$this->Form->input('HigherEducationBackground.0.name',
		                array('div'=>false,'label'=>false))."</td>";
					 
					    
					    echo "<td>".$this->Form->input('HigherEducationBackground.0.field_of_study',
		                array('div'=>false,'label'=>false))."</td>";
					
					    echo "<td>".$this->Form->input('HigherEducationBackground.0.diploma_awarded',
		                array('div'=>false,'label'=>false))."</td>";
					echo "<td>".$this->Form->input('HigherEducationBackground.0.date_graduated',
		                array('div'=>false,'label'=>false))."</td>";
					
					echo "<td>".$this->Form->input('HigherEducationBackground.0.cgpa_at_graduation',
		                array('div'=>false,'label'=>false,'size'=>5))."</td>";
		              echo "<td>".$this->Form->input('HigherEducationBackground.0.city',
		                array('div'=>false,'label'=>false,'type'=>'text'))."</td>";
		            
		               echo "<td>".$this->Form->input('HigherEducationBackground.0.first_degree_taken',
		                array('div'=>false,'label'=>false,'type'=>'checkbox'))."</td>";
		            
		               echo "<td>".$this->Form->input('HigherEducationBackground.0.second_degree_taken',
		                array('div'=>false,'label'=>false,'type'=>'checkbox'))."</td>";
		            
		                
		            echo "</tr>";
		         
		         ?>
		       
			
			<?php 
             }   
              
		        ?>
		          <table><tr><td colspan=6>
		<INPUT type="button" value="Add Row" onclick="addRow('higher_education_background','HigherEducationBackground',8,'<?php echo  $higher_all_fields; ?>')" />
		<INPUT type="button" value="Delete Row" onclick="deleteRow('higher_education_background')" />
	</td></tr></table>
		        <?php 
		        echo '</tr>';
		       
		        echo '</tbody></table>';
		        echo "<table>";
		        	$eheece_fields=array('subject'=>'1','mark'=>'2','exam_year'=>'3');
		            $eheece_all_fields = "";
			        $sepeheece = "";

			        foreach ($eheece_fields as $key => $tag) {
				        $eheece_all_fields.= $sepeheece.$key;
				        $sepeheece = ",";
			        }
		         
		            $eslce_fields=array('subject'=>'1','grade'=>'2','exam_year'=>'3');
		            $eslce_all_fields = "";
			        $sepeslce = "";

			        foreach ($eslce_fields as $key => $tag) {
				        $eslce_all_fields.= $sepeslce.$key;
				        $sepeslce = ",";
			        }
	           	$from = date('Y') - Configure::read('Calendar.birthdayInPast');
                   $to = date('Y')-1;
                   $format = Configure::read('Calendar.yearFormat');
		
                   $yearoptions=array();
		    
                    for($j=$to;$j>=$from;$j--) {
                        $yearoptions[$j]=$j;
                    } 
					debug($yearoptions);
		            echo "<tr>";
		             
		            echo "<td width='50%'><div class='smallheading'>EHEECE Results</div><table id='eheece_result'>";
		            echo '<tr><th>No.</th><th>Subject</th><th>Mark</th><th>Exam Year</th></tr>';
		            if (!empty($this->data['EheeceResult'])) {
		                    $count=0;
		                    
		                      foreach ($this->data['EheeceResult'] as $bk=>$bv) {
		                        echo $this->Form->hidden('EheeceResult.'.$bk.'.id');
		                        echo "<tr><td>".++$count."</td><td>".$this->Form->input('EheeceResult.'.$bk.'.subject',
		            array('name'=>"data[EheeceResult][$bk][subject]",
		            'value'=>isset($this->data['EheeceResult'][$bk]['subject'])?
					$this->data['EheeceResult'][$bk]['subject']:'','div'=>false,'label'=>false))."</td>";
					
					echo "<td>".$this->Form->input('EheeceResult.'.$bk.'.mark',
		            array('name'=>"data[EheeceResult][$bk][mark]",
		            'value'=>isset($this->data['EheeceResult'][$bk]['mark'])?
					$this->data['EheeceResult'][$bk]['mark']:'','size'=>5,'div'=>false,'label'=>false))."</td>";
					
					
					echo "<td>".$this->Form->input('EheeceResult.'.$bk.'.exam_year',
		            array('name'=>"data[EheeceResult][$bk][exam_year][]",
		            'value'=>!empty($this->data['EheeceResult'][$bk]['exam_year'])?
					$this->data['EheeceResult'][$bk]['exam_year']:'','div'=>false,'label'=>false,'dateFormat'=>'YM','maxYear' => date('Y'), 'orderYear' => 'desc'))."</td></tr>";
					debug($this->data['EheeceResult'][$bk]['exam_year']);
		                      }
		            } else {
		                echo $this->Form->hidden('EheeceResult.0.id');
		                echo "<tr><td>1</td><td>".$this->Form->input('EheeceResult.0.subject',
		                array('name'=>"data[EheeceResult][0][subject]",
		                'value'=>isset($this->data['EheeceResult'][0]['subject'])?
					    $this->data['EheeceResult'][0]['subject']:'','div'=>false,'label'=>false))."</td>";
					
					    echo "<td>".$this->Form->input('EheeceResult.0.mark',
		                array('name'=>"data[EheeceResult][0][mark]",
		                'value'=>isset($this->data['EheeceResult'][0]['mark'])?
					    $this->data['EheeceResult'][0]['mark']:'','size'=>5,'div'=>false,'label'=>false))."</td>";
					
					    echo "<td>".$this->Form->input('EheeceResult.0.exam_year',
		                array('name'=>"data[EheeceResult][0][exam_year][]",'label'=>false,'dateFormat'=>'YM','maxYear' => date('Y'), 'orderYear' => 'desc'))."</td></tr>";

		               
		            }
		            echo "</table>";

		            
		            ?>
		              <table><tr><td colspan=4>
		<INPUT type="button" value="Add Row" onclick="addRow('eheece_result','EheeceResult',3,'<?php echo  $eheece_all_fields; ?>','<?php echo $from; ?>')" />
		<INPUT type="button" value="Delete Row" onclick="deleteRow('eheece_result')" />
	</td></tr></table>
		            <?php 
		            echo "</td>";
		            echo "<td width='50%'><div class='smallheading'>ESLCE Results</div><table id='eslce_result'>";
		            echo '<tr><th>No.</th><th>Subject</th><th>Grade</th><th>Exam Year</th></tr>';
		            
                      ///echo '<tr><td>'.$this->Form->input('birthdate',array('label'=>'Birth date','dateFormat'=>$format,'minYear'=>$from,'maxYear'=>$to)).'</td></tr>';
		            if (!empty($this->data['EslceResult'])) {
		                    $count=0;
		                      foreach ($this->data['EslceResult'] as $bk=>$bv) {
		                      echo $this->Form->hidden('EslceResult.'.$bk.'.id');
		                        echo "<tr><td>".++$count."</td><td>".$this->Form->input('EslceResult.'.$bk.'.subject',
		            array('name'=>"data[EslceResult][$bk][subject]",
		            'value'=>isset($this->data['EslceResult'][$bk]['subject'])?
					$this->data['EslceResult'][$bk]['subject']:'','div'=>false,'label'=>false))."</td>";
					
					echo "<td>".$this->Form->input('EslceResult.'.$bk.'.grade',
		            array('name'=>"data[EslceResult][$bk][grade]",
		            'value'=>isset($this->data['EslceResult'][$bk]['grade'])?
					$this->data['EslceResult'][$bk]['grade']:'','size'=>4,'div'=>false,'label'=>false))."</td>";
						
					echo "<td>".$this->Form->input('EslceResult.'.$bk.'.exam_year',
		            array('value'=>isset($this->data['EslceResult'][$bk]['exam_year'])?
					$this->data['EslceResult'][$bk]['exam_year']:'','div'=>false,'label'=>false,
					'dateFormat'=>'YM','maxYear' => date('Y'), 'orderYear' => 'desc'))."</td></tr>";
					
					
					
		                      }
		            } else {
		               echo $this->Form->hidden('EslceResult.0.id');
					
		               echo "<tr><td>1</td><td>".$this->Form->input('EslceResult.0.subject',
		            array('name'=>"data[EslceResult][0][subject]",
		            'value'=>isset($this->data['EslceResult'][0]['subject'])?
					$this->data['EslceResult'][0]['subject']:'','div'=>false,'label'=>false))."</td>";
					
					echo "<td>".$this->Form->input('EslceResult.0.grade',
		            array('name'=>"data[EslceResult][0][grade]",
		            'value'=>isset($this->data['EslceResult'][0]['grade'])?
					$this->data['EslceResult'][0]['grade']:'','size'=>4,'div'=>false,'label'=>false))."</td>";
					
					echo "<td>".$this->Form->input('EslceResult.0.exam_year',
		            array('name'=>"data[EslceResult][0][exam_year][]",
		            'value'=>isset($this->data['EslceResult'][0]['exam_year'])?
					$this->data['EslceResult'][0]['exam_year']:'','div'=>false,'label'=>false,'dateFormat'=>'YM','maxYear' => date('Y'), 'orderYear' => 'desc'))."</td></tr>";


				
		            
		            }
		            
		            echo "</table>";
		            ?>
		             <table><tr><td colspan=4>
		<INPUT type="button" value="Add Row" onclick="addRow('eslce_result','EslceResult',3,'<?php echo  $eslce_all_fields; ?>','<?php echo $from ?>')" />
		<INPUT type="button" value="Delete Row" onclick="deleteRow('eslce_result')" />
	</td></tr></table>
		            <?php 
		            echo "</td>";
		            echo "</tr>";
		         echo "</table>";
            echo "</div>";
            ?>
                            </div>
       
              </div>
             <!-- end of tabs -->
         	
<?php echo $this->Form->end(__('Update Student Detail', true));?>
        
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row -->
      </div> <!-- end of box-body -->
</div><!-- end of box -->


<a class="close-reveal-modal">&#215;</a>

<div class="row">
	<div class="large-12 columns">
		<div id="myModalChangeName" class="reveal-modal" data-reveal>

		</div>

		<div id="myModalCorrectName" class="reveal-modal" data-reveal>

		</div>


	</div>
</div>

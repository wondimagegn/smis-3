<?php echo $this->Form->create('Student', array('action' => 'admit','type'=>'file'));?>
<div class="students form" style="align:center">
<?php echo $this->Html->script('amharictyping'); ?> 
<script type="text/javascript">
var region=Array();		
//alert("<?PHP foreach ($regions as $k=>$v) echo $v; ?>"); 

<?PHP 
   
    foreach($regions as $region_id=>$region_name){
    ?>
        region["<?php echo $region_id; ?>"] = "<?php echo $region_name; ?>";
    <?php
    
        }
?>
//alert(region);
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
				        var element = document.createElement("select");
					    var d = new Date();
			            var full_year =d.getFullYear();
			            var string='<option value="">--select year--- </option>';
			            
			            for(var j=full_year-1;j>other;j--) {
			                
				            string += '<option value="'+j+'"> '+j+'</option>';
			            }
			
			            element.innerHTML = string;
				
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
<?php if (!isset($admitsearch)) { ?>
<table cellpadding="0" cellspacing="0">

	<tr>
    <td> <?php 
			echo $this->Form->input('AcceptedStudent.academicyear',array('id'=>'academicyear',
            'label' => 'Academic Year','type'=>'select','options'=>$acyear_array_data)); ?>
	</td>
	<td>
	   <?php 
	       if (!empty($college_level)) {
			echo $this->Form->input('AcceptedStudent.college_id',array('label'=>'Select College','type'=>'select','empty'=>'---Select College --'));
			 }
			 if (!empty($department_level)) {
			    echo $this->Form->input('AcceptedStudent.department_id',array('label'=>'Select Department','type'=>'select','empty'=>'---Select Department --'));
			 }
			
			 ?>  
	</td>

	</tr>
	<tr>
		<td>
		   <?php 
		   echo $this->Form->input('AcceptedStudent.program_type_id'); ?>  
		</td>
	   <td>
		   <?php 
		   echo $this->Form->input('AcceptedStudent.program_id'); ?>  
		</td>
	</tr>
	<tr><td><?php echo $this->Form->Submit('Search',array('div'=>false,'name'=>'getacceptedstudent','class'=>'tiny radius button bg-blue')); ?> </td>	
</tr></table>
<?php } ?>
<?php 
?>
<table><tbody><tr><td>
<?php 
if(!empty($acceptedStudents) && !isset($id)){

?>
	<table>
   
	<tr>
           
            <th><?php echo $this->Paginator->sort('full_name');?></th>
			<th><?php echo $this->Paginator->sort('sex');?></th>
			<th><?php echo $this->Paginator->sort('Student Number','studentnumber');?></th>
			
			<th><?php echo $this->Paginator->sort('EHEECE Result','EHEECE_total_results');?></th>
			<th><?php echo $this->Paginator->sort('Department Name','department_id');?></th>
			 <th><?php echo $this->Paginator->sort('College','college_id');?></th>
			<th><?php echo $this->Paginator->sort('Program Type','program_type_id');?></th>
			<th><?php echo $this->Paginator->sort('Academic Year','academicyear');?></th>
			<th><?php echo $this->Paginator->sort('Department Approved','Placement_Approved_By_Department');?></th>
		   
			<th class="actions"><?php echo __('Actions');?></th>
	</tr>
	<?php
	$i = 0;
	
	foreach ($acceptedStudents as $acceptedStudent):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
      
        <td><?php echo $acceptedStudent['AcceptedStudent']['full_name']; ?>&nbsp;</td>
		<td><?php echo $acceptedStudent['AcceptedStudent']['sex']; ?>&nbsp;</td>
		<td><?php echo $acceptedStudent['AcceptedStudent']['studentnumber']; ?>&nbsp;</td>
		
		<td><?php echo $acceptedStudent['AcceptedStudent']['EHEECE_total_results']; ?>&nbsp;</td>
		<td>
			<?php echo $this->Html->link($acceptedStudent['Department']['name'], array('controller' => 'departments', 'action' => 'view', $acceptedStudent['Department']['id'])); ?>
		</td>
		<td>
			<?php echo $this->Html->link($acceptedStudent['College']['name'], array('controller' => 'colleges', 'action' => 'view', $acceptedStudent['College']['id'])); ?>
		</td>
		<td>
			<?php echo $this->Html->link($acceptedStudent['ProgramType']['name'], array('controller' => 'program_types', 'action' => 'view', $acceptedStudent['ProgramType']['id'])); ?>
		</td>
		<td><?php echo $acceptedStudent['AcceptedStudent']['academicyear']; ?>&nbsp;</td>
		<td><?php echo $acceptedStudent['AcceptedStudent']['Placement_Approved_By_Department']==1?'Approved':'Not Approved'; ?>&nbsp;</td>
		
		
	
		<td class="actions" >
			
			<?php //echo $this->Html->link(__('Edit'), array('action' => 'edit', $acceptedStudent['AcceptedStudent']['id'])); ?>
            <?php echo $this->Html->link(__('Admit'), array('controller'=>'students','action' => 'admit', $acceptedStudent['AcceptedStudent']['id'])); ?>
			
		</td>
	</tr>
<?php endforeach; ?>
	</table>
   
	<p>
	<?php
	echo $this->Paginator->counter(array(
	'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%')
	));
	?>	</p>

	<div class="paging">
		<?php echo $this->Paginator->prev('<< ' . __('previous'), array(), null, array('class'=>'disabled'));?>
	 | 	<?php echo $this->Paginator->numbers();?>
 |
		<?php echo $this->Paginator->next(__('next') . ' >>', array(), null, array('class' => 'disabled'));?>
	</div>

<?php 
}
?>
</td></td>
</tbody></table>
<?php if(isset($id)) { ?>

<?php echo $this->Form->create('Student',array('type'=>'file','class'=>'ajax_page',
'action'=>'admit'));?>
	
		<div class="headerfont"><?php echo __('Admit Student'); ?></div>
		<div id="basic_fields" style="display:block">	
		 <?php
            echo $this->element('user_tab_menu',
                array('current_tab' => 'basic_fields'));
            
            echo "<div class=\"AddTab\">\n";
                echo '<table cellspacing="0" cellpading="0"><tbody>';
                echo "<tr><td><table><tbody>";
             
                 echo '<tr><td colspan=2><strong>Demographic Information</strong></td></tr>';
                
	            // save name in the basic information
		        echo '<tr><td>'.$this->Form->input('first_name',array('label'=>'First name in English')).'</td></tr>';
		         echo '<tr><td>'.$this->Form->input('amharic_first_name',array('label'=>'First name in Amharic','id'=>'AmharicText','onkeypress'=>"return AmharicPhoneticKeyPress(event,this);")).'</td></tr>';
		        echo '<tr><td>'.$this->Form->input('middle_name',array('label'=>'Middle name in English')).'</td></tr>';
		         echo '<tr><td>'.$this->Form->input('amharic_middle_name',array('label'=>'Middle name in  Amharic','id'=>'AmharicText','onkeypress'=>"return AmharicPhoneticKeyPress(event,this);")).'</td></tr>';
		        echo '<tr><td>'.$this->Form->input('last_name',array('label'=>'Last name in English')).'</td></tr>';
		         echo '<tr><td>'.$this->Form->input('amharic_last_name',array('label'=>'Last name in  Amharic','id'=>'AmharicText','onkeypress'=>"return AmharicPhoneticKeyPress(event,this);")).'</td></tr>';
		         
		        echo '<tr><td>'. $this->Form->input('estimated_grad_date').'</td></tr>';
		        
		        echo '<tr><td>'. $this->Form->input('marital_status',
		        array('options'=>array('single'=>'Single','Married'=>'Married',
            'divorced'=>'Divorced','windowed'=>'Windowed'),'empty'=>'--select marital status--')).'</td></tr>';
		       
		       
		        $options=array('male'=>'Male','female'=>'Female');
		        $attributes=array('legend'=>false,'label'=>false);
		        //echo $this->Form->radio('gender',$options,$attributes);
		        echo '<tr><td style="padding-left:100px;">'. $this->Form->radio('gender',$options,$attributes).'</td></tr>';
		        
		        echo $this->Form->hidden('curriculum_id',array('value'=>isset($this->request->data['Student']['curriculum_id'])?
		        $this->request->data['Student']['curriculum_id']:''));
		        echo '<tr><td>'. $this->Form->input('nationality',array('label'=>'Nationality')).'</td></tr>';
		        echo '<tr><td>'. $this->Form->input('ethnicity',array('label'=>'Ethnicity')).'</td></tr>';
		        echo '<tr><td>'. $this->Form->input('lanaguage',array('label'=>'Primary Lanaguage')).'</td></tr>';
		         echo '<tr><td>'. $this->Form->input('studentnumber',array('label'=>'Student Identification Number','readonly'=>'readonly')).'</td></tr>';
		         echo '<tr><td>'. $this->Form->hidden('accepted_student_id').'</td></tr>';
		
		         echo '<tr><td>'. $this->Form->input('email',array('label'=>'Email')).'</td></tr>';
		         echo '<tr><td>'. $this->Form->input('email_alternative',array('label'=>'Alternative Email')).'</td></tr>';
		         echo '<tr><td>'. $this->Form->input('phone_home',array('label'=>'Home Phone')).'</td></tr>';
		          
		          echo '<tr><td>'. $this->Form->input('phone_mobile',array('label'=>'Mobile Phone')).'</td></tr>';
		         $from = date('Y') - Configure::read('Calendar.birthdayInPast');
                 $to = date('Y') + Configure::read('Calendar.birthdayAhead');
                 $format = Configure::read('Calendar.dateFormat');
             
		        echo '<tr><td>'.$this->Form->input('birthdate',array('label'=>'Birth date','dateFormat'=>$format,'minYear'=>$from,'maxYear'=>$to)).'</td></tr>';
		        echo '<tr><td>'.$this->Form->input('place_of_birth',array('label'=>'Place of Birth')).'</td></tr>';
		        echo "</tbody></table></td>";
		
		          // save account information in the user table
		        echo '<td><table><tbody>';
		         echo '<tr><td colspan=2><strong>Profile Picture</strong></td></tr>';
		          echo '<tr><td valign="top" align="right"><img src="/img/noimage.jpg" width="144" class="profile-picture"></td></tr>';
		         echo '<tr><td>'.$this->element('attachments', array('plugin' => 'media','label'=>false)).'</td></tr>';
		       
		        echo '<tr><td colspan=2><strong>Access Information</strong></td></tr>';
                echo $this->Form->hidden('User.id',array('value'=>$this->request->data['User']['id']));
		      
		        echo "<tr><td> Username: ".$this->request->data['Student']['studentnumber']."</td></tr>";
		          echo '<tr><td colspan=2><strong>Classification of Admission</strong></td></tr>';
		        
                   	echo $this->Form->hidden('program_id',array('value'=>$this->request->data['Student']['program_id']));
                    echo $this->Form->hidden('college_id',array('value'=>$this->request->data['Student']['college_id']));
                     echo $this->Form->hidden('department_id');
		        	echo $this->Form->hidden('program_type_id');
			    	echo "<tr><td> Program: ".$programs[$this->request->data['Student']['program_id']] ."</td></tr>";
			    
			     echo "<tr><td> Program Type: ".$programTypes[$this->request->data['Student']['program_type_id']] ."</td></tr>";
			     
				
		        echo "<tr><td> College: ".$colleges[$this->request->data['Student']['college_id']]."</td></tr>";
               
                  if (!empty($this->request->data['Student']['department_id'])) {
                        echo "<tr><td> Department:".$departments[$this->request->data['Student']['department_id']].'</td></tr>';
                      
                  } else {
                  
                        echo "<tr><td> Department:--- </td></tr>";
                        
                  }
                  
                  //$this->Form->input('admissionyear',array('label'=>'Admission Year','disabled'=>true))
                
                     echo '<tr><td>'. $this->Form->input('admissionyear',array('label'=>'Admission Year')).'</td></tr>';
                  
                  
                  
                  
                  
                  
                  
                  
		        echo '</tbody></table></td>';
		        
		         echo "</tr>";
		      
		        echo '</tbody></table>';
		    echo "</div>"; // end add tab div
		    
	    ?>
	   </div> <!-- end basic info block --->
	 <div id="education_background" style="display:none">	
		 <?php
		 
			  $fields=array('school_level'=>'1','name'=>'2','national_exam_taken'=>3,'town'=>4,'zone'=>5,'region_id'=>6);
		            $all_fields = "";
			        $sep = "";

			        foreach ($fields as $key => $tag) {
				        $all_fields.= $sep.$key;
				        $sep = ",";
			        }
            echo $this->element('user_tab_menu',
                array('current_tab' => 'education_background'));
            
            echo "<div class=\"AddTab\">\n";
             // save account information in the user table
		        echo '<table><tbody><tr><td> <div class="smallheading">Senior Secondary/Preparatory school attended </div> <table  id="high_school_education"><tbody>';
		       // echo '<tr><td colspan=6><hr/></td></tr>';
		        //echo '<tr><td colspan=6><strong>Senior Secondary/Preparatory school attended</strong></td></tr>';
                //echo '<tr><td colspan=6><hr/></td></tr>';
                echo '<tr><th>No.</th><th>School Level</th><th>Name</th>
                <th>National Exam Taken</th><th>Town</th><th>Zone</th><th>Region</th></tr>';
		           
		            if (!empty($this->request->data['HighSchoolEducationBackground'])) {
		                    $count=1;
		                      foreach ($this->request->data['HighSchoolEducationBackground'] as $bk=>$bv) {
		            echo "<tr><td>".$count."</td><td>".$this->Form->input('HighSchoolEducationBackground.'.$bk.'.school_level',
		            array(
		            'label'=>false,'div'=>false,'size'=>13)).'</td><td>'.
		            $this->Form->input('HighSchoolEducationBackground.'.$bk.'.name',
		            array('label'=>false,'div'=>false,'size'=>13)).'</td>';
		            echo '<td>';
		            echo $this->Form->input('HighSchoolEducationBackground.'.$bk.'.national_exam_taken',
		            array('label'=>false,'div'=>false,'size'=>13));
		            
		            echo '</td>';
		            
		            echo '<td>'.
		            
					$this->Form->input('HighSchoolEducationBackground.'.$bk.'.town',
		            array('label'=>false,'div'=>false,'size'=>13)).'</td><td>'.
		            $this->Form->input('HighSchoolEducationBackground.'.$bk.'.zone',
		            array('label'=>false,'div'=>false,'size'=>13)).'</td><td>'.
					$this->Form->input('HighSchoolEducationBackground.'.$bk.'.region_id',
		            array('options'=>$regions,'type'=>'select','label'=>false,'empty'=>'--select region--')).'</td><td>'.'</td></tr>';
				        $count++;
		            }
		         } else {
		            echo "<tr><td>1</td><td>".$this->Form->input('HighSchoolEducationBackground.0.school_level',
		            array(
		            'value'=>isset($this->request->data['HighSchoolEducationBackground'][0]['school_level'])?$this->request->data['HighSchoolEducationBackground'][0]['school_level']:'','label'=>false,'div'=>false,'size'=>13)).'</td><td>'.
		            $this->Form->input('HighSchoolEducationBackground.0.name',
		            array(
		            'label'=>false,'div'=>false,'size'=>13)).'</td>';
		               echo '<td>';
		            echo $this->Form->input('HighSchoolEducationBackground.0.national_exam_taken',
		            array('label'=>false,'div'=>false,'size'=>13));
		            
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
                    $higher_fields=array('name'=>'1','field_of_study'=>'2','diploma_awarded'=>'3','date_graduated'=>'4','cgpa_at_graduation'=>'5');
		            $higher_all_fields = "";
			        $sepp = "";

			        foreach ($higher_fields as $key => $tag) {
				       $higher_all_fields.= $sepp.$key;
				        $sepp = ",";
			        }
		        echo '<tr><td><div class="smallheading">Higher Education Attended</div><table id="higher_education_background"><tbody>';
		       
                 echo '<tr><th>No.</th><th>Name of Institution/College</th><th>Field of study</th><th>Diploam Awared</th><th>Date graduated </th><th>CGPA at Graduation</th></tr>';
		           
		            if (!empty($this->request->data['HigherEducationBackground'])) {
		                    $count=1;
		                      foreach ($this->request->data['HigherEducationBackground'] as $bk=>$bv) {
		            echo "<tr><td>".$count."</td><td>".$this->Form->input('HigherEducationBackground.'.$bk.'.name',
		            array('label'=>false,'div'=>false)).'</td><td>'.
		            $this->Form->input('HigherEducationBackground.'.$bk.'.name',
		            array('label'=>false,'div'=>false)).'</td><td>'.
					$this->Form->input('HigherEducationBackground.'.$bk.'.diploma_awarded',
		            array('label'=>false,'div'=>false)).'</td><td>'.
		            $this->Form->input('HigherEducationBackground.'.$bk.'.date_graduated',
		            array('label'=>false,'type'=>'text','div'=>false)).'</td><td>'.
					$this->Form->input('HigherEducationBackground.'.$bk.'.cgpa_at_graduation',
		            array('size'=>4,'label'=>false,'div'=>false)).'</td></tr>';
				        $count++;
		            }
		         } else {
		         
		         ?>
		         
		            <TR>
				        <TD> 1 </TD>
				        <TD > <INPUT type="text" size="13"  name="data[HigherEducationBackground][0][name]"/> </TD>
				        <TD> <INPUT type="text" size="13" name="data[HigherEducationBackground][0][field_of_study]"/> </TD>
				        <TD> <INPUT type="text" size="13" name="data[HigherEducationBackground][0][diploma_awarded]"/> </TD>
				        <TD> <INPUT type="text" size="13" name="data[HigherEducationBackground][0][date_graduated]"/> </TD>
				        <TD> <INPUT type="text" size="13" name="data[HigherEducationBackground][0][cgpa_at_graduation]"/> </TD>
				
			        </TR>
			
			<?php 
             }   
              
		        ?>
		          <table><tr><td colspan=6>
		<INPUT type="button" value="Add Row" onclick="addRow('higher_education_background','HigherEducationBackground',5,'<?php echo  $higher_all_fields; ?>')" />
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
                    for($j=date('Y');$j>=$from;$j--) {
                        $yearoptions[$j]=$j;
                    }
		            echo "<tr>";
		             
		            echo "<td width='50%'><div class='smallheading'>EHEECE Results</div><table id='eheece_result'>";
		            echo '<tr><th>No.</th><th>Subject</th><th>Mark</th><th>Exam Year</th></tr>';
		            if (!empty($this->request->data['EheeceResult'])) {
		                    $count=0;
		                    
		                      foreach ($this->request->data['EheeceResult'] as $bk=>$bv) {
		                      
		                        echo "<tr><td>".++$count."</td><td>".$this->Form->input('EheeceResult.'.$bk.'.subject',
		            array('name'=>"data[EheeceResult][$bk][subject]",
		            'value'=>isset($this->request->data['EheeceResult'][$bk]['subject'])?
					$this->request->data['EheeceResult'][$bk]['subject']:'','div'=>false,'label'=>false))."</td>";
					
					echo "<td>".$this->Form->input('EheeceResult.'.$bk.'.mark',
		            array('name'=>"data[EheeceResult][$bk][mark]",
		            'value'=>isset($this->request->data['EheeceResult'][$bk]['mark'])?
					$this->request->data['EheeceResult'][$bk]['mark']:'','size'=>5,'div'=>false,'label'=>false))."</td>";
					
					
					echo "<td>".$this->Form->input('EheeceResult.'.$bk.'.exam_year',
		            array('name'=>"data[EheeceResult][$bk][exam_year]",
		            'value'=>!empty($this->request->data['EheeceResult'][$bk]['exam_year'])?
					$this->request->data['EheeceResult'][$bk]['exam_year']:'','div'=>false,'label'=>false,
					'type'=>'select','options'=>$yearoptions,
					'selected'=>!empty($this->request->data['EheeceResult'][$bk]['exam_year'])?
					$this->request->data['EheeceResult'][$bk]['exam_year']:''))."</td></tr>";
					
		                      }
		            } else {
		                echo "<tr><td>1</td><td>".$this->Form->input('EheeceResult.0.subject',
		                array('name'=>"data[EheeceResult][0][subject]",
		                'value'=>isset($this->request->data['EheeceResult'][0]['subject'])?
					    $this->request->data['EheeceResult'][0]['subject']:'','div'=>false,'label'=>false))."</td>";
					
					    echo "<td>".$this->Form->input('EheeceResult.0.mark',
		                array('name'=>"data[EheeceResult][0][mark]",
		                'value'=>isset($this->request->data['EheeceResult'][0]['mark'])?
					    $this->request->data['EheeceResult'][0]['mark']:'','size'=>5,'div'=>false,'label'=>false))."</td>";
					
					    echo "<td>".$this->Form->input('EheeceResult.0.exam_year',
		                array('name'=>"data[EheeceResult][0][exam_year]",'div'=>false,'label'=>false,
					'type'=>'select','options'=>$yearoptions,'empty'=>'--select year--'))."</td></tr>";
		               
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
		            if (!empty($this->request->data['EslceResult'])) {
		                    $count=0;
		                      foreach ($this->request->data['EslceResult'] as $bk=>$bv) {
		                        echo "<tr><td>".++$count."</td><td>".$this->Form->input('EslceResult.'.$bk.'.subject',
		            array('name'=>"data[EslceResult][$bk][subject]",
		            'value'=>isset($this->request->data['EslceResult'][$bk]['subject'])?
					$this->request->data['EslceResult'][$bk]['subject']:'','div'=>false,'label'=>false))."</td>";
					
					echo "<td>".$this->Form->input('EslceResult.'.$bk.'.grade',
		            array('name'=>"data[EslceResult][$bk][grade]",
		            'value'=>isset($this->request->data['EslceResult'][$bk]['grade'])?
					$this->request->data['EslceResult'][$bk]['grade']:'','size'=>4,'div'=>false,'label'=>false))."</td>";
						
					echo "<td>".$this->Form->input('EslceResult.'.$bk.'.exam_year',
		            array('value'=>isset($this->request->data['EslceResult'][$bk]['exam_year'])?
					$this->request->data['EslceResult'][$bk]['exam_year']:'','div'=>false,'label'=>false,
					'type'=>'select','options'=>$yearoptions,'selected'=>!empty($this->request->data['EslceResult'][$bk]['exam_year'])?
					$this->request->data['EslceResult'][$bk]['exam_year']:''))."</td></tr>";
					
					
					
		                      }
		            } else {
		               echo "<tr><td>1</td><td>".$this->Form->input('EslceResult.0.subject',
		            array('name'=>"data[EslceResult][0][subject]",
		            'value'=>isset($this->request->data['EslceResult'][0]['subject'])?
					$this->request->data['EslceResult'][0]['subject']:'','div'=>false,'label'=>false))."</td>";
					
					echo "<td>".$this->Form->input('EslceResult.0.grade',
		            array('name'=>"data[EslceResult][0][grade]",
		            'value'=>isset($this->request->data['EslceResult'][0]['grade'])?
					$this->request->data['EslceResult'][0]['grade']:'','size'=>4,'div'=>false,'label'=>false))."</td>";
					
					echo "<td>".$this->Form->input('EslceResult.0.exam_year',
		            array('name'=>"data[EslceResult][0][exam_year]",
		            'value'=>isset($this->request->data['EslceResult'][0]['exam_year'])?
					$this->request->data['EslceResult'][0]['exam_year']:'','div'=>false,'label'=>false,
					'type'=>'select','options'=>$yearoptions,'empty'=>'--select year--'))."</td></tr>";
				
		            
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
	<div id="add_address" style="display:none">
	<?php		
        echo $this->element('user_tab_menu',
                array('current_tab' => 'add_address'));

        echo "<div class=\"AddTab\">\n";
                echo '<table cellspacing="0" cellpading="0"><tbody>';
                echo "<tr><td><table><tbody>";
                 echo '<tr><td colspan=2><hr/></td></tr>';
                 echo '<tr><td colspan=2><strong>Address & Contact </strong></td></tr>';
                  echo '<tr><td colspan=2><hr/></td></tr>';
		        
	            // save student primary address 
		        echo '<tr><td>'.$this->Form->input('Contact.0.id',array('type'=>'hidden')).'</td></tr>';
		       
				 echo '<tr><td>'.$this->Form->input('country_id',array('id'=>'country_id_2','onchange'=>'updateRegionCity(2)','label'=>'Country','error'=>false,'empty'=>false,'value'=>68,
				 'style'=>'width:250px')).'</td></tr>';
		        echo '<tr><td>'.$this->Form->input('region_id',array('id' => 'region_id_2',
		        'onchange'=>'updateCity(2)','label' => 'Region/City','style'=>'width:250px','error' => false,'empty' => 'Select Country First')).'</td></tr>';
		        echo '<tr><td>'. $this->Form->input('city_id',array('label'=>'City','id'=>'city_id_2','style'=>'width:250px')).'</td></tr>';
		        
				echo '<tr><td>'. $this->Form->input('zone_subcity',array('label'=>'Zone/Subcity')).'</td></tr>';
		        echo '<tr><td>'.$this->Form->input('woreda').'</td></tr>';
		       	      
		        echo '<tr><td>'.$this->Form->input('kebele').'</td></tr>';
		        echo '<tr><td>'.$this->Form->input('house_number',array('label'=>'House Number')).'</td></tr>';
		        
		        echo '<tr><td>'.$this->Form->input('address1',array('label'=>'Address1')).'</td></tr>';
		        
		        echo "</tbody></table></td>";
		
		          // save account information in the user table
		        echo '<td><table><tbody>';
		        echo '<tr><td colspan=2><hr/></td></tr>';
		        echo '<tr><td colspan=2><strong>Primary Emergency Contact</strong></td></tr>';
                echo '<tr><td colspan=2><hr/></td></tr>';
		        echo '<tr><td>'.$this->Form->input('Contact.0.first_name',array('label'=>'First Name')).'</td></td>';
		        echo '<tr><td>'.$this->Form->input('Contact.0.middle_name',array('label'=>'Middle Name')).'</td></tr>';
		         echo '<tr><td>'.$this->Form->input('Contact.0.last_name',
            array('label'=>'Last Name')).'</td></tr>';
          
             echo '<tr><td>'.$this->Form->input('Contact.0.country_id',
            array('label'=>'Country','style'=>'width:250px','id'=>'country_id_1','value'=>68,'onchange'=>'updateRegionCity(1)','style'=>'width:250px')).'</td></tr>';
              echo '<tr><td>'.$this->Form->input('Contact.0.region_id',
            array('label'=>'Region','id'=>'region_id_1','onchange'=>'updateCity(1)',
            'style'=>'width:250px')).'</td></tr>';
              echo '<tr><td>'.$this->Form->input('Contact.0.city_id',
            array('label'=>'City','style'=>'width:250px','id'=>'city_id_1',
            'style'=>'width:250px')).'</td></tr>';
            
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
             
		echo '</div>'; // End add tab div 
		echo $this->Js->get('#StudentCountryId')->event('change', 
	$this->Js->request(array(
		'controller'=>'students',
		'action'=>'get_regions'
		), array(
		'update'=>'#StudentRegionId',
		'async' => true,
		'method' => 'post',
		'dataExpression'=>true,
		'data'=> $this->Js->serializeForm(array(
			'isForm' => false,
			'inline' => true
			))
		))
	);
	
	echo $this->Js->get('#StudentRegionId')->event('change', 
	$this->Js->request(array(
		'controller'=>'students',
		'action'=>'get_cities'
		), array(
		'update'=>'#StudentCityId',
		'async' => true,
		'method' => 'post',
		'dataExpression'=>true,
		'data'=> $this->Js->serializeForm(array(
			'isForm' => false,
			'inline' => true
			))
		))
	);
		?>
	 </div> <!-- End address block -->
	</fieldset>
<?php echo $this->Form->Submit('Save',array('div'=>false,
'name'=>'admit'));?>
<?php } ?>
</div>
<?php echo $this->Form->end();?>


<?= $this->Html->script('amharictyping'); ?>  
<?php
if (isset($this->data['Curriculum']['registrar_approved']) && !empty($this->data['Curriculum']['registrar_approved'])) {
	$approvedState = $this->data['Curriculum']['registrar_approved'];
	$readOnly = true;
} else {
	$approvedState = 0;
	$readOnly = false;
} ?>
<div class="box">
	<div class="box-header bg-transparent">
        <div class="box-title" style="margin-top: 10px;"><i class="fontello-edit" style="font-size: larger; font-weight: bold;"></i>
            <span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= 'Edit Curriculum: ' . (isset($this->data['Curriculum']['name']) ? $this->data['Curriculum']['name'] . ' - ' . $this->data['Curriculum']['year_introduced'] : ''); ?></span>
        </div>
    </div>
     <div class="box-body">
       <div class="row">
		<div class="large-12 columns">
			<?= $this->Form->create('Curriculum', array('type'=>'file','enctype' => 'multipart/form-data'));?>
		    <?php 
			 if ($approvedState == 1){ 
			  $readOnly ='readOnly'; ?>
			  The curriculum is approved by the registrar and locked.
				<?php 
			} ?></p>
		</div>
		<div class="large-12 columns">
			 <strong style="color:red">Important Note:</strong> <ol><li>English and amharic degree nomenclature is used 
		    	in temporary and permanent diploma. Please make sure that it is correct and complete.</li>
		    <li>Course category which are marked as mandatory will be used to check if a student is ligible for graduation. Please 
		    make sure that it is correct and complete.</li>
		    <li> Minimum credit point/hour be used to check if a student is eligible for graduation. Please 
		    make sure that it is correct and complete  </li>
		    </ol>
	    </div>
	      <div class="large-5 columns">
		<?php
	    echo "<table style='border:0px;'><tbody><tr><td>";
	    echo "<table><tbody>";
	    echo '<tr><td class="font">'.$college_name.'</td></tr>';
	    echo '<tr><td class="font">'.$department_name.'</td></tr>';
	    echo $this->Form->hidden('id',array('value'=>$this->data['Curriculum']['id']));
		echo '<tr><td>'.$this->Form->input('name').'</td></tr>';
		echo '<tr><td>'.$this->Form->input('program_id',array('empty'=>'--select program--','readOnly'=>$readOnly)).'</td></tr>';
		
		echo '<tr><td>'.$this->Form->input('year_introduced').'</td></tr>';
		echo '<tr><td>'.$this->Form->input('type_credit',array('empty'=>'--select credit type--','id'=>'CreditType','readOnly'=>$readOnly,'onchange'=>'updateCreditLable("CreditType")','options'=>array('ECTS Credit Point'=>'ECTS Credit Point','Credit'=>'Credit'))).'</td></tr>';
		echo '<tr><td>'.$this->Form->input('amharic_degree_nomenclature',array('id'=>'AmharicText','readOnly'=>$readOnly,'onkeypress'=>"return AmharicPhoneticKeyPress(event,this);",
		'after'=>'ምሳሌ የሳይንስ ማስተርስ ዲግሪ ፤ የሳይንስ ባለር ዲግሪ  '));
		echo '<tr><td>'.$this->Form->input('specialization_amharic_degree_nomenclature',array('id'=>'AmharicText','readOnly'=>$readOnly,'onkeypress'=>"return AmharicPhoneticKeyPress(event,this);"));
		echo '<tr><td>'.$this->Form->input('english_degree_nomenclature',
		array('after'=>'Master of Science, Bachelor of Science')).'</td></tr>';
		
		echo '<tr><td>'.$this->Form->input('specialization_english_degree_nomenclature').'</td></tr>';
		echo '<tr><td>'.$this->Form->input('certificate_name',
		array('readOnly'=>$readOnly,'after'=>'  E.g M.Sc. Program, B.Sc. Program ')).'</td></tr>';
		
		echo '<tr><td>'.$this->Form->input('minimum_credit_points', array('id'=>'minimum_credit_points')).'</td></tr>';
		echo '<tr><td>'.$this->Form->input('department_study_program_id',array('label' => 'Study Program: ', 'options' => $getDepartmentStudyProgramList, 'empty'=>'--Select Study Program--', 'required', 'style' => 'width: 70%')).'</td></tr>';
		
		echo $this->Form->hidden('department_id',array('value'=>$department_id,'readOnly'=>$readOnly));
		echo '<tr><td>';
		echo '</td></tr>';
		echo "</tbody></table>";
		echo "</td></tr></tbody></table>";
		
		?>
	      </div>

	      <div class="large-7 columns">
		<table style='border:0px;'>
		<?php 
		$fields=array('name'=>1,'code'=>2,'total_credit'=>3,'mandatory_credit'=>4);
		$all_fields = "";
	    $sep = "";
	    foreach ($fields as $key => $tag) {
				$all_fields.= $sep.$key;
				$sep = ",";
	    }
	     $course_category_values=array('mandatory'=>'Manadatory',' optional'=>'Optional','general'=>'General','elective'=>'Elective');
	    $course_category_list = "";
			$ccl ="";
			foreach($course_category_values as $pk =>$pv) {
				$course_category_list.=$ccl.$pv;
				$ccl = ",";
			}
		?>
		<tr><th colspan=5>Course Category and Its Total Credit Point/Hours</th></tr>
	  
		</table>
		<TABLE id="course_categories" >
		 <TR><td>S.N<u>o</u></td><td>Name</td> <td>Code</td>
		<td>Total <div class="credit">Credit</div></td><td>Mandatory <div class="credit">Credit</div></td></TR>
		<?php 
		    if (isset($this->data['CourseCategory']) && count($this->data['CourseCategory'])>0 ) {
		       $count=1;
		         foreach ($this->data['CourseCategory'] as $ck=>$cv) {
		            if (!empty($cv['id'])) {
		                      echo $this->Form->hidden('CourseCategory.'.$ck.'.id');
		                      echo $this->Form->hidden('CourseCategory.'.$ck.'.curriculum_id');
		                      $action_controller_id='edit~curriculums~'.$cv['curriculum_id'];
		                      
		            }
		           
		            echo "<tr><td>".$count."</td><td>".$this->Form->input('CourseCategory.'.$ck.'.name',array('readOnly'=>$readOnly,'label'=>false,'size'=>'7px')).'</td>';
		            
		            ?> 
		        
		            <?php 

 echo '<td>';
				     
		echo $this->Form->input('CourseCategory.'.$ck.'.code',array('label'=>false,
				     'size'=>13,'readOnly'=>$readOnly));
				     echo "</td>";
		          
		            echo '<td>'.$this->Form->input('CourseCategory.'.$ck.'.total_credit',
		            array('label'=>false,
'id'=>'t'.$count,'onBlur'=>'updateSum("course_categories");','size'=>'7px','readOnly'=>$readOnly)).'</td>';
		            echo '<td>'.$this->Form->input('CourseCategory.'.$ck.'.mandatory_credit',
		            array('label'=>false,
'id'=>'m'.$count,'onBlur'=>'updateSum("course_categories");','size'=>'7px','readOnly'=>$readOnly)).'</td>';
		            if(!empty($action_controller_id)) {
		                
		                echo "<td>".$this->Html->link(__('Delete', true), array('action' => 'deleteCourseCategory', $cv['id'],$action_controller_id), null, sprintf(__('Are you sure you want to delete # %s?', true), $cv['name'])).'</td>';  
		                
		            }
		            
		            echo "</tr>";
		          $count++;
		        }
		    } else {
		    ?>
			      <TR>
			        <TD> 1 </TD>
			    
				
				<?php 
				     echo "<td>";
				     echo $this->Form->input('CourseCategory.0.name',array('label'=>false,'size'=>13,'readOnly'=>$readOnly));
				     echo "</td>";
  echo "<td>";
				     echo $this->Form->input('CourseCategory.0.code',array('label'=>false,'size'=>13,'readOnly'=>$readOnly));
				     echo "</td>";

					 echo "<td>".$this->Form->input('CourseCategory.0.total_credit',
					 array('size'=>5,'label'=>false,'id'=>'t1','onBlur'=>'updateSum("course_categories");','div'=>false,'readOnly'=>$readOnly)).'</td>';
					
					echo '<td>'.$this->Form->input('CourseCategory.0.mandatory_credit',
					array('label'=>false,
'id'=>'m1','onBlur'=>'updateSum("course_categories");','size'=>5,'readOnly'=>$readOnly)).'</td>';
		            ?>
			        <td>&nbsp;&nbsp;</td>
		         </TR>
		    <?php 
		    }
		?>
		
		</TABLE>
		
	        <table style='border:0px;'>
	            <tr>
			     <td width="38px">
				&nbsp;
			     </td>
                              <td width="116px">
				&nbsp;
			     </td>
 <td width="116px">
				&nbsp;
			     </td>

			     <td id="t_sum"  width="52px" style="align:right;">
		
			     </td>
		             <td id="m_sum" width="52px" style="align:right;">
		
			     </td>
                              <td  width="52px">
					&nbsp;
			     </td>
                           
		   </tr>
		</table>
		<?php if($approvedState==1){ ?>
		  Curriculum approved and locked.
		<?php } else {?>
		<table style="border:0px;"><tr><td colspan=4>
		
               
		<INPUT type="button" value="Add Row" onclick="addRow('course_categories','CourseCategory',4,
		'<?= $all_fields; ?>')" /> 
		<INPUT type="button" value="Delete Row" onclick="deleteRow('course_categories')" />

	</td></tr></table>
	      <?php } ?>
	      
	      </div>
		
		<div class="large-12 columns">
<?php 
if($approvedState==1){
  echo 'Curriculum approved and locked.';

} else {
echo $this->Form->end(array('label'=>__('Update', true),'class'=>'tiny radius button bg-blue'));

}
?>
	       </div>
	</div>
    </div>
</div>


<script type="text/javascript">
	function updateSum(tableID) {
		var total_sum=0;
     	var total_mandatory=0;
	       
		for (i = 1; i < document.getElementById(tableID).rows.length; i++) {
			total_sum +=adjustSum("t"+i,0);
			total_mandatory +=adjustSum(0,"m"+i);
		}
               
	    updateReft = window.document.getElementById('t_sum');
        updateReft.innerHTML=total_sum;
	    updateRefm = window.document.getElementById('m_sum');
        updateRefm.innerHTML=total_mandatory;
        updateRefminInput = window.document.getElementById('minimum_credit_points');
        updateRefminInput.value=total_mandatory;
    }
	

    function updateCreditLable(id) {
        $(".credit").empty();
	  	$(".credit").append(document.getElementById(id).value);
	}
     
    function adjustSum(x,y) {
	    if (y==0) {
            ref = window.document.getElementById(x);
		    if(!isNaN(ref.value) & ref.value >= 0) {
		       return Number(ref.value);
		    } else {
				return 0;
		    }
	    } 
           
        if (x==0) {
	        ref = window.document.getElementById(y);
		    if(!isNaN(ref.value) & ref.value >= 0) {
		       return Number(ref.value);
		    } else {
		    	return 0;
		    } 
	    }
	 
    }
                
	function addRow(tableID,model,no_of_fields,all_fields) {
	       
		var elementArray = all_fields.split(',');
		var table = document.getElementById(tableID);

		var rowCount = table.rows.length;
		var row = table.insertRow(rowCount);

		var cell0 = row.insertCell(0);
		cell0.innerHTML = rowCount ;
		cell0.classList.add("center");
			
		for (var i = 1; i<= no_of_fields; i++) {
			var cell = row.insertCell(i);
			
			
			if (elementArray[i-1] == "name") {
				var element = document.createElement("input");
				element.type = "text";
				element.style = "width:100%;";
				element.id = "CourseCategoryName_" + rowCount;	              
			}
			/* else if (elementArray[i-1] == "code") {
				var element = document.createElement("input");
				element.size = "13";
				element.type = "text";		              
			} */ 
			
			else if (elementArray[i-1] == "total_credit") {
				var element = document.createElement("input");
				element.type = "number";
				element.style = "width:100%";
				element.id = 't'+rowCount;

				element.onchange = function() {
					checkCreditRange(this);
				};
				
				element.onblur = function () {
					return updateSum('course_categories');
				};
				
			} else if (elementArray[i-1] == "mandatory_credit") {

				var element = document.createElement("input");
				element.type = "number";
				element.style = "width:100%";
				element.id = 'm'+rowCount;

				element.onchange = function() {
					checkCreditRange(this);
				};
				
				element.onblur = function () {
					return updateSum('course_categories');
				};
				
			}
			
			element.name = "data["+model+"]["+rowCount+"]["+elementArray[i-1]+"]";
			
			cell.appendChild(element);
		}

		// added now
		updateSum('course_categories');
		updateSequence('course_categories');

	}

	function deleteRow(tableID) {
		try {
			var table = document.getElementById(tableID);
			var rowCount = table.rows.length;
			if (rowCount > 1) {
				table.deleteRow(rowCount - 1);
				updateSequence(tableID);
				updateSum(tableID);
			} else {
				alert('No more rows to delete');
			}
		} catch (e) {
			alert(e);
		}

		updateSum('course_categories');
	}

	function updateSequence(tableID) {
		var s_count = 1;
		for (i = 1; i < document.getElementById(tableID).rows.length; i++) {
			document.getElementById(tableID).rows[i].cells[0].childNodes[0].data = s_count++;
		}
	}

	function checkCreditRange(selectObject) {
		var inputCredit = parseInt(selectObject.value);
		if (typeof inputCredit != 'undefined') {
			if (inputCredit < 1) {
				alert('Credit can not less than 0');
				selectObject.value = 0;
			}
			if (inputCredit > 300) {
				alert('Credit can not be more than 300');
				selectObject.value = 300;
			}
		}
	}
		
	function addDropdownRow(tableID, controller, no_of_fields, all_fields,course_list_category) {
		var elementArray = all_fields.split(',');
		var cclist = course_list_category.split(',');
		
		var table = document.getElementById(tableID);
		
		var rowCount = table.rows.length;
		var row = table.insertRow(rowCount);
		
		var pcount = cclist.length;

           for(var i=1;i<=no_of_fields;i++) {
              
			var cell0 = row.insertCell(0);
			cell0.innerHTML = rowCount+1 ;
			var cell = row.insertCell(1);
			var element = document.createElement("select");
							
			var string='<option value="None">--Select Course Category--- </option>'
			for(var i=0;i<pcount;i++) {
				
				string += '<option value="'+i+'"> '+cclist[i]+'</option>';
			}
		
			element.innerHTML = string;
			element.name = "data["+controller+"]["+rowCount+"]["+elementArray[0]+"]";
			cell.appendChild(element);
			
			var cell1 = row.insertCell(i);
			var element = document.createElement("input");
			element.type = "text";
			
			if (elementArray[i-1]== "total_credit") {
				element.size = "13";
			}

			element.name = "data["+controller+"]["+rowCount+"]["+elementArray[i-1]+"]";
			cell.appendChild(element);
		}
	}
   	updateSum('course_categories');
  	updateCreditLable('CreditType');
</script>

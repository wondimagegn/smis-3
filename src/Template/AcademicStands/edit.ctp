<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
	     <h6 class="box-title">
		<?php echo __('Add Academic Rule'); ?>
	     </h6>
	  </div>
	  <div class="large-12 columns">
             

<div class="academicStands form">

<?php echo $this->Form->create('AcademicStand',
array('novalidate' => true));?>
    <div style="padding:20px"></div>
	<!-- <fieldset> -->
		<legend class="smallheading"><?php echo __('Add Academic Stand'); ?></legend>
		<table border=2>
		<?php 
		    $fields=array('scmo'=>1,'sgpa'=>2,'operatorI'=>3,'ccmo'=>4,'cgpa'=>5,
		    'operatorII'=>6,'tcw'=>7,'pfw'=>9);
		   
		    
		    $all_fields = "";
	        $sep = "";
	        foreach ($fields as $key => $tag) {
				    $all_fields.= $sep.$key;
				    $sep = ",";
	        }
	        /********************Arithmetic Operator**********************/
	        $arithmetic_operator=array('<'=>'<','<='=>'<=','>'=>'>','>='=>'>=');
	       
	        $arithmetic_operator_list = "";
			$aol ="";
			foreach($arithmetic_operator as $pk =>$pv) {
				    $arithmetic_operator_list.=$aol.$pv;
				    $aol = ",";
			}
		   
		    /********************Logical Operator**********************/
	        $logical_operator=array('and'=>'AND','or'=>'OR');
	        $logical_operator_list = "";
			$lol ="";
			foreach($logical_operator as $pk =>$pv) {
				    $logical_operator_list.=$lol.$pv;
				    $lol = ",";
			}
		
		?>
		<tr><th colspan=8>Academic Rule For Selected Academic Status</th></tr>
		<?php echo $this->Form->input('AcademicStand.id'); ?>
		<tr><td colspan=8> 	<?php 
		    
		     if (isset($this->request->data['AcademicStand']['academic_year_from'])) {
		         $post_fix_academic_year_formated=substr($this->request->data['AcademicStand']['academic_year_from'], -2, 2)+1; 
		         $academic_year_formated=$this->request->data['AcademicStand']['academic_year_from'].'/'.$post_fix_academic_year_formated;
		     } else {
		         $academic_year_formated=$defaultacademicyear;
		     }
		    
		    echo $this->Form->input('academic_year_from',array('id'=>'academicyear',
            'label' => 'Academic year','type'=>'select','options'=>$acyear_array_data,
            'empty'=>"--Select Academic Year--",'selected'=>isset($academic_year_formated)?$academic_year_formated:'')); 
            
            ?>
            
            </td></tr>
		<tr><td colspan=8 style="padding-left:200px;"> <?php echo $this->Form->input('applicable_for_all_current_student',array('label'=>'The rule applied for all current  not graduated students.'));?></td></tr>
		
		<tr><td colspan=8><?php echo $this->Form->input('academic_status_id'); ?></td></tr>
		<tr><td colspan=8><?php echo $this->Form->input('program_id'); ?></td></tr>
		
		<tr><td colspan=4><?php echo $this->Form->input('year_level_id',array('type'=>'select','multiple'=>'checkbox')); ?></td> <td colspan=4><?php echo $this->Form->input('semester',array('options'=>array('I'=>'I','II'=>'II',
            'III'=>'III'),'type'=>'select','multiple'=>'checkbox')); ?></td></tr>
	  
		</table>
		<table id="acadmic_stands">
	        <TR><td>No.</td><td></td><td>SGPA</td><td>Operator</td><td></td><td>CGPA</td><td>Operator</td>
	        <td>Two Consecutive Warnings</td><td>Probation followed by Warning</td></TR>
		
		<?php 
		
		  if (isset($this->request->data['AcademicRule']) && !empty($this->request->data['AcademicRule'])) {
		         $count=1;
		         foreach ($this->request->data['AcademicRule'] as $ar=>$av) {
		              if (isset($av['id']) && !empty($av['id'])) {
		                       
		                        $action_academicstand_id='edit~academicStands~'.$av['academic_stand_id'];
		                              
		                } 
		                echo "<tr><td>".$count++."</td>";
		                echo $this->Form->hidden('AcademicRule.'.$ar.'.id',array('value'=>$av['id']));
		                echo "<td>".$this->Form->input('AcademicRule.'.$ar.'.scmo',
		            array(
		            'value'=>isset($this->request->data['AcademicRule'][$ar]['scmo'])?
					$this->request->data['AcademicRule'][$ar]['scmo']:'',
					'options'=>$arithmetic_operator,'type'=>'select','div'=>false,'label'=>false))."</td>";
					  echo "<td>".$this->Form->input('AcademicRule.'.$ar.'.sgpa',
					  array('label'=>false,'size'=>4,
					  'value'=>isset($this->request->data['AcademicRule'][$ar]['sgpa']) &&
					  $this->request->data['AcademicRule'][$ar]['sgpa']==0 ?
					" " :$this->request->data['AcademicRule'][$ar]['sgpa']
					  ,'div'=>false))."</td>";
					echo "<td>".$this->Form->input('AcademicRule.'.$ar.'.operatorI',
		            array('name'=>"data[AcademicRule][$ar][operatorI]",
		            'default'=>isset($this->request->data['AcademicRule'][$ar]['operatorI'])?
					$this->request->data['AcademicRule'][$ar]['operatorI']:'',
					'options'=>$logical_operator,
					'type'=>'select','div'=>false,'label'=>false,'empty'=>'--select--'))."</td>";
					
					  echo "<td>".$this->Form->input('AcademicRule.'.$ar.'.ccmo',
		            array(
		            'value'=>isset($this->request->data['AcademicRule'][$ar]['ccmo'])?
					$this->request->data['AcademicRule'][$ar]['ccmo']:'','options'=>$arithmetic_operator,'type'=>'select','div'=>false,'label'=>false))."</td>";
					
					 echo "<td>".$this->Form->input('AcademicRule.'.$ar.'.cgpa',
					 array('label'=>false,'size'=>4,
					 
					  'value'=>isset($this->request->data['AcademicRule'][$ar]['cgpa']) &&
					  $this->request->data['AcademicRule'][$ar]['cgpa']==0 ?
					" " :$this->request->data['AcademicRule'][$ar]['cgpa']
					 
					 ,'div'=>false))."</td>";
					echo "<td>".$this->Form->input('AcademicRule.'.$ar.'.operatorII',
		            array('name'=>"data[AcademicRule][$ar][operatorII]",
		            'value'=>isset($this->request->data['AcademicRule'][$ar]['operatorII'])?
					$this->request->data['AcademicRule'][$ar]['operatorII']:'','options'=>$logical_operator,'type'=>'select','div'=>false,'label'=>false,'empty'=>'--select--'))."</td>";
					
					echo "<td>".$this->Form->input('AcademicRule.'.$ar.'.tcw',array('name'=>"data[AcademicRule][$ar][tcw]",
		            'value'=>isset($this->request->data['AcademicRule'][$ar]['tcw'])?
					$this->request->data['AcademicRule'][$ar]['tcw']:'','label'=>false,'div'=>false,
					'onChange'=>'disabledOtherRule('.$ar.',"AcademicRule'.$ar.'Tcw","Pfw")'))."</td>";
					
								echo "<td>".$this->Form->input('AcademicRule.'.$ar.'.pfw',array('name'=>"data[AcademicRule][$ar][pfw]",
		            'value'=>isset($this->request->data['AcademicRule'][$ar]['pfw'])?
					$this->request->data['AcademicRule'][$ar]['pfw']:'','label'=>false,'div'=>false,
					'onChange'=>'disabledOtherRule('.$ar.',"AcademicRule'.$ar.'Pfw","Tcw")'));
					if(!empty($action_academicstand_id)) {
		                
		                echo "<td>".$this->Html->link(__('Delete'), 
		                array('controller'=>'academicRules','action' => 'delete', $av['id'],$action_academicstand_id), null, 
		                sprintf(__('Are you sure you want to delete # %s?'), $av['id']))."</td>";  
		                
		              }
					echo "</td>";
					
					echo "</tr>";
					
		         }
		  
		  } else {
		  ?>
		     <TR>
			     <TD> 1 </TD>
			     <TD> 
			
				     <?php 
					 echo $this->Form->input('AcademicRule.0.scmo',
		            array('name'=>"data[AcademicRule][0][scmo]",
		            'value'=>isset($this->request->data['AcademicRule'][0]['scmo'])?
					$this->request->data['AcademicRule'][0]['scmo']:'','options'=>$arithmetic_operator,'type'=>'select','div'=>false,'label'=>false));
					
				
		            ?>
		            
		            
				  </TD><TD>
                     <?php //echo $this->Form->input('sgpa',array('size'=>4,'label'=>'','div'=>false));
                     
                     echo $this->Form->input('AcademicRule.0.sgpa',array('name'=>"data[AcademicRule][0][sgpa]",
		            'value'=>isset($this->request->data['AcademicRule'][0]['sgpa'])?
					$this->request->data['AcademicRule'][0]['sgpa']:'','label'=>false,'size'=>4,'div'=>false)); 
					
					?>
					
				
				 </TD>
				 <TD>
				     <?php 
				      echo $this->Form->input('AcademicRule.0.operatorI',
		            array('name'=>"data[AcademicRule][0][operatorI]",
		            'value'=>isset($this->request->data['AcademicRule'][0]['operatorI'])?
					$this->request->data['AcademicRule'][0]['operatorI']:'','options'=>$logical_operator,'type'=>'select','div'=>false,'label'=>false,'empty'=>'--select--'));
				     ?>
                    
				 </TD>
				 <TD> 
				     <?php 
				         echo $this->Form->input('AcademicRule.0.ccmo',
		            array('name'=>"data[AcademicRule][0][ccmo]",
		            'value'=>isset($this->request->data['AcademicRule'][0]['ccmo'])?
					$this->request->data['AcademicRule'][0]['ccmo']:'','options'=>$arithmetic_operator,'type'=>'select','div'=>false,'label'=>false));
				     ?>
				  </TD>
				  <TD>
                     <?php // echo $this->Form->input('cgpa',array('label'=>'','size'=>4,'div'=>false));
					   echo $this->Form->input('AcademicRule.0.cgpa',array('name'=>"data[AcademicRule][0][cgpa]",
		            'value'=>isset($this->request->data['AcademicRule'][0]['cgpa'])?
					$this->request->data['AcademicRule'][0]['cgpa']:'','label'=>false,'size'=>4,'div'=>false));
					?>
				 </TD>
				 <TD> 
				    <?php 
				         echo $this->Form->input('AcademicRule.0.operatorII',
		            array('name'=>"data[AcademicRule][0][operatorII]",
		            'value'=>isset($this->request->data['AcademicRule'][0]['operatorII'])?
					$this->request->data['AcademicRule'][0]['operatorII']:'','options'=>$logical_operator,'type'=>'select','div'=>false,'label'=>false,'empty'=>'--select--'));
				     ?>
				 </TD>
				 <TD> 
				 <?php //echo $this->Form->input('tcw',array('label'=>'','div'=>false)); ?>
				 <?php echo $this->Form->input('AcademicRule.0.tcw',array('name'=>"data[AcademicRule][0][tcw]",
		            'value'=>isset($this->request->data['AcademicRule'][0]['tcw'])?
					$this->request->data['AcademicRule'][0]['tcw']:'','label'=>false,'div'=>false,
					'onChange'=>'disabledOtherRule(0,"AcademicRule0Tcw","Pfw")')); ?>
				 </TD>
				 
				<td><?php echo $this->Form->input('AcademicRule.0.pfw',array('name'=>"data[AcademicRule][0][pfw]",
		            'value'=>isset($this->request->data['AcademicRule'][0]['pfw'])?
					$this->request->data['AcademicRule'][0]['pfw']:'','label'=>false,'div'=>false,
					'onChange'=>'disabledOtherRule(0,"AcademicRule0Pfw","Tcw")')); ?>
					
					</td>
			
		        </TR>
		  <?php 
		  
		  }
		?>
	</table>
	<table><tr><td colspan=3>
		
		<INPUT type="button" value="OR" onclick="addRow('acadmic_stands','AcademicRule',9,'<?php echo $all_fields; ?>','<?php echo $logical_operator_list; ?>','<?php echo $arithmetic_operator_list; ?>')" /> 
		<!-- 
		<INPUT type="button" value="Delete Row" onclick="deleteRow('acadmic_stands')" /> -->
	</td></tr></table> 
	<!-- </fieldset> -->
<?php echo $this->Form->end(array('label'=>__('Submit'),
'class'=>'tiny radius button bg-blue'));?>
</div>

	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->

<SCRIPT language="javascript">
	
	 function addRow(tableID,model,no_of_fields,all_fields,logical_operator_list,arithmetic_operator_list
        ) {
		  
	    var elementArray = all_fields.split(',');
            var aol = arithmetic_operator_list.split(',');
            var lol = logical_operator_list.split(',');
			var table = document.getElementById(tableID);
			var rowCount = table.rows.length;
			
			var row = table.insertRow(rowCount);
            //the first cell constructed.
			var cell0 = row.insertCell(0);
			//cell0.innerHTML = rowCount+1 ;
			cell0.innerHTML = rowCount;
			//prepare for the drop down box
			var acount = aol.length;
			var lcount = lol.length;
			//construct the other cells
			for(var j=1;j<no_of_fields;j++) {
				var cell = row.insertCell(j);
				
				
				if (elementArray[j-1] == "scmo") {
				       var element = document.createElement("select");
								
			            var string='';
			            for(var i=0;i<acount;i++) {
			             
				            string += '<option value="'+aol[i]+'"> '+aol[i]+'</option>';
			            }
			
			            element.innerHTML = string;
			            element.id = "AcademicRule"+rowCount+"Scmo";     
				}
				
				
				if (elementArray[j-1] == "sgpa") {
				   var element = document.createElement("input");
				   element.size = "4";
				   element.type = "text";
				   element.id = "AcademicRule"+rowCount+"Sgpa";
				}
				
				
					
				if (elementArray[j-1] == "operatorI") {
				       var element = document.createElement("select");
								
			            var string='<option value="">--Select--- </option>';
			            for(var i=0;i<lcount;i++) {
			             
				            string += '<option value="'+lol[i]+'"> '+lol[i]+'</option>';
			            }
			
			            element.innerHTML = string;
			            element.id = "AcademicRule"+rowCount+"OperatorI";
			            
				}
				if (elementArray[j-1] == "ccmo") {
				       var element = document.createElement("select");
								
			            var string='';
			            for(var i=0;i<acount;i++) {
			             
				            string += '<option value="'+aol[i]+'"> '+aol[i]+'</option>';
			            }
			
			           element.innerHTML = string;
			           element.id = "AcademicRule"+rowCount+"Ccmo";
			            
				}
				if (elementArray[j-1] == "cgpa") {
				   var element = document.createElement("input");
				   element.size = "4";
				   element.type = "text";
				   element.id = "AcademicRule"+rowCount+"Cgpa";
				 
				}
							
				
				if (elementArray[j-1] == "operatorII") {
				        var element = document.createElement("select");
								
			            var string='<option value="">--Select--- </option>';
			            for(var i=0;i<lcount;i++) {
			             
				            string += '<option value="'+lol[i]+'"> '+lol[i]+'</option>';
			            }
			
			            element.innerHTML = string;
			            element.id = "AcademicRule"+rowCount+"OperatorII";
			            
				}
					
				
				
				if (elementArray[j-1] == "tcw") {
				   var element = document.createElement("input");
				  // element.size = "4";
				   element.value=1;
				   element.type = "checkbox";
				    element.id = "AcademicRule"+rowCount+"Tcw";
				   element.name = "data["+model+"]["+rowCount+"]["+elementArray[j-1]+"]";
				
				   element.addEventListener('change', 
            	             function(){disabledOtherRule(rowCount,this.id,'Pfw')}, false);
            	   
				   cell.appendChild(element);
				   continue;
                 
				 
				}
			
				if (elementArray[j-1] == "pfw") {
				   var element = document.createElement("input");
				  // element.size = "4";
				  	element.value=1;
				    element.type = "checkbox";
				    element.id = "AcademicRule"+rowCount+"Pfw";
				    element.name = "data["+model+"]["+rowCount+"]["+elementArray[j-1]+"]";
				
				    element.addEventListener('change', 
            	             function(){disabledOtherRule(rowCount,this.id,'Tcw')}, false);
            	   
				    cell.appendChild(element);
				   continue;
				 
				}
				
				element.name = "data["+model+"]["+rowCount+"]["+elementArray[j-1]+"]";
				
				cell.appendChild(element);
				
			}
			
		
			
		
            
		}

		function deleteRow(tableID) {
		   
			try {
			    var table = document.getElementById(tableID);
			    var rowCount = table.rows.length;
			    if(rowCount !=2 ){
                    table.deleteRow(rowCount-1);
			    } else {
			
			        //alert('No more rows to delete');
			    }
			
			}catch(e) {
				alert(e);
			}
			
		}
		
		function disabledOtherRule (rowCount,id,type_pfw_tfw) {
           //AcademicRule0OperatorII
            //var type = String(type_pfw_tfw);
            var type = String(type_pfw_tfw);
	        if(window.document.getElementById(id).checked) {
		        window.document.getElementById("AcademicRule"+rowCount+"Sgpa").disabled=true;
		        window.document.getElementById("AcademicRule"+rowCount+"Scmo").disabled=true;
		        window.document.getElementById("AcademicRule"+rowCount+"OperatorI").disabled=true;
		        window.document.getElementById("AcademicRule"+rowCount+"Ccmo").disabled=true;
		        window.document.getElementById("AcademicRule"+rowCount+"Cgpa").disabled=true;
		        if (type == 'Pfw') {
		            window.document.getElementById("AcademicRule"+rowCount+'Pfw').disabled=true;
		        } else if (type == 'Tcw') {
		           window.document.getElementById("AcademicRule"+rowCount+'Tcw').disabled=true;
		        }
		        window.document.getElementById("AcademicRule"+rowCount+"OperatorII").disabled=true;
	        } else {
	        
	              if (window.document.getElementById("AcademicRule"+rowCount+"Sgpa").disabled) {
	                  window.document.getElementById("AcademicRule"+rowCount+"Sgpa").disabled=false;
	              }
	              
	              if (window.document.getElementById("AcademicRule"+rowCount+"Cgpa").disabled) {
	                  window.document.getElementById("AcademicRule"+rowCount+"Cgpa").disabled=false;
	              }
	              
	              if (window.document.getElementById("AcademicRule"+rowCount+"Scmo").disabled) {
	                window.document.getElementById("AcademicRule"+rowCount+"Scmo").disabled=false;
	              }
	              
	              if (window.document.getElementById("AcademicRule"+rowCount+"OperatorI").disabled) {
	                window.document.getElementById("AcademicRule"+rowCount+"OperatorI").disabled=false;
	              }
	              
	              if (window.document.getElementById("AcademicRule"+rowCount+"Ccmo").disabled) {
	                window.document.getElementById("AcademicRule"+rowCount+"Ccmo").disabled=false;
	              }
	              //AcademicRule0OperatorII
	                if (type == 'Pfw') {
		                 if (window.document.getElementById("AcademicRule"+rowCount+'Pfw').disabled) {
	                        window.document.getElementById("AcademicRule"+rowCount+'Pfw').disabled=false;
	                     }
		               // window.document.getElementById("AcademicRule"+rowCount+'Pfw').disabled=true;
		            } else {
		               //window.document.getElementById("AcademicRule"+rowCount+'Tfw').disabled=true;
		                if (window.document.getElementById("AcademicRule"+rowCount+'Tcw').disabled) {
	                        window.document.getElementById("AcademicRule"+rowCount+'Tcw').disabled=false;
	                     }
		            
		            }
	              
	              if (window.document.getElementById("AcademicRule"+rowCount+"OperatorII").disabled) {
	                window.document.getElementById("AcademicRule"+rowCount+"OperatorII").disabled=false;
	              }
	              
	        
	        }
       }
		
</SCRIPT>

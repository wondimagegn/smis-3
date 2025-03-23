<?php
?>
<script type="text/javascript">

var prerequisite_courses=Array();
var index = 0;

<?PHP
    if(!empty($prerequisite_courses)){
        foreach($prerequisite_courses as $course_id=>$course_name){
        ?>
        index = prerequisite_courses.length;
        prerequisite_courses[index] = new Array();
        prerequisite_courses[index][0] = "<?php echo $course_id; ?>";
        prerequisite_courses[index][1] = "<?php echo $course_name; ?>";
      
        <?php
            }
     }
?>

       
 function addRowNew(tableID,model,no_of_fields,left_fields,right_fields) {
            var elementArrayLeft = left_fields.split(',');
			var elementArrayRight = right_fields.split(',');
           
			var table = document.getElementById(tableID);

			var rowCount = table.rows.length;
			var row = table.insertRow(rowCount);

			var cell0 = row.insertCell(0);
			cell0.innerHTML = rowCount;
			var left=1;
			var right=2;
			for(var i=1;i<=no_of_fields;i++) {
				var cell = row.insertCell(i);
			
			    //construct left_fields
			    if(left==i) {
			         
			          for(var l=1;l<=elementArrayLeft.length;l++){
			            //////////////////ISBN//////////////////////////////////////
			              if (elementArrayLeft[l-1] == "ISBN") {
			                 var text=document.createTextNode('ISBN');
			                 
			                 var td1=document.createElement('TD');
			                 td1.appendChild(text);
			                 var tr1 = document.createElement('TR');
			                 tr1.appendChild(td1); 
			                 cell.appendChild(tr1);
			                  var td2=document.createElement('TD');
			                  var element = document.createElement("input");
						      element.type = "text";
				                            
            	              element.name = "data["+model+"]["+rowCount+"]["+elementArrayLeft[l-1]+"]";
            	              td2.appendChild(element);
            	              
			                 var tr2 = document.createElement('TR');
			                
			                 tr2.appendChild(td2);
			                
			               
            		         cell.appendChild(tr2);
     
            	          }
            	       
            	          ///////////////////title///////////////////////////////
            	        if (elementArrayLeft[l-1] == "title") {
			                 var text=document.createTextNode('Title');
			                 
			                 var td1=document.createElement('TD');
			                 td1.appendChild(text);
			                 var tr1 = document.createElement('TR');
			                 tr1.appendChild(td1); 
			                 cell.appendChild(tr1);
			                  var td2=document.createElement('TD');
			                  var element = document.createElement("input");
						      element.type = "text";
				            		                
            	              element.name = "data["+model+"]["+rowCount+"]["+elementArrayLeft[l-1]+"]";
            	              td2.appendChild(element);
            	              
			                 var tr2 = document.createElement('TR');
			                
			                 tr2.appendChild(td2);
			                
			               
            		         cell.appendChild(tr2);
            	            
            	       }
            	       
            	       /////////////////////////Edition//////////////////////////////////
            	       if (elementArrayLeft[l-1] == "edition") {
			                 var text=document.createTextNode('Edition');
			                 
			                 var td1=document.createElement('TD');
			                 td1.appendChild(text);
			                 var tr1 = document.createElement('TR');
			                 tr1.appendChild(td1); 
			                 cell.appendChild(tr1);
			                  var td2=document.createElement('TD');
			                  var element = document.createElement("input");
						      element.type = "text";
				            	                
            	              element.name = "data["+model+"]["+rowCount+"]["+elementArrayLeft[l-1]+"]";
            	              td2.appendChild(element);
            	              
			                 var tr2 = document.createElement('TR');
			                
			                 tr2.appendChild(td2);
			                
			               
            		         cell.appendChild(tr2);
            	            
            	          }
            	          
            	         if (elementArrayLeft[l-1] == "author") {
			                 var text=document.createTextNode('Author');
			                 
			                 var td1=document.createElement('TD');
			                 td1.appendChild(text);
			                 var tr1 = document.createElement('TR');
			                 tr1.appendChild(td1); 
			                 cell.appendChild(tr1);
			                  var td2=document.createElement('TD');
			                  var element = document.createElement("input");
						      element.type = "text";
				            		                
            	              element.name = "data["+model+"]["+rowCount+"]["+elementArrayLeft[l-1]+"]";
            	              td2.appendChild(element);
            	              
			                 var tr2 = document.createElement('TR');
			                
			                 tr2.appendChild(td2);
			                
			               
            		         cell.appendChild(tr2);
            	            
            	          }
            	          
            	         if (elementArrayLeft[l-1] == "journal_title") {
			                 var text=document.createTextNode('Journal Title');
			                 
			                 var td1=document.createElement('TD');
			                 td1.appendChild(text);
			                 var tr1 = document.createElement('TR');
			                 tr1.appendChild(td1); 
			                 cell.appendChild(tr1);
			                  var td2=document.createElement('TD');
			                  var element = document.createElement("input");
						      element.type = "text";
				            		                
            	              element.name = "data["+model+"]["+rowCount+"]["+elementArrayLeft[l-1]+"]";
            	              td2.appendChild(element);
            	              
			                 var tr2 = document.createElement('TR');
			                
			                 tr2.appendChild(td2);
			                
			               
            		         cell.appendChild(tr2);
            	            
            	          }
            	           
            	         if (elementArrayLeft[l-1] == "article_title") {
			                 var text=document.createTextNode('Article Title');
			                 
			                 var td1=document.createElement('TD');
			                 td1.appendChild(text);
			                 var tr1 = document.createElement('TR');
			                 tr1.appendChild(td1); 
			                 cell.appendChild(tr1);
			                  var td2=document.createElement('TD');
			                  var element = document.createElement("input");
						      element.type = "text";
				            		                
            	              element.name = "data["+model+"]["+rowCount+"]["+elementArrayLeft[l-1]+"]";
            	              td2.appendChild(element);
            	              
			                 var tr2 = document.createElement('TR');
			                
			                 tr2.appendChild(td2);
			                
			               
            		         cell.appendChild(tr2);
            	            
            	          }
            	          
            	 
                        
            	         if (elementArrayLeft[l-1] == "url_address") {
			                 var text=document.createTextNode('URL Address');
			                 
			                 var td1=document.createElement('TD');
			                 td1.appendChild(text);
			                 var tr1 = document.createElement('TR');
			                 tr1.appendChild(td1); 
			                 cell.appendChild(tr1);
			                  var td2=document.createElement('TD');
			                  var element = document.createElement("input");
						      element.type = "text";
				            		                
            	              element.name = "data["+model+"]["+rowCount+"]["+elementArrayLeft[l-1]+"]";
            	              td2.appendChild(element);
            	              
			                 var tr2 = document.createElement('TR');
			                
			                 tr2.appendChild(td2);
			                
			               
            		         cell.appendChild(tr2);
            	            
            	          }

				         var table = document.createElement('TABLE');
			             table.appendChild(tr1);
			             table.appendChild(tr2); 
			             cell.appendChild(table); 
				       
			             
			          } // end of foreach
			        
			     } // end of left fields
			     
			     if (right==i) {
			        
			          
			        for(var r=1;r<=elementArrayRight.length;r++) {
			            if (elementArrayRight[r-1] == "publisher") {
			                 var text=document.createTextNode('Publisher');
			                 
			                 var td1=document.createElement('TD');
			                 td1.appendChild(text);
			                 var tr1 = document.createElement('TR');
			                 tr1.appendChild(td1); 
			                 cell.appendChild(tr1);
			                  var td2=document.createElement('TD');
			                  var element = document.createElement("input");
						      element.type = "text";
				            		                
            	              element.name = "data["+model+"]["+rowCount+"]["+elementArrayRight[r-1]+"]";
            	              td2.appendChild(element);
            	              
			                 var tr2 = document.createElement('TR');
			                
			                 tr2.appendChild(td2);
			                
			               
            		         cell.appendChild(tr2);
     
            	          }
            	          if (elementArrayRight[r-1] == "place_of_publication") {
			                 var text=document.createTextNode('Place of Publication');
			                 
			                 var td1=document.createElement('TD');
			                 td1.appendChild(text);
			                 var tr1 = document.createElement('TR');
			                 tr1.appendChild(td1); 
			                 cell.appendChild(tr1);
			                  var td2=document.createElement('TD');
			                  var element = document.createElement("input");
						      element.type = "text";
				            		                
            	              element.name = "data["+model+"]["+rowCount+"]["+elementArrayRight[r-1]+"]";
            	              td2.appendChild(element);
            	              
			                 var tr2 = document.createElement('TR');
			                
			                 tr2.appendChild(td2);
			                
			               
            		         cell.appendChild(tr2);
     
            	          }
            	          
            	          //year_of_publication
			             if (elementArrayRight[r-1] == "year_of_publication") {
			                 var text=document.createTextNode('Year of Publication');
			                 
			                 var td1=document.createElement('TD');
			                 td1.appendChild(text);
			                 var tr1 = document.createElement('TR');
			                 tr1.appendChild(td1); 
			                 cell.appendChild(tr1);
			                  var td2=document.createElement('TD');
			                  var element = document.createElement("input");
						      element.type = "text";
				            		                
            	              element.name = "data["+model+"]["+rowCount+"]["+elementArrayRight[r-1]+"]";
            	              td2.appendChild(element);
            	              
			                 var tr2 = document.createElement('TR');
			                
			                 tr2.appendChild(td2);
			                
			               
            		         cell.appendChild(tr2);
     
            	          }
			             
			             
			              //year
			              
			             if (elementArrayRight[r-1] == "year") {
			                 var text=document.createTextNode('Year');
			                 
			                 var td1=document.createElement('TD');
			                 td1.appendChild(text);
			                 var tr1 = document.createElement('TR');
			                 tr1.appendChild(td1); 
			                 cell.appendChild(tr1);
			                  var td2=document.createElement('TD');
			                  var element = document.createElement("input");
						      element.type = "text";
				            		                
            	              element.name = "data["+model+"]["+rowCount+"]["+elementArrayRight[r-1]+"]";
            	              td2.appendChild(element);
            	              
			                 var tr2 = document.createElement('TR');
			                
			                 tr2.appendChild(td2);
			                
			               
            		         cell.appendChild(tr2);
     
            	          }
            	        if (elementArrayRight[r-1] == "url_address") {
			                 var text=document.createTextNode('URL Address');
			                 
			                 var td1=document.createElement('TD');
			                 td1.appendChild(text);
			                 var tr1 = document.createElement('TR');
			                 tr1.appendChild(td1); 
			                 cell.appendChild(tr1);
			                  var td2=document.createElement('TD');
			                  var element = document.createElement("input");
						      element.type = "text";
				            		                
            	              element.name = "data["+model+"]["+rowCount+"]["+elementArrayRight[r-1]+"]";
            	              td2.appendChild(element);
            	              
			                 var tr2 = document.createElement('TR');
			                
			                 tr2.appendChild(td2);
			                
			               
            		         cell.appendChild(tr2);
     
            	          }
			         
			          if (elementArrayRight[r-1] == "volume") {
			                 var text=document.createTextNode('Volume');
			                 
			                 var td1=document.createElement('TD');
			                 td1.appendChild(text);
			                 var tr1 = document.createElement('TR');
			                 tr1.appendChild(td1); 
			                 cell.appendChild(tr1);
			                  var td2=document.createElement('TD');
			                  var element = document.createElement("input");
						      element.type = "text";
				            		                
            	              element.name = "data["+model+"]["+rowCount+"]["+elementArrayRight[r-1]+"]";
            	              td2.appendChild(element);
            	              
			                 var tr2 = document.createElement('TR');
			                
			                 tr2.appendChild(td2);
			                
			               
            		         cell.appendChild(tr2);
     
            	          }
			           
			              if (elementArrayRight[r-1] == "issue") {
			                 var text=document.createTextNode('Issue');
			                 
			                 var td1=document.createElement('TD');
			                 td1.appendChild(text);
			                 var tr1 = document.createElement('TR');
			                 tr1.appendChild(td1); 
			                 cell.appendChild(tr1);
			                  var td2=document.createElement('TD');
			                  var element = document.createElement("input");
						      element.type = "text";
				            		                
            	              element.name = "data["+model+"]["+rowCount+"]["+elementArrayRight[r-1]+"]";
            	              td2.appendChild(element);
            	              
			                 var tr2 = document.createElement('TR');
			                
			                 tr2.appendChild(td2);
			                
			               
            		         cell.appendChild(tr2);
     
            	          }
            	          
            	           if (elementArrayRight[r-1] == "page_number") {
			                 var text=document.createTextNode('Page Number');
			                 
			                 var td1=document.createElement('TD');
			                 td1.appendChild(text);
			                 var tr1 = document.createElement('TR');
			                 tr1.appendChild(td1); 
			                 cell.appendChild(tr1);
			                  var td2=document.createElement('TD');
			                  var element = document.createElement("input");
						      element.type = "text";
				            		                
            	              element.name = "data["+model+"]["+rowCount+"]["+elementArrayRight[r-1]+"]";
            	              td2.appendChild(element);
            	              
			                 var tr2 = document.createElement('TR');
			                
			                 tr2.appendChild(td2);
			                
			               
            		         cell.appendChild(tr2);
     
            	          }
			        
			        }
			     } // end of right fields 
			} // end for each
			
       }
       
		function addRow(tableID,controller,no_of_fields,all_fields) {
			 var elementArray = all_fields.split(',');
           
			var table = document.getElementById(tableID);

			var rowCount = table.rows.length;
			var row = table.insertRow(rowCount);

			var cell0 = row.insertCell(0);
			cell0.innerHTML = rowCount+1 ;
			for(var i=1;i<=no_of_fields;i++) {
				var cell = row.insertCell(i);
				var element = document.createElement("input");
				element.type = "text";
				if(controller =="Book") {
					element.size = "13";
				} else if(controller =="Journal") {
					element.size = "10";
				} else if(controller =="Weblink") {
					element.size = "30";
				} else if (elementArray[i-1] == "prerequisite_course_id") {
				     
				       var element = document.createElement("select");						
					    var string='<option value="">--Select Prerequisite course---</option>';
						for (var f=0;f<prerequisite_courses.length;f++) {
						   string += '<option value="'+prerequisite_courses[f][0]
						   +'">'+prerequisite_courses[f][1]+'</option>';
						}
						string +='<option value="">None</option>';
			            element.innerHTML = string;
			          		              
				
				}
				element.name = "data["+controller+"]["+rowCount+"]["+elementArray[i-1]+"]";
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
	 
	
</SCRIPT>
<div class="courses form">

<?php echo $this->Form->create('Course');?>

		<legend class="smallheading"><?php echo __(' Edit Course '.$this->request->data['Course']['course_code_title'].'. '); ?></legend>
	<table>
		<tr><td>
		<?php 
		echo "<table><tbody>";
		echo $this->Form->input('id');
		echo "<tr><td>".$this->Form->input('year_level_id',array('empty'=>"---Select Year Level---"))."</td></tr>";
		echo "<tr><td>".$this->Form->input('semester',array('type'=>'select','options'=>$semester_array,'empty'=>"---Select Semester---"))."</td></tr>";
		echo "<tr><td>".$this->Form->input('course_title')."</td></tr>";
		
		echo $this->Form->hidden('Course.curriculum_id',array('value'=>$this->request->data['Course']['curriculum_id']));
		echo "<tr><td>".$this->Form->input('course_code')."</td></tr>";
		echo "<tr><td>".$this->Form->input('credit',array('label'=>$creditname))."</td></tr>";
		echo "<tr><td>".$this->Form->input('lecture_hours')."</td></tr>";
		echo "<tr><td>".$this->Form->input('tutorial_hours')."</td></tr>";
		echo "<tr><td>".$this->Form->input('laboratory_hours')."</td></tr>";
		echo "<tr><td>".$this->Form->input('course_category_id',array('empty'=>"---Select Course Category---"))."</td></tr>";
		echo "<tr><td style='padding-left:200px;'>".$this->Form->input('major',array('checked'=>'checked'))."</td></tr>";
		echo "<tr><td style='padding-left:200px;'>".$this->Form->input('thesis')."</td></tr>";
		
		echo "</tbody></table></td><td><table><tbody>";
		//echo "<tr><td>&nbsp;</td></tr>";
		echo "<tr><td>".$this->Form->input('course_description')."</td></tr>";
		echo "<tr><td>".$this->Form->input('course_objective')."</td></tr>";
		echo $this->Form->hidden('department_id',array('value'=>$department_id));
		echo "<tr><td>".$this->Form->input('lecture_attendance_requirement')."</td></tr>";
		
		echo "<tr><td>".$this->Form->input('lab_attendance_requirement')."</td></tr>";
		echo "<tr><td>".$this->Form->input('grade_type_id',array('empty'=>"---Select Grade Type---"))."</td></tr>";
		echo "</tbody></table>";
		?>
		</td>
		<table>
		<?php 
		$fields=array('prerequisite_course_id'=>'1');
		    $all_fields = "";
			$sep = "";

			foreach ($fields as $key => $tag) {
				$all_fields.= $sep.$key;
				$sep = ",";
				
			$prerequisite_course_list = "";
			$se ="";
			foreach($prerequisite_courses as $pk =>$pv) {
				$prerequisite_course_list .=$se.$pv;
				$se = ",";
			}
			}
		?>
		<tr><th colspan=3>Prerequisite Course </th></tr>
	   <TR><td style="width:89px;">No.</td><td>Prerequisite Course </td>
	   <?php 
	        if (!empty($this->request->data['Prerequisite'])) {
	                echo "<td>Action</td>";
	        }
	   ?>
	   </TR>
		</table>
		<TABLE id="prerequisite" border="1">
			<?php 
			
		     if (isset($this->request->data['Prerequisite']) && count($this->request->data['Prerequisite'])>0 ) {
		                $count=1;
		                 foreach ($this->request->data['Prerequisite'] as $ck=>$cv) {
		                    if (!empty($cv['id'])) {
		                                 echo $this->Form->hidden('Prerequisite.'.$ck.'.id');
		                             
		                              $action_model_id='edit~Prerequisite~'.$cv['course_id'];
		                              
		                    }
		                   
		                    echo "<tr><td>".$count."</td><td>".$this->Form->input('Prerequisite.'.$ck.'.prerequisite_course_id',
		                    array(
		                    'options'=>$prerequisite_courses,'type'=>'select','label'=>false,
		                    'selected'=>(!empty($this->request->data['Prerequisite'][$ck]['prerequisite_course_id']))?$this->request->data['Prerequisite'][$ck]['prerequisite_course_id'] :''));
		                    echo "</td><td>";
		             
		                    
		                       if(!empty($action_model_id)) {
		                
		                echo "&nbsp;&nbsp;&nbsp;".$this->Html->link(__('Delete'), array('action' => 'deleteChildren', $cv['id'],$action_model_id), null, sprintf(__('Are you sure you want to delete # %s?'), $cv['id']));  
		                
		                      }
		                    
		                    echo '</td></tr>';
		                    $count++;
		            }
		        
			} else {
			
			?>
				<TR>
					<TD> 1 </TD>
					<!--<TD> <INPUT type="text"  name="data[Prerequisite][0][prerequisite_course_code]"/> </TD> -->
					<TD> <select name="data[Prerequisite][0][prerequisite_course_id]">
						<option value="None">--Select Prerequisite course --- </option>
					<?php foreach($prerequisite_courses as $key=> $prerequisite_course) {
					?>
						 <option value="<?php echo $key;?>"><?php echo $prerequisite_course; ?> </option>
					<?php }?> 
				</select>

				</TD>
				</TR>
			<?php 
			
			}
			?>
		</TABLE>
	<table><tr><td colspan=3>
				<INPUT type="button" value="Add Row" onclick="addRow('prerequisite','Prerequisite',1, '<?php echo $all_fields; ?>')" />
				<!--
		<INPUT type="button" value="Delete Row" onclick="deleteRow('prerequisite')" /> -->
	</td></tr></table>
		<table>
		<?php 
		$fields=array('ISBN'=>'1','title'=>'2','publisher'=>'3','edition'=>'4','author'=>'5','place_of_publication'=>'6','year_of_publication'=>'7');
		    $all_fields = "";
			$sep = "";

			foreach ($fields as $key => $tag) {
				$all_fields.= $sep.$key;
				$sep = ",";
			}
			
		?>
		
		
		<tr><th colspan=9>Course Books Detail</th></tr>
	   <TR><td>No.</td><td style="width:125px;">ISBN</td><td style="width:125px;">Title</td><td style="width:125px">Publisher</td>
			<td>Edition</td><td>Author</td><td style="width:125px;">Place Of Publication</td>
			<td style="width:125px;">Year Of Publication</td>
			<?php 
			/*
			  if (!empty($this->request->data['Book'])) {
	                echo "<td>Action</td>";
	          }
	        */
	        ?>
			</TR>
		</table>
		<TABLE id="book"  border="1">
		<?php 
		    if (!empty($this->request->data['Book'] )) {
		        $count=1;
		        foreach ($this->request->data['Book'] as $bk=>$bv) {
		            if (!empty($bv['id'])) {
		                         echo $this->Form->input('Book.'.$bk.'.id');
		                             
		                         $action_book_id='edit~Book~'.$bv['course_id'];
		                              
		            }
		                   
					//echo $this->Form->input('Book.'.$bk.'.id');
		            echo "<tr><td>".$count."</td><td>".$this->Form->input('Book.'.$bk.'.ISBN',
		            array(
		            'value'=>isset($this->request->data['Book'][$bk]['ISBN'])?$this->request->data['Book'][$bk]['ISBN']:'','size'=>13,'label'=>'','div'=>false)).'</td><td>'.
		            $this->Form->input('Book.'.$bk.'.title',
		            array(
		            'value'=>isset($this->request->data['Book'][$bk]['title'])?$this->request->data['Book'][$bk]['title']:'','size'=>13,'label'=>'','div'=>false)).'</td><td>'.
					$this->Form->input('Book.'.$bk.'.publisher',
		            array(
		            'value'=>isset($this->request->data['Book'][$bk]['publisher'])?$this->request->data['Book'][$bk]['publisher']:'','size'=>13,'label'=>'','div'=>false)).'</td><td>'.
		            $this->Form->input('Book.'.$bk.'.edition',
		            array(
		            'value'=>isset($this->request->data['Book'][$bk]['edition'])?$this->request->data['Book'][$bk]['edition']:'','size'=>13,'label'=>'','div'=>false)).'</td><td>'.
					$this->Form->input('Book.'.$bk.'.author',
		            array(
		            'value'=>isset($this->request->data['Book'][$bk]['author'])?$this->request->data['Book'][$bk]['author']:'','size'=>13,'label'=>'','div'=>false)).'</td><td>'.
		            $this->Form->input('Book.'.$bk.'.place_of_publication',
		            array(
		            'value'=>isset($this->request->data['Book'][$bk]['place_of_publication'])?$this->request->data['Book'][$bk]['place_of_publication']:'','size'=>13,'label'=>'','div'=>false)).'</td><td>'.
					$this->Form->input('Book.'.$bk.'.year_of_publication',
		            array(
		            'value'=>isset($this->request->data['Book'][$bk]['year_of_publication'])?$this->request->data['Book'][$bk]['year_of_publication']:'','type'=>'text','size'=>13,'label'=>'','div'=>false)).'</td>';
		              
		                       if(!empty($action_book_id)) {
		                
		                echo "&nbsp;&nbsp;&nbsp;".$this->Html->link(__('Delete'), 
		                array('action' => 'deleteChildren', $bv['id'],$action_book_id), null, 
		                sprintf(__('Are you sure you want to delete # %s?'), $bv['id']));  
		                
		                      }
		                    
		            echo '</tr>';
				 $count++;
		        }
		    } else {
		 ?>
			<TR>
				<TD> 1 </TD>
				<TD > <INPUT type="text" size="13" name="data[Book][0][ISBN]"/> </TD>
				<TD> <INPUT type="text" size="13" name="data[Book][0][title]"/> </TD>
				<TD> <INPUT type="text" size="13" name="data[Book][0][publisher]"/> </TD>
				<TD> <INPUT type="text" size="13" name="data[Book][0][edition]"/> </TD>
				<TD> <INPUT type="text" size="13" name="data[Book][0][author]"/> </TD>
				<TD> <INPUT type="text" size="13" name="data[Book][0][place_of_publication]"/> </TD>
				<TD> <INPUT type="text" size="13" name="data[Book][0][year_of_publication]"/> </TD>
			</TR>
		<?php 
			
			}
		?>
		</TABLE>
	<table><tr><td colspan=3>
		<INPUT type="button" value="Add Row" onclick="addRow('book','Book',7,'<?php echo $all_fields; ?>')" />
		<INPUT type="button" value="Delete Row" onclick="deleteRow('book')" />
	</td></tr></table>
	<table>
	<?php 
		$fields=array('journal_title'=>'1','article_title'=>'2','author'=>'3','url_address'=>'4','volume'=>'5','issue'=>'6',
			'page_number'=>'7','ISBN'=>'8');
		    $all_fields = "";
			$sep = "";

			foreach ($fields as $key => $tag) {
				$all_fields.= $sep.$key;
				$sep = ",";
			}
		?>
		<tr><th colspan=9>Course Journals Detail</th></tr>
	    <TR><td>No.</td><td style="width:130px;">Journal Title</td><td style="width:130px;">Article Title</td>
			<td style="width:130px;">Author</td><td style="width:130px;">URL Address</td><td style="width:130px;">Volume</td>
			<td style="width:130px;">Issue</td><td style="width:130px;">Page Number</td><td style="width:130px;">ISBN</td>
			
			<?php 
			 /*
			  if (!empty($this->request->data['Journal'])) {
	                echo "<td>Action</td>";
	          }
	        */
	        ?>
			</TR>
		</table>
		<TABLE id="journal"  border="1" cellspacing="0">
		<?php 
		    if (!empty($this->request->data['Journal'] )) {
		        $count=1;
		        foreach ($this->request->data['Journal'] as $jk=>$jv) {
				
					  if (!empty($jv['id'])) {
		                       	echo $this->Form->hidden('Journal.'.$jk.'.id',
		                       	array('value'=>$this->request->data['Journal'][$jk]['id']));    
		                        $action_journal_id='edit~Journal~'.$jv['course_id'];
		                              
		              }
		            echo "<tr><td>".$count."</td><td>".$this->Form->input('Journal.'.$jk.'.journal_title',
		            array(
		            'value'=>isset($this->request->data['Journal'][$jk]['journal_title'])?$this->request->data['Journal'][$jk]['journal_title']:'','size'=>10,'label'=>'','div'=>false)).'</td><td>'.
		            $this->Form->input('Journal.'.$jk.'.article_title',
		            array(
		            'value'=>isset($this->request->data['Journal'][$jk]['article_title'])?$this->request->data['Journal'][$jk]['article_title']:'','size'=>10,'label'=>'','div'=>false)).'</td><td>'.
					$this->Form->input('Journal.'.$jk.'.author',
		            array(
		            'value'=>isset($this->request->data['Journal'][$jk]['author'])?$this->request->data['Journal'][$jk]['author']:'','size'=>10,'label'=>'','div'=>false)).'</td><td>'.
		            $this->Form->input('Journal.'.$jk.'.url_address',
		            array(
		            'value'=>isset($this->request->data['Journal'][$jk]['url_address'])?$this->request->data['Journal'][$jk]['url_address']:'','size'=>10,'label'=>'','div'=>false)).'</td><td>'.
					$this->Form->input('Journal.'.$jk.'.volume',
		            array(
		            'value'=>isset($this->request->data['Journal'][$jk]['volume'])?$this->request->data['Journal'][$jk]['volume']:'','size'=>10,'label'=>'','div'=>false)).'</td><td>'.
		            $this->Form->input('Journal.'.$jk.'.issue',
		            array(
		            'value'=>isset($this->request->data['Journal'][$jk]['issue'])?$this->request->data['Journal'][$jk]['issue']:'','size'=>10,'label'=>'','div'=>false)).'</td><td>'.
					$this->Form->input('Journal.'.$jk.'.page_number',
		            array(
		            'value'=>isset($this->request->data['Journal'][$jk]['page_number'])?$this->request->data['Journal'][$jk]['page_number']:'','size'=>10,'label'=>'','div'=>false)).'</td><td>'.
					$this->Form->input('Journal.'.$jk.'.ISBN',
		            array(
		            'value'=>isset($this->request->data['Journal'][$jk]['ISBN'])?$this->request->data['Journal'][$jk]['ISBN']:'','type'=>'text','size'=>10,'label'=>'','div'=>false)).'</td>';
		            
		               if(!empty($action_journal_id)) {
		                
		                echo "&nbsp;&nbsp;&nbsp;".$this->Html->link(__('Delete'), 
		                array('action' => 'deleteChildren', $jv['id'],$action_journal_id), null, 
		                sprintf(__('Are you sure you want to delete # %s?'), $jv['id']));  
		                
		               }
		            
		            echo '</tr>';
				 $count++;
		        }
		    } else {
		 ?>
			<TR>
				<TD> 1 </TD>
				<TD> <INPUT type="text" size="10" name="data[Journal][0][journal_title]"/> </TD>
				<TD> <INPUT type="text" size="10" name="data[Journal][0][article_title]"/> </TD>
				<TD> <INPUT type="text" size="10" name="data[Journal][0][author]"/> </TD>
				<TD> <INPUT type="text" size="10" name="data[Journal][0][url_address]"/> </TD>
				<TD> <INPUT type="text" size="10" name="data[Journal][0][volume]"/> </TD>
				<TD> <INPUT type="text" size="10" name="data[Journal][0][issue]"/> </TD>
				<TD> <INPUT type="text" size="10" name="data[Journal][0][page_number]"/> </TD>
				<TD> <INPUT type="text" size="10" name="data[Journal][0][ISBN]"/> </TD>
			</TR>
		<?php 
			}
		?>	
		</TABLE>
	<table><tr><td colspan=3>
		<INPUT type="button" value="Add Row" onclick="addRow('journal','Journal',8,'<?php echo $all_fields; ?>')" />
		<INPUT type="button" value="Delete Row" onclick="deleteRow('journal')" />
	</td></tr></table>
	<table>
	<?php 
		$fields=array('title'=>'1','url_address'=>'2','author'=>'3','year'=>'4');
		    $all_fields = "";
			$sep = "";

			foreach ($fields as $key => $tag) {
				$all_fields.= $sep.$key;
				$sep = ",";
			}
		?>
		<tr><th colspan=5>Course Weblinks Detail</th></tr>
		<TR><td style="width:15px;">No.</td><td style="width:140px;">Title</td><td style="width:140px;">URL Address</td>
			<td style="width:140px;">Author</td><td style="width:140px;">Year</td></TR>
		  <?php 
			/*
			  if (!empty($this->request->data['Journal'])) {
	                echo "<td>Action</td>";
	          }
	        */
	        ?>
		</table>
		<TABLE id="weblink"  border="1">
		<?php 
		    if (!empty($this->request->data['Weblink'] )) {
		        $count=1;
		        foreach ($this->request->data['Weblink'] as $wk=>$wv) {
					
					  if (!empty($wv['id'])) {
		                       	echo $this->Form->input('Weblink.'.$wk.'.id');    
		                        $action_weblink_id='edit~Weblink~'.$wv['course_id'];
		                              
		              }
		              
					//echo $this->Form->input('Weblink.'.$wk.'.id');
		            echo "<tr><td>".$count."</td><td>".$this->Form->input('Weblink.'.$wk.'.title',
		            array(
		            'value'=>isset($this->request->data['Weblink'][$wk]['title'])?$this->request->data['Weblink'][$wk]['title']:'','size'=>30,'label'=>'','div'=>false)).'</td><td>'.
		            $this->Form->input('Weblink.'.$wk.'.url_address',
		            array(
		            'value'=>isset($this->request->data['Weblink'][$wk]['url_address'])?$this->request->data['Weblink'][$wk]['url_address']:'','size'=>30,'label'=>'','div'=>false)).'</td><td>'.
					$this->Form->input('Weblink.'.$wk.'.author',
		            array(
		            'value'=>isset($this->request->data['Weblink'][$wk]['author'])?$this->request->data['Weblink'][$wk]['author']:'','size'=>30,'label'=>'','div'=>false)).'</td><td>'.
					$this->Form->input('Weblink.'.$wk.'.year',
		            array(
		            'value'=>isset($this->request->data['Weblink'][$wk]['year'])?$this->request->data['Weblink'][$wk]['year']:'','type'=>'text','size'=>30,'label'=>'','div'=>false)).'</td>';
		            
		               if(!empty($action_weblink_id)) {
		                    
		                    echo '&nbsp;&nbsp;&nbsp;'.$this->Html->link(__('Delete'), 
		                    array('action' => 'deleteChildren', $wv['id'],
		                     $action_weblink_id), null, 
		                    sprintf(__('Are you sure you want to delete # %s?'), $wv['id']));  
		                    
		               }

		            
		            echo '</tr>';
				 $count++;
		        }
		    } else {
		 ?>
			<TR>
				<TD> 1 </TD>
				<TD> <INPUT type="text" size="30" name="data[Weblink][0][title]"/> </TD>
				<TD> <INPUT type="text" size="30" name="data[Weblink][0][url_address]"/> </TD>
				<TD> <INPUT type="text" size="30" name="data[Weblink][0][author]"/> </TD>
				<TD> <INPUT type="text" size="30" name="data[Weblink][0][year]"/> </TD>
			</TR>
		<?php 
			}
		?>
		</TABLE>
	<table><tr><td colspan=3>
		<INPUT type="button" value="Add Row" onclick="addRow('weblink','Weblink',4,'<?php echo $all_fields; ?>')" />
		<INPUT type="button" value="Delete Row" onclick="deleteRow('weblink')" />
	</td></tr></table>
</tr>
		<tr> <td colspan="2" style="text-align:center;">
<?php echo $this->Form->Submit('Submit',array('name'=>'submit','div'=>false));?>	
	
		</td></tr>
		</table>
		<?php  
echo $this->Form->end();

 ?>
</div>

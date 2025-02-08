<?php echo $this->Form->create('TakenProperty');?>
<script type='text/javascript'>
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

function addRow(tableID,model,no_of_fields,all_fields,other) {
	       
			var elementArray = all_fields.split(',');
            
			var table = document.getElementById(tableID);

			var rowCount = table.rows.length;
			var row = table.insertRow(rowCount);

			var cell0 = row.insertCell(0);
			cell0.innerHTML = rowCount ;
			
			for(var i=1;i<=no_of_fields;i++) {
				var cell = row.insertCell(i);
				if (elementArray[i-1] == "name") {
				    var element = document.createElement("input");
				    element.type = "text";
				    element.size = "16";
				} else if (elementArray[i-1] == "remark") {
				    var element = document.createElement("textarea");
				    element.rows=6;
				    element.cols=30;
				    
				} else if (elementArray[i-1] == "taken_date") {
			            var element1 = document.createElement("select");						
						string = "";
						for (var f=0;f<months.length;f++) {
						   string += '<option value="'+months[f][0]+'"> '+months[f][1]+'</option>';
						}
						element1.name = "data["+model+"]["+rowCount+"]["+elementArray[i-1]+"][month]";
			            element1.innerHTML = string;
        				cell.appendChild(element1);
        				//cell.appendChild(document.createElement("br"));

			            var element2 = document.createElement("select");						
						string = "";
						for (var f=1;f<=31;f++) {
						   string += '<option value="'+(f < 10 ? '0'+f : f)+'"> '+f+'</option>';
						}
						element2.name = "data["+model+"]["+rowCount+"]["+elementArray[i-1]+"][day]";
			            element2.innerHTML = string;
        				cell.appendChild(element2);
        				//cell.appendChild(document.createElement("br"));				        
				        
				        var element3 = document.createElement("select");
					    var d = new Date();
			            var full_year =d.getFullYear();
			           string = "";
			            for(var j=full_year;j>=other;j--) {
				            string += '<option value="'+j+'"> '+j+'</option>';
			            }
			            element3.name = "data["+model+"]["+rowCount+"]["+elementArray[i-1]+"][year]";
			            element3.innerHTML = string;
			            cell.appendChild(element3);
			            continue;
			            
			            
				} else if (elementArray[i-1] == "return_date") {
				        var element1 = document.createElement("select");						
						string = "";
						for (var f=0;f<months.length;f++) {
						   string += '<option value="'+months[f][0]+'"> '+months[f][1]+'</option>';
						}
						element1.name = "data["+model+"]["+rowCount+"]["+elementArray[i-1]+"][month]";
			            element1.innerHTML = string;
        				cell.appendChild(element1);
        				//cell.appendChild(document.createElement("br"));

			            var element2 = document.createElement("select");						
						string = "";
						for (var f=1;f<=31;f++) {
						   string += '<option value="'+(f < 10 ? '0'+f : f)+'"> '+f+'</option>';
						}
						element2.name = "data["+model+"]["+rowCount+"]["+elementArray[i-1]+"][day]";
			            element2.innerHTML = string;
        				cell.appendChild(element2);
        				//cell.appendChild(document.createElement("br"));				        
				        
				        var element3 = document.createElement("select");
					    var d = new Date();
			            var full_year =d.getFullYear();
			           string = "";
			            for(var j=full_year;j>=other;j--) {
				            string += '<option value="'+j+'"> '+j+'</option>';
			            }
			            element3.name = "data["+model+"]["+rowCount+"]["+elementArray[i-1]+"][year]";
			            element3.innerHTML = string;
			            cell.appendChild(element3);
			            continue;
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
</script>
<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
            
<div class="takenProperties form">

	
<?php
 
$from = Configure::read('Calendar.applicationStartYear');

$properties_fields=array('name'=>'1','taken_date'=>2,'return_date'=>3,'remark'=>4);
$taken_properties_fields = "";
$sep = "";
foreach ($properties_fields as $key => $tag) {
        $taken_properties_fields.= $sep.$key;
        $sep = ",";
}
if (!isset($studentIDs)) {

?>
<table cellpadding="0" cellspacing="0">
<?php 	
        echo '<tr><td class="smallheading">Add Taken Property Of Student </td></tr>';
        
		echo '<tr><td class="font">'.$this->Form->input('Search.studentID',array('label' => 'Student ID/Number')).'</td></tr>';
       echo '<tr><td>'. $this->Form->Submit('Continue',array('name'=>'continue','div'=>false,'class'=>'tiny radius button bg-blue')).'</td></tr>';
?>
</table>
<?php 
}
if (isset($studentIDs)) {
        $from = date('Y') - 5;
        $to = date('Y') + 1;
        echo '<table>';
        echo '<tr>';
        echo '<td>';
             echo $this->element('student_basic');
          
        echo '</td>';
        echo '</tr>';
        
        echo '<tr>';
        echo '<td>';
                echo '<table id="taken_properties_id">';
                echo '<tr><th>S.N<u>o</u></th><th>Property</th><th>Taken Date</th><th>
                Return Date</th><th>Remark</th></tr>';
                if (!empty($this->request->data['TakenProperty'])) {
                    $count=1;
                    foreach ($this->request->data['TakenProperty'] as $key=>$value) {
                           echo '<tr>';
                           echo '<td>'.$count++.'</td>';
                           echo '<td>'.$this->Form->input('TakenProperty.'.$key.'.name',array('label'=>false)).'</td>';
                            echo '<td>'.$this->Form->input('TakenProperty.'.$key.'.taken_date',array('label'=>false)).'</td>';
                             echo '<td>'.$this->Form->input('TakenProperty.'.$key.'.return_date',array('label'=>false)).'</td>';
                             echo '<td>'.$this->Form->input('TakenProperty.'.$key.'.remark',array('label'=>false)).'</td>';
                               echo $this->Form->hidden('TakenProperty.'.$key.'.student_id');
                           
                           echo '</tr>';
                    
                    }
                
                } else {
                echo "<tr>";
                echo '<td>1</td>';
                echo "<td>".$this->Form->input('TakenProperty.0.name',array('label'=>false))."</td>";
	            
	           echo "<td>".$this->Form->input('TakenProperty.0.taken_date',
	           array('minYear'=>$from,'maxYear'=>$to,'orderYear' => 'desc','label'=>false)).'</td>';
	
	              echo "<td>".$this->Form->input('TakenProperty.0.return_date',
	           array('minYear'=>$from,'maxYear'=>$to,'orderYear' => 'desc','label'=>false)).'</td>';
	           
		        echo "<td>".$this->Form->input('TakenProperty.0.remark',array('label'=>false)).'</td>';
		        echo $this->Form->hidden('TakenProperty.0.student_id',array('value'=>$student_section_exam_status['StudentBasicInfo']['id']));

		        echo '</tr>';
		        
		        }
		      echo '</table>';
		        ?>
		         <table><tr><td>
		                <INPUT type="button" value="Add Row" 
		                onclick="addRow ('taken_properties_id','TakenProperty',4,
		                '<?php echo $taken_properties_fields; ?>','<?php echo $from ?>')" />
		                <INPUT type="button" value="Delete Row" onclick="deleteRow ('taken_properties_id')" />
	                </td>
	                </tr>
	            </table>
	            <?php 
		echo '</td>';
		echo '</tr>';
		echo '</table>';
		
		echo "<tr><td>".$this->Form->Submit('Save',array('name'=>'saveTakenProperties','class'=>'tiny radius button bg-blue','div'=>false)).'</td></tr>';
		echo '</table>';
}
?>
	
	
	<?php
	
?>
</div>
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row -->
      </div> <!-- end of box-body -->
</div><!-- end of box -->

<?php ?>
<script type="text/javascript">
 function addRow(tableID,model,no_of_fields,all_fields
        ) {
		  
		   	var elementArray = all_fields.split(',');
		  
			var table = document.getElementById(tableID);

			var rowCount = table.rows.length;
			
			var row = table.insertRow(rowCount);
            //the first cell constructed.
			var cell0 = row.insertCell(0);
			//cell0.innerHTML = rowCount+1 ;
			cell0.innerHTML = rowCount;
			//prepare for the drop down box
			
			//construct the other cells
			for(var j=1;j<=no_of_fields;j++) {
				var cell = row.insertCell(j);
				
	           if (elementArray[j-1] == "room_code") {
				   var element = document.createElement("input");
				   element.size = "8";
				   element.type = "text";
				 
				}
				
			
				if (elementArray[j-1] == "available_for_lecture") {
				   var element = document.createElement("input");
				  // element.size = "4";
				   element.type = "checkbox";
				 
				}
				if (elementArray[j-1] == "available_for_exam") {
				   var element = document.createElement("input");
				  
				   element.type = "checkbox";
				 
				}
				
			    if (elementArray[j-1] == "lecture_capacity") {
				   var element = document.createElement("input");
				   element.size = "4";
				   element.type = "text";
				 
				}
				
				
				  if (elementArray[j-1] == "exam_capacity") {
				   var element = document.createElement("input");
				   element.size = "4";
				   element.type = "text";
				 
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

</script>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
            
<div class="classRoomBlocks form">
<?php 

 $fields=array('room_code'=>1,'available_for_lecture'=>2,'available_for_exam'=>3,'lecture_capacity'=>4,
 'exam_capacity'=>5);
		   
		    
$all_fields = "";
$sep = "";
foreach ($fields as $key => $tag) {
		$all_fields.= $sep.$key;
		$sep = ",";
}
?>
<?php echo $this->Form->create('ClassRoomBlock');?>

     <div class="smallheading"><?php echo __('Add Class Room Block'); ?></div>
     <div class="font"><?php echo $college_name; ?></div>
     <div class="info-box info-message"><font color=RED><u>Beware:</u></font><br/> - If the Class Room  is only for laboratoriy, Please don't check available for lecture and exam check box.</div>
	<table>
	<?php
		echo $this->Form->hidden('college_id',array('value'=>$college_id));
		echo "<td width='50%'>".$this->Form->input('campus_id')."</td>";
		echo "<td width='50%'>".$this->Form->input('block_code')."</td></tr>";
		echo "<tr><td colspan='2'>";
	?>
	<table id="class_rooms">
	  <TR><td width="5%">No.</td><td width="15%">Room Code </td><td width="10%">Available For Lecture</td>
	  <td width="10%">Available For Exam</td>
	  <td width="15%">Lecture Capacity</td><td width="15%">Exam Capacity</td>
	   </TR>
	   <?php 
	     if (!empty($this->request->data['ClassRoom']) && count($this->request->data['ClassRoom'])>0) {
		     $count=1;
		         foreach ($this->request->data['ClassRoom'] as $ar=>$av) {
		                echo "<tr><td>".$count++."</td>";
		                echo "<td>".$this->Form->input('ClassRoom.'.$ar.'.room_code',
		            array('name'=>"data[ClassRoom][".$ar."][room_code]",
		            'value'=>isset($this->request->data['ClassRoom'][$ar]['room_code'])?
					$this->request->data['ClassRoom'][$ar]['room_code']:'','size'=>8,'label'=>false,'div'=>false))."</td>";
					  echo "<td>".$this->Form->input('ClassRoom.'.$ar.'.available_for_lecture',array(
		            'value'=>isset($this->request->data['ClassRoom'][$ar]['available_for_lecture'])?
					$this->request->data['ClassRoom'][$ar]['available_for_lecture']:'','label'=>false,'div'=>false))."</td>";
			
  echo "<td>".$this->Form->input('ClassRoom.'.$ar.'.available_for_exam',array(
		            'value'=>isset($this->request->data['ClassRoom'][$ar]['available_for_exam'])?
					$this->request->data['ClassRoom'][$ar]['available_for_exam']:'','label'=>false,'div'=>false))."</td>";
					
					echo "<td>".$this->Form->input('ClassRoom.'.$ar.'.lecture_capacity',
		            array('name'=>"data[ClassRoom][".$ar."][lecture_capacity]",
		            'value'=>isset($this->request->data['ClassRoom'][$ar]['lecture_capacity'])?
					$this->request->data['ClassRoom'][$ar]['lecture_capacity']:'','size'=>4,'label'=>false,'div'=>false))."</td>";
					
					echo "<td>".$this->Form->input('ClassRoom.'.$ar.'.exam_capacity',
		            array('name'=>"data[ClassRoom][".$ar."][exam_capacity]",
		            'value'=>isset($this->request->data['ClassRoom'][$ar]['exam_capacity'])?
					$this->request->data['ClassRoom'][$ar]['exam_capacity']:'','size'=>4,'label'=>false,'div'=>false))."</td></tr>";
					
					
		         }
		 } else {
		 ?>
		 <TR>
			     <TD> 1 </TD>
			     <TD> 
			   
				     <?php 
					 echo $this->Form->input('ClassRoom.0.room_code',
		            array('name'=>"data[ClassRoom][0][room_code]",
		            'value'=>isset($this->request->data['ClassRoom'][0]['room_code'])?
					$this->request->data['ClassRoom'][0]['room_code']:'','size'=>8,'label'=>false,'div'=>false));
				
		            ?>
		                
				  </TD><TD>
                    
					<?php echo $this->Form->input('ClassRoom.0.available_for_lecture',
					array('name'=>"data[ClassRoom][0][available_for_lecture]",
		            'value'=>isset($this->request->data['ClassRoom'][0]['available_for_lecture'])?
					$this->request->data['ClassRoom'][0]['available_for_lecture']:'','label'=>false,'div'=>false)); ?>
				
				 </TD>
				 <TD>
                    
					<?php echo $this->Form->input('ClassRoom.0.available_for_exam',
					array('name'=>"data[ClassRoom][0][available_for_exam]",
		            'value'=>isset($this->request->data['ClassRoom'][0]['available_for_exam'])?
					$this->request->data['ClassRoom'][0]['available_for_exam']:'','label'=>false,'div'=>false)); ?>
				
				 </TD>
				<TD> 
			   
				     <?php 
					 echo $this->Form->input('ClassRoom.0.lecture_capacity',
		            array('name'=>"data[ClassRoom][0][lecture_capacity]",
		            'value'=>isset($this->request->data['ClassRoom'][0]['lecture_capacity'])?
					$this->request->data['ClassRoom'][0]['lecture_capacity']:'','size'=>4,'label'=>false,'div'=>false));
				
		            ?>
		                
				  </TD>
				  <TD> 
			   
				     <?php 
					 echo $this->Form->input('ClassRoom.0.exam_capacity',
		            array('name'=>"data[ClassRoom][0][exam_capacity]",
		            'value'=>isset($this->request->data['ClassRoom'][0]['exam_capacity'])?
					$this->request->data['ClassRoom'][0]['exam_capacity']:'','size'=>4,'label'=>false,'div'=>false));
				
		            ?>
		                
				  </TD>
	
				</TR>
				<?php 
		 
		 }
	   ?>
	</table>
	
	 <table><tr><td colspan=3>
		
		<INPUT type="button" value="Add Row" onclick="addRow('class_rooms','ClassRoom',5,'<?php echo $all_fields; ?>')" /> 
		<INPUT type="button" value="Delete Row" onclick="deleteRow('class_rooms')" />
	</td></tr></table> 
	
	
	</td></tr>
	</table>

<?php 

echo $this->Form->end(array('label'=>__('Submit'),'class'=>'tiny radius button bg-blue'));?>
</div>
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->

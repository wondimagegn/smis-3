<?php ?>
<script type="text/javascript">
var floor_data=Array();
var index = 0;

<?PHP
    if(!empty($floor_data)){
        foreach($floor_data as $floor_id=>$floor_name){
        ?>
        index = floor_data.length;
        floor_data[index] = new Array();
        floor_data[index][0] = "<?php echo $floor_id; ?>";
        floor_data[index][1] = "<?php echo $floor_name; ?>";
      
        <?php
            }
     }
?>
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
				
	           if (elementArray[j-1] == "dorm_number") {
				   var element = document.createElement("input");
				   element.size = "8";
				   element.type = "text";
				 
				}
				
			
				if (elementArray[j-1] == "floor") {
				   var element = document.createElement("select");						
					    var string='<option value="">--Select floor---</option>';
						for (var f=0;f<floor_data.length;f++) {
						   string += '<option value="'+floor_data[f][0]
						   +'">'+floor_data[f][1]+'</option>';
						}
			            element.innerHTML = string;
			          		              
				 
				}
				if (elementArray[j-1] == "capacity") {
				   var element = document.createElement("input");
				   element.size = "8";
				   element.type = "text";
				 
				}
				if (elementArray[j-1] == "available") {
				   var element = document.createElement("input");
				  
				   element.type = "checkbox";
				   element.checked = true;
				 
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
<div class="dormitoryBlocks form">
<?php 

 $fields=array('dorm_number'=>1,'floor'=>2,'capacity'=>3,'available'=>4);
		   
		    
$all_fields = "";
$sep = "";
foreach ($fields as $key => $tag) {
		$all_fields.= $sep.$key;
		$sep = ",";
}
?>

<?php echo $this->Form->create('DormitoryBlock');?>

     <div class="smallheading"><?php echo __('Add Dormitory Block and Dormitories'); ?></div>
	<table>
	<?php
		echo "<tr><td width='50%'>".$this->Form->input('campus_id')."</td>";
		echo "<td width='50%'>".$this->Form->input('block_name')."</td></tr>";
		echo "<tr><td width='50%'>".$this->Form->input('type',array('type'=>'select','options'=>array('male'=>'Male','female'=>'Female'),'empty'=>'--Select Gender--'))."</td>";
		echo "<td width='50%'>".$this->Form->input('location')."</td></tr>";
		echo "<tr><td width='50%'>".$this->Form->input('telephone_number')."</td>";
		echo "<td width='50%'>".$this->Form->input('alt_telephone_number')."</td></tr>";
		echo "<tr><td colspan='2'>";
	?>

	<table id="dormitories">
	  <TR><td width="5%">No.</td><td width="15%"> Dorm Name </td><td width="20%">Floor</td>
	  <td width="15%">Capacity</td>
	  <td width="10%">Available</td>
	   </TR>
	   <?php 
	     if (!empty($this->request->data['Dormitory']) && count($this->request->data['Dormitory'])>0) {
		     $count=1;
		         foreach ($this->request->data['Dormitory'] as $ar=>$av) {
		                echo "<tr><td>".$count++."</td>";
		                echo "<td>".$this->Form->input('Dormitory.'.$ar.'.dorm_number',
		            array('name'=>"data[Dormitory][".$ar."][dorm_number]",
		            'value'=>isset($this->request->data['Dormitory'][$ar]['dorm_number'])?
					$this->request->data['Dormitory'][$ar]['dorm_number']:'','size'=>8,'label'=>false,'div'=>false))."</td>";
					  
					  echo "<td>".$this->Form->input('Dormitory.'.$ar.'.floor',array(
		            'value'=>isset($this->request->data['Dormitory'][$ar]['floor'])?
					$this->request->data['Dormitory'][$ar]['floor']:'','options'=>$floor_data,'type'=>'select', 'empty'=>'--Select floor---','label'=>false,'div'=>false))."</td>";
			
					echo "<td>".$this->Form->input('Dormitory.'.$ar.'.capacity',
		            array('name'=>"data[Dormitory][".$ar."][capacity]",
		            'value'=>isset($this->request->data['Dormitory'][$ar]['capacity'])?
					$this->request->data['Dormitory'][$ar]['capacity']:'', 'size'=>8, 'label'=>false, 'div'=>false))."</td>";
					
  					echo "<td>".$this->Form->input('Dormitory.'.$ar.'.available',array(
		            'value'=>isset($this->request->data['Dormitory'][$ar]['available'])?
					$this->request->data['Dormitory'][$ar]['available']:'','label'=>false,'div'=>false))."</td></tr>";
					
						
		         }
		 } else {
		 ?>
		 <TR>
			     <TD> 1 </TD>
			     <TD> 
			   
				     <?php 
					 echo $this->Form->input('Dormitory.0.dorm_number',
		            array('name'=>"data[Dormitory][0][dorm_number]",
		            'value'=>isset($this->request->data['Dormitory'][0]['dorm_number'])?
					$this->request->data['Dormitory'][0]['dorm_number']:'','size'=>8,'label'=>false,'div'=>false));
				
		            ?>
		                
				  </TD><TD>
                    
					<?php echo $this->Form->input('Dormitory.0.floor',
					array('name'=>"data[Dormitory][0][floor]",
		            'value'=>isset($this->request->data['Dormitory'][0]['floor'])?
					$this->request->data['Dormitory'][0]['floor']:'','options'=>$floor_data,'type'=>'select','empty'=>'--Select floor---','label'=>false,'div'=>false)); ?>
				
				 </TD>
				 <TD>
                    
                  <?php  echo $this->Form->input('Dormitory.0.capacity',
		            array('name'=>"data[Dormitory][0][capacity]",
		            'value'=>isset($this->request->data['Dormitory'][0]['capacity'])?
					$this->request->data['Dormitory'][0]['capacity']:'','size'=>8,'label'=>false,'div'=>false)); ?>
		            
					
				 </TD>
				<TD> 
			   
					<?php echo $this->Form->input('Dormitory.0.available',
					array('name'=>"data[Dormitory][0][available]",
		            'value'=>isset($this->request->data['Dormitory'][0]['available'])?
					$this->request->data['Dormitory'][0]['available']:'','checked'=>true,'label'=>false,'div'=>false)); ?>
		                
				  </TD>

				</TR>
				<?php 
		 
		 }
	   ?>
	</table>
	
	 <table><tr><td colspan=3>
		
		<INPUT type="button" value="Add Row" onclick="addRow('dormitories','Dormitory',4,'<?php echo $all_fields; ?>')" /> 
		<INPUT type="button" value="Delete Row" onclick="deleteRow('dormitories')" />
	</td></tr></table> 
	
	
	</td></tr>
	</table>

<?php 
echo $this->Form->submit(__('Submit'), array('div' => false,'class'=>'tiny radius button bg-blue'));
echo $this->Form->end();?>
</div>

<?php 
?>
<SCRIPT language="javascript">
//Sub Cat Combo 1
    function updateSubCategory(id,tableID) {
            var table = document.getElementById(tableID);
			var rowCount = table.rows.length;
            //serialize form data
            var formData = $("#grade_type_id").val();
                for(i=1;i<=rowCount;i++){
			    $("#"+id+i).empty();
			    $("#"+id+i).attr('disabled', true);
			    }
			    //get form action
                var formUrl = '/grades/get_grade_combo/'+formData;
                $.ajax({
                    type: 'get',
                    url: formUrl,
                    data: formData,
                    success: function(data,textStatus,xhr){
						    for(i=1;i<=rowCount;i++){
						    $("#"+id+i).attr('disabled', false);
						    $("#"+id+i).empty();
						    $("#"+id+i).append(data);
						    }
						    grades_combo = data;
				    },
                    error: function(xhr,textStatus,error){
                            alert(textStatus);
                    }
			    });
		
		return false;
}
var grades=Array();
var grades_combo='';			
var index = 0;

<?PHP
    foreach($grades as $grade_id=>$grade_name){
    ?>
    index = grades.length;
    grades[index] = new Array();
    grades[index][0] = "<?php echo $grade_id; ?>";
    grades[index][1] = "<?php echo $grade_name; ?>";
    grades_combo +="<option value='<?php echo $grade_id;?>'><?php echo $grade_name;?></option>";
    <?php
        }
?>

		function addRow(tableID,model,no_of_fields,all_fields) {
		    
		   	var elementArray = all_fields.split(',');
		  
			var table = document.getElementById(tableID);

			rowCount = table.rows.length;
			
			var row = table.insertRow(rowCount);
           
			var cell0 = row.insertCell(0);
			
			cell0.innerHTML = rowCount;
			
			//construct the other cells
			for(var j=1;j<=no_of_fields;j++) {
				var cell = row.insertCell(j);
				
				if (elementArray[j-1] == "grade_id") {
				        var element = document.createElement("select");						
						string = "";
						for (var f=0;f<grades.length;f++) {
						   string += '<option value="'+grades[f][0]+'"> '+grades[f][1]+'</option>';
						}
						//element.addEventListener('change', function(){updateSubCategory(rowCount)}, false);
						element.id = "grade_id_"+rowCount;
			            //element.innerHTML = string;
			            element.innerHTML = grades_combo;
				 
				} else if (elementArray[j-1] == 'minimum_result') {
				       var element = document.createElement("input");
				       element.size = "4";
				       element.type = "text";
				} else if (elementArray[j-1] == "maximum_result") {
				      
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
			    if(rowCount !=0 ){
                    table.deleteRow(rowCount-1);
			    } else {
			
			        alert('No more rows to delete');
			    }
			
			}catch(e) {
				alert(e);
			}
			
		}
		
</SCRIPT>

<?php 

$grade_scale_detail=array('grade_id'=>1,'minimum_result'=>2,'maximum_result'=>3);
$all_grade_scale_detail = "";
$sep = "";
foreach ($grade_scale_detail  as $key => $tag) {
        $all_grade_scale_detail.= $sep.$key;
        $sep = ",";
}
?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
            
<div class="gradeScales form">
<?php echo $this->Form->create('GradeScale');?>
	<fieldset>
		<legend class="smallheading"><?php echo __('Set Grade Scale'); ?></legend>
	<table>
	  <td><table>
	    <?php
	
		
		    echo $this->Form->hidden('id',array('value'=>$this->request->data['GradeScale']['id']));
		    echo '<tr><td>'.$this->Form->input('name',array('label'=>'Scale Name')).'</td><tr>';
		    //echo '<tr><td>'.$this->Form->input('department_id').'</td><tr>';
		    echo '<tr><td>'.$this->Form->input('program_id').'</td><tr>';
		    if ($role_id ==ROLE_COLLEGE ) {
		            echo '<tr><td>'.$this->Form->input('own',array('label'=>'Scale for freshman/Department Unassigned students')).'</td></tr>';
	        } else if ($role_id==ROLE_REGISTRAR ) {
	            // echo '<tr><td>'.$this->Form->input('own',array('label'=>'Scale for freshman/Department Unassigned students')).'</td></tr>';
	        }
		  
	        //echo $this->Form->input('active');
	    ?>
	    </table>
	   </td>
	   <td>
	        <table><tr><td colspan=4 ><?php echo $this->Form->input('grade_type_id',array('id'=>'grade_type_id',
		    'onchange' => 'updateSubCategory("grade_id_", "grade_scale_details")')); ?></td></tr></table>
	        <table id="grade_scale_details">
	            <tr><th>S.No</th><th>Grade</th><th>Minimum Result</th><th>Maximum Result</th><th>Action</th></tr>
	            
	                <?php 
	                 
	                   if (!empty($this->request->data['GradeScaleDetail'])) {
	                       
		                    $count=1;
		                      foreach ($this->request->data['GradeScaleDetail'] as $bk=>$bv) {
		                      if (!empty($bv['id'])) {
		                        echo $this->Form->hidden('GradeScaleDetail.'.$bk.'.id',
		                        array('value'=>$bv['id']));
		                      $action_controller_id='edit~gradeScales~'.$bv['grade_id'];
		                      
		                      }
		                      echo "<tr><td>".$count."</td><td>".$this->Form->input('GradeScaleDetail.'.$bk.'.grade_id',
		            array(
		            'options'=>$grades,'type'=>'select','label'=>false,'selected'=>!empty($this->request->data['GradeScaleDetail'][$bk]['grade_id']) ? $this->request->data['GradeScaleDetail'][$bk]['grade_id']:'','id'=>"grade_id_".$count))."</td><td>".$this->Form->input('GradeScaleDetail.'.$bk.'.minimum_result',
		            array(
		            'value'=>isset($this->request->data['GradeScaleDetail'][$bk]['minimum_result'])?$this->request->data['GradeScaleDetail'][$bk]['minimum_result']:'','label'=>false,'div'=>false,'size'=>4))."</td><td>".$this->Form->input('GradeScaleDetail.'.$bk.'.maximum_result',
		            array( 'value'=>isset($this->request->data['GradeScaleDetail'][$bk]['maximum_result'])?$this->request->data['GradeScaleDetail'][$bk]['maximum_result']:'','label'=>false,'div'=>false,'size'=>4));
		                    echo "</td><td>";
		                     echo $this->Html->link(__('Delete'), array('controller' => 'grade_scale_details', 'action' => 'delete', $bv['grade_id']), null, sprintf(__('Are you sure you want to delete # %s?'), $bv['grade_id']));  
		                      echo "</td></tr>";
		                      $count++;
		                  }
		         } else {
		            echo "<tr><td>1</td><td>".$this->Form->input('GradeScaleDetail.0.grade_id',
		            array(
		            'options'=>$grades,'type'=>'select','label'=>false,'selected'=>!empty($this->request->data['GradeScaleDetail'][0]['grade_id'])? $this->request->data['GradeScaleDetail'][0]['grade_id']:'','id'=>'grade_id_1'))."</td><td>".$this->Form->input('GradeScaleDetail.0.minimum_result',
		            array(
		            'value'=>isset($this->request->data['GradeScaleDetail'][0]['minimum_result'])?$this->request->data['GradeScaleDetail'][0]['minimum_result']:'','label'=>false,'size'=>4))."</td><td>".$this->Form->input('GradeScaleDetail.0.maximum_result',
		            array('value'=>isset($this->request->data['GradeScaleDetail'][0]['maximum_result'])?$this->request->data['GradeScaleDetail'][0]['maximum_result']:'','label'=>false,'size'=>4));
		                      echo "</td><td>&nbsp;</td></tr>";
		                      
		         }
		         ?>
	                
	            
	        </table>
	        <table>
	            <tr><td colspan=4><INPUT type="button" value="Add Row" 
	            onclick="addRow('grade_scale_details','GradeScaleDetail',3,
	            '<?php echo $all_grade_scale_detail; ?>')" />
		          
	            </td></tr>
	        </table>
	    </td>
	</tr>
	</table>
	</fieldset>
<?php echo $this->Form->end(array('label'=>__('Submit'),
'class'=>'tiny radius button bg-blue'));?>
</div>
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->

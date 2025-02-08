<?php echo $this->Form->create('SurveyQuestion',array('novalidate' => true)); ?>
<div class="box"> 
     <div class="box-body">
       <div class="row">    
       		<div class="large-12 columns">
       			<h1> <?php echo __('Edit Survey Question'); ?> </h1>
       		</div>
			<div class="large-6 columns">
				
					
					<?php
					echo $this->Form->input('id');
					echo $this->Form->input('question_english');
					echo $this->Form->input('question_amharic');
					
					?>
			</div>

			<div class="large-6 columns">
				<?php
				
					echo $this->Form->input('allow_multiple_answers');
					echo $this->Form->input('answer_required_yn',
					array('id'=>'ansryn'));
					echo $this->Form->input('require_remark_text',
					array('id'=>'RequireRemarkText'));
					echo $this->Form->input('mother');
					
					echo $this->Form->input('father');
					
					?>
			</div>
			
       </div>
       <div class="row">
       		<div class="large-12 columns">
       				<h1>Add Question Answer Options(If Exist)</h1>
<table cellspacing="0" cellpadding="0" id="surveyanswersetting" style="margin-bottom:5px">
		<tr>
			<th style="width:5%">No</th>
			<th style="width:25%">Answer(English)</th>
			<th style="width:20%">Answer(Amharic)</th>
			<th style="width:20%">Order</th>
			<th style="width:20%">&nbsp;</th>
		</tr>
		<?php
		if(empty($this->request->data)) {
			if(empty($survey_question_answers)) {
				?>
				<tr id="SurveyQuestionAnswer_1">
					<td style="vertical-align:middle">1</td>
					<td><?php echo $this->Form->input('SurveyQuestionAnswer.1.answer_english', array('label' => false));?></td>
					<td><?php echo $this->Form->input('SurveyQuestionAnswer.1.answer_amharic', array('label' => false));?></td>
					<td><?php echo $this->Form->input('SurveyQuestionAnswer.1.order', array('maxlength' => '2','type' => 'text', 'label' => false, 'maxlength' => '2', 'style' => 'width:75px'));?></td>
					
					<td><a href="javascript:deleteSpecificRow('SurveyQuestionAnswer_1')">Delete</a></td>
				</tr>
				<?php
				}
			else {
				$count = 0;
				foreach($survey_question_answers as $key => $survey_question_answer) {
				
						?>
						<tr>
							<td style="vertical-align:middle"><?php echo ++$count; ?></td>
							<td><?php echo $survey_question_answer['SurveyQuestionAnswer']['answer_english']; ?></td>
							<td><?php echo $survey_question_answer['SurveyQuestionAnswer']['answer_amharic']; ?></td>
							<td><?php echo $survey_question_answer['SurveyQuestionAnswer']['order']; ?></td>
							
							<td>&nbsp;</td>
						</tr>
						<?php
									}
			$count++;
			}
		}
		else {//debug($this->request->data);
			$count = 1;
			foreach($this->request->data['SurveyQuestionAnswer'] as $key => $surveyQuestionAnswer) {
				if(is_array($surveyQuestionAnswer))
					{
			?>
			<tr id="SurveyQuestionAnswer_<?php echo $count; ?>">
				<td style="vertical-align:middle"><?php echo ($count); ?></td>
				<td><?php if(isset($surveyQuestionAnswer['id'])) echo $this->Form->input('SurveyQuestionAnswer.'.$key.'.id', array('type' => 'hidden'));?>
					<?php echo $this->Form->input('SurveyQuestionAnswer.'.$key.'.answer_english', array('label' => false));?></td>
				<td><?php echo $this->Form->input('SurveyQuestionAnswer.'.$key.'.answer_amharic', array('label' => false, 'style' => 'width:75px'));?></td>
				<td><?php echo $this->Form->input('SurveyQuestionAnswer.'.$key.'.order', array('maxlength' => '2', 'type' => 'text', 'label' => false, 'maxlength' => '2', 'style' => 'width:75px'));?></td>
				
				<td><a href="javascript:deleteSpecificRow('SurveyQuestionAnswer_<?php echo $count++; ?>')">Delete</a></td>
			</tr>
			<?php
					}
				}
			}
		?>
</table>
<?php 
$all_answer_setup_detail = 'answer_english,answer_amharic,order,edit';
?>
	<p><input type="button" value="Add Row" onclick="addRow('surveyanswersetting', 'SurveyQuestionAnswer', 4, '<?php echo $all_answer_setup_detail; ?>')" /></p>
	
       		</div>
       	
       </div>
       <div class="row">
       		<div class="large-12 columns">

						<?php 

						echo $this->Form->Submit('Submit',array('name'=>'search','div'=>false,'class'=>'tiny radius button bg-blue'));


						?>
			</div>
       	
       </div>
    </div>
</div>
<?php echo $this->Form->end();?>
<script language="javascript">
var totalRow = <?php if(!empty($this->request->data)) echo (count($this->request->data['SurveyQuestionAnswer'])); else if(!empty($survey_question_answer)) echo (count($survey_question_answer)); else echo 2; ?>;

function updateSequence(tableID) {
	var s_count = 1;
	for(i = 1; i < document.getElementById(tableID).rows.length; i++) {
		document.getElementById(tableID).rows[i].cells[0].childNodes[0].data = s_count++;
	}
}

function addRow(tableID,model,no_of_fields,all_fields) {
	var elementArray = all_fields.split(',');
	var table = document.getElementById(tableID);
	var rowCount = table.rows.length;
	var row = table.insertRow(rowCount);
	totalRow++;
	row.id = model+'_'+totalRow;
	var cell0 = row.insertCell(0);
	cell0.innerHTML = rowCount;
	//construct the other cells
	for(var j=1;j<=no_of_fields;j++) {
		var cell = row.insertCell(j);
		
		if (elementArray[j-1] == 'answer_english') {
		   var element = document.createElement("input");
		   //element.size = "4";
		   element.type = "text";
		
		} else if (elementArray[j-1] == 'answer_amharic') {	
			   var element = document.createElement("input");
			   element.style.width = "75px";
			   element.type = "text";
			   element.maxLength = 5;
		} else if (elementArray[j-1] == 'order') {
			   var element = document.createElement("input");
			   element.style.width = "75px";
			   element.type = "text";
			   element.maxLength = 2;
			   
		} else if (elementArray[j-1] == 'edit') {
			   var element = document.createElement("a");
			   element.innerText = "Delete";
			   element.textContent = "Delete";
			   element.setAttribute('href','javascript:deleteSpecificRow(\''+model+'_'+totalRow+'\')');
		}
		
		element.name = "data["+model+"]["+rowCount+"]["+elementArray[j-1]+"]";
		cell.appendChild(element);
	}
	updateSequence(tableID);
}

function deleteRow(tableID) {
	try {
		var table = document.getElementById(tableID);
		var rowCount = table.rows.length;
		if(rowCount >2 ){
			table.deleteRow(rowCount-1);
			updateSequence(tableID);
		} else {
			alert('No more rows to delete');
		}
	}catch(e) {
		alert(e);
	}
}

function deleteSpecificRow(id) {
	try {
		var row = document.getElementById(id);
		//var table = row.parentElement;
		var table = row.parentNode;
		if(table.rows.length > 2 ){
			row.parentNode.removeChild(row);
			updateSequence('surveyanswersetting');
			//row.parentElement.removeChild(row);
		} else {
			alert('There must be at least one exam type.');
		}
	}catch(e) {
		alert(e);
	}
}

$(document).ready(function()
{
/*
     if($("#ansryn").is(":checked")){
        $('#RequireRemarkText').show();        
     } else { 
        $('#RequireRemarkText').hide();         
     }                            
*/
})
</script>

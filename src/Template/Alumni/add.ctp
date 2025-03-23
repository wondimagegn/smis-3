<?php echo $this->Form->create('Alumnus',
array('controller'=>'alumni','action'=>'add',
'method'=>'post')); ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  	<div class="large-12 columns">

	  		<h3> <?php echo __('BASELINE SURVEY QUESTIONNAIRE'); ?>
	  		</h3>
	  		<!-- tabs -->
			<ul  class="tabs" data-tab>
			    <li class="tab-title active"><a href="#tab1" data-toggle="tab">Introduction</a>
			    </li>
				 <li class="tab-title"><a href="#tab2" data-toggle="tab">A. CONTACT ADDRESS/አድራሻ</a>
			    </li>
			     <li class="tab-title"><a href="#tab3" data-toggle="tab">B. SOCIO-BIOGRAPHIC CHARACTERISTICS AND EDUCATION </a>
			    </li>
			</ul>
			<div class="tabs-content edumix-tab-horz">
			    <div class="content active" id="tab1">
			    	<h5>Dear Participant, </h5>
			      	<p>
	  			
The Ministry of Education aims at improving the Higher Education system in Ethiopia with the overall objective of enabling Higher Education graduates to find adequate employment in the national labour market. 
This survey is aimed at collecting information about your training and your employment aspirations after graduation in order to evaluate the Higher Education system and to identify needs for improvement or change. Thus, we will contact you again within a year time in person or via phone to learn about your employment situation. Your contact information (section A) will only be used for this purpose. 
Your responses are voluntary and will be treated highly confidential. We assure you that the responses of this questionnaire will not use individual identifier. All the responses will be aggregated together and analysed as a group. Your response is highly appreciated and is an important contribution to the further improvement of the relevance of academic programs in higher education institutions in the country. 
	  		</p>
	  		
	  		<p style="text-align: center;">THANK YOU FOR YOUR PARTICIPATION! </p>

	  		<h5>የተከበሩ ተሳታፊ፡-  </h5>
	  		<p>
	  		
			የጥናቱ ዋና ዓላማ የከፍተኛ ትምህርት ስልጠናን በኢትዮጵያ ለማሻሻል እና የተመራቂዎችን ተመጣጣኝ ስራ የማገኘት እድል ለማስፋት ነው፡፡ ጥናቱም ያተኮረው ከስልጠና እና ከምረቃ በኋላ ስላለው የስራ ሁኔታ መረጃ ሰብስቦ ከመዘነ በኋላ በከፍተኛ ትምህርት ስልጠና መሻሻል ወይም መቀየር ያለበትን ለመመዘን ነው፡፡ ከአንድ ዓመት በኋላ በአካል ወይም በስልክዎ ደውለን የስራዎን ሁኔታ እና በምን ደረጃ ላይ እንዳሉ መረጃ እንዲሰጡን እንጠይቅዎታለን፡፡ በፈቃደኝነት የሰጡንን መረጃ በሙሉ በሚስጥር እንደምንጠብ እያረጋገጥን፤ከሁሉም የጥናቱ ተሳታፊዎች የሚገኘውን መረጃ በአንድ ላይ በማቀናጀት በህብረት ጥናት እና ትንተና እናደርግበታለን፡፡ 
			
	  		</p>
	  		<p style="text-align: center;">
	  		ለትብብርዎ በጣም እናመሰግናለን፡፡
	  		</p>

			      <a class="tiny radius button bg-blue btnNext">Next</a>
		</div>

	    <div class="content " id="tab2">
	       <table>
	       		<tr>
	       			<td><strong>A1:</strong> Name of University/ የዩኒቨርሲቲ ስም:</td>
	       			<td> <?php echo $university['University']['name']; ?></td>
	       		</tr>
	       		
	       		<tr>
	       			<td><strong>A2:</strong> Full Name/ ስም ከነአያት: </td>
	       			<td><?php echo $this->Form->input('Alumnus.full_name',array('label'=>'','readOnly'=>true,'value'=>$student['Student']['full_name'])); 
	       			
	       			echo $this->Form->hidden('Alumnus.student_id',array('value'=>$student['Student']['id']));
	       			
	       				
	       			echo $this->Form->hidden('Alumnus.studentnumber',array('value'=>$student['Student']['studentnumber']));
	       			
	       			?></td>
	       		</tr>
	       		
	       		<tr>
	       			<td><strong>A3.</strong> Father´s name/ የአባት ስም: </td>
	       			<td><?php echo $this->Form->input('Alumnus.father_name',array('label'=>'','readOnly'=>true,'value'=>$student['Student']['middle_name'].' '.$student['Student']['last_name'] )); ?></td>
	       		</tr>
	       		
	       		<tr>
	       			<td><strong>A4.</strong> Home address/ የመኖሪያ አድራሻ፡   </td>
	       			<td>
	       			<table>
	       			<tr>
	       			<td><?php echo $this->Form->input('Alumnus.region',array('label'=>'Region','value'=>$student['Region']['name'])); ?></td>
	       			<td><?php echo $this->Form->input('Alumnus.woreda',array('label'=>'Woreda',
	       			'value'=>$student['Student']['woreda'])); ?></td>
	       			
	       			<td><?php echo $this->Form->input('Alumnus.kebele',array('label'=>'Kebele','value'=>$student['Student']['kebele'])); ?></td>
	       			
	       			<td><?php echo $this->Form->input('Alumnus.housenumber',array('label'=>'Housenumber','value'=>$student['Student']['house_number'])); ?></td>
	       			</tr>
	       				</table>
	       			</td>
	       			
	       		</tr>
	       		
	       		<tr>
	       			<td><strong>A5.</strong> Mobile phone/ የሞባይል ስልክ: </td>
	       			<td>
	       				<?php 
	       			echo $this->Form->input('Alumnus.mobile',array('label'=>'','value'=>$student['Student']['phone_mobile'])); ?>
	       			</td>
	       			
	       		</tr>
	       		
	       		<tr>
	       			<td><strong>A6.</strong> Home /Second phone/ የቤት ወይም 2ኛ ስልክ ቁጥር:  </td>
	       			<td>
	       			 <?php echo $this->Form->input('Alumnus.home_second_phone',array('label'=>'','value'=>$student['Student']['phone_home'])); 
	       			 
	       			 
	       			 ?>
	       			</td>
	       			
	       		</tr>
	       		
	       		<tr>
			   		<td colspan="2">
			   		<strong>A7.</strong>
			   		<table>
			   			<tr>
			   				<td>Email:<?php  echo $this->Form->input('Alumnus.email',array('label'=>'','value'=>$student['Student']['email'])); ?>  </td>
			   				<td> <?php echo $this->Form->input('Alumnus.facebookaddress'); ?></td>
			   			</tr>
			   		</table>
			   		
			   		</td>
	       			
	       		</tr>
	       		
	       		<tr>
	       			<td><strong>A8.</strong> College ID/ የኮሌጅ መታወቂያ ቁጥር:</td>
	       			<td><?php echo $student['Student']['studentnumber'];?></td>
	       			
	       		</tr>
	       		<tr>
	       			<td>Graduation Year:</td>
	       			<td>
	       			
	       			<?php echo $this->Form->input('Alumnus.gradution_academic_year', array('id' => 'AcadamicYear', 
		'label' => false, 'class' => 'fs14', 'style' => 'width:125px', 'type' => 'select',
		 'options' => $acyear_array_data, 
		 'default' => (isset($academic_year_selected) ? $academic_year_selected : $defaultacademicyear))); ?>
	       			
	       			
	       			</td>
	       			
	       		</tr>
	       		
	       		
	       </table>
	       <a class="tiny radius button bg-blue btnPrevious">Previous</a>
	         <a class="tiny radius button bg-blue btnNext">Next</a>
	    </div>

	      	<div class="content " id="tab3">
	      	
	      	  <table>
	       		<tr>
	       			<td><strong>B1.</strong> Sex/ፆታ:</td>
	       			<td> <?php echo $this->Form->input('Alumnus.sex',array('type'=>'radio','value'=>$student['Student']['gender'],'legend'=>false,'label'=>false,
	       			 'options' => array('female' => 'Female ሴት',
		         'male' => 'Male ወንድ'),'separator'=>'<br/>')); ?></td>
	  
	       		</tr>
	       		
	       		<tr>
	       			<td><strong>B2.</strong>Age/እድሜ:</td>
	       			<td> <span><?php echo $this->Form->input('Alumnus.age',array('label'=>'',
	       			'type'=>'number',
	       			'maxlength' => '2',
	       			'value'=>$student['Student']['age'],
	       			'style' => 'width:55px', 
	       		
	       			)); ?> Years/አመት </span></td>
	       		</tr>
	       		<tr>
	       			<td><strong>B3.</strong>Place of Birth/የትዉልድ ቦታ:</td>
	       			<td>
	       				<table>
	       					<tr>
					   			<td>Region</td>
					   			<td> <?php echo $this->Form->input('Alumnus.placeofbirthregion',array('options'=>$regions,'label'=>'')); ?></td>
					   			<td>Woreda</td>
					   			<td> <?php echo $this->Form->input('Alumnus.placeofbirthworeda',array('label'=>'')); ?></td>
	       					</tr>
	       				</table>
	       			</td>
	       		</tr>
	       		<?php 
	       		$count=4;
	       		
	       		foreach($surveyQuestions as $k=>$v) { ?>
	       		
	       		<tr>
	       			<td colspan="2"><strong>B<?php echo $count;?></strong> <?php echo $v['SurveyQuestion']['question_english'].' '.$v['SurveyQuestion']['question_amharic']; 
	       			
	       				echo $this->Form->hidden('AlumniResponse.'.$k.'.survey_question_id',array('value'=>$v['SurveyQuestion']['id']));
	       			
	       			?>
	       			<?php if(!empty($v['SurveyQuestionAnswer'])) { 
	       			   if($v['SurveyQuestion']['allow_multiple_answers']==1){
	       			?>
						<table>
						<?php 

						foreach($v['SurveyQuestionAnswer'] as $sqa=>$sqv) { ?>
						<tr>
							<td>
							<?php 
							echo $this->Form->checkbox('AlumniResponse.'.$k.'.answer.'.$sqv['id'],array('class'=>'checkbox1 response-form-field'));
	
							echo $sqv['answer_english'].' '.$sqv['answer_amharic'];
							?>
							</td>
						</tr>

						<?php } ?> 
						<tr>
						<td>
						<?php
						if($v['SurveyQuestion']['require_remark_text']==1){
	       			 echo $this->Form->input('AlumniResponse.'.$k.'.specifiy',array('label'=>'')); 
	       			 }
	       			 
					 ?>
						</td>
						</tr>
					
						</table>
	       			<?php } else if($v['SurveyQuestion']['answer_required_yn']==1) {
						$options=array();	
						 $attributes=array('legend'=>false,'label'=>false,'separator'=>'<br/>',
						 'class'=>'response-form-field');

						foreach($v['SurveyQuestionAnswer'] as $sqa=>$sqv) {
							 $options[$sqv['id']]=$sqv['answer_english'] .' '.$sqv['answer_amharic'];

						}
						if($v['SurveyQuestion']['mother']==1 && $v['SurveyQuestion']['mother']==1){
						
						$optionsf=array();	
						$optionsm=array();	
						 $attributes=array('legend'=>false,'label'=>false,'separator'=>'<br/>',
						 'class'=>'response-form-field');

						foreach($v['SurveyQuestionAnswer'] as $sqa=>$sqv) {
							 $optionsf[$sqv['id']]=$sqv['answer_english'] .' '.$sqv['answer_amharic'];
							 $optionsm[$sqv['id']]='';

						}
						
	       			 ?>
	       			
	       			   <table>
	       					
	       					<tr>
	       						<td width="6%">Mother</td>
	       						<td width="90%">Father</td>
	       					</tr>
	       					<tr>
	       					
	       						<td width="6%">	<?php 
									 echo $this->Form->radio('AlumniResponse.'.$k.'.answer.mother',$optionsm,$attributes);
									?>
								</td>
	       						<td width="90%">
									<?php 
									 echo $this->Form->radio('AlumniResponse.'.$k.'.answer.father',$optionsf,$attributes);
									?>
								</td>
								
	       					</tr>
	       				 </table>	
	       			
	       				<?php } else { ?>
	       				
	       				  <table>
	       					<tr>
	       						<td>
									<?php 
									echo $this->Form->radio('AlumniResponse.'.$k.'.answer',$options,$attributes);
									?>
								</td>
	       					</tr>
	       				 </table>	
	       				<?php } ?>
	       			 <?php 
	       			 if($v['SurveyQuestion']['require_remark_text']==1){
	       			 echo $this->Form->input('AlumniResponse.'.$k.'.specifiy',array('label'=>'')); 
	       			 }
	       			 
	       			 ?>	
	       				
	       			<?php } ?>
	       						
	       			<?php } else { ?>
	       			 <?php echo $this->Form->input('AlumniResponse.'.$k.'.specifiy',array('label'=>'','class'=>'response-form-field')); ?>
	       			<?php } ?>
	       			</td>
	       			
	       		</tr>
	       		
	       		<?php
	       		  
	       		  $count++;
	       		 } ?>
	       		
	       </table>
	       
				<a class="tiny radius button bg-blue btnPrevious">Previous</a>


			       <?php 
			       echo $this->Form->end(
array('label'=>__('Submit',true),'class'=>'tiny radius button bg-blue'));
		
?>
				<span style="color:red;"></span>
	    	</div>

		  </div>
	  	</div>
	   </div>
	  </div>
</div>
<?php //echo $this->Form->end(__('Submit')); ?>

<script type="text/javascript">
	$(document).ready(function() {
		$('.btnNext').click(function (){
			$('.tabs > .active').next('li').find('a').trigger('click');
		});

		$('.btnPrevious').click(function (){
			$('.tabs > .active').prev('li').find('a').trigger('click');
		});
		/*
	    $( "form" ).submit(function( event ) {
			  $('.response-form-field').each(function() {
			  	alert("Hello");
    			if ( $(this).val() !== '' || 
    			$(this).is(':checked') ){
    			 
        		   return true;
        		} else {
		    		
		    		$("span").text( "Some of the form fields not completed!" ).
		    		show().fadeOut(6000);
		    		event.preventDefault();	
        		}
        		//return true;
  		   });
			  
	   });
	   */

	});
	
</script>

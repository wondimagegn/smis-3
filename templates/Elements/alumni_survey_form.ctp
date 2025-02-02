<h3> <?php echo __('BASELINE SURVEY QUESTIONNAIRE'); ?>
</h3>
<!-- tabs -->
<ul  class="tabs" data-tab>
    
	 <li  class="tab-title active" ><a href="#tab1" data-toggle="tab">A. CONTACT ADDRESS/አድራሻ</a>
    </li>
     <li class="tab-title"><a href="#tab2" data-toggle="tab">B. SOCIO-BIOGRAPHIC CHARACTERISTICS AND EDUCATION </a>
    </li>
</ul>
<div class="tabs-content edumix-tab-horz">
<div  class="content active" id="tab1">
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
		
		<td><?php 
			echo "Region".$alumniDetail['Alumnus']['region']; ?>
		</td>
		<td><?php echo 'Woreda: '.$alumniDetail['Alumnus']['woreda']; ?></td>
		
		<td><?php echo 'Kebele: '.$alumniDetail['Alumnus']['kebele']; ?></td>
		
		<td><?php echo 'Housenumber: '.$alumniDetail['Alumnus']['housenumber']; ?></td>
		</tr>
			</table>
		</td>
		
	</tr>
	
	<tr>
		<td><strong>A5.</strong> Mobile phone/ የሞባይል ስልክ: </td>
		<td>
			<?php 
		echo $alumniDetail['Alumnus']['mobile'] ?>
		</td>
		
	</tr>
	
	<tr>
		<td><strong>A6.</strong> Home /Second phone/ የቤት ወይም 2ኛ ስልክ ቁጥር:  </td>
		<td>
		 <?php echo $alumniDetail['Alumnus']['home_second_phone'];
		 ?>
		</td>
		
	</tr>
	
	<tr>
   		<td colspan="2">
   		<strong>A7.</strong>
   		<table>
   			<tr>
   				<td>Email:<?php  $alumniDetail['Alumnus']['email']; ?>  </td>
   				<td> 
   				<?php 
   				echo $alumniDetail['Alumnus']['email']; 
   				?></td>
   			</tr>
   		</table>
   		
   		</td>
		
	</tr>
	
	<tr>
		<td><strong>A8.</strong> College ID/ የኮሌጅ መታወቂያ ቁጥር:</td>
		<td><?php echo $student['Student']['studentnumber'];?></td>
		
	</tr>
	<tr>
		<td>Graduation Approved Date:</td>
		<td>
		
		<?php echo $student['Student']['GraduateList']['graduate_date'];?>
		</td>
		
	</tr>
</table>
</div>

<div class="content " id="tab2">

  <table>
	<tr>
		<td><strong>B1.</strong> Sex/ፆታ:</td>
		<td> <?php echo $alumniDetail['Alumnus']['sex']; ?></td>

	</tr>
	
	<tr>
		<td><strong>B2.</strong>Age/እድሜ:</td>
		<td> <span><?php echo $alumniDetail['Alumnus']['age']; ?> Years/አመት </span></td>
	</tr>
	<tr>
		<td><strong>B3.</strong>Place of Birth/የትዉልድ ቦታ:</td>
		<td>
			<table>
				<tr>
		   			<td>Region</td>
		   			<td> <?php echo $this->Form->input('Alumnus.placeofbirthregion',array('value'=>$alumniDetail['Alumnus']['placeofbirthregion'],'label'=>'')); ?></td>
		   			<td>Woreda</td>
		   			<td> <?php echo $this->Form->input('Alumnus.placeofbirthworeda',array('label'=>'',
		   			'value'=>$alumniDetail['Alumnus']['placeofbirthworeda'])); ?></td>
				</tr>
			</table>
		</td>
	</tr>
	<?php 
	$count=4;
	$formattedQuestion=array();
	foreach($alumniDetail['AlumniResponse'] as $k=>$v){
			$formattedQuestion[$v['SurveyQuestion']['question_english'].' '.$v['SurveyQuestion']['question_amharic']][]=$v;
	}

	foreach($formattedQuestion as $k=>$v) {
	?>
	<tr>
		<td colspan="2"><strong>B<?php echo $count;?></strong> 
		<?php 
			echo $k;
		?>
		</td>
	</tr>
	<?php foreach($v as $kk=>$vv) {
			if($vv['mother']==1){
	?>
		
		<tr>
			<td>Mother</td>
			<td><?php echo $vv['SurveyQuestionAnswer']['answer_english'].' '.$vv['SurveyQuestionAnswer']['answer_amharic']; ?></td>
		</tr>
		
		<?php } else if ($vv['father']==1){ ?>
			  <tr>
			<td>Father</td>
			<td><?php echo $vv['SurveyQuestionAnswer']['answer_english'].' '.$vv['SurveyQuestionAnswer']['answer_amharic']; ?></td>
		</tr>
	<?php } else { ?>
	    <?php if($vv['SurveyQuestion']['require_remark_text']==false) {?>
	     <tr>
			
			<td colspan="2"><?php echo $vv['SurveyQuestionAnswer']['answer_english'].' '.$vv['SurveyQuestionAnswer']['answer_amharic']; ?></td>
		</tr>
	<?php 
			} else {
			?>
			 <tr>
			
			<td colspan="2"><?php echo $vv['specifiy']; ?></td>
		</tr>
			<?php 
			}
		  }
		}
	?>
	<?php 
	 $count++;
	}
		?>
</table>


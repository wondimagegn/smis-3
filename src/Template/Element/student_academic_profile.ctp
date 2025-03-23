<script src="/js/clipboard.min.js"></script>
<!-- <script src="https://cdn.jsdelivr.net/clipboard.js/1.5.12/clipboard.min.js"></script> -->
<script>
	$(function() {
		new Clipboard('.copy-text');
	});
</script>
<?php

	$graduated = $student_academic_profile['BasicInfo']['Student']['graduated'];

	(isset($academicYR) ? debug($academicYR) : '');
    (isset($student_section_exam_status) ? debug($student_section_exam_status) : '');

	if (isset($student_section_exam_status['StudentBasicInfo']) && is_null($student_section_exam_status['StudentBasicInfo']['curriculum_id']) && !is_null($student_section_exam_status['StudentBasicInfo']['department_id'])) {
        if ($this->Session->read('Auth.User')['role_id'] == ROLE_STUDENT) { ?>
        	<div class='warning-box warning-message'><span style='margin-right: 15px;'></span><i style="font-family: 'Times New Roman', Times, serif; font-weight: bold;">Your profile is not attached to any Curriculum. Please Communicate your department to attach a curriculum to your profile.</i></div>
            <?php
        } else if ($this->Session->read('Auth.User')['role_id'] == ROLE_DEPARTMENT) { ?>
        	<div class='warning-box warning-message'><span style='margin-right: 15px;'></span><i style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><?= $student_academic_profile['BasicInfo']['Student']['full_name'] . ' ('. $student_academic_profile['BasicInfo']['Student']['studentnumber'] .')'; ?> is not attached to any Curriculum. Please attach a curriculum to the student profile in "Placement > Accepted Students > Attach Curriculum" with filters set as: <?= $student_section_exam_status['StudentBasicInfo']['academicyear'] ?> as Admission Year, <?= $student_academic_profile['BasicInfo']['Program']['name']; ?> as Program and <?= $student_academic_profile['BasicInfo']['ProgramType']['name']; ?> as Program Type.</i></div>
            <?php
        } else if ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR) { ?>
           <div class='warning-box warning-message'><span style='margin-right: 15px;'></span><i style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><?= $student_academic_profile['BasicInfo']['Student']['full_name'] . ' ('. $student_academic_profile['BasicInfo']['Student']['studentnumber'] .')'; ?> is not attached to any Curriculum. Please Communicate his/her department to attach a curriculum to students profile.</i></div>
            <?php
        } else { ?>
            <div class='warning-box warning-message'><span style='margin-right: 15px;'></span><i style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><?= $student_academic_profile['BasicInfo']['Student']['full_name'] . ' ('. $student_academic_profile['BasicInfo']['Student']['studentnumber'] .')'; ?> is not attached to any Curriculum.</i></div>
            <?php
        }

    }


    if (isset($student_section_exam_status) && $isTheStudentDismissed && !$isTheStudentReadmitted) {
        if ($this->Session->read('Auth.User')['role_id'] == ROLE_STUDENT) { ?>
        	<div class='info-box info-message'><span style='margin-right: 15px;'></span><i style="font-family: 'Times New Roman', Times, serif; font-weight: bold;">You're Dismmised in <?= ($student_section_exam_status['StudentExamStatus']['semester'] == 'I' ? '1st' : ($student_section_exam_status['StudentExamStatus']['semester'] == 'II' ? '2nd' : ($student_section_exam_status['StudentExamStatus']['semester'] == 'III' ? '3rd' : $student_section_exam_status['StudentExamStatus']['semester']))) ; ?> Semester of <?= $student_section_exam_status['StudentExamStatus']['academic_year']; ?> Academic Year. Please, Advise Registrar for Readmission if Applicable.</i></div>
            <?php
        } else if ($this->Session->read('Auth.User')['role_id'] == ROLE_DEPARTMENT) { ?>
        	<div class='info-box info-message'><span style='margin-right: 15px;'></span><i style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><?= $student_academic_profile['BasicInfo']['Student']['full_name'] . ' ('. $student_academic_profile['BasicInfo']['Student']['studentnumber'] .')'; ?> is  Dismmised in <?= ($student_section_exam_status['StudentExamStatus']['semester'] == 'I' ? '1st' : ($student_section_exam_status['StudentExamStatus']['semester'] == 'II' ? '2nd' : ($student_section_exam_status['StudentExamStatus']['semester'] == 'III' ? '3rd' : $student_section_exam_status['StudentExamStatus']['semester']))) ; ?> Semester of <?= $student_section_exam_status['StudentExamStatus']['academic_year']; ?> Academic Year. Please, Advise The Student for Readmission if Applicable.</i></div>
            <?php
        } else if ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR) { ?>
            <div class='info-box info-message'><span style='margin-right: 15px;'></span><i style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><?= $student_academic_profile['BasicInfo']['Student']['full_name'] . ' ('. $student_academic_profile['BasicInfo']['Student']['studentnumber'] .')'; ?> is  Dismmised in <?= ($student_section_exam_status['StudentExamStatus']['semester'] == 'I' ? '1st' : ($student_section_exam_status['StudentExamStatus']['semester'] == 'II' ? '2nd' : ($student_section_exam_status['StudentExamStatus']['semester'] == 'III' ? '3rd' : $student_section_exam_status['StudentExamStatus']['semester']))) ; ?> Semester of <?= $student_section_exam_status['StudentExamStatus']['academic_year']; ?> Academic Year. Please, Advise The Student for Readmission if Applicable.</i></div>
            <?php
        } else { ?>
            <div class='info-box info-message'><span style='margin-right: 15px;'></span><i style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><?= $student_academic_profile['BasicInfo']['Student']['full_name'] . ' ('. $student_academic_profile['BasicInfo']['Student']['studentnumber'] .')'; ?> is  Dismmised in <?= ($student_section_exam_status['StudentExamStatus']['semester'] == 'I' ? '1st' : ($student_section_exam_status['StudentExamStatus']['semester'] == 'II' ? '2nd' : ($student_section_exam_status['StudentExamStatus']['semester'] == 'III' ? '3rd' : $student_section_exam_status['StudentExamStatus']['semester']))) ; ?> Semester of <?= $student_section_exam_status['StudentExamStatus']['academic_year']; ?> Academic Year. Please, Advise The Student for Readmission if Applicable.</i></div>
            <?php
        }

    } else if (isset($student_section_exam_status) && empty($student_section_exam_status['Section']) && !$graduated) {
		
		if (isset($student_section_exam_status['StudentExamStatus']) && !empty($student_section_exam_status['StudentExamStatus']['academic_year'])) {
			if ($academicYR == $student_section_exam_status['StudentExamStatus']['academic_year']) {
				// nothing
			} else {
				$academicYR = (explode('/', $student_section_exam_status['StudentExamStatus']['academic_year'])[0] + 1) . '/'. (explode('/', $student_section_exam_status['StudentExamStatus']['academic_year'])[1] + 1);
			}
		}
                    
        if ($this->Session->read('Auth.User')['role_id'] == ROLE_STUDENT) { ?>
        	<div class='info-box info-message'><span style='margin-right: 15px;'></span><i style="font-family: 'Times New Roman', Times, serif; font-weight: bold;">You're Sectionless in <?= $academicYR; ?> Academic Year. Please, Advise your Department for Section Assignment.</i></div>
            <?php
        } else if ($this->Session->read('Auth.User')['role_id'] == ROLE_DEPARTMENT) { ?>
            <div class='info-box info-message'><span style='margin-right: 15px;'></span><i style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><?= $student_academic_profile['BasicInfo']['Student']['full_name'] . ' ('. $student_academic_profile['BasicInfo']['Student']['studentnumber'] .')'; ?> is  Sectionless for <?= $academicYR; ?>. Please, Add Him/her to appropraite section created for <?= $academicYR; ?> Academic Year.</i></div>
            <?php
        } else if ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR) { ?>
            <div class='info-box info-message'><span style='margin-right: 15px;'></span><i style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><?= $student_academic_profile['BasicInfo']['Student']['full_name'] . ' ('. $student_academic_profile['BasicInfo']['Student']['studentnumber'] .')'; ?> is  Sectionless for <?= $academicYR; ?>. The student must have an appropraite section assignment in section created for <?= $academicYR; ?> Academic Year.</i></div>
        	<?php
        } else { ?>
            <div class='info-box info-message'><span style='margin-right: 15px;'></span><i style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><?= $student_academic_profile['BasicInfo']['Student']['full_name'] . ' ('. $student_academic_profile['BasicInfo']['Student']['studentnumber'] .')'; ?> is  Sectionless for <?= $academicYR; ?>. The student must have an appropraite section assignment in section created for <?= $academicYR; ?> Academic Year.</i></div>
            <?php
        }
	} 
?>

<div class="row">
	<div class="large-12 columns">
		<?php 
		$credit_type = 'Credit';

		if(isset($student_academic_profile['Curriculum']['type_credit']) && !empty($student_academic_profile['Curriculum']['type_credit'])){
			$crtype = explode('ECTS',$student_academic_profile['Curriculum']['type_credit']);
			//debug($crtype);
			if(count($crtype) == 2){
				$credit_type = 'ECTS';
			} else {
				$credit_type = 'Credit';
			}
		}

		?>
		<!-- tabs -->
		<ul class="tabs" data-tab>
			<li class="tab-title active">
				<a href="#basicinformation">Basic</a>
			</li>
			<li class="tab-title">
				<a href="#exemption">Exemption</a>
			</li>
			<li class="tab-title">
				<a href="#registration">Registration</a>
			</li>
			<li class="tab-title">
				<a href="#addcourses">Add Courses</a>
			</li>
			<li class="tab-title">
				<a href="#dropcourses">Drop Courses</a>
			</li>
			<li class="tab-title">
				<a href="#examresults">Results</a>
			</li>
			<li class="tab-title">
				<a href="#curriculum">Curriculum</a>
			</li>
			<li class="tab-title">
				<a href="#Billing">Billing</a>
			</li>
			<?php
			if (SHOW_OTP_TAB_ON_STUDENT_ACADEMIC_PROFILE_FOR_STUDENTS == 1 && ($this->Session->read('Auth.User')['role_id'] == ROLE_STUDENT || $this->Session->read('Auth.User')['role_id'] == ROLE_SYSADMIN || $this->Session->read('Auth.User')['role_id'] == ROLE_DEPARTMENT || $this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR) && isset($otps) && !empty($otps)) { ?>
				<li class="tab-title">
					<a href="#OTP">OTPs</a>
				</li>
				<?php
			} ?>
		</ul>

		<div class="tabs-content edumix-tab-horz">
			<div class="content active" id="basicinformation">
				<?php
				if (!empty($student_academic_profile)) { ?>
					<!-- <div class="AddTab"> -->
						<table cellspacing="0" cellpading="0" class="table-borderless">
							<tr>
								<td>
									<div class="large-6 columns" style="padding: 0.7rem;">
										<table cellspacing="0" cellpading="0" class="table">
											<tbody>
												<tr>
													<td colspan=2><strong>Demographic Information</strong></td>
												</tr>
												<tr>
													<td style="padding-left:30px;">First Name: <strong id="copySTFN" class="copy-text" data-clipboard-target="#copySTFN" title="Click here once to copy text"><?= $student_academic_profile['BasicInfo']['Student']['first_name']; ?></strong></td>
													<td></td>
												</tr>
												<tr>
													<td style="padding-left:30px;">ስም: <strong id="copySTAFN" class="copy-text" data-clipboard-target="#copySTAFN" title="Click here once to copy text"><?= $student_academic_profile['BasicInfo']['Student']['amharic_first_name']; ?></strong></td>
													<td></td>
												</tr>
												<tr>
													<td style="padding-left:30px;">Middle Name: <strong id="copySTMN" class="copy-text" data-clipboard-target="#copySTMN" title="Click here once to copy text"><?= $student_academic_profile['BasicInfo']['Student']['middle_name']; ?></strong></td>
													<td></td>
												</tr>
												<tr>
													<td style="padding-left:30px;">የአባት ስም: <strong id="copySTAMN" class="copy-text" data-clipboard-target="#copySTAMN" title="Click here once to copy text"><?= $student_academic_profile['BasicInfo']['Student']['amharic_middle_name']; ?></strong></td>
													<td></td>
												</tr>
												<tr>
													<td style="padding-left:30px;">Last Name: <strong id="copySTLN" class="copy-text" data-clipboard-target="#copySTLN" title="Click here once to copy text"><?= $student_academic_profile['BasicInfo']['Student']['last_name']; ?></strong></td>
													<td></td>
												</tr>
												<tr>
													<td style="padding-left:30px;">የአያት ስም: <strong id="copySTALN" class="copy-text" data-clipboard-target="#copySTALN" title="Click here once to copy text"><?= $student_academic_profile['BasicInfo']['Student']['amharic_last_name']; ?></strong></td>
													<td></td>
												</tr>
												<tr>
													<td style="padding-left:30px;">Sex: <strong><?= (strcasecmp(trim($student_academic_profile['BasicInfo']['Student']['gender']), 'male') == 0 ?  'Male' : ((strcasecmp(trim($student_academic_profile['BasicInfo']['Student']['gender']), 'female') == 0) ? 'Female' : ''/* $student_academic_profile['BasicInfo']['Student']['gender'] */)); ?></strong></td>
													<td></td>
												</tr>
												<tr>
													<td style="padding-left:30px;">Student ID: <strong id="copySTID" class="copy-text" data-clipboard-target="#copySTID" title="Click here once to copy text"><?= $student_academic_profile['BasicInfo']['Student']['studentnumber']; ?></strong></td>
													<td></td>
												</tr>
												<tr>
													<td style="padding-left:30px;">Birth Date: <?= (isset($student_academic_profile['BasicInfo']['Student']['birthdate']) ? $this->Time->format("M j, Y", $student_academic_profile['BasicInfo']['Student']['birthdate'], NULL, NULL) : '---'); ?></td>
													<td></td>
												</tr>
												<?php
												if ($this->Session->read('Auth.User')['role_id'] != ROLE_STUDENT) { ?>
													<tr>
														<td style="padding-left:30px;">ID Card Printed: <?= ((!isset($student_academic_profile['BasicInfo']['Student']['print_count']) && !empty($student_academic_profile['BasicInfo']['Student']['print_count'])) || (isset($student_academic_profile['BasicInfo']['Student']['print_count']) && $student_academic_profile['BasicInfo']['Student']['print_count'] == 0 ) ?  'No' : (($student_academic_profile['BasicInfo']['Student']['print_count'] == 1) ? '1 time' : $student_academic_profile['BasicInfo']['Student']['print_count'] . ' times')); ?></td>
														<td></td>
													</tr>
													<?php
												} ?>
												<tr>
													<td style="padding-left:30px;">National Student ID: <?= (!empty($student_academic_profile['BasicInfo']['Student']['student_national_id']) ? '<strong id="copySTNTID" class="copy-text" data-clipboard-target="#copySTNTID" title="Click here once to copy text">' . $student_academic_profile['BasicInfo']['Student']['student_national_id'] . '</strong>' : '---'); ?>
														<?php
														/* if (($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR  && $this->Session->read('Auth.User')['is_admin'] == 1 && !isset($student_academic_profile['BasicInfo']['Student']['student_national_id'])) || ($this->Session->read('Auth.User')['role_id'] == ROLE_SYSADMIN && !isset($student_academic_profile['BasicInfo']['Student']['student_national_id']))) { ?>
															&nbsp;&nbsp;&nbsp;&nbsp;
															<?= $this->Html->link('Add National ID', '#', array()); ?>
															<?php //echo $this->Html->link(_('Add National ID'), array('controller' => 'Students', 'action' => 'addNationalID', )); ?>
															<?php //echo $this->Html->link('Add National ID','#',array('data-animation'=>"fade",'data-reveal-id'=>'myModalAdd','data-reveal-ajax'=>'/students/add_student_national_id/'.$student_academic_profile['BasicInfo']['Student']['id'])); ?>
															<?php
														} */ ?>
													</td>
													<td></td>
												</tr>
												<?php
												if ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR && !empty($student_academic_profile['BasicInfo']['Student']['region_id'])) { 
													if (!empty($student_academic_profile['BasicInfo']['Student']['country_id']) && $student_academic_profile['BasicInfo']['Student']['country_id'] != COUNTRY_ID_OF_ETHIOPIA) { ?>
														<tr>
															<td style="padding-left:30px;">Country: <?= $student_academic_profile['BasicInfo']['Country']['name']; ?></td>
															<td></td>
														</tr>
														<?php
													} ?>
													<tr>
														<td style="padding-left:30px;">Region: <?= $student_academic_profile['BasicInfo']['Region']['name']; ?></td>
														<td></td>
													</tr>
													<?php
												}

												$prevSection = array();
												$movedOrDeletedSectionsFromRegistration = array();
												$sectionLess = true;
												$section_ids_with_reg = array();
												//$graduated = $student_academic_profile['BasicInfo']['Student']['graduated'];
												//debug($graduated);

												if (isset($student_academic_profile['BasicInfo']['Student']['id'])) {
													$section_ids_with_reg = $movedOrDeletedSectionsFromRegistration = ClassRegistry::init('CourseRegistration')->getAllSectionIdsForStudentFromCourseRegistrations($student_academic_profile['BasicInfo']['Student']['id']);
												} 
												
												//debug($section_ids_with_reg);

												if(isset($studentAttendedSections) && !empty($studentAttendedSections)){ ?>
													<tr>
														<td colspan=2><strong>Attended Sections</strong></td>
													</tr>
													<?php
													foreach ($studentAttendedSections as $index => $student_copys) {
														if ($prevSection != $student_copys['Section']) { ?>
															<tr>
																<td style="padding-left: 30px;" class="vcenter">
																	<?php
																	if (!empty($student_copys['YearLevel']['name'])) {
																		echo '<span id="copySECTION_' . $index . '" class="copy-text" data-clipboard-target="#copySECTION_' . $index . '" >'. $student_copys['Section']['name'] . '</span> (' . $student_copys['YearLevel']['name'] . ', ' . (!empty($student_copys['Section']['academicyear']) ? '' . $student_copys['Section']['academicyear'] . ')' : '');
																	} else {
																		echo '<span id="copySECTION_' . $index . '" class="copy-text" data-clipboard-target="#copySECTION_' . $index . '" >'.$student_copys['Section']['name'] . '</span> (Pre/1st, ' . (!empty($student_copys['Section']['academicyear']) ? '' . $student_copys['Section']['academicyear'] . ')' : '');
																	}
																	echo $student_copys['Section']['archive'] == true ? ' Previous' : ' <b>Current</b>';
																	?>
																</td>
																<td style="padding-left: 30px;" class="vcenter">
																	<?php
																	if ($student_copys['Section']['archive'] == false) {
																		$sectionLess = false;
																	}

																	if (!$graduated && in_array($student_copys['Section']['id'], $section_ids_with_reg) && ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR || $this->Session->read('Auth.User')['role_id'] == ROLE_COLLEGE || $this->Session->read('Auth.User')['role_id'] == ROLE_DEPARTMENT || $this->Session->read('Auth.User')['role_id'] == ROLE_SYSADMIN)) {
																		if (!$student_copys['Section']['archive']) {
																			echo $this->Html->link(__('Archieve'), array('controller' => 'Sections', 'action' => 'archieveUnarchieveStudentSection', $student_copys['Section']['id'], $student_academic_profile['BasicInfo']['Student']['id'], 1), null, sprintf(__('Are you sure you want to archive {0} from {1} section? The current section will be lebeled as (Previous) and student will be section-less so that you can add him to new setion.'), $student_academic_profile['BasicInfo']['Student']['full_name']. '('. $student_academic_profile['BasicInfo']['Student']['studentnumber'].')', $student_copys['Section']['name']));
																			echo '<br/>';
																		}
																	}

																	if (!$graduated && !in_array($student_copys['Section']['id'], $section_ids_with_reg) && ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR || $this->Session->read('Auth.User')['role_id'] == ROLE_COLLEGE || $this->Session->read('Auth.User')['role_id'] == ROLE_DEPARTMENT || $this->Session->read('Auth.User')['role_id'] == ROLE_SYSADMIN)) {
																		if (!$student_copys['Section']['archive']) {
																			echo $this->Html->link('Move', '#', array('data-animation' => "fade", 'data-reveal-id' => 'myModalAdd', 'data-reveal-ajax' => '/sections/move_student_section_to_new/' . $student_copys['Section']['id'] . '/' . $student_academic_profile['BasicInfo']['Student']['id']));
																			echo '<br/>';
																		}
																		echo $this->Html->link(__('Delete'), array('controller' => 'Sections', 'action' => 'deleteStudent', $student_copys['Section']['id'], str_replace('/', '-', $student_academic_profile['BasicInfo']['Student']['studentnumber'])), null, sprintf(__('Are you sure you want to delete {0} from {1} section?'), $student_academic_profile['BasicInfo']['Student']['full_name']. '('. $student_academic_profile['BasicInfo']['Student']['studentnumber'].')', $student_copys['Section']['name']));
																		// old implementation
																		//echo $this->Html->link(__('Delete'), array('controller'=>'Sections', 'action' => 'deleteStudentforThisSection', $student_copys['Section']['id'], str_replace('/','-',$student_academic_profile['BasicInfo']['Student']['studentnumber'])),null, sprintf(__('Are you sure you want to delete {0}?'),$student_academic_profile['BasicInfo']['Student']['full_name'], str_replace('/', '-',$student_academic_profile['BasicInfo']['Student']['studentnumber'])));
																	}
																	//echo '<br/>';
																	//echo $this->Html->link('Upgrade','#',array('data-animation'=>"fade",'data-reveal-id'=>'myModalAdd','data-reveal-ajax'=>'/sections/upgrade_selected_student_section/'. $student_copys['Section']['id'].'/'.$student_academic_profile['BasicInfo']['Student']['id']));
																	?>
																</td>
															</tr>
															<?php
															$prevSection = $student_copys['Section'];
														} 

														if (!empty($section_ids_with_reg) && in_array($student_copys['Section']['id'], $section_ids_with_reg)) {
															if (!empty($movedOrDeletedSectionsFromRegistration)) {
																foreach ($movedOrDeletedSectionsFromRegistration as $sec_key => $sec_value) {
																	if ($sec_value == $student_copys['Section']['id']) {
																		unset($movedOrDeletedSectionsFromRegistration[$sec_key]);
																	}
																	//debug($movedOrDeletedSectionsFromRegistration);
																}
															}
														}
													}
												}

												if (!empty($movedOrDeletedSectionsFromRegistration) && isset($studentAttendedSections) && !empty($studentAttendedSections)) {
													foreach ($movedOrDeletedSectionsFromRegistration as $seckey => $sevalue) { 
														
														$deletedSection = ClassRegistry::init('Section')->find('first', array('conditions' => array('Section.id' => $sevalue), 'contain' => array('YearLevel')));

														if (!empty($deletedSection)) { ?>
															<tr>
																<td style="padding-left: 30px;" class="vcenter rejected">
																	<?php
																	if (!empty($deletedSection['YearLevel']['name'])) {
																		echo $deletedSection['Section']['name'] . ' (' . $deletedSection['YearLevel']['name'] . ', ' . (!empty($deletedSection['Section']['academicyear']) ? '' . $deletedSection['Section']['academicyear'] . ')' : '');
																	} else {
																		echo $deletedSection['Section']['name'] . ' (Pre/1st, ' . (!empty($deletedSection['Section']['academicyear']) ? '' . $deletedSection['Section']['academicyear'] . ')' : '');
																	}
																	$archieve_value = ($deletedSection['Section']['archive'] == true ?  1 : 0);
																	?>
																</td>
																
																<td style="padding-left: 30px;" class="vcenter">
																	<?php
																	if (!$graduated && ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR || $this->Session->read('Auth.User')['role_id'] == ROLE_COLLEGE || $this->Session->read('Auth.User')['role_id'] == ROLE_DEPARTMENT || $this->Session->read('Auth.User')['role_id'] == ROLE_SYSADMIN)) {
																		echo $this->Html->link(__('Restore'), array('controller' => 'Sections', 'action' => 'restore_student_section', $deletedSection['Section']['id'], $student_academic_profile['BasicInfo']['Student']['id'], $archieve_value), null, sprintf(__('You are about to restore %s as ' . ($archieve_value ? 'previous' : 'current'). ' section for %s? '.($archieve_value ? '' : ' Restoring this section as a current section will archieve any other existing current sections of the student as previous. Are you sure you want to proceed?'). ''), $deletedSection['Section']['name'], $student_academic_profile['BasicInfo']['Student']['full_name']. '('. $student_academic_profile['BasicInfo']['Student']['studentnumber'].')'));
																	} ?>
																</td>
															</tr>
															<?php
														}
													}
												}

												if ($this->Session->read('Auth.User')['role_id'] == ROLE_DEPARTMENT || $this->Session->read('Auth.User')['role_id'] == ROLE_COLLEGE  || $this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR || ($this->Session->read('Auth.User')['role_id'] == ROLE_SYSADMIN && $this->Session->read('Auth.User')['is_admin'] == 1)) {
													if ($sectionLess && !$graduated && (!$isTheStudentDismissed || $isTheStudentReadmitted) /* && isset($studentAttendedSections) && !empty($studentAttendedSections) */ && ((!empty($student_academic_profile['BasicInfo']['Student']['department_id']) && !empty($student_academic_profile['BasicInfo']['Student']['curriculum_id'])) || (empty($student_academic_profile['BasicInfo']['Student']['department_id']) && empty($student_academic_profile['BasicInfo']['Student']['curriculum_id'])))) { ?>
														<tr>
															<td style="padding-left: 30px;" class="vcenter"><?= $this->Html->link('Add Student To Section', '#', array('data-animation' => "fade", 'data-reveal-id' => 'myModalAdd', 'data-reveal-ajax' => '/sections/add_student_to_section/' . $student_academic_profile['BasicInfo']['Student']['id'])); ?></td>
															<td></td>
														</tr>
														<?php
													}
												}

												if ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR && !$graduated && isset($studentAttendedSections) && !empty($studentAttendedSections)) {
													if ((!$isTheStudentDismissed || $isTheStudentReadmitted) && !empty($section_ids_with_reg)) { ?>
														<tr>
															<td style="padding-left: 30px;" class="vcenter"><?= $this->Html->link('Manage Missing Registration & NG', '#', array('data-animation' => "fade", 'data-reveal-id' => 'myModalReg', 'data-reveal-ajax' => '/courseRegistrations/manage_missing_registration/' . $student_academic_profile['BasicInfo']['Student']['id'])); ?></td>
															<td></td>
														</tr>
														<?php
													}
													if (isset($student_academic_profile['BasicInfo']['Student']['department_id']) && !is_null($student_academic_profile['BasicInfo']['Student']['department_id']) && (!$isTheStudentDismissed || $isTheStudentReadmitted) && $student_academic_profile['BasicInfo']['Student']['program_id'] != PROGRAM_REMEDIAL) { ?>
														<tr>
															<td style="padding-left: 30px;" class="vcenter"><?= $this->Html->link('Add Transferred Courses from other University', '#', array('data-animation' => "fade", 'data-reveal-id' => 'myModalAdd', 'data-reveal-ajax' => '/courseExemptions/add_student_exempted_course/' . $student_academic_profile['BasicInfo']['Student']['id'])); ?></td>
															<td></td>
														</tr>
														<?php
													} 
													if (($isTheStudentDismissed || $isTheStudentReadmitted) && $student_academic_profile['BasicInfo']['Student']['program_id'] != PROGRAM_REMEDIAL) { ?>
														<tr>
															<td style="padding-left: 30px;" class="vcenter"><?= $this->Html->link('Maintain Readmission', '#', array('data-animation' => "fade", 'data-reveal-id' => 'myModalAdd', 'data-reveal-ajax' => '/readmissions/ajax_readmitted_year/' . $student_academic_profile['BasicInfo']['Student']['id'])); ?></td>
															<td></td>
														</tr>
														<?php
													}
												}

												if ($graduated) {

													$checkInGraduateList = ClassRegistry::init('GraduateList')->find('first', array(
														'conditions' => array(
															'GraduateList.student_id' => $student_academic_profile['BasicInfo']['Student']['id'],
														),
														'recursive' => -1,
													));

													if (!empty($checkInGraduateList)) {
														if ($this->Session->read('Auth.User')['role_id'] == ROLE_SYSADMIN || ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR && $this->Session->read('Auth.User')['is_admin'] == 1)) { ?>
															<tr>
																<td colspan=2>
																	<div class="warning-box warning-message" style="font-family: 'Times New Roman', Times, serif; font-weight: bold;">
																		<span style='margin-right: 15px;'></span> You are accessing graduated student profile<br/> 
																		<p>
																			<ul>
																				<li>Date Graduated: <b><?= $this->Time->format("M j, Y", $checkInGraduateList['GraduateList']['graduate_date'], NULL, NULL); ?></b></li>
																				<li>Minute No: <b><?= $checkInGraduateList['GraduateList']['minute_number']; ?></b></li>
																				<li>Date Added: <b><?= $this->Time->format("M j, Y g:i:s A", $checkInGraduateList['GraduateList']['created'], NULL, NULL); ?></b></li>
																			</ul>
																		</p>

																		<?php

																		$graduation_date = $checkInGraduateList['GraduateList']['graduate_date'];

																		if (Configure::read('Calendar.graduateApprovalInPast')) {
																			//debug(Configure::read('Calendar.graduateApprovalInPast'));
																			$days_back = Configure::read('Calendar.graduateApprovalInPast') * 365;
																		} else {
																			$days_back = 1 * 365;
																		}
																		//debug($days_back);

																		$minimum_allowed_graduation_date_to_delete = date('Y-m-d', strtotime("-" . $days_back . " day "));
																		//debug($minimum_allowed_graduation_date_to_delete);

																		if ($minimum_allowed_graduation_date_to_delete < $graduation_date) {
																			echo $this->Html->link(__('Delete Student from Graduate List', true), array('action' => 'delete_student_from_graduate_list_for_correction', $student_academic_profile['BasicInfo']['Student']['id']), array('confirm' => __('Are you sure you want to delete "{0} ({1})" from both Graduate and Senate Lists? Deleting the student from these lists requires recording the minute number and graduation dates for adding ' . (strcasecmp(trim($student_academic_profile['BasicInfo']['Student']['gender']), 'male') == 0 ?  'him' : 'her'). ' back to the lists if you are deleting the student for some sort of correction. Are you sure you want proceed?', $student_academic_profile['BasicInfo']['Student']['full_name'], $student_academic_profile['BasicInfo']['Student']['studentnumber'])));
																		} ?>
																	</div>
																</td>
															</tr>
															<?php
														} else { ?>
															<tr>
																<td colspan=2>
																	<div class="warning-box warning-message" style="font-family: 'Times New Roman', Times, serif; font-weight: bold;">
																		<span style='margin-right: 15px;'></span> You are accessing graduated student profile<br/> 
																		<p>
																			<ul>
																				<li>Date Graduated: <b><?= $this->Time->format("M j, Y", $checkInGraduateList['GraduateList']['graduate_date'], NULL, NULL); ?></b></li>
																				<!-- <li>Minute No: <b><?php //echo $checkInGraduateList['GraduateList']['minute_number']; ?></b></li>
																				<li>Date Added: <b><?php //echo $this->Time->format("M j, Y g:i:s A", $checkInGraduateList['GraduateList']['created'], NULL, NULL); ?></b></li> -->
																			</ul>
																		</p>
																	</div>
																</td>
															</tr>
															<?php
														}
													} else { ?>
														<tr>
															<td colspan=2>
																<div class="error-box error-message" style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style='margin-right: 15px;'></span>There is a student profile error, please contact system administrator for a fix. </div>
															</td>
														</tr>
														<?php
													}
												} ?> 
											</tbody>
										</table>
									</div>
				
									<div class="large-6 columns" style="padding: 0.7rem;">
										<table cellspacing="0" cellpading="0" class="table">
											<tbody>
												<tr>
													<td><strong>Student Photo</strong></td>
												</tr>
												<?php
												if (isset($student_academic_profile['BasicInfo']['Attachment']) && !empty($student_academic_profile['BasicInfo']['Attachment'])) {
													/* foreach ($student_academic_profile['BasicInfo']['Attachment'] as $ak => $av) {
														if (!empty($av['dirname']) && !empty($av['basename'])) { */ ?>
															<?php // echo $this->Media->embed($this->Media->file('s'.DS.$av['dirname'].DS.$av['basename'])); ?>
															<tr>
																<td class="vcenter" style="background-color: white;">
																	<?php
																	if ($this->Media->file($student_academic_profile['BasicInfo']['Attachment'][0]['dirname'] . DS . $student_academic_profile['BasicInfo']['Attachment'][0]['basename'])) {
																		//echo $this->Media->embed($this->Media->file($av['dirname'] . DS . $av['basename']), array('width' => '144', 'class' => 'profile-picture')); 
																	 	echo $this->Media->embed($this->Media->file($student_academic_profile['BasicInfo']['Attachment'][0]['dirname'] . DS . $student_academic_profile['BasicInfo']['Attachment'][0]['basename']), array('width' => '144', 'class' => 'profile-picture')); 
																	} else { ?>
																		<!-- <span class="rejected">Profile deleted or not found</span> <br> -->
																		<img src="/img/noimage.jpg" width="144" class="profile-picture">
																		<?php
																	} ?>
																</td>
															</tr>
															<?php
														/* }
													} */
												} else { ?>
													<tr>
														<td class="vcenter" style="background-color: white;"><img src="/img/noimage.jpg" width="144" class="profile-picture"></td>
													</tr>
													<?php
												} ?>
												<tr>
													<td><strong>Access Information</strong></td>
												</tr>
												<?php
												if (isset($student_academic_profile['BasicInfo']['User']['username'])) { ?>
													<tr>
														<td style="padding-left:30px;">Username: <?= $student_academic_profile['BasicInfo']['User']['username']; ?></td>
													</tr>
													<tr>
														<td style="padding-left:30px;">Last Login: <?= (($student_academic_profile['BasicInfo']['User']['last_login'] == '' ||  $student_academic_profile['BasicInfo']['User']['last_login'] == '0000-00-00 00:00:00' || is_null($student_academic_profile['BasicInfo']['User']['last_login'])) ? '<span class="rejected">Never loggedin</span>' : $this->Time->timeAgoInWords($student_academic_profile['BasicInfo']['User']['last_login'], array('format' => 'M j, Y', 'end' => '1 year', 'accuracy' => array('month' => 'month')))); ?></td>
													</tr>
													<tr>
														<td style="padding-left:30px;">Last Password Change: <?= (($student_academic_profile['BasicInfo']['User']['last_password_change_date'] == '' ||  $student_academic_profile['BasicInfo']['User']['last_password_change_date'] == '0000-00-00 00:00:00' || is_null($student_academic_profile['BasicInfo']['User']['last_password_change_date'])) ? '<span class="rejected">Never Changed</span>' : $this->Time->timeAgoInWords($student_academic_profile['BasicInfo']['User']['last_password_change_date'], array('format' => 'M j, Y', 'end' => '1 year', 'accuracy' => array('month' => 'month')))); ?></td>
													</tr>
													<tr>
														<td style="padding-left:30px;">Failed Logins: <?= (isset($student_academic_profile['BasicInfo']['User']['failed_login']) && $student_academic_profile['BasicInfo']['User']['failed_login'] != 0  ?  $student_academic_profile['BasicInfo']['User']['failed_login'] : '---'); ?></td>
													</tr>
													<tr>
														<td style="padding-left:30px;">Ecardnumber: <?= (isset($student_academic_profile['BasicInfo']['Student']['ecardnumber']) && !empty($student_academic_profile['BasicInfo']['Student']['ecardnumber']) ? $student_academic_profile['BasicInfo']['Student']['ecardnumber'] : '---'); ?></td>
													</tr>
													<?php
													if (($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR || $this->Session->read('Auth.User')['role_id'] == ROLE_SYSADMIN) && !$graduated) { ?>
														<tr>
															<td class="center"><?= ($student_academic_profile['BasicInfo']['User']['active'] == 1 ? $this->Html->link(__('Disable System Access', true), array('action' => 'activate_deactivate_profile', $student_academic_profile['BasicInfo']['Student']['id']), array('confirm' => __('Are you sure you want revoke system access of {0} ({1})? Disabling System Access will prevent the student from logging in to ' . Configure::read('ApplicationShortName'). ' and perform some basic task like Registring for courses, adding courses, viewing results and evaluating instructors etc. Are you sure you want proceed?', $student_academic_profile['BasicInfo']['Student']['full_name'], $student_academic_profile['BasicInfo']['Student']['studentnumber']))) : $this->Html->link(__('Enable System Access', true), array('action' => 'activate_deactivate_profile', $student_academic_profile['BasicInfo']['Student']['id']), array('confirm' => __('Are you sure you want grant back system access of {0} ({1})? Enabling System Access will allow the student to logging in to ' . Configure::read('ApplicationShortName'). ' as before and perform some basic task like Registring for courses, adding courses, viewing results and evaluating instructors etc. Are you sure you want proceed?', $student_academic_profile['BasicInfo']['Student']['full_name'], $student_academic_profile['BasicInfo']['Student']['studentnumber'])))); ?></td>
														</tr>
														<?php
													} 
												} else { ?>
													<tr>
														<td style="padding-left:30px;" class="on-process">Username and password is not issued by the <?= (!is_null($student_academic_profile['BasicInfo']['Student']['department_id']) ? (isset($student_academic_profile['BasicInfo']['Department']['type']) && !empty($student_academic_profile['BasicInfo']['Department']['type']) ? $student_academic_profile['BasicInfo']['Department']['type'] : 'Department') : ((isset($student_academic_profile['BasicInfo']['College']['type']) && !empty($student_academic_profile['BasicInfo']['College']['type']) ? $student_academic_profile['BasicInfo']['College']['type'] : 'College'))); ?></td>
													</tr>
													<?php
												}

												$preEngineeringColleges = Configure::read('preengineering_college_ids');

												if ($student_academic_profile['BasicInfo']['Student']['program_id'] == PROGRAM_REMEDIAL) {
													$stream = 'Remedial Program';
												} else if (isset($student_academic_profile['BasicInfo']['College']['stream']) && $student_academic_profile['BasicInfo']['College']['stream'] == STREAM_NATURAL && in_array($student_academic_profile['BasicInfo']['Student']['college_id'], $preEngineeringColleges)) {
													$stream = 'Freshman - Pre Engineering';
												} else if (isset($student_academic_profile['BasicInfo']['College']['stream']) && $student_academic_profile['BasicInfo']['College']['stream'] == STREAM_NATURAL) {
													$stream = 'Freshman - Natural Stream';
												} else if (isset($student_academic_profile['BasicInfo']['College']['stream']) && $student_academic_profile['BasicInfo']['College']['stream'] == STREAM_SOCIAL) {
													$stream = 'Freshman - Social Stream';
												} else {
													$stream = '---';
												} ?>
												
												<tr>
													<td><strong>Classification of Admission</strong></td>
												</tr>
												<tr>
													<td style="padding-left:30px;">Program: <?= $student_academic_profile['BasicInfo']['Program']['name']; ?></td>
												</tr>
												<tr>
													<td style="padding-left:30px;">Program Type: <?= $student_academic_profile['BasicInfo']['ProgramType']['name']; ?></td>
												</tr>
												<tr>
													<td style="padding-left:30px;"><?= (isset($student_academic_profile['BasicInfo']['College']['type']) && !empty($student_academic_profile['BasicInfo']['College']['type']) ? $student_academic_profile['BasicInfo']['College']['type'].': ' : 'College: '); ?><span id="copySTCOL" class="copy-text" data-clipboard-target="#copySTCOL" title="Click here once to copy text"><?= $student_academic_profile['BasicInfo']['College']['name']; ?></span></td>
												</tr>
												<tr>
													<td style="padding-left:30px;"><?= (isset($student_academic_profile['BasicInfo']['Department']['type']) && !empty($student_academic_profile['BasicInfo']['Department']['type']) ? $student_academic_profile['BasicInfo']['Department']['type'].': ' : 'Department: '); ?><span id="copySTDEPT" class="copy-text" data-clipboard-target="#copySTDEPT" title="Click here once to copy text"><?= (isset($student_academic_profile['BasicInfo']['Student']['department_id']) && !is_null($student_academic_profile['BasicInfo']['Student']['department_id']) ? $student_academic_profile['BasicInfo']['Department']['name'] : $stream); ?></span></td>
												</tr>
												<tr>
													<td style="padding-left:30px;">Admission Year: <?= (isset($student_academic_profile['BasicInfo']['Student']['academicyear']) ? $student_academic_profile['BasicInfo']['Student']['academicyear'] : '---'); ?></td>
												</tr>
												<tr>
													<td style="padding-left:30px;">Date Admitted: <?= $this->Time->format("M j, Y", $student_academic_profile['BasicInfo']['Student']['admissionyear'], NULL, NULL); ?></td>
												</tr>
												<tr>
													<td class="center"><?= ($student_academic_profile['BasicInfo']['Student']['admissionyear'] < '2019-09-20' ? $this->Html->link('View Preferences', '#', array('data-animation' => "fade", 'data-reveal-id' => 'myModalPref', 'data-reveal-ajax' => '/preferences/getStudentPreference/' . $student_academic_profile['BasicInfo']['Student']['accepted_student_id'])) : $this->Html->link('View Preferences', '#', array('data-animation' => "fade", 'data-reveal-id' => 'myModalPref', 'data-reveal-ajax' => '/placement_preferences/getStudentPreference/' . $student_academic_profile['BasicInfo']['Student']['id']))); ?></td>
												</tr>
											</tbody>
										</table>
									</div>
								</td>
							</tr>
						</table>
					<!-- </div> --> 
					<!-- end add tab div -->
					<?php
				} ?>
			</div>

			<div class="content" id="exemption">
				<!-- <div class="AddTab"> -->
					<?php
					if (!empty($student_academic_profile['CourseExemption'])) { ?>
						<div style="overflow-x:auto;">
							<table cellpadding="0" cellspacing="0" class="table">
								<thead>
									<?php
									if (!empty($student_academic_profile['CourseExemption'][0])) { ?>
										<tr>
											<th colspan="6">From: <?= strtoupper($student_academic_profile['CourseExemption'][0]['transfer_from']) ; ?>
										</tr>
										<?php
									} else { ?>
										<tr>
											<th colspan="6">Course Transfered University is not added, <a href="#"> Add University </a>
										</tr>
										<?php
									} ?>
									<tr>
										<th class="center">#</th>
										<th class="vcenter">Taken Course</th>
										<th class="center">Cr.</th>
										<th class="center">Gr.</th>
										<th class="vcenter">Exempted By</th>
										<th class="center">Cr.</th>
									</tr>
								</thead>
								<tbody>
									<?php
									$exemtcnt = 1;
									$exempted_course_sum = 0;
									foreach ($student_academic_profile['CourseExemption'] as $in => $value) { ?> 
										<tr>
											<td class="center"><?= $exemtcnt++; ?></td>
											<td class="vcenter"><?= $value['taken_course_title'] . ' (' . $value['taken_course_code'] . ')'; ?></td>
											<td class="center"><?= $value['course_taken_credit']; ?></td>
											<td class="center"><?= (isset($value['grade']) && !empty($value['grade']) ? ($value['grade']) : ''); ?></td>
											<td class="venter"><?= $value['Course']['course_title'] . ' (' . $value['Course']['course_code'] . ')'; ?></td>
											<td class="center"><?= $value['Course']['credit']; ?></td>
										</tr>
										<?php
										$exempted_course_sum += $value['Course']['credit'];
									} ?>
									<tr>
										<td colspan="5" style="text-align:right;font-weight: bold">Total:</td><td style="text-align:center;font-weight: bold"><?= $exempted_course_sum; ?></td>
									</tr>
								</tbody>
							</table>
						</div>
						<?php
					} else { ?>
						<div class="info-box info-message" style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style="margin-right: 15px;"></span>There is no record of course exemption for the selected student.</div>
						<?php
					} ?>
				<!-- </div> -->
			</div>

			<div class="content" id="registration">
				<!-- <div class="AddTab"> -->
					<?php
					if (!empty($student_academic_profile['Course Registered'])) { ?>
						<div style="overflow-x:auto;">
							<table cellpadding="0" cellspacing="0" class="table">
								<thead>
									<tr>
										<td class="center">#</td>
										<td class="vcenter">Course</td>
										<td class="center"><?= $credit_type; ?></td>
										<td class="center">ACY</td>
										<td class="center">Semester</td>
									</tr>
								</thead>
								<tbody>
									<?php
									$total_registered_credits = 0;
									$regcnt = 1;
									foreach ($student_academic_profile['Course Registered'] as $in => $value) { ?>
										<tr>
											<td class="center"><?= $regcnt++; ?></td>
											<td class="vcenter"><?= $value['course_title']; ?></td>
											<td class="center"><?= $value['credit']; ?></td>
											<td class="center"><?= $value['acadamic_year']; ?></td>
											<td class="center"><?= $value['semester']; ?></td>
										</tr>
										<?php
										$total_registered_credits += $value['credit'];
									} ?>
								</tbody>
								<tfoot>
									<tr>
										<td>&nbsp;</td>
										<td style="text-align: right; vertical-align: middle;">Total</td>
										<td class="center"><?= $total_registered_credits; ?></td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
									</tr>
								</tfoot>
							</table>
						</div>
						<?php
					} else { ?>
						<div class="info-box info-message" style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style="margin-right: 15px;"></span>There is no record of course registration for the selected student.</div>
						<?php
					} ?>
				<!-- </div> -->
			</div>

			<div class="content" id="addcourses">
				<!-- <div class="AddTab"> -->
					<?php
					if (!empty($student_academic_profile['Course Added'])) { ?>
						<div style="overflow-x:auto;">
							<table cellpadding="0" cellspacing="0" class="table">
								<thead>
									<tr>
										<td class="center">#</td>
										<td class="vcenter">Course</td>
										<td class="center"><?= $credit_type; ?></td>
										<td class="center">ACY</td>
										<td class="center">Semester</td>
										<td class="center">Section</td>
									</tr>
								</thead>
								<tbody>
									<?php
									$total_added_credits = 0;
									$addcnt = 1;
									foreach ($student_academic_profile['Course Added'] as $in => $value) { ?>
										<tr>
											<td class="center"><?= $addcnt++; ?></td>
											<td class="vcenter"><?= $value['course_title']; ?></td>
											<td class="center"><?= $value['credit']; ?></td>
											<td class="center"><?= $value['acadamic_year']; ?></td>
											<td class="center"><?= $value['semester']; ?></td>
											<td class="vcenter"><?= $value['sectionName'] . ' (' . $value['curriculumName'] . ')'; ?></td>
										</tr>
										<?php
										$total_added_credits += $value['credit'];
									} ?>
								</tbody>
								<tfoot>
									<tr>
										<td>&nbsp;</td>
										<td style="text-align: right; vertical-align: middle;">Total</td>
										<td class="center"><?= $total_added_credits; ?></td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
									</tr>
								</tfoot>
							</table>
						</div>
						<?php
					} else { ?>
						<div class="info-box info-message" style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style="margin-right: 15px;"></span>There is no record of course add for the selected student.</div>
						<?php
					} ?>
				<!-- </div> -->
			</div>

			<div class="content" id="dropcourses">
				<!-- <div class="AddTab"> -->
					<?php
					if (!empty($student_academic_profile['Course Dropped'])) { ?>
						<div style="overflow-x:auto;">
							<table cellpadding="0" cellspacing="0" class="table">
								<thead>
									<tr>
										<td class="center">#</td>
										<td class="vcenter">Course</td>
										<td class="center"><?= $credit_type; ?></td>
										<td class="center">ACY</td>
										<td class="center">Semester</td>
									</tr>
								</thead>
								<tbody>
									<?php
									$total_dropped_credits = 0;
									$dropcnt = 1;
									foreach ($student_academic_profile['Course Dropped'] as $in => $value) { ?>
										<tr>
											<td class="center"><?= $dropcnt++; ?></td>
											<td class="venter"><?= $value['course_title']; ?></td>
											<td class="center"><?= $value['credit']; ?></td>
											<td class="center"><?= $value['acadamic_year']; ?></td>
											<td class="center"><?= $value['semester']; ?></td>
										</tr>
										<?php
										$total_dropped_credits += $value['credit'];
									} ?>
								</tbody>
								<tfoot>
									<tr>
										<td>&nbsp;</td>
										<td style="text-align: right; vertical-align: middle;">Total</td>
										<td class="center"><?= $total_dropped_credits; ?></td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
									</tr>
								</tfoot>
							</table>
						</div>
						<?php
					} else { ?>
						<div class="info-box info-message" style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style="margin-right: 15px;"></span>There is no record of course drop for the selected student.</div>
						<?php
					} ?>
				<!-- </div> -->
			</div>

			<div class="content" id="examresults">
				<!-- <div class="AddTab"> -->
					<?php
					if (!empty($student_academic_profile['Course Registered']) || !empty($student_academic_profile['Course Added']) || !empty($student_copys)) { ?>
						<?= $this->element('grade_report_organized_by_ac_semester'); ?>
						<?php
					} else { ?>
						<div class="info-box info-message" style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style="margin-right: 15px;"></span>There is no exam result record for the selected student.</div>
						<?php
					} ?>
				<!-- </div> -->
			</div>

			<div class="content" id="curriculum">
				<!-- <div class="AddTab"> -->
					<?php
					if (!empty($student_academic_profile['Curriculum']['id'])) { ?>
						<div style="overflow-x:auto;">
							<fieldset style="padding-bottom: 10px;padding-top: 10px;">
								<legend>&nbsp;&nbsp; <span class="fs14 text-gray">Curriculum Deatails</span> &nbsp;&nbsp;</legend>
								<span class="fs15 text-black">
									<strong class="fs14 text-gray">Curriculum Name:</strong> &nbsp;<?= $student_academic_profile['Curriculum']['name']; ?> <br>
									<strong class="fs14 text-gray">English Degree Nomenclature:</strong> &nbsp;<?= $student_academic_profile['Curriculum']['english_degree_nomenclature']; ?> <br>
									<strong class="fs14 text-gray">Amharic Degree Nomenclature:</strong> &nbsp;<?= $student_academic_profile['Curriculum']['amharic_degree_nomenclature']; ?> <br>
									<strong class="fs14 text-gray">Program:</strong> &nbsp;<?= $student_academic_profile['Curriculum']['Program']['name']; ?> <br>
									<strong class="fs14 text-gray">Specialization:</strong> &nbsp;<?= $student_academic_profile['Curriculum']['specialization_english_degree_nomenclature']; ?> <br>
									<strong class="fs14 text-gray">Year Introduced:</strong> &nbsp;<?= $student_academic_profile['Curriculum']['year_introduced']; ?> <br>
									<strong class="fs14 text-gray">Program:</strong> &nbsp;<?= $student_academic_profile['Curriculum']['Program']['name']; ?> <br>
									<strong class="fs14 text-gray">Type Of Credit:</strong> &nbsp;<?= $student_academic_profile['Curriculum']['type_credit']; ?> <br>
									<strong class="fs14 text-gray">Minimum <?= $student_academic_profile['Curriculum']['type_credit']; ?> for Graduation:</strong> &nbsp;<?= $student_academic_profile['Curriculum']['minimum_credit_points']; ?> <br>
								</span>
							</fieldset>
						</div>
						<?= $this->element('curriculum_organized_semester_courses'); ?>
						<?php
					} else { ?>
						<div class="info-box info-message" style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style="margin-right: 15px;"></span>The student is not yet attached to any curriculum.</div>
						<?php
					} ?>
				<!-- </div> -->
			</div>

			<div class="content" id="Billing">
				<!-- <div class="AddTab"> -->
					<?= $this->element('billing'); ?>
				<!-- </div> -->
			</div>

			<?php
			if (SHOW_OTP_TAB_ON_STUDENT_ACADEMIC_PROFILE_FOR_STUDENTS == 1 && ($this->Session->read('Auth.User')['role_id'] == ROLE_STUDENT || $this->Session->read('Auth.User')['role_id'] == ROLE_SYSADMIN || $this->Session->read('Auth.User')['role_id'] == ROLE_DEPARTMENT || $this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR) && isset($otps) && !empty($otps)) { ?>
				<div class="content" id="OTP">
					<div class="row">
						<!-- <div class="info-box info-message" style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style="margin-right: 15px;"></span>One time password is only valid until you change the passoword on the specified web address, once changed, you will use the new password you set for the site.</div> -->
						<?php
						$otp_services_option = Configure::read('otp_services_option');
						foreach ($otps as $key => $otp) { ?>
							<div class="large-6 columns">
								<fieldset style="padding-bottom: 15px; padding-top: 5px;">
									<legend>&nbsp;&nbsp; <span class="fs15 text-black">One Time Password for <?= $otp_services_option[$otp['Otp']['service']]; ?></span> &nbsp;&nbsp;</legend>
									<div class="row" style="line-height: 2;">
										<div class="large-12 columns">
											<br/>
											<span class="fs15 text-black">Username: <strong id="copyOTPusername<?= $key ?>" class="copy-text" data-clipboard-target="#copyOTPusername<?= $key ?>" title="Click here once to copy <?= $otp_services_option[$otp['Otp']['service']] ?> OTP username"><?= $otp['Otp']['username']; ?></strong></span><br/>
										</div>
										<div class="large-12 columns">
											<span class="fs15 text-black">Password: <strong id="copyOTPpassword<?= $key ?>" class="copy-text" data-clipboard-target="#copyOTPpassword<?= $key ?>" title="Click here once to copy <?= $otp_services_option[$otp['Otp']['service']] ?> OTP password"><?= $otp['Otp']['password']; ?></strong></span><br/>
										</div>
										<div class="large-12 columns">
											<hr/>
											<span class="fs15 text-black">Last Updated: &nbsp;<?= $this->Time->timeAgoInWords($otp['Otp']['modified'], array('format' => 'M j, Y', 'end' => '1 year', 'accuracy' => array('month' => 'month'))); ?></span><br/>
										</div>

										<?php
										if (isset($otp['Otp']['exam_center']) && !empty($otp['Otp']['exam_center'])) { ?>
											<div class="large-12 columns">
												<span class="fs15 text-black">Exam Center:  &nbsp;<?= $otp['Otp']['exam_center']; ?></span><br/>
											</div>
											<?php
										}

										if ((isset($otp['Otp']['portal']) && !empty($otp['Otp']['portal'])) || $otp['Otp']['service'] == 'Office365' || $otp['Otp']['service'] == 'Elearning') { ?>
											<div class="large-12 columns">
												<span class="fs15 text-black" ><?= isset($otp['Otp']['portal']) && !empty($otp['Otp']['portal']) ? 'Web URL:  &nbsp;' . $otp['Otp']['portal'] . ' &nbsp; &nbsp; <a href="' . $otp['Otp']['portal'] . '" target="_blank">Open Web Address</a><br/>' : ($otp['Otp']['service'] == 'Office365' ? ('Outlook URL:  &nbsp;' . OTP_OFFICE_365_OUTLOOK_URL . ' &nbsp; &nbsp; <a href="' . OTP_OFFICE_365_OUTLOOK_URL . '" target="_blank">Open Outlook (Email)</a><br/>Office 365 URL:  &nbsp;' . OTP_OFFICE_365_MAIN_URL . ' &nbsp; &nbsp; <a href="' .  OTP_OFFICE_365_MAIN_URL . '" target="_blank">Open Office 365 Main Page</a><br/>') : ('E-Learning Portal:  &nbsp;' . MOODLE_SITE_URL . ' &nbsp; &nbsp; <a href="' . MOODLE_SITE_URL . '" target="_blank">Open E-Learning Portal</a><br/>')); ?></span>
											</div>
											<?php
										}

										if ($otp['Otp']['service'] == 'Elearning') {  ?>
											<div class="large-12 columns">
												<div class="info-box fs15" style="font-family: 'Times New Roman', Times, serif; font-weight: normal; text-align: justify;">If you never changed your SMiS password since you started using the E-learning portal, Please change your SMiS password to change the default initial OTP password (<?= $otp['Otp']['password']; ?>) which was set for <b><?= MOODLE_SITE_URL; ?></b> so that you can use the same password for both sites and secure your E-Learning account from being used by someone else. If you already done that, ignore this notification message.</div> 
											</div>
											<?php
										} else { ?>
											<div class="large-12 columns">
												<div class="info-box fs15" style="font-family: 'Times New Roman', Times, serif; font-weight: normal; text-align: justify;">This One time password (OTP) is only valid until you change the password on the specified web address, once changed, you are required to remember the new password you set for the site and use that password afterwards. If you already done that, ignore this notification message.</div> 
											</div>
											<?php
										} ?>
									</div>
								</fieldset>
							</div>
							<?php
						} ?>
					</div>
				</div>
				<?php
			} ?>
		</div>
	</div>
</div>

<!-- <a class="close-reveal-modal">&#215;</a> -->

<div class="row">
	<div class="large-12 columns">
		<div id="myModalMove" class="reveal-modal" data-reveal>

		</div>

		<div id="myModalAdd" class="reveal-modal" data-reveal>

		</div>

		<div id="myModalReg" class="reveal-modal" data-reveal>

		</div>

		<div id="myModalPref" class="reveal-modal" data-reveal>

		</div>

	</div>
</div>
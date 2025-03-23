<div class="box">
    <div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-check" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR ? __('Confirm Course Adds') : __('Approve Course Adds')); ?></span>
		</div>
	</div>
    <div class="box-body">
        <div class="row">
            <div class="large-12 columns">
                <div style="margin-top: -20px;">
                    <?= $this->Form->create('CourseAdd', array('onSubmit' => 'return checkForm(this);')); ?>
                    <?php
                    if (!empty($coursesss)) { ?>
                        <hr>
                        <h6 class="fs14 text-gray">List of students who submitted Course Add request for approval:</h6>
                        <hr>

                        <h6 id="validation-message_non_selected" class="text-red fs14"></h6>

                        <?php
                        $count = 0;
                        $auto_rejections = 0;
                        foreach ($coursesss as $department_name => $program) {
                            //echo "<span class='fs14'><strong class='text-gray'>Department: </strong><b>" . $department_name . '</b></span><br>';
                            foreach ($program as $program_name => $programType) {
                                //echo "<span class='fs14'><strong class='text-gray'>Program: </strong><b>" . $program_name . '</b></span><br>';
                                foreach ($programType as $program_type_name => $sections) {
                                    //echo  "<span class='fs14'><strong class='text-gray'>Program Type: </strong><b>" . $program_type_name . '</b></span><br>';

                                    $display_button = 0;
                                    $section_count = 0;

                                    foreach ($sections as $section_id => $coursss) {
                                        $section_count++;
                                        //debug($coursss[0]);
                                        if (!empty($coursss)) { ?>
                                            <br>
                                            <div style="overflow-x:auto;">
                                                <table cellpadding="0" cellspacing="0" class="table">
                                                    <thead>
                                                        <tr>
                                                            <td colspan=11 style="vertical-align:middle; border-bottom-width: 2px; border-bottom-style: solid; border-bottom-color: rgb(85, 85, 85); line-height: 1.5;">
                                                                <!-- <br style="line-height: 0.35;"> -->
                                                                <span style="font-size:16px;font-weight:bold; margin-top: 25px;"> 
                                                                    From: <?= $section_id . ' ' . (isset($coursss[0]['PublishedCourse']['Section']['YearLevel']['name'])  ?  ' (' . $coursss[0]['PublishedCourse']['Section']['YearLevel']['name'] .', '. $coursss[0]['PublishedCourse']['academic_year']. ')' : ' (Pre/1st)'); ?>
                                                                </span>
                                                                <br style="line-height: 0.35;">
                                                                <span class="text-gray" style="padding-top: 13px; font-size: 13px; font-weight: bold">
                                                                    <?php //echo (isset($coursss[0]['PublishedCourse']['Section']['Curriculum']['name']) ? ucwords(strtolower($coursss[0]['PublishedCourse']['Section']['Curriculum']['name'])) . ' - ' . $coursss[0]['PublishedCourse']['Section']['Curriculum']['year_introduced'] . ' (' .  (count(explode('ECTS', $coursss[0]['PublishedCourse']['Section']['Curriculum']['type_credit'])) >= 2 ? 'ECTS' : 'Credit') . ') <br style="line-height: 0.35;">' : ''); ?>
                                                                    <?= (isset($coursss[0]['PublishedCourse']['Section']['Department']) && !empty($coursss[0]['PublishedCourse']['Section']['Department']['name']) ? $coursss[0]['PublishedCourse']['Section']['Department']['name'] : $coursss[0]['PublishedCourse']['Section']['College']['name'] . ' - Pre/Freshman'); ?> &nbsp; | &nbsp; 
                                                                    <?= (isset($program_name) ? $program_name :  ''); ?>  &nbsp; | &nbsp; <?= (isset($program_type_name) ? $program_type_name :  ''); ?> <br>
                                                                </span>
                                                                <span class="text-black" style="padding-top: 14px; font-size: 13px; font-weight: bold">
                                                                    <?= (isset($coursss[0]['Student']['Department']['id']) ?  $coursss[0]['Student']['Department']['name'] : $coursss[0]['Student']['College']['name'] . ' - Pre/Freshman'); ?> <br>
                                                                </span>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th class="center">#</th>
                                                            <th class="vcenter">Full Name</th>
                                                            <th class="center">Sex</th>
                                                            <th class="center">Student ID</th>
                                                            <th class="center">Sem</th>
                                                            <th class="center">Load</th>
                                                            <th class="center">Course</th>
                                                            <th class="center"><?= (!isset($coursss[0]['PublishedCourse']['Section']['Curriculum']['id']) ? 'Cr.' : (count(explode('ECTS', $coursss[0]['PublishedCourse']['Section']['Curriculum']['type_credit'])) >= 2 ? 'ECTS' : 'Credit')); ?></th>
                                                            <th class="center">LTL</th>
                                                            <?php
                                                            if ($role_id == ROLE_DEPARTMENT || $role_id == ROLE_COLLEGE) { ?>
                                                                <th class="center">Decision</th>
                                                                <?php
                                                                $options = array('1' => ' Accept', '0' => ' Reject');
                                                                $attributes = array('legend' => false, 'separator' => " "); ?>
                                                                <?php
                                                            }

                                                            if ($role_id == ROLE_REGISTRAR || ROLE_REGISTRAR == $this->Session->read('Auth.User')['Role']['parent_id']) {
                                                                $options = array('1' => ' Accept', '0' => ' Reject');
                                                                $attributes = array('legend' => false, 'separator' => "<br>"); ?>
                                                                <th class="center">Decision</th>
                                                                <?php
                                                            } ?>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        foreach ($coursss as $kc => $vc) { ?>
                                                            <tr <?= (($vc['Student']['max_load'] > $vc['Student']['maximumCreditPerSemester']  || $vc['Student']['max_load'] == 0 || $vc['Student']['overCredit'] ) ? 'class="rejected"': ''); ?> >
                                                                <td class="center">
                                                                    <?= ++$count; ?>
                                                                    <?= $this->Form->hidden('CourseAdd.' . $count . '.id', array('value' => $vc['CourseAdd']['id'])); ?>
                                                                    <?= $this->Form->hidden('CourseAdd.' . $count . '.student_id', array('value' => $vc['CourseAdd']['student_id'])); ?>
                                                                    <?= $this->Form->hidden('CourseAdd.' . $count . '.published_course_id', array('value' => $vc['CourseAdd']['published_course_id'])); ?>
                                                                    <?= $this->Form->hidden('CourseAdd.' . $count . '.academic_year', array('value' => $vc['CourseAdd']['academic_year'])); ?>
                                                                    <?= $this->Form->hidden('CourseAdd.' . $count . '.semester', array('value' => $vc['CourseAdd']['semester'])); ?>
                                                                    <?= $this->Form->hidden('CourseAdd.' . $count . '.credit', array('value' => $vc['PublishedCourse']['Course']['credit'])); ?>
                                                                </td>
                                                                <td class="vcenter"><?= $this->Html->link($vc['Student']['full_name'], '#', array('class' => 'jsview', 'data-animation' => "fade",'data-reveal-id' => 'myModal', 'data-reveal-ajax' => "/students/get_modal_box/" . $vc['Student']['id'])); ?></td>
                                                                <td class="center"><?= (strcasecmp(trim($vc['Student']['gender']), 'male') == 0 ? 'M' : (strcasecmp(trim($vc['Student']['gender']), 'female') == 0 ? 'F' : '')); ?></td>
                                                                <td class="center"><?= $vc['Student']['studentnumber']; ?></td>
                                                                <td class="center"><?= $vc['PublishedCourse']['semester']; ?></td>
                                                                <td class="center"><?= $vc['Student']['max_load']; ?></td>
                                                                <td class="center"><?= $vc['PublishedCourse']['Course']['course_title'] . ' (' . $vc['PublishedCourse']['Course']['course_code'] . ')'; ?></td>
                                                                <td class="center"><?= $vc['PublishedCourse']['Course']['credit']; ?></td>
                                                                <td class="center"><?= $vc['PublishedCourse']['Course']['course_detail_hours']; ?></td>
                                                                <?php
                                                                if ($role_id == ROLE_DEPARTMENT || $role_id == ROLE_COLLEGE) { 
                                                                    if ($vc['Student']['max_load'] == 0) { 
                                                                        $auto_rejections ++; ?>
                                                                        <?= $this->Form->hidden('CourseAdd.' . $count . '.department_approval', array('value' => '0')); ?>
                                                                        <?= $this->Form->hidden('CourseAdd.' . $count . '.auto_rejected', array('value' => 1)); ?>
                                                                        <?= $this->Form->hidden('CourseAdd.' . $count . '.reason', array('value' => 'Auto Rejected. Reason: At least one Registration Required.')); ?>
                                                                        <td class="center">Will Be Auto Rejected <br>(Aleast one Registration Required)</td>
                                                                        <?php
                                                                    } else if ($vc['Student']['willBeOverMaxLoadWithThisAdd'] || $vc['Student']['overCredit'] != 0) { 
                                                                        $auto_rejections ++; ?>
                                                                        <?= $this->Form->hidden('CourseAdd.' . $count . '.department_approval', array('value' => '0')); ?>
                                                                        <?= $this->Form->hidden('CourseAdd.' . $count . '.auto_rejected', array('value' => 1)); ?>
                                                                        <?= $this->Form->hidden('CourseAdd.' . $count . '.reason', array('value' => 'Auto Rejected. Reason: Will be over Max allowed '. ((isset($vc['Student']['Curriculum']) && !empty($vc['Student']['Curriculum']['type_credit'])) ? (count(explode('ECTS', $vc['Student']['Curriculum']['type_credit'])) >= 2  ? 'ECTS' : 'Credit') : 'Credit'). ' for ' . $vc['Student']['Program']['name'] . ' - ' . $vc['Student']['ProgramType']['name'] . ' Program per semester('.$vc['Student']['maximumCreditPerSemester']. ') by ' . $vc['Student']['overCredit']. ' '.  ((isset($vc['Student']['Curriculum']) && !empty($vc['Student']['Curriculum']['type_credit'])) ? (count(explode('ECTS', $vc['Student']['Curriculum']['type_credit'])) >= 2  ? 'ECTS' : 'Credit') : 'Credit').'')); ?>
                                                                        <td class="center">Will Be Auto Rejected <br>(Will be over Max allowed <?= ((isset($vc['Student']['Curriculum']) && !empty($vc['Student']['Curriculum']['type_credit'])) ? (count(explode('ECTS', $vc['Student']['Curriculum']['type_credit'])) >= 2  ? 'ECTS' : 'Credit') : 'Credit'); ?> for <?= $vc['Student']['Program']['name'] . ' - ' . $vc['Student']['ProgramType']['name']; ?> Program per semester( <?= $vc['Student']['maximumCreditPerSemester']; ?>) by <?= $vc['Student']['overCredit']. ' '.  ((isset($vc['Student']['Curriculum']) && !empty($vc['Student']['Curriculum']['type_credit'])) ? (count(explode('ECTS', $vc['Student']['Curriculum']['type_credit'])) >= 2  ? 'ECTS' : 'Credit') : 'Credit') ?>)</td>
                                                                        <?php
                                                                    } else if ($vc['Student']['max_load'] <= $vc['Student']['maximumCreditPerSemester']) { ?>
                                                                        <td class="center">
                                                                            <?= $this->Form->radio('CourseAdd.' . $count . '.department_approval', $options, $attributes); ?>
                                                                            <?= $this->Form->input('CourseAdd.' . $count . '.reason', array('placeholder'=> 'Your reason here if any...', 'size' => '2', 'rows' => '2', 'value' => isset($this->request->data['CourseAdd'][$count]['reason']) ? $this->request->data['CourseAdd'][$count]['reason'] : '', 'label' => false, 'div' => false)); ?>
                                                                            <?= $this->Form->hidden('CourseAdd.' . $count . '.auto_rejected', array('value' => '0')); ?>
                                                                        </td>
                                                                        <?php 
                                                                    } else { 
                                                                        $auto_rejections ++; ?>
                                                                        <?= $this->Form->hidden('CourseAdd.' . $count . '.department_approval', array('value' => '')); ?>
                                                                        <?= $this->Form->hidden('CourseAdd.' . $count . '.auto_rejected', array('value' => 1)); ?>
                                                                        <?= $this->Form->hidden('CourseAdd.' . $count . '.reason', array('value' => 'Auto Rejected. Reason: Will be over from currently allowed maximum '. ((isset($vc['Student']['Curriculum']) && !empty($vc['Student']['Curriculum']['type_credit'])) ? (count(explode('ECTS', $vc['Student']['Curriculum']['type_credit'])) >= 2  ? 'ECTS' : 'Credit') : 'Credit'). ' for ' . $vc['Student']['Program']['name'] . '/' . $vc['Student']['ProgramType']['name'] . ' per semester('.$vc['Student']['maximumCreditPerSemester']. ') by ' . $vc['Student']['overCredit']. ' '.  ((isset($vc['Student']['Curriculum']) && !empty($vc['Student']['Curriculum']['type_credit'])) ? (count(explode('ECTS', $vc['Student']['Curriculum']['type_credit'])) >= 2  ? 'ECTS' : 'Credit') : 'Credit').'')); ?>
                                                                        <td class="center">Will Be Auto Rejected <br>(Will be over Max allowed <?= ((isset($vc['Student']['Curriculum']) && !empty($vc['Student']['Curriculum']['type_credit'])) ? (count(explode('ECTS', $vc['Student']['Curriculum']['type_credit'])) >= 2  ? 'ECTS' : 'Credit') : 'Credit'); ?> for <?= $vc['Student']['Program']['name'] . ' - ' . $vc['Student']['ProgramType']['name']; ?> Program per semester (<?= $vc['Student']['maximumCreditPerSemester'] .' '.  ((isset($vc['Student']['Curriculum']) && !empty($vc['Student']['Curriculum']['type_credit'])) ? (count(explode('ECTS', $vc['Student']['Curriculum']['type_credit'])) >= 2  ? 'ECTS' : 'Credit') : 'Credit'); ?>))</td>
                                                                        <?php
                                                                    }
                                                                } else if ($role_id == ROLE_REGISTRAR) { 
                                                                    if ($vc['Student']['max_load'] == 0) { 
                                                                        $auto_rejections ++; ?>
                                                                        <?= $this->Form->hidden('CourseAdd.' . $count . '.registrar_confirmation', array('value' => '0')); ?>
                                                                        <?= $this->Form->hidden('CourseAdd.' . $count . '.auto_rejected', array('value' => 1)); ?>
                                                                        <?= $this->Form->hidden('CourseAdd.' . $count . '.reason', array('value' => 'Auto Rejected. Reason: At least one Registration Required.')); ?>
                                                                        <td class="center">Will Be Auto Rejected <br>(Aleast one Registration Required).</td>
                                                                        <?php
                                                                    } else if ($vc['Student']['max_load'] >= $vc['Student']['maximumCreditPerSemester'] && $vc['CourseAdd']['department_approval'] == 1) { ?>
                                                                        <td class="center">Department Approved/Cancelled Auto Rejection.<br>
                                                                        <?= $this->Form->radio('CourseAdd.' . $count . '.registrar_confirmation', $options, $attributes); ?></td>
                                                                        <?= $this->Form->hidden('CourseAdd.' . $count . '.reason', array('value' => 'Registrar Confirmed Departments Auto Course Rejection Cancellation. ' . $vc['CourseAdd']['reason'])); ?>
                                                                        <?= $this->Form->hidden('CourseAdd.' . $count . '.auto_rejected', array('value' => 0)); ?>
                                                                        <?= $this->Form->hidden('CourseAdd.' . $count . '.registrar_confirmed_by', array('value' => $this->Session->read('Auth.User')['id'])); ?>
                                                                        <?= $this->Form->hidden('CourseAdd.' . $count . '.modified', array('value' =>  date('Y-m-d H:i:s'))); ?>
                                                                        <?php 
                                                                    } else if ($vc['Student']['willBeOverMaxLoadWithThisAdd'] || $vc['Student']['overCredit'] != 0) { 
                                                                        $auto_rejections ++; ?>
                                                                        <?= $this->Form->hidden('CourseAdd.' . $count . '.registrar_confirmation', array('value' => '0')); ?>
                                                                        <?= $this->Form->hidden('CourseAdd.' . $count . '.auto_rejected', array('value' => 1)); ?>
                                                                        <?= $this->Form->hidden('CourseAdd.' . $count . '.reason', array('value' => 'Auto Rejected. Reason: Will be over from currently allowed maximum '. ((isset($vc['Student']['Curriculum']) && !empty($vc['Student']['Curriculum']['type_credit'])) ? (count(explode('ECTS', $vc['Student']['Curriculum']['type_credit'])) >= 2  ? 'ECTS' : 'Credit') : 'Credit'). ' for ' . $vc['Student']['Program']['name'] . '/' . $vc['Student']['ProgramType']['name'] . ' per semester('.$vc['Student']['maximumCreditPerSemester']. ') by ' . $vc['Student']['overCredit']. ' '.  ((isset($vc['Student']['Curriculum']) && !empty($vc['Student']['Curriculum']['type_credit'])) ? (count(explode('ECTS', $vc['Student']['Curriculum']['type_credit'])) >= 2  ? 'ECTS' : 'Credit') : 'Credit').'')); ?>
                                                                        <td class="center">Will Be Auto Rejected <br>(Will be over Max allowed <?= ((isset($vc['Student']['Curriculum']) && !empty($vc['Student']['Curriculum']['type_credit'])) ? (count(explode('ECTS', $vc['Student']['Curriculum']['type_credit'])) >= 2  ? 'ECTS' : 'Credit') : 'Credit'); ?> for <?= $vc['Student']['Program']['name'] . ' - ' . $vc['Student']['ProgramType']['name']; ?> Program per semester( <?= $vc['Student']['maximumCreditPerSemester']; ?>) by <?= $vc['Student']['overCredit']. ' '.  ((isset($vc['Student']['Curriculum']) && !empty($vc['Student']['Curriculum']['type_credit'])) ? (count(explode('ECTS', $vc['Student']['Curriculum']['type_credit'])) >= 2  ? 'ECTS' : 'Credit') : 'Credit') ?>)</td>
                                                                        <?php
                                                                    } else if ($vc['Student']['max_load'] <= $vc['Student']['maximumCreditPerSemester']) { ?>
                                                                        <td class="center"><?= $this->Form->radio('CourseAdd.' . $count . '.registrar_confirmation', $options, $attributes); ?></td>
                                                                        <?= $this->Form->hidden('CourseAdd.' . $count . '.auto_rejected', array('value' => '0')); ?>
                                                                        <?php 
                                                                    } else { 
                                                                        $auto_rejections ++; ?>
                                                                        <?= $this->Form->hidden('CourseAdd.' . $count . '.registrar_confirmation', array('value' => '0')); ?>
                                                                        <?= $this->Form->hidden('CourseAdd.' . $count . '.auto_rejected', array('value' => 1)); ?>
                                                                        <?= $this->Form->hidden('CourseAdd.' . $count . '.reason', array('value' => 'Auto Rejected. Reason: Will be over from currently allowed maximum '. ((isset($vc['Student']['Curriculum']) && !empty($vc['Student']['Curriculum']['type_credit'])) ? (count(explode('ECTS', $vc['Student']['Curriculum']['type_credit'])) >= 2  ? 'ECTS' : 'Credit') : 'Credit'). ' for ' . $vc['Student']['Program']['name'] . '/' . $vc['Student']['ProgramType']['name'] . ' per semester('.$vc['Student']['maximumCreditPerSemester']. ') by ' . $vc['Student']['overCredit']. ' '.  ((isset($vc['Student']['Curriculum']) && !empty($vc['Student']['Curriculum']['type_credit'])) ? (count(explode('ECTS', $vc['Student']['Curriculum']['type_credit'])) >= 2  ? 'ECTS' : 'Credit') : 'Credit').'')); ?>
                                                                        <td class="center">Will Be Auto Rejected <br>(Will be over Max allowed <?= ((isset($vc['Student']['Curriculum']) && !empty($vc['Student']['Curriculum']['type_credit'])) ? (count(explode('ECTS', $vc['Student']['Curriculum']['type_credit'])) >= 2  ? 'ECTS' : 'Credit') : 'Credit'); ?> for <?= $vc['Student']['Program']['name'] . ' - ' . $vc['Student']['ProgramType']['name']; ?> Program per semester (<?= $vc['Student']['maximumCreditPerSemester'] .' '.  ((isset($vc['Student']['Curriculum']) && !empty($vc['Student']['Curriculum']['type_credit'])) ? (count(explode('ECTS', $vc['Student']['Curriculum']['type_credit'])) >= 2  ? 'ECTS' : 'Credit') : 'Credit'); ?>))</td>
                                                                        <?php
                                                                    }
                                                                } ?>
                                                            </tr>
                                                            <?php
                                                        } ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <br>
                                            <?php
                                        } else {
                                            $display_button++;
                                        }
                                        //$count++;
                                    }

                                }
                            }
                        } 

                        if ($display_button != $section_count) {
                            if ($role_id == ROLE_DEPARTMENT || $role_id == ROLE_COLLEGE) { ?>
                                <hr>
                                <?= $this->Form->submit('Approve/Reject Course Add', array('name' => 'approverejectadd', 'id' => 'approveRejectAdd', 'class' => 'tiny radius button bg-blue', 'div' => false)); ?>
                                <?php
                            } else if ($role_id ==  ROLE_REGISTRAR) { ?>
                                <hr>
                                <?= $this->Form->submit('Confirm/Deny Course Add', array('name' => 'approverejectadd', 'id' => 'approveRejectAdd', 'class' => 'tiny radius button bg-blue', 'div' => false)); ?>
                                <?php
                            }
                        }

                    } ?>
                    <?= $this->Form->end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">

	var form_being_submitted = false;

    const validationMessageNonSelected = document.getElementById('validation-message_non_selected');

	var checkForm = function(form) {

        var autoRejections = <?= $auto_rejections; ?>; 
        var radios = document.querySelectorAll('input[type="radio"]');
		var checkedOne = Array.prototype.slice.call(radios).some(x => x.checked);

        //alert(autoRejections);
        //alert(checkedOne);
		if (!checkedOne && autoRejections == 0) {
            alert('At least one Course Add Must be Accepted or Rejected!');
			validationMessageNonSelected.innerHTML = 'At least one Course Add Must be Accepted or Rejected!';
			return false;
		}

		if (form_being_submitted) {
			alert("Approving/Rejecting Course Add, please wait a moment...");
			form.approveRejectAdd.disabled = true;
			return false;
		}

		form.approveRejectAdd.value = 'Approving/Rejecting Course Add...';
		form_being_submitted = true;
		return true;
	};

	if (window.history.replaceState) {
		window.history.replaceState(null, null, window.location.href);
	}
</script>
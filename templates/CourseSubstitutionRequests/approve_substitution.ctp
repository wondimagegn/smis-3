<div class="box">
    <div class="box-header bg-transparent">
        <div class="box-title" style="margin-top: 10px;"><i class="fontello-check" style="font-size: larger; font-weight: bold;"></i>
            <span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= ($role_id != ROLE_STUDENT ? __('Latest List of course substitution requests') : __('List of your course substitution requests')); ?></span>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="large-12 columns">
                <?php
				if (isset($courseSubstitutionRequests) && !empty($courseSubstitutionRequests)) { ?>
					<?php //debug($courseSubstitutionRequests);  ?>
					<div style="overflow-x:auto;">
						<table cellpadding="0" cellspacing="0" class="table">
							<thead>
								<tr>
									<td class="center">#</td>
									<td class="vcenter">Full name</td>
									<td class="vcenter">Sex</td>
									<td class="vcenter">Student ID</td>
									<td class="vcenter">Course For Substition</td>
									<td class="vcenter">Course to be Substitued</td>
									<td class="center">Request Date</td>
									<td class="center">Status</td>
									<?= (($this->Session->read('Auth.User')['role_id'] == ROLE_DEPARTMENT || $this->Session->read('Auth.User')['role_id'] == ROLE_COLLEGE || $this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR)  ? '<td class="center">Actions</td>' : '');  ?>
								</tr>
							</thead>
							<tbody>
								<?php
								$start = 1;
								foreach ($courseSubstitutionRequests as $courseSubstitutionRequest) { ?>
									<tr>
										<td class="center"><?= $start++; ?></td>
										<td class="vcenter"><?= $this->Html->link($courseSubstitutionRequest['Student']['full_name'], array('controller' => 'students', 'action' => 'student_academic_profile', $courseSubstitutionRequest['Student']['id'])); ?></td>
										<td class="center"><?= (strcasecmp(trim($courseSubstitutionRequest['Student']['gender']), 'male') == 0 ? 'M' : (strcasecmp(trim($courseSubstitutionRequest['Student']['gender']), 'female') == 0 ? 'F' : '')); ?></td>
										<td class="center"><?= $courseSubstitutionRequest['Student']['studentnumber']; ?></td>
										<td class="vcenter"><?= $this->Html->link($courseSubstitutionRequest['CourseForSubstitued']['course_code'] . ' - ' . $courseSubstitutionRequest['CourseForSubstitued']['course_title'] /* . '-' . $courseSubstitutionRequest['CourseForSubstitued']['Curriculum']['name'] . ' ' . $courseSubstitutionRequest['CourseForSubstitued']['Curriculum']['year_introduced'] . ' (' . $courseSubstitutionRequest['CourseForSubstitued']['Department']['name'] . ')' */, array('controller' => 'courses', 'action' => 'view', $courseSubstitutionRequest['CourseForSubstitued']['id'])); ?></td>
										<td class="vcenter"><?= $this->Html->link($courseSubstitutionRequest['CourseBeSubstitued']['course_code'] . ' - ' . $courseSubstitutionRequest['CourseBeSubstitued']['course_title']/*  . '-' . $courseSubstitutionRequest['CourseBeSubstitued']['Curriculum']['name'] . ' ' . $courseSubstitutionRequest['CourseBeSubstitued']['Curriculum']['year_introduced'] . ' (' . $courseSubstitutionRequest['CourseBeSubstitued']['Department']['name'] . ')' */, array('controller' => 'courses', 'action' => 'view', $courseSubstitutionRequest['CourseBeSubstitued']['id'])); ?></td>
										<td class="center"><?= $this->Time->format("M j, Y", $courseSubstitutionRequest['CourseSubstitutionRequest']['request_date'], NULL, NULL); ?></td>
										<td class="center">
											<?php
											if ($courseSubstitutionRequest['CourseSubstitutionRequest']['department_approve'] == 1) {
												echo 'Accepted';
											} else {
												if (is_null($courseSubstitutionRequest['CourseSubstitutionRequest']['department_approve'])) {
													echo 'waiting approval';
												} else if ($courseSubstitutionRequest['CourseSubstitutionRequest']['department_approve'] == 0) {
													echo 'Rejected';
												}
											} ?>
										</td>
										<?php
										if ($this->Session->read('Auth.User')['role_id'] == ROLE_DEPARTMENT || $this->Session->read('Auth.User')['role_id'] == ROLE_COLLEGE || $this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR ) { ?>
											<td class="center">
												<?= $this->Html->link(__('Review & Approve'), array('action' => 'approve_substitution', $courseSubstitutionRequest['CourseSubstitutionRequest']['id'])); ?>
											</td>
											<?php
										} ?>
									</tr>
									<?php
								} ?>
							</tbody>
						</table>
					</div>
					<hr>
					<?php
				}  else { ?>

                    <?= $this->Form->create('CourseSubstitutionRequest' , array('onSubmit' => 'return checkForm(this);')); ?>

                    <?php
                    if (isset($student_section_exam_status) && !empty($student_section_exam_status)) {
                        echo '<hr>';
                        echo $this->element('student_basic');
                    }

                    if (!empty($previous_substitution_accepted)) { ?>
                        <table cellspacing="0" cellpadding="0" class="table">
                            <tr>
                                <td style="background-color: white;">
                                    <table cellspacing="0" cellpadding="0" class="table">
                                        <tr>
                                            <td colspan=3><h6 class="fs14 text-gray">Previous course substitution request by this students and accepted by the department.</h6></td>
                                        </tr>
                                        <?php
                                        $count = 0;
                                        foreach ($previous_substitution_accepted as $psk => $pvv) { ?>
                                            <tr>
                                                <td class="vcenter" style="background-color: white;">
                                                    <table cellspacing="0" cellpadding="0" class="table">
                                                        <thead>
                                                            <tr>
                                                                <td class="vcenter">Course Title</td>
                                                                <td class="center">Course Code</td>
                                                                <td class="center">Credit</td>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td class="vcenter" style="background-color: white;"><?= $pvv['CourseForSubstitued']['course_title']; ?></td>
                                                                <td class="center" style="background-color: white;"><?= $pvv['CourseForSubstitued']['course_code']; ?></td>
                                                                <td class="center" style="background-color: white;"><?= $pvv['CourseForSubstitued']['credit']; ?></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </td>
                                                <td class="vcenter" style="background-color: white;" >Substituted by => </td>
                                                <td style="background-color: white;">
                                                    <table cellspacing="0" cellpadding="0" class="table">
                                                        <thead>
                                                            <tr>
                                                                <td class="vcenter">Course Title</td>
                                                                <td class="center">Course Code</td>
                                                                <td class="center">Credit</td>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td class="vcenter" style="background-color: white;"><?= $pvv['CourseBeSubstitued']['course_title']; ?></td>
                                                                <td class="center" style="background-color: white;"><?= $pvv['CourseBeSubstitued']['course_code']; ?></td>
                                                                <td class="center" style="background-color: white;"><?= $pvv['CourseBeSubstitued']['credit']; ?></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>
                                            <?php
                                        } ?>
                                    </table>
                                </td>
                            </tr>
                        </table>
                        <?php
                    } ?>

                    <hr>

                    <h6 class="fs14 text-gray"><?= __('Course Substitution Request Waiting Decision'); ?></h6>
                    <?php
                    //debug($student_section_exam_status['StudentBasicInfo']['id']);
                    echo $this->Form->hidden('id', array('value' => $this->request->data['CourseSubstitutionRequest']['id']));
                    echo $this->Form->hidden('student_id', array('value' => $this->request->data['CourseSubstitutionRequest']['student_id']));
                    echo $this->Form->hidden('request_date', array('value' => $this->request->data['CourseSubstitutionRequest']['request_date']));

                    $options = array('1' => ' Accept', '0' => ' Reject');
                    $attributes = array('legend' => false, 'label' => false, 'separator' => "   ");

                    //debug($this->request->data);
                    ?>
                    <div style="overflow-x:auto;">

                        <table cellspacing="0" cellpadding="0" class="table">
                            <tbody>
                                <tr>
                                    <td style="width:20%" class="vcenter">Course For Substituted:</td>
                                    <td style="width:80%">
                                        <?php
                                        //echo $courseBeSubstitueds[$this->request->data['CourseSubstitutionRequest']['course_for_substitued_id']];
                                        echo 'Course: ' . $this->request->data['CourseForSubstitued']['course_code'] . ' - ' . $this->request->data['CourseForSubstitued']['course_title'] . '<br> Credit Type: ' . (count(explode('ECTS', $this->request->data['CourseForSubstitued']['Curriculum']['type_credit'])) == 2 ? 'ECTS' : 'Credit') . '<br> ' . (count(explode('ECTS', $this->request->data['CourseForSubstitued']['Curriculum']['type_credit'])) == 2 ? 'ECTS' : 'Credits'). ': ' . $this->request->data['CourseForSubstitued']['credit'] . '<br> Curriculum: ' . $this->request->data['CourseForSubstitued']['Curriculum']['name'] . ' - ' . $this->request->data['CourseForSubstitued']['Curriculum']['year_introduced'] . '<br> Department: ' . $this->request->data['CourseForSubstitued']['Department']['name'];
                                        echo $this->Form->hidden('course_for_substitued_id',  array('value' => $this->request->data['CourseSubstitutionRequest']['course_for_substitued_id'])); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="vcenter">Course Be Substituted:</td>
                                    <td>
                                        <?php
                                        //echo $courseBeSubstitueds[$this->request->data['CourseSubstitutionRequest']['course_be_substitued_id']];
                                        echo 'Course: ' . $this->request->data['CourseBeSubstitued']['course_code'] . ' - ' . $this->request->data['CourseBeSubstitued']['course_title'] . '<br> Credit Type: ' . (count(explode('ECTS', $this->request->data['CourseBeSubstitued']['Curriculum']['type_credit'])) == 2 ? 'ECTS' : 'Credit') . '<br> ' . (count(explode('ECTS', $this->request->data['CourseBeSubstitued']['Curriculum']['type_credit'])) == 2 ? 'ECTS' : 'Credits'). ': ' . $this->request->data['CourseBeSubstitued']['credit'] . '<br> Curriculum: ' . $this->request->data['CourseBeSubstitued']['Curriculum']['name'] . ' - ' . $this->request->data['CourseBeSubstitued']['Curriculum']['year_introduced'] . '<br> Department: ' . $this->request->data['CourseBeSubstitued']['Department']['name'];
                                        echo $this->Form->hidden('course_be_substitued_id', array('value' => $this->request->data['CourseSubstitutionRequest']['course_be_substitued_id'])); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="vcenter">Request Date:</td>
                                    <br>
                                    <td><?= $this->Time->format("M j, Y", $this->request->data['CourseSubstitutionRequest']['request_date'], NULL, NULL); ?></td>
                                </tr>
                                <tr>
                                    <td>Accept/Reject Request:</td>
                                    <td>
                                        <br>
                                        <?= $this->Form->radio('department_approve', $options, $attributes); ?> <br>
                                        Remark: <?= $this->Form->input('remark', array('label' => false)); ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <hr>
                    <?= $this->Form->submit(__('Approve/Reject Request'), array('name' => 'approveRejectSubstitutionRequest', 'id' => 'approveRejectSubstitutionRequest', 'class' => 'tiny radius button bg-blue', 'div' => false)); ?>
                    <?php
                } ?>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">

	var form_being_submitted = false;

	var checkForm = function(form) {

		if (form_being_submitted) {
			alert("Approving/Rejecting Substitution Request, please wait a moment...");
			form.approveRejectSubstitutionRequest.disabled = true;
			return false;
		}

		form.approveRejectSubstitutionRequest.value = 'Approving/Rejecting Substitution Request...';
		form_being_submitted = true;
		return true;
	};

	if (window.history.replaceState) {
		window.history.replaceState(null, null, window.location.href);
	}
</script>
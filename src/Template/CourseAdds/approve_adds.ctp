<?php
$role_id=$this->getRequest()->getSession()->read('Auth')['User']['role_id'];

$this->assign('title',$role_id == ROLE_REGISTRAR ? __('Confirm Course Adds') :
    __('Approve Course Adds'));
?>
<div class="box">
    <div class="box-header bg-transparent">
        <div class="box-title" style="margin-top: 10px;"><i class="fontello-check" style="font-size: larger; font-weight: bold;"></i>
            <?= $role_id == ROLE_REGISTRAR ? __('Confirm Course Adds') : __('Approve Course Adds'); ?>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-12">
                <?php
                echo $this->Form->create(null, ['id' => 'courseAddForm', 'onsubmit' => 'return checkForm(this);']);
                if (!empty($courses)) {
                    echo '<hr>';
                    echo '<h6 class="fs-6 text-gray">List of students who submitted Course Add request for approval:</h6>';
                    echo '<hr>';
                    echo '<h6 id="validation-message_non_selected" class="text-red fs-6"></h6>';

                    $count = 0;
                    $autoRejections = 0;
                    foreach ($courses as $departmentName => $program) {
                        foreach ($program as $programName => $programType) {
                            foreach ($programType as $programTypeName => $sections) {
                                $displayButton = 0;
                                $sectionCount = 0;
                                foreach ($sections as $sectionId => $courseData) {
                                    $sectionCount++;
                                    if (!empty($courseData)) {
                                        ?>
                                        <div class="table-responsive">
                                            <table class="table table-striped table-bordered">
                                                <thead>
                                                <tr>
                                                    <td colspan="11" style="border-bottom: 2px solid #555; line-height: 1.5;">
                                                            <span class="fw-bold fs-5">
                                                                From: <?= h($sectionId) . ' ' .
                                                                (!empty($courseData[0]['published_course']['section']['year_level']['name']) ? ' (' . h($courseData[0]['published_course']['section']['year_level']['name']) . ', ' . h($courseData[0]['published_course']['academic_year']) . ')' : ' (Pre/1st)'); ?>
                                                            </span>
                                                        <br>
                                                        <span class="text-gray fw-bold fs-6">
                                                                <?= !empty($courseData[0]['published_course']['section']['department']['name'])
                                                                    ? h($courseData[0]['published_course']['section']['department']['name'])
                                                                    : h($courseData[0]['published_course']['section']['college']['name']) . ' - Pre/Freshman'; ?>
                                                                &nbsp; | &nbsp; <?= h($programName); ?>
                                                                &nbsp; | &nbsp; <?= h($programTypeName); ?>
                                                            </span>
                                                        <br>
                                                        <span class="text-black fw-bold fs-6">
                                                                <?= !empty($courseData[0]['student']['department']['id'])
                                                                    ? h($courseData[0]['student']['department']['name'])
                                                                    : h($courseData[0]['student']['college']['name']) . ' - Pre/Freshman'; ?>
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
                                                    <th class="center">
                                                        <?= empty($courseData[0]['published_course']['section']['curriculum']['id'])
                                                            ? 'Cr.'
                                                            : (stripos($courseData[0]['published_course']['section']['curriculum']['type_credit'],
                                                                'ECTS') !== false ? 'ECTS' : 'Credit'); ?>
                                                    </th>
                                                    <th class="center">LTL</th>
                                                    <th class="center">Decision</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php foreach ($courseData as $vc) { ?>
                                                    <tr <?= ($vc['student']['max_load'] > $vc['student']['maximumCreditPerSemester'] || $vc['student']['max_load'] == 0 || $vc['student']['overCredit']) ? 'class="rejected"' : ''; ?>>
                                                        <td class="center">
                                                            <?= ++$count; ?>
                                                            <?= $this->Form->hidden("CourseAdd.$count.id", ['value' => $vc['id']]); ?>
                                                            <?= $this->Form->hidden("CourseAdd.$count.student_id",
                                                                ['value' => $vc['student_id']]); ?>
                                                            <?= $this->Form->hidden("CourseAdd.$count.published_course_id",
                                                                ['value' => $vc['published_course_id']]); ?>
                                                            <?= $this->Form->hidden("CourseAdd.$count.academic_year", ['value' =>
                                                                $vc['academic_year']]); ?>
                                                            <?= $this->Form->hidden("CourseAdd.$count.semester", ['value' =>
                                                                $vc['semester']]); ?>
                                                            <?= $this->Form->hidden("CourseAdd.$count.credit", ['value' =>
                                                                $vc['published_course']['course']['credit']]); ?>
                                                        </td>
                                                        <td class="vcenter">

                                                            <?= $this->Html->link($vc['student']['full_name'], '#', array('class' => 'jsview',
                                                                'data-animation' => "fade",'data-reveal-id' => 'myModal', 'data-reveal-ajax' => "/Students/getModalBox/" .
                                                                    $vc['student']['id'])); ?>
                                                        </td>
                                                        <td class="center">
                                                            <?= strcasecmp(trim($vc['student']['gender']), 'male') === 0 ? 'M' : (strcasecmp(trim($vc['student']['gender']), 'female') === 0 ? 'F' : ''); ?>
                                                        </td>
                                                        <td class="center"><?= h($vc['student']['studentnumber']); ?></td>
                                                        <td class="center"><?= h($vc['published_course']['semester']); ?></td>
                                                        <td class="center"><?= h($vc['student']['max_load']); ?></td>
                                                        <td class="center">
                                                            <?= h($vc['published_course']['course']['course_title'] . ' (' . $vc['published_course']['course']['course_code'] . ')'); ?>
                                                        </td>
                                                        <td class="center"><?= h($vc['published_course']['course']['credit']); ?></td>
                                                        <td class="center"><?= h($vc['published_course']['course']['course_detail_hours']); ?></td>
                                                        <td class="center">
                                                            <?php
                                                            $options = ['1' => 'Accept', '0' => 'Reject'];
                                                            $attributes = ['legend' => false, 'separator' => $role_id == ROLE_REGISTRAR ? '<br>' : ' '];

                                                            if ($role_id == ROLE_DEPARTMENT || $role_id == ROLE_COLLEGE) {
                                                                if ($vc['student']['max_load'] == 0) {
                                                                    $autoRejections++;
                                                                    echo $this->Form->hidden("CourseAdd.$count.department_approval", ['value' => '0']);
                                                                    echo $this->Form->hidden("CourseAdd.$count.auto_rejected", ['value' => 1]);
                                                                    echo $this->Form->hidden("CourseAdd.$count.reason", ['value' => 'Auto Rejected. Reason: At least one Registration Required.']);
                                                                    echo 'Will Be Auto Rejected <br>(At least one Registration Required)';
                                                                } elseif ($vc['student']['willBeOverMaxLoadWithThisAdd'] || $vc['student']['overCredit'] != 0) {
                                                                    $autoRejections++;
                                                                    $creditType = !empty($vc['student']['curriculum']['type_credit'])
                                                                    && stripos($vc['student']['curriculum']['type_credit'], 'ECTS') !== false
                                                                        ? 'ECTS' : 'Credit';
                                                                    echo $this->Form->hidden("CourseAdd.$count.department_approval", ['value' => '0']);
                                                                    echo $this->Form->hidden("CourseAdd.$count.auto_rejected", ['value' => 1]);
                                                                    echo $this->Form->hidden("CourseAdd.$count.reason", [
                                                                        'value' => "Auto Rejected. Reason: Will be over Max allowed {$creditType} for {$vc['student']['program']['name']} -
                                                                        {$vc['student']['program_type']['name']} Program per semester({$vc['student']['maximumCreditPerSemester']}) by
                                                                        {$vc['student']['overCredit']} {$creditType}"
                                                                    ]);
                                                                    echo "Will Be Auto Rejected <br>(Will be over Max allowed {$creditType} for {$vc['student']['program']['name']} - {$vc['student']['program_type']['name']} Program per semester({$vc['student']['maximumCreditPerSemester']}) by {$vc['student']['overCredit']} {$creditType})";
                                                                } elseif ($vc['student']['max_load'] <= $vc['student']['maximumCreditPerSemester']) {
                                                                    echo $this->Form->radio("CourseAdd.$count.department_approval", $options, $attributes);
                                                                    echo $this->Form->textarea("CourseAdd.$count.reason", [
                                                                        'placeholder' => 'Your reason here if any...',
                                                                        'rows' => 2,
                                                                        'value' => $this->request->getData("CourseAdd.$count.reason", ''),
                                                                        'class' => 'form-control'
                                                                    ]);
                                                                    echo $this->Form->hidden("CourseAdd.$count.auto_rejected", ['value' => '0']);
                                                                } else {
                                                                    $autoRejections++;
                                                                    $creditType = !empty($vc['student']['curriculum']['type_credit']) && stripos($vc['student']['curriculum']['type_credit'], 'ECTS') !== false ? 'ECTS' : 'Credit';
                                                                    echo $this->Form->hidden("CourseAdd.$count.department_approval", ['value' => '']);
                                                                    echo $this->Form->hidden("CourseAdd.$count.auto_rejected", ['value' => 1]);
                                                                    echo $this->Form->hidden("CourseAdd.$count.reason", [
                                                                        'value' => "Auto Rejected. Reason: Will be over from currently allowed maximum {$creditType} for {$vc['student']['program']['name']}/{$vc['student']['program_type']['name']} per semester({$vc['student']['maximumCreditPerSemester']}) by {$vc['student']['overCredit']} {$creditType}"
                                                                    ]);
                                                                    echo "Will Be Auto Rejected <br>(Will be over Max allowed {$creditType} for {$vc['student']['program']['name']} - {$vc['student']['program_type']['name']} Program per semester ({$vc['student']['maximumCreditPerSemester']} {$creditType}))";
                                                                }
                                                            } elseif ($role_id == ROLE_REGISTRAR) {

                                                                if ($vc['student']['max_load'] == 0) {
                                                                    $autoRejections++;
                                                                    echo $this->Form->hidden("CourseAdd.$count.registrar_confirmation", ['value' => '0']);
                                                                    echo $this->Form->hidden("CourseAdd.$count.auto_rejected", ['value' => 1]);
                                                                    echo $this->Form->hidden("CourseAdd.$count.reason", ['value' => 'Auto Rejected. Reason: At least one Registration Required.']);
                                                                    echo 'Will Be Auto Rejected <br>(At least one Registration Required).';
                                                                } elseif ($vc['student']['max_load'] >= $vc['student']['maximumCreditPerSemester']
                                                                    && $vc['department_approval'] == 1) {
                                                                    echo 'Department Approved/Cancelled Auto Rejection.<br>';
                                                                    echo $this->Form->radio("CourseAdd.$count.registrar_confirmation", $options, $attributes);
                                                                    echo $this->Form->hidden("CourseAdd.$count.reason",
                                                                        ['value' => 'Registrar Confirmed Departments Auto Course Rejection Cancellation. ' . $vc['reason']]);
                                                                    echo $this->Form->hidden("CourseAdd.$count.auto_rejected", ['value' => 0]);
                                                                    echo $this->Form->hidden("CourseAdd.$count.registrar_confirmed_by",
                                                                        ['value' =>$this->getRequest()->getSession()->read('Auth')['User']['id']]);
                                                                    echo $this->Form->hidden("CourseAdd.$count.modified", ['value' => date('Y-m-d H:i:s')]);
                                                                } elseif ($vc['student']['willBeOverMaxLoadWithThisAdd'] || $vc['student']['overCredit'] != 0) {
                                                                    $autoRejections++;
                                                                    $creditType = !empty($vc['student']['curriculum']['type_credit'])
                                                                    && stripos($vc['student']['curriculum']['type_credit'], 'ECTS') !== false ?
                                                                        'ECTS' : 'Credit';
                                                                    echo $this->Form->hidden("CourseAdd.$count.registrar_confirmation", ['value' => '0']);
                                                                    echo $this->Form->hidden("CourseAdd.$count.auto_rejected", ['value' => 1]);
                                                                    echo $this->Form->hidden("CourseAdd.$count.reason", [
                                                                        'value' => "Auto Rejected. Reason: Will be over from currently allowed maximum {$creditType}
                                                                         for {$vc['student']['program']['name']}/{$vc['student']['program_type']['name']}
                                                                         per semester({$vc['student']['maximumCreditPerSemester']}) by {$vc['student']['overCredit']} {$creditType}"
                                                                    ]);
                                                                    echo "Will Be Auto Rejected <br>(Will be over Max allowed {$creditType} for {$vc['student']['program']['name']} - {$vc['student']['program_type']['name']} Program per semester({$vc['student']['maximumCreditPerSemester']}) by {$vc['student']['overCredit']} {$creditType})";
                                                                } elseif ($vc['student']['max_load'] <= $vc['student']['maximumCreditPerSemester']) {
                                                                    echo $this->Form->radio("CourseAdd.$count.registrar_confirmation", $options, $attributes);
                                                                    echo $this->Form->hidden("CourseAdd.$count.auto_rejected", ['value' => '0']);
                                                                } else {
                                                                    $autoRejections++;
                                                                    $creditType = !empty($vc['student']['curriculum']['type_credit']) && stripos($vc['student']['curriculum']['type_credit'], 'ECTS') !== false ? 'ECTS' : 'Credit';
                                                                    echo $this->Form->hidden("CourseAdd.$count.registrar_confirmation", ['value' => '0']);
                                                                    echo $this->Form->hidden("CourseAdd.$count.auto_rejected", ['value' => 1]);
                                                                    echo $this->Form->hidden("CourseAdd.$count.reason", [
                                                                        'value' => "Auto Rejected. Reason: Will be over from currently allowed maximum {$creditType} for {$vc['student']['program']['name']}/{$vc['student']['program_type']['name']} per semester({$vc['student']['maximumCreditPerSemester']}) by {$vc['student']['overCredit']} {$creditType}"
                                                                    ]);
                                                                    echo "Will Be Auto Rejected <br>(Will be over Max allowed {$creditType} for {$vc['student']['program']['name']} - {$vc['student']['program_type']['name']} Program per semester ({$vc['student']['maximumCreditPerSemester']} {$creditType}))";
                                                                }
                                                            }
                                                            ?>
                                                        </td>
                                                    </tr>
                                                <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                        <br>
                                        <?php
                                    } else {
                                        $displayButton++;
                                    }
                                }
                            }
                        }
                    }

                    if ($displayButton != $sectionCount) {
                        if ($role_id == ROLE_DEPARTMENT || $role_id == ROLE_COLLEGE) {
                            echo '<hr>';
                            echo $this->Form->submit('Approve/Reject Course Add', [
                                'name' => 'approverejectadd',
                                'id' => 'approveRejectAdd',
                                'class' => 'tiny radius button bg-blue'
                            ]);
                        } elseif ($role_id == ROLE_REGISTRAR) {
                            echo '<hr>';
                            echo $this->Form->submit('Confirm/Deny Course Add', [
                                'name' => 'approverejectadd',
                                'id' => 'approveRejectAdd',
                                'class' => 'tiny radius button bg-blue'
                            ]);
                        }
                    }
                }
                echo $this->Form->end();
                ?>
            </div>
        </div>
    </div>
</div>


<script>
    let formBeingSubmitted = false;

    function checkForm(form) {
        const autoRejections = <?= json_encode($autoRejections); ?>;
        const radios = document.querySelectorAll('input[type="radio"]');
        const checkedOne = Array.from(radios).some(x => x.checked);
        const validationMessageNonSelected = document.getElementById('validation-message_non_selected');

        if (!checkedOne && autoRejections === 0) {
            validationMessageNonSelected.textContent = 'At least one Course Add must be Accepted or Rejected!';
            alert('At least one Course Add must be Accepted or Rejected!');
            return false;
        }

        if (formBeingSubmitted) {
            alert('Approving/Rejecting Course Add, please wait a moment...');
            form.approveRejectAdd.disabled = true;
            return false;
        }

        form.approveRejectAdd.value = 'Approving/Rejecting Course Add...';
        formBeingSubmitted = true;
        return true;
    }

    // Prevent form resubmission on page reload
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }

    // AJAX for modal content
    document.querySelectorAll('.jsview').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const url = this.getAttribute('href');
            fetch(url)
                .then(response => response.text())
                .then(data => {
                    document.querySelector('#studentModal .modal-body').innerHTML = data;
                })
                .catch(error => {
                    console.error('Error loading modal content:', error);
                    document.querySelector('#studentModal .modal-body').innerHTML = 'Error loading student details.';
                });
        });
    });
</script>

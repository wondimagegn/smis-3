<?php
use Cake\I18n\I18n;

$this->set('title', __('Edit Accepted Student'));
$this->Html->script(['jquery-1.6.2.min'], ['block' => 'script']);
?>

<script type="text/javascript">
    $(document).ready(function() {
        $("#college-id").change(function() {
            $("#department-id").prop('disabled', true);
            $("#college-id").prop('disabled', true);
            var cid = $("#college-id").val();
            var formUrl = '<?= $this->Url->build(['controller' => 'Departments', 'action' => 'getDepartmentCombo', '_ext' => 'json']) ?>/' + cid;

            $.ajax({
                type: 'GET',
                url: formUrl,
                data: { cid: cid },
                dataType: 'json',
                success: function(data, textStatus, xhr) {
                    $("#department-id").prop('disabled', false);
                    $("#college-id").prop('disabled', false);
                    $("#department-id").empty();
                    $.each(data, function(key, value) {
                        $("#department-id").append('<option value="' + key + '">' + value + '</option>');
                    });
                },
                error: function(xhr, textStatus, error) {
                    alert(textStatus);
                }
            });
            return false;
        });

        var form_being_submitted = false;
        $("#save-it").click(function() {
            if (form_being_submitted) {
                alert('<?= __('Updating Student Profile, please wait a moment...') ?>');
                $(this).prop('disabled', true);
                return false;
            }
            $(this).val('<?= __('Updating Student Profile...') ?>');
            form_being_submitted = true;
            return true;
        });

        $("#country-id-1").change(function() {
            var countryId = $(this).val();
            $("#region-id-1").prop('disabled', true);
            $("#zone-id-1").prop('disabled', true);
            $("#woreda-id-1").prop('disabled', true);
            $("#city-id-1").prop('disabled', true);

            if (countryId) {
                $.ajax({
                    url: '<?= $this->Url->build(['controller' => 'Students', 'action' => 'getRegions', '_ext' => 'json']) ?>/' + countryId,
                    type: 'GET',
                    data: { countryId: countryId },
                    dataType: 'json',
                    success: function(data, textStatus, xhr) {
                        $("#region-id-1").prop('disabled', false);
                        $("#region-id-1").empty().append(data);
                        $("#zone-id-1").empty().append('<option value=""><?= __('[ Select Zone ]') ?></option>');
                        $("#woreda-id-1").empty().append('<option value=""><?= __('[ Select Woreda ]') ?></option>');
                        $("#city-id-1").empty().append('<option value=""><?= __('[ Select City ]') ?></option>');
                    },
                    error: function(xhr, textStatus, error) {
                        alert(textStatus);
                    }
                });
            } else {
                $("#region-id-1").empty().append('<option value=""><?= __('[ Select Region ]') ?></option>');
                $("#zone-id-1").empty().append('<option value=""><?= __('[ Select Zone ]') ?></option>');
                $("#woreda-id-1").empty().append('<option value=""><?= __('[ Select Woreda ]') ?></option>');
                $("#city-id-1").empty().append('<option value=""><?= __('[ Select City ]') ?></option>');
            }
            return false;
        });

        $("#region-id-1").change(function() {
            var regionId = $(this).val();
            $("#zone-id-1").prop('disabled', true);
            $("#woreda-id-1").prop('disabled', true);
            $("#city-id-1").prop('disabled', true);

            if (regionId) {
                $.ajax({
                    url: '<?= $this->Url->build(['controller' => 'Students', 'action' => 'getZones', '_ext' => 'json']) ?>/' + regionId,
                    type: 'GET',
                    data: { regionId: regionId },
                    dataType: 'json',
                    success: function(data, textStatus, xhr) {
                        $("#zone-id-1").prop('disabled', false);
                        $("#zone-id-1").empty().append(data);
                        $("#woreda-id-1").empty().append('<option value=""><?= __('[ Select Woreda ]') ?></option>');
                        $("#city-id-1").empty().append('<option value=""><?= __('[ Select City ]') ?></option>');
                    },
                    error: function(xhr, textStatus, error) {
                        alert(textStatus);
                    }
                });
            } else {
                $("#zone-id-1").empty().append('<option value=""><?= __('[ Select Zone ]') ?></option>');
                $("#woreda-id-1").empty().append('<option value=""><?= __('[ Select Woreda ]') ?></option>');
                $("#city-id-1").empty().append('<option value=""><?= __('[ Select City ]') ?></option>');
            }
            return false;
        });

        $("#zone-id-1").change(function() {
            var zoneId = $(this).val();
            $("#woreda-id-1").prop('disabled', true);
            $("#city-id-1").prop('disabled', true);

            if (zoneId) {
                $.ajax({
                    url: '<?= $this->Url->build(['controller' => 'Students', 'action' => 'getWoredas', '_ext' => 'json']) ?>/' + zoneId,
                    type: 'GET',
                    data: { zoneId: zoneId },
                    dataType: 'json',
                    success: function(data, textStatus, xhr) {
                        $("#woreda-id-1").prop('disabled', false);
                        $("#woreda-id-1").empty().append(data);

                        var regionId = $("#region-id-1").val();
                        $.ajax({
                            url: '<?= $this->Url->build(['controller' => 'Students', 'action' => 'getCities', '_ext' => 'json']) ?>/' + regionId,
                            type: 'GET',
                            data: { regionId: regionId },
                            dataType: 'json',
                            success: function(data, textStatus, xhr) {
                                $("#city-id-1").prop('disabled', false);
                                $("#city-id-1").empty().append(data);
                            },
                            error: function(xhr, textStatus, error) {
                                alert(textStatus);
                            }
                        });
                    },
                    error: function(xhr, textStatus, error) {
                        alert(textStatus);
                    }
                });
            } else {
                $("#woreda-id-1").empty().append('<option value=""><?= __('[ Select Woreda ]') ?></option>');
                $("#city-id-1").empty().append('<option value=""><?= __('[ Select City ]') ?></option>');
            }
            return false;
        });

        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    });
</script>

<div class="container">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-edit" style="font-size: larger; font-weight: bold;"></i>
                <span style="font-size: medium; font-weight: bold; margin-top: 20px;">
                    <?= __('Edit Accepted Student: ') . (isset($isAdmittedAndHaveDepartment->full_name) ? h($isAdmittedAndHaveDepartment->full_name) . ' (' . h($isAdmittedAndHaveDepartment->studentnumber) . ')' : '') ?>
                </span>
            </h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <div style="margin-top: -30px;"><hr></div>
                    <?= $this->Form->create(null, ['type' => 'post', 'url' => ['action' => 'edit'], 'class' => 'form-horizontal']) ?>
                    <?php if (($this->request->getSession()->read('Auth.User.role_id') == ROLE_REGISTRAR || $this->request->getSession()->read('Auth.User.role_id') == ROLE_COLLEGE) && !empty($this->request->getData('AcceptedStudent'))): ?>
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-4">
                                    <?= $this->Form->control('id') ?>
                                    <?= $this->Form->control('first_name', [
                                        'style' => 'width: 100%',
                                        'label' => ['text' => __('First Name'), 'class' => 'control-label'],
                                        'required' => true,
                                        'readonly' => ($this->request->getSession()->read('Auth.User.role_id') != ROLE_REGISTRAR || (isset($isAdmittedAndHaveDepartment->Student) || $isAdmittedAndHaveDepartment->Student->graduated) || ($this->request->getSession()->read('Auth.User.role_id') == ROLE_REGISTRAR && $this->request->getSession()->read('Auth.User.is_admin') != 1)) ? true : false,
                                        'class' => 'form-control'
                                    ]) ?>
                                    <?= ($this->request->getSession()->read('Auth.User.role_id') != ROLE_REGISTRAR || (isset($isAdmittedAndHaveDepartment->Student) || $isAdmittedAndHaveDepartment->Student->graduated) || ($this->request->getSession()->read('Auth.User.role_id') == ROLE_REGISTRAR && $this->request->getSession()->read('Auth.User.is_admin') != 1)) ? $this->Form->hidden('first_name') : '' ?>
                                </div>
                                <div class="col-md-4">
                                    <?= $this->Form->control('middle_name', [
                                        'style' => 'width: 100%',
                                        'label' => ['text' => __('Middle Name'), 'class' => 'control-label'],
                                        'required' => true,
                                        'readonly' => ($this->request->getSession()->read('Auth.User.role_id') != ROLE_REGISTRAR || (isset($isAdmittedAndHaveDepartment->Student) || $isAdmittedAndHaveDepartment->Student->graduated) || ($this->request->getSession()->read('Auth.User.role_id') == ROLE_REGISTRAR && $this->request->getSession()->read('Auth.User.is_admin') != 1)) ? true : false,
                                        'class' => 'form-control'
                                    ]) ?>
                                    <?= ($this->request->getSession()->read('Auth.User.role_id') != ROLE_REGISTRAR || (isset($isAdmittedAndHaveDepartment->Student) || $isAdmittedAndHaveDepartment->Student->graduated) || ($this->request->getSession()->read('Auth.User.role_id') == ROLE_REGISTRAR && $this->request->getSession()->read('Auth.User.is_admin') != 1)) ? $this->Form->hidden('middle_name') : '' ?>
                                </div>
                                <div class="col-md-4">
                                    <?= $this->Form->control('last_name', [
                                        'style' => 'width: 100%',
                                        'label' => ['text' => __('Last Name'), 'class' => 'control-label'],
                                        'required' => true,
                                        'readonly' => ($this->request->getSession()->read('Auth.User.role_id') != ROLE_REGISTRAR || (isset($isAdmittedAndHaveDepartment->Student) || $isAdmittedAndHaveDepartment->Student->graduated) || ($this->request->getSession()->read('Auth.User.role_id') == ROLE_REGISTRAR && $this->request->getSession()->read('Auth.User.is_admin') != 1)) ? true : false,
                                        'class' => 'form-control'
                                    ]) ?>
                                    <?= ($this->request->getSession()->read('Auth.User.role_id') != ROLE_REGISTRAR || (isset($isAdmittedAndHaveDepartment->Student) || $isAdmittedAndHaveDepartment->Student->graduated) || ($this->request->getSession()->read('Auth.User.role_id') == ROLE_REGISTRAR && $this->request->getSession()->read('Auth.User.is_admin') != 1)) ? $this->Form->hidden('last_name') : '' ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-4">
                                    <h6 class="text-muted fs-5"><?= __('Sex') ?>:</h6>
                                    <?= $this->Form->control('sex', [
                                        'options' => ['male' => __('Male'), 'female' => __('Female')],
                                        'type' => 'radio',
                                        'label' => false,
                                        'separator' => ' &nbsp; ',
                                        'class' => 'form-check-input'
                                    ]) ?>
                                </div>
                                <div class="col-md-4">
                                    <?= $this->Form->control('EHEECE_total_results', [
                                        'style' => 'width: 100%',
                                        'label' => ['text' => __('EHEECE Result'), 'class' => 'control-label'],
                                        'required' => true,
                                        'readonly' => ($this->request->getSession()->read('Auth.User.role_id') != ROLE_REGISTRAR) ? true : false,
                                        'class' => 'form-control'
                                    ]) ?>
                                </div>
                                <div class="col-md-4">
                                    <?= $this->Form->control('moeadmissionnumber', [
                                        'style' => 'width: 100%',
                                        'label' => ['text' => __('MoE Admission Number'), 'class' => 'control-label'],
                                        'readonly' => ($this->request->getSession()->read('Auth.User.role_id') == ROLE_REGISTRAR && $this->request->getSession()->read('Auth.User.is_admin') == 1) ? false : true,
                                        'class' => 'form-control'
                                    ]) ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="row">
                                <?php if (isset($isAdmittedAndHaveDepartment->Student) && !empty($isAdmittedAndHaveDepartment->Student)): ?>
                                    <div class="col-md-4">
                                        <?= $this->Form->control('studentnumber', [
                                            'style' => 'width: 100%',
                                            'label' => ['text' => __('Student ID'), 'class' => 'control-label'],
                                            'required' => true,
                                            'readonly' => true,
                                            'class' => 'form-control'
                                        ]) ?>
                                    </div>
                                <?php else: ?>
                                    <div class="col-md-4">
                                        <?= $this->Form->control('studentnumber', [
                                            'style' => 'width: 100%',
                                            'label' => ['text' => __('Student ID'), 'class' => 'control-label'],
                                            'required' => true,
                                            'readonly' => ($this->request->getSession()->read('Auth.User.role_id') == ROLE_REGISTRAR && $this->request->getSession()->read('Auth.User.is_admin') == 1) ? false : true,
                                            'class' => 'form-control'
                                        ]) ?>
                                    </div>
                                <?php endif; ?>
                                <div class="col-md-8">
                                    <?= $this->Form->control('high_school', [
                                        'style' => 'width: 100%',
                                        'label' => ['text' => __('High School Attended'), 'class' => 'control-label'],
                                        'readonly' => ($this->request->getSession()->read('Auth.User.role_id') != ROLE_REGISTRAR) ? true : false,
                                        'class' => 'form-control'
                                    ]) ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <hr>
                            <div class="row">
                                <?php if ($this->request->getSession()->read('Auth.User.role_id') == ROLE_REGISTRAR): ?>
                                    <div class="col-md-3">
                                        <?= $this->Form->control('academic_year', [
                                            'style' => 'width: 100%',
                                            'id' => 'academic-year',
                                            'label' => ['text' => __('Admission Year'), 'class' => 'control-label'],
                                            'type' => 'select',
                                            'options' => $academicYearList,
                                            'disabled' => ($this->request->getSession()->read('Auth.User.role_id') == ROLE_REGISTRAR && $this->request->getSession()->read('Auth.User.is_admin') == 1) ? false : true,
                                            'empty' => __('[ Select Admission Year ]'),
                                            'value' => $currentAcademicYearData ?? '',
                                            'class' => 'form-control'
                                        ]) ?>
                                        <?= ($this->request->getSession()->read('Auth.User.role_id') == ROLE_REGISTRAR && $this->request->getSession()->read('Auth.User.is_admin') != 1) ? $this->Form->hidden('academic_year') : '' ?>
                                    </div>
                                <?php else: ?>
                                    <div class="col-md-3">
                                        <?= $this->Form->control('academic_year', [
                                            'style' => 'width: 100%',
                                            'id' => 'academic-year',
                                            'label' => ['text' => __('Admission Year'), 'class' => 'control-label'],
                                            'type' => 'select',
                                            'options' => $academicYearList,
                                            'disabled' => true,
                                            'empty' => __('[ Select Admission Year ]'),
                                            'value' => $currentAcademicYearData ?? '',
                                            'class' => 'form-control'
                                        ]) ?>
                                        <?= ($this->request->getSession()->read('Auth.User.role_id') == ROLE_REGISTRAR && $this->request->getSession()->read('Auth.User.is_admin') != 1) ? $this->Form->hidden('academic_year') : '' ?>
                                    </div>
                                <?php endif; ?>
                                <?php if (isset($isAdmittedAndHaveDepartment->Student) && !empty($isAdmittedAndHaveDepartment->Student)): ?>
                                    <div class="col-md-3">
                                        <?= $this->Form->control('program_id', [
                                            'style' => 'width: 100%',
                                            'label' => ['text' => __('Program'), 'class' => 'control-label'],
                                            'disabled' => true,
                                            'class' => 'form-control'
                                        ]) ?>
                                        <?= $this->Form->hidden('program_id') ?>
                                    </div>
                                    <div class="col-md-3">
                                        <?= $this->Form->control('program_type_id', [
                                            'style' => 'width: 100%',
                                            'label' => ['text' => __('Program Type'), 'class' => 'control-label'],
                                            'disabled' => true,
                                            'class' => 'form-control'
                                        ]) ?>
                                        <?= $this->Form->hidden('program_type_id') ?>
                                    </div>
                                <?php else: ?>
                                    <div class="col-md-3">
                                        <?= $this->Form->control('program_id', [
                                            'style' => 'width: 100%',
                                            'label' => ['text' => __('Program'), 'class' => 'control-label'],
                                            'disabled' => ($this->request->getSession()->read('Auth.User.role_id') == ROLE_REGISTRAR && $this->request->getSession()->read('Auth.User.is_admin') == 1) ? false : true,
                                            'class' => 'form-control'
                                        ]) ?>
                                        <?= ($this->request->getSession()->read('Auth.User.role_id') == ROLE_REGISTRAR && $this->request->getSession()->read('Auth.User.is_admin') == 1) ? '' : $this->Form->hidden('program_id') ?>
                                    </div>
                                    <div class="col-md-3">
                                        <?= $this->Form->control('program_type_id', [
                                            'style' => 'width: 100%',
                                            'label' => ['text' => __('Program Type'), 'class' => 'control-label'],
                                            'disabled' => ($this->request->getSession()->read('Auth.User.role_id') == ROLE_REGISTRAR && $this->request->getSession()->read('Auth.User.is_admin') == 1) ? false : true,
                                            'class' => 'form-control'
                                        ]) ?>
                                        <?= ($this->request->getSession()->read('Auth.User.role_id') == ROLE_REGISTRAR && $this->request->getSession()->read('Auth.User.is_admin') == 1) ? '' : $this->Form->hidden('program_type_id') ?>
                                    </div>
                                <?php endif; ?>
                                <div class="col-md-3">
                                    <?= $this->Form->control('placement_type_id', [
                                        'style' => 'width: 100%',
                                        'label' => ['text' => __('Placement Type'), 'class' => 'control-label'],
                                        'empty' => __('[ Select Placement Type ]'),
                                        'disabled' => ($this->request->getSession()->read('Auth.User.role_id') == ROLE_REGISTRAR && $this->request->getSession()->read('Auth.User.is_admin') == 1) ? false : true,
                                        'class' => 'form-control'
                                    ]) ?>
                                    <?= ($this->request->getSession()->read('Auth.User.role_id') == ROLE_REGISTRAR && $this->request->getSession()->read('Auth.User.is_admin') == 1) ? '' : $this->Form->hidden('placement_type_id') ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <?php if (isset($isAdmittedAndHaveDepartment->Student) && !empty($isAdmittedAndHaveDepartment->Student) && !empty($isAdmittedAndHaveDepartment->Student->department_id)): ?>
                                <?php if (isset($isAdmittedAndHaveDepartment->Student->CourseRegistration) && !empty($isAdmittedAndHaveDepartment->Student->CourseRegistration)): ?>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <?= $this->Form->control('campus_id', [
                                                'style' => 'width: 100%',
                                                'label' => ['text' => __('Campus'), 'class' => 'control-label'],
                                                'disabled' => true,
                                                'empty' => __('[ Select Campus ]'),
                                                'class' => 'form-control'
                                            ]) ?>
                                            <?= $this->Form->hidden('campus_id') ?>
                                        </div>
                                        <div class="col-md-4">
                                            <?= $this->Form->control('college_id', [
                                                'style' => 'width: 100%',
                                                'label' => ['text' => __('College'), 'class' => 'control-label'],
                                                'id' => 'college-id',
                                                'disabled' => true,
                                                'class' => 'form-control'
                                            ]) ?>
                                            <?= $this->Form->hidden('college_id') ?>
                                        </div>
                                        <div class="col-md-4">
                                            <?= $this->Form->control('department_id', [
                                                'style' => 'width: 100%',
                                                'label' => ['text' => __('Department'), 'class' => 'control-label'],
                                                'value' => $selectedDepartment ?? '',
                                                'empty' => __('College Freshman'),
                                                'id' => 'department-id',
                                                'disabled' => true,
                                                'class' => 'form-control'
                                            ]) ?>
                                            <?= $this->Form->hidden('department_id') ?>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <?= $this->Form->control('campus_id', [
                                                'style' => 'width: 100%',
                                                'label' => ['text' => __('Campus'), 'class' => 'control-label'],
                                                'disabled' => true,
                                                'empty' => __('[ Select Campus ]'),
                                                'class' => 'form-control'
                                            ]) ?>
                                            <?= $this->Form->hidden('campus_id') ?>
                                        </div>
                                        <div class="col-md-4">
                                            <?= $this->Form->control('college_id', [
                                                'style' => 'width: 100%',
                                                'label' => ['text' => __('College'), 'class' => 'control-label'],
                                                'id' => 'college-id',
                                                'disabled' => true,
                                                'class' => 'form-control'
                                            ]) ?>
                                            <?= $this->Form->hidden('college_id') ?>
                                        </div>
                                        <div class="col-md-4">
                                            <?= $this->Form->control('department_id', [
                                                'style' => 'width: 100%',
                                                'label' => ['text' => __('Department'), 'class' => 'control-label'],
                                                'value' => $selectedDepartment ?? '',
                                                'empty' => __('College Freshman'),
                                                'id' => 'department-id',
                                                'disabled' => true,
                                                'class' => 'form-control'
                                            ]) ?>
                                            <?= $this->Form->hidden('department_id') ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php else: ?>
                                <div class="row">
                                    <div class="col-md-4">
                                        <?= $this->Form->control('campus_id', [
                                            'style' => 'width: 100%',
                                            'label' => ['text' => __('Campus'), 'class' => 'control-label'],
                                            'disabled' => true,
                                            'empty' => __('[ Select Campus ]'),
                                            'class' => 'form-control'
                                        ]) ?>
                                        <?= $this->Form->hidden('campus_id') ?>
                                    </div>
                                    <div class="col-md-4">
                                        <?= $this->Form->control('college_id', [
                                            'style' => 'width: 100%',
                                            'label' => ['text' => __('College'), 'class' => 'control-label'],
                                            'id' => 'college-id',
                                            'disabled' => ($this->request->getSession()->read('Auth.User.role_id') == ROLE_REGISTRAR && $this->request->getSession()->read('Auth.User.is_admin') == 1) ? false : true,
                                            'class' => 'form-control'
                                        ]) ?>
                                        <?= ($this->request->getSession()->read('Auth.User.role_id') == ROLE_REGISTRAR && $this->request->getSession()->read('Auth.User.is_admin') == 1) ? '' : $this->Form->hidden('college_id') ?>
                                    </div>
                                    <div class="col-md-4">
                                        <?= $this->Form->control('department_id', [
                                            'style' => 'width: 100%',
                                            'label' => ['text' => __('Department'), 'class' => 'control-label'],
                                            'value' => $selectedDepartment ?? '',
                                            'empty' => __('College Freshman'),
                                            'id' => 'department-id',
                                            'disabled' => ($this->request->getSession()->read('Auth.User.role_id') == ROLE_REGISTRAR && $this->request->getSession()->read('Auth.User.is_admin') == 1) ? false : true,
                                            'class' => 'form-control'
                                        ]) ?>
                                        <?= ($this->request->getSession()->read('Auth.User.role_id') == ROLE_REGISTRAR && $this->request->getSession()->read('Auth.User.is_admin') == 1) ? '' : $this->Form->hidden('department_id') ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-4">
                                    <?= $this->Form->control('country_id', [
                                        'id' => 'country-id-1',
                                        'label' => ['text' => __('Country'), 'class' => 'control-label'],
                                        'required' => true,
                                        'options' => $countries,
                                        'style' => 'width: 100%',
                                        'disabled' => ($this->request->getSession()->read('Auth.User.role_id') == ROLE_REGISTRAR) ? false : true,
                                        'class' => 'form-control'
                                    ]) ?>
                                    <?= ($this->request->getSession()->read('Auth.User.role_id') == ROLE_REGISTRAR) ? '' : $this->Form->hidden('Student.country_id') ?>
                                </div>
                                <div class="col-md-4">
                                    <?= $this->Form->control('region_id', [
                                        'id' => 'region-id-1',
                                        'label' => ['text' => __('Region'), 'class' => 'control-label'],
                                        'required' => true,
                                        'options' => $regions,
                                        'style' => 'width: 100%',
                                        'empty' => __('[ Select Region ]'),
                                        'disabled' => ($this->request->getSession()->read('Auth.User.role_id') == ROLE_REGISTRAR) ? false : true,
                                        'class' => 'form-control'
                                    ]) ?>
                                    <?= ($this->request->getSession()->read('Auth.User.role_id') == ROLE_REGISTRAR) ? '' : $this->Form->hidden('region_id') ?>
                                </div>
                                <div class="col-md-4">
                                    <?= $this->Form->control('zone_id', [
                                        'id' => 'zone-id-1',
                                        'label' => ['text' => __('Zone'), 'class' => 'control-label'],
                                        'required' => true,
                                        'options' => $zones,
                                        'style' => 'width: 100%',
                                        'empty' => __('[ Select Zone ]'),
                                        'disabled' => ($this->request->getSession()->read('Auth.User.role_id') == ROLE_REGISTRAR) ? false : true,
                                        'class' => 'form-control'
                                    ]) ?>
                                    <?= ($this->request->getSession()->read('Auth.User.role_id') == ROLE_REGISTRAR) ? '' : $this->Form->hidden('Student.zone_id') ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-4">
                                    <?= $this->Form->control('woreda_id', [
                                        'id' => 'woreda-id-1',
                                        'label' => ['text' => __('Woreda'), 'class' => 'control-label'],
                                        'required' => true,
                                        'options' => $woredas,
                                        'style' => 'width: 100%',
                                        'empty' => __('[ Select Woreda ]'),
                                        'disabled' => ($this->request->getSession()->read('Auth.User.role_id') == ROLE_REGISTRAR) ? false : true,
                                        'class' => 'form-control'
                                    ]) ?>
                                    <?= ($this->request->getSession()->read('Auth.User.role_id') == ROLE_REGISTRAR) ? '' : $this->Form->hidden('Student.woreda_id') ?>
                                </div>
                                <div class="col-md-4">
                                    <?= $this->Form->control('city_id', [
                                        'id' => 'city-id-1',
                                        'label' => ['text' => __('City'), 'class' => 'control-label'],
                                        'options' => $cities,
                                        'value' => (!empty($studentDetail->Student->city_id) ? $studentDetail->Student->city_id : ''),
                                        'style' => 'width: 100%',
                                        'empty' => __('[ Select City ]'),
                                        'disabled' => ($this->request->getSession()->read('Auth.User.role_id') == ROLE_REGISTRAR) ? false : true,
                                        'class' => 'form-control'
                                    ]) ?>
                                    <?= ($this->request->getSession()->read('Auth.User.role_id') == ROLE_REGISTRAR) ? '' : $this->Form->hidden('Student.city_id') ?>
                                </div>
                                <div class="col-md-4"></div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="row">
                                <hr>
                                <div class="col-md-4">
                                    <?= $this->Form->control('benefit_group', [
                                        'label' => ['text' => __('Benefit Group'), 'class' => 'control-label'],
                                        'options' => \Cake\Core\Configure::read('benefit_groups'),
                                        'style' => 'width: 100%',
                                        'value' => 'Normal',
                                        'empty' => __('[ Select Benefit Group ]'),
                                        'disabled' => ($this->request->getSession()->read('Auth.User.role_id') == ROLE_REGISTRAR) ? false : true,
                                        'class' => 'form-control'
                                    ]) ?>
                                    <?= ($this->request->getSession()->read('Auth.User.role_id') == ROLE_REGISTRAR) ? '' : $this->Form->hidden('benefit_group') ?>
                                </div>
                                <div class="col-md-4">
                                    <?= $this->Form->control('disability_id', [
                                        'label' => ['text' => __('Disability'), 'class' => 'control-label'],
                                        'style' => 'width: 100%',
                                        'empty' => __('[ Select Disability (If Applicable) ]'),
                                        'class' => 'form-control'
                                    ]) ?>
                                </div>
                                <div class="col-md-4">
                                    <?= $this->Form->control('foreign_program_id', [
                                        'label' => ['text' => __('Foreign Program'), 'class' => 'control-label'],
                                        'style' => 'width: 100%',
                                        'empty' => __('[ Select Foreign Program (If Applicable) ]'),
                                        'class' => 'form-control'
                                    ]) ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="row">
                                <hr>
                                <div class="form-group">
                                    <?= $this->Form->button(__('Save Changes'), ['id' => 'save-it', 'name' => 'updateAcceptedStudentDetail', 'class' => 'btn btn-primary']) ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    <?= $this->Form->end() ?>
                </div>
            </div>
        </div>
    </div>
</div>

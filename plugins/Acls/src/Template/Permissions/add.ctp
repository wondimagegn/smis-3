<?php
/**
 * ACL Management Plugin
 *
 * @copyright     Copyright 2010, Joseph B Crawford II
 * @link          http://www.jbcrawford.net
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
?>
<div class="box">
    <div class="box-header bg-transparent">
        <div class="box-title" style="margin-top: 10px;">
            <i class="fontello-plus"></i>
            <span style="font-size: medium; font-weight: bold; margin-top: 20px;">
                <?= __('Add Permission to <u>{0}</u>', !empty($aco->privilage) ? h($aco->privilage) : h(implode('/', $path))) ?>
            </span>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="large-12 columns" style="margin-top: -20px;">
                <?= $this->Form->create($permission, ['url' => ['action' => 'add'], 'onsubmit' => 'return checkForm(this);']) ?>
                <?= $this->Form->control('aco_id', ['type' => 'hidden']) ?>
                <hr>
                <div class="row">
                    <div class="large-4 columns">
                        <?= $this->Form->control('role_id', [
                            'label' => 'Role: ',
                            'style' => 'width:90%',
                            'onchange' => 'getUsersBasedOnRole(this)',
                            'id' => 'RoleID',
                            'type' => 'select',
                            'default' => $roleId,
                            'options' => $roles,
                        ]) ?>
                    </div>
                </div>
                <div class="row">
                    <div class="large-4 columns">
                        <?= $this->Form->control('aro_id', [
                            'label' => 'Users: ',
                            'style' => 'width:90%',
                            'id' => 'AroID',
                            'onchange' => 'toggleSubmitButtonActive()',
                            'options' => $users,
                        ]) ?>
                    </div>
                </div>
                <div class="row">
                    <div class="large-4 columns">
                        <?= $this->Form->control('privilege', [
                            'label' => 'Privilege: ',
                            'id' => 'PrivilegeID',
                            'style' => 'width:50%',
                            'options' => $perms,
                        ]) ?>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="large-2 columns">
                        <?= $this->Form->button(__('Submit'), ['id' => 'SubmitID', 'disabled' => true, 'class' => 'tiny radius button bg-blue']) ?>
                    </div>
                    <div class="large-10 columns">
                        <?= $this->Html->link(__('Cancel'), ['action' => 'index', $permission->aco_id ?? $aco->id], ['class' => 'tiny radius button bg-blue']) ?>
                    </div>
                </div>
                <?= $this->Form->end() ?>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    function getUsersBasedOnRole(obj) {
        $("#RoleID").attr('disabled', true);
        $("#AroID").attr('disabled', true);
        $("#PrivilegeID").attr('disabled', true);
        $("#SubmitID").attr('disabled', true);
        window.location.replace(<?= json_encode($this->Url->build(['action' => 'add', $aco->id, '_ext' => ''])) ?> + obj.value);
    }

    function toggleSubmitButtonActive() {
        const aroId = $("#AroID").val();
        if (aroId != 0 && aroId != '') {
            $("#SubmitID").attr('disabled', false);
        }
    }

    let formBeingSubmitted = false; /* global variable */

    function checkForm(form) {
        if (form.AroID.value == 0) {
            form.AroID.focus();
            return false;
        }
        if (form.RoleID.value == 0) {
            form.RoleID.focus();
            return false;
        }

        if (formBeingSubmitted) {
            alert("Adding Permission, please wait a moment...");
            form.SubmitID.disabled = true;
            return false;
        }

        form.SubmitID.value = 'Adding Permission...';
        formBeingSubmitted = true;
        return true; /* submit form */
    }

    // Prevent form resubmission and disable default JS warning dialog
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }
</script>

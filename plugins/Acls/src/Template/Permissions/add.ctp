<?php
/**
 * ACL Management Plugin
 *
 * @copyright     Copyright 2010, Joseph B Crawford II
 * @link          http://www.jbcrawford.net
 * @license       MIT License[](http://www.opensource.org/licenses/mit-license.php)
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
                <?= $this->Form->create($permission, ['url' => ['action' => 'add', $aco->id, $roleId], 'onsubmit' => 'return checkForm(this);']) ?>
                <?= $this->Form->control('Permission.aco_id', ['type' => 'hidden', 'value' => $aco->id]) ?>
                <hr>
                <div class="row">
                    <div class="large-4 columns">
                        <?= $this->Form->control('Permission.role_id', [
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
                        <?= $this->Form->control('Permission.aro_id', [
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
                        <?= $this->Form->control('Permission.privilege', [
                            'label' => 'Privilege: ',
                            'id' => 'PrivilegeID',
                            'style' => 'width:50%',
                            'options' => $perms,
                            'empty' => '[ Select Privilege ]'
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
        const acoId = <?= json_encode($aco->id) ?>;
        const roleId = obj.value;
        const url = <?= json_encode($this->Url->build(['action' => 'add', '_ext' => ''])) ?> + '/' + acoId + '/' + roleId;
        window.location.replace(url);
    }

    function toggleSubmitButtonActive() {
        const aroId = $("#AroID").val();
        if (aroId != 0 && aroId != '') {
            $("#SubmitID").attr('disabled', false);
        }
    }

    let formBeingSubmitted = false;

    function checkForm(form) {
        if (form['Permission.aro_id'].value == 0) {
            form['Permission.aro_id'].focus();
            return false;
        }
        if (form['Permission.role_id'].value == 0) {
            form['Permission.role_id'].focus();
            return false;
        }

        if (formBeingSubmitted) {
            alert("Adding Permission, please wait a moment...");
            form.SubmitID.disabled = true;
            return false;
        }

        form.SubmitID.value = 'Adding Permission...';
        formBeingSubmitted = true;
        return true;
    }

    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }
</script>

<div class="box">
    <div class="box-header bg-transparent">
        <div class="box-title" style="margin-top: 10px;">
            <i class="fontello-plus"></i>
            <span style="font-size: medium; font-weight: bold; margin-top: 20px;">
                Add Permission to <u><?= !empty($aco->privilage) ? h($aco->privilage) : implode('/', $path); ?></u>
            </span>
        </div>
    </div>

    <div class="box-body">
        <div class="row">
            <div class="large-12 columns" style="margin-top: -20px;">
                <?= $this->Form->create($permission, ['onsubmit' => 'return checkForm(this);']) ?>
                <?= $this->Form->control('aco_id', ['type' => 'hidden']) ?>

                <hr>

                <div class="row">
                    <div class="large-4 columns">
                        <?= $this->Form->control('role_id', [
                            'label' => 'Role:',
                            'style' => 'width:90%',
                            'onchange' => 'getUsersBasedOnRole(this)',
                            'id' => 'RoleID',
                            'type' => 'select',
                            'default' => $role_id,
                            'options' => $roles
                        ]) ?>
                    </div>
                </div>

                <div class="row">
                    <div class="large-4 columns">
                        <?= $this->Form->control('aro_id', [
                            'label' => 'Users:',
                            'style' => 'width:90%',
                            'id' => 'AroID',
                            'onchange' => 'toggleSubmitButtonActive()',
                            'options' => $users
                        ]) ?>
                    </div>
                </div>

                <div class="row">
                    <div class="large-4 columns">
                        <?= $this->Form->control('privilege', [
                            'label' => 'Privilege:',
                            'id' => 'PrivilegeID',
                            'style' => 'width:50%',
                            'options' => $perms
                        ]) ?>
                    </div>
                </div>

                <hr>

                <div class="row">
                    <div class="large-2 columns">
                        <?= $this->Form->button('Submit', [
                            'id' => 'SubmitID',
                            'disabled' => true,
                            'class' => 'tiny radius button bg-blue'
                        ]) ?>
                    </div>

                    <div class="large-10 columns">
                        <?= $this->Html->link('Cancel', ['action' => 'index', $this->request->getData('aco_id')], ['class' => 'tiny radius button bg-blue']) ?>
                    </div>
                </div>

                <?= $this->Form->end() ?>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    function getUsersBasedOnRole(obj) {
        document.getElementById("RoleID").disabled = true;
        document.getElementById("AroID").disabled = true;
        document.getElementById("PrivilegeID").disabled = true;
        document.getElementById("SubmitID").disabled = true;
        window.location.href = "/acls/permissions/add/<?= $aco->id; ?>/" + obj.value;
    }

    function toggleSubmitButtonActive() {
        const aroID = document.getElementById("AroID");
        const submitID = document.getElementById("SubmitID");

        submitID.disabled = !(aroID.value && aroID.value !== "0");
    }

    let formBeingSubmitted = false;

    function checkForm(form) {
        if (form.AroID.value === "0" || form.AroID.value === "") {
            form.AroID.focus();
            return false;
        }
        if (form.RoleID.value === "0" || form.RoleID.value === "") {
            form.RoleID.focus();
            return false;
        }

        if (formBeingSubmitted) {
            alert("Adding Permission, please wait a moment...");
            form.SubmitID.disabled = true;
            return false;
        }

        form.SubmitID.value = "Adding Permission...";
        formBeingSubmitted = true;
        return true;
    }

    // Prevent resubmission using back button or page refresh
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }
</script>

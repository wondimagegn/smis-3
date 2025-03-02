<div class="box">
    <div class="box-header bg-transparent">
        <div class="box-title" style="margin-top: 10px;">
            <i class="fontello-params" style="font-size: larger; font-weight: bold;"></i>
            <span style="font-size: medium; font-weight: bold; margin-top: 20px;"><?= __('Permission Management'); ?></span>
        </div>
    </div>

    <div class="box-body">
        <div class="row">
            <div class="large-12 columns">
                <div id="breadcrumbs">
                    <?php
                    if (!empty($path)) {
                        $last = array_pop($path);
                        foreach ($path as $id => $alias) {
                            echo $this->Html->link($alias, ['controller' => 'Acos', 'action' => 'index', $id]) . ' &#8250; ';
                        }
                    }
                    echo $this->Html->link('Tasks', ['controller' => 'Acos', 'action' => 'index', $id]) .
                        ' &#8250; ' . (!empty($aco->privilage) ? h($aco->privilage) : h($last));
                    ?>
                    <hr>
                    <?= $this->Html->link('Add Permission', ['action' => 'add', $aco_id], ['escape' => false, 'title' => 'Add Permission', 'class' => 'tiny radius button bg-blue']) ?>
                </div>

                <?php if (!empty($permissions)): ?>
                    <?= $this->Form->create($permission, ['url' => ['action' => 'delete'], 'id' => 'permission-form', 'onsubmit' => 'return checkForm(this);']) ?>

                    <h6 id="validation-message_non_selected" class="text-red fs14"></h6>
                    <br>

                    <div style="overflow-x:auto;">
                        <table cellpadding="0" cellspacing="0" class="table">
                            <thead>
                            <tr>
                                <td style="width:5%" class="center"><?= $this->Form->control('select_all', ['type' => 'checkbox', 'id' => 'select-all']) ?></td>
                                <td style="width:5%" class="center">&nbsp;</td>
                                <td style="width:30%" class="vcenter">User/Role</td>
                                <td style="width:10%;" class="center">Privilege</td>
                                <td style="width:50%;" class="center"></td>
                            </tr>
                            </thead>

                            <tbody>
                            <?php foreach ($permissions as $i): ?>
                                <tr>
                                    <td class="center">
                                        <?php if (($this->request->getSession()->read('Auth.User.role_id') == ROLE_SYSADMIN ||
                                                $this->request->getSession()->read('Auth.User.role_id') == ROLE_REGISTRAR) &&
                                            ($i['Aro']['model'] == 'Role')): ?>
                                            <?= $this->Form->control('Permission.delete.' . $i['Permission']['id'], ['type' => 'checkbox', 'class' => 'checkbox1']) ?>
                                        <?php elseif ($i['Aro']['model'] == 'User'): ?>
                                            <?= $this->Form->control('Permission.delete.' . $i['Permission']['id'], ['type' => 'checkbox', 'class' => 'checkbox1']) ?>
                                        <?php else: ?>
                                            **
                                        <?php endif; ?>
                                    </td>

                                    <td class="center">
                                        <?= $this->Html->link(
                                            $this->Html->image('/acls/img/edit.png', ['alt' => 'Edit Permission']),
                                            ['action' => 'edit', $aco_id, $i['Permission']['id']],
                                            ['escape' => false, 'title' => 'Edit Permission']
                                        ) ?>
                                    </td>

                                    <td class="vcenter">
                                        <?= $i['Aro']['model'] == 'Role' ? 'Role: ' . $roles[$i['Aro']['foreign_key']] : 'Users: ' . $users[$i['Aro']['foreign_key']] ?>
                                        <br>
                                        <small><?= !empty($i['Aco']['privilage']) ? h($i['Aco']['privilage']) : h($i['Permission']['path']) ?></small>
                                    </td>

                                    <td class="center">
                                        <?= $i['Permission']['_create'] == 1
                                            ? '<span class="accepted">' . $perms[$i['Permission']['_create']] . '</span>'
                                            : '<span class="rejected">' . $perms[$i['Permission']['_create']] . '</span>'
                                        ?>
                                    </td>

                                    <td class="center">&nbsp;</td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <hr>

                    <?= $this->Form->control('aco_id', ['type' => 'hidden', 'value' => $aco_id]) ?>
                    <?= $this->Form->button('Delete Selected', ['id' => 'deleteSelectedPermissions', 'class' => 'tiny radius button bg-blue']) ?>

                    <?= $this->Form->end() ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    var formBeingSubmitted = false;
    const validationMessageNonSelected = document.getElementById('validation-message_non_selected');

    const checkForm = (form) => {
        const checkboxes = document.querySelectorAll('input[type="checkbox"].checkbox1');
        const checkedOne = Array.from(checkboxes).some(x => x.checked);

        if (!checkedOne) {
            validationMessageNonSelected.innerHTML = 'At least one permission must be selected to delete!';
            return false;
        }

        if (formBeingSubmitted) {
            alert("Deleting Selected Permissions, please wait a moment...");
            form.deleteSelectedPermissions.disabled = true;
            return false;
        }

        form.deleteSelectedPermissions.value = 'Deleting Selected Permissions...';
        formBeingSubmitted = true;
        return true;
    };

    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }

    document.getElementById('select-all').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.checkbox1');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });
</script>

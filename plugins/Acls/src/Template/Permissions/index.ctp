<?php
/**
 * ACL Management Plugin
 *
 * @copyright     Copyright 2010, Joseph B Crawford II
 * @link          http://www.jbcrawford.net
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
use Cake\Utility\Inflector;
?>
<div class="box">
    <div class="box-header bg-transparent">
        <div class="box-title" style="margin-top: 10px;">
            <i class="fontello-params" style="font-size: larger; font-weight: bold;"></i>
            <span style="font-size: medium; font-weight: bold; margin-top: 20px;"><?= __('Permission Management') ?></span>
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
                            $this->Html->addCrumb($alias, ['controller' => 'Acos', 'action' => 'index', $id]);
                        }
                    }

                    echo $this->Html->link('Tasks', array('controller' => 'Acos', 'action' => 'index', $id)) . ' &#8250; ' .
                        (!empty($aco->privilage) ? Inflector::humanize(Inflector::underscore($aco->privilage)) :
                            Inflector::humanize(Inflector::underscore($last))); ?>
                    <hr>
                    <?= $this->Html->link(__('Add Permission'), ['action' => 'add', $acoId], ['class' => 'tiny radius button bg-blue', 'title' => 'Add Permission']) ?>
                </div>


                <?php if (!empty($permissions)): ?>
                    <?= $this->Form->create(null,
                        ['url' => ['action' => 'delete'],
                            'id' => 'permission-form', 'onsubmit' => 'return checkForm(this);']) ?>

                    <h6 id="validation-message_non_selected" class="text-red fs14"></h6>
                    <br>

                    <div style="overflow-x:auto;">
                        <table cellpadding="0" cellspacing="0" class="table">
                            <thead>
                            <tr>
                                <td style="width:5%" class="center"><?= $this->Form->checkbox('select_all', ['id' => 'select-all']) ?></td>
                                <td style="width:5%" class="center"> </td>
                                <td style="width:30%" class="vcenter"><?= __('User/Role') ?></td>
                                <td style="width:10%;" class="center"><?= __('Privilege') ?></td>
                                <td style="width:50%;" class="center"></td>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $count = 0; ?>
                            <?php foreach ($permissions as $i): ?>
                                <?php $count++; ?>
                                <tr>
                                    <td class="center">
                                        <?php
                                        $currentRoleId = $this->request->getSession()->read('Auth.User.role_id');
                                        if (($currentRoleId == ROLE_SYSADMIN || $currentRoleId == ROLE_REGISTRAR)
                                            && $i->aro->model == 'Roles') {
                                            echo $this->Form->checkbox("Permission.delete.{$i->id}", ['class' => 'checkbox1']);
                                        } elseif ($i->aro->model == 'Users') {
                                            echo $this->Form->checkbox("Permission.delete.{$i->id}", ['class' => 'checkbox1']);
                                        } else {
                                            echo '**';
                                        }
                                        ?>
                                    </td>
                                    <td class="center">
                                        <?= $this->Html->link(
                                            $this->Html->image('/acls/img/edit.png', ['alt' => 'Edit Permission']),
                                            ['action' => 'edit', $acoId, $i->id],
                                            ['escape' => false, 'title' => 'Edit Permission']
                                        ) ?>
                                    </td>
                                    <td class="vcenter">
                                        <?= $i->aro->model == 'Roles' ?
                                            __('Role: {0}', h($roles[$i->aro->foreign_key])) :
                                            __('Users: {0}', h($users[$i->aro->foreign_key])) ?>
                                        <br><small><?= !empty($i->aco->privilage) ? h($i->aco->privilage) : h($i->path) ?></small>
                                    </td>
                                    <td class="center">
                                        <?= $i->_create == 1 ?
                                            '<span class="accepted">' . h($perms[$i->_create]) . '</span>' :
                                            '<span class="rejected">' . h($perms[$i->_create]) . '</span>' ?>
                                    </td>
                                    <td class="center"> </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <hr>
                    <?= $this->Form->control('aco_id', ['type' => 'hidden', 'value' => $acoId]) ?>
                    <?= $this->Form->button(__('Delete Selected'), ['id' => 'deleteSelectedPermissions', 'class' => 'tiny radius button bg-blue']) ?>
                    <?= $this->Form->end() ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    let formBeingSubmitted = false;
    const validationMessageNonSelected = document.getElementById('validation-message_non_selected');

    function checkForm(form) {
        const checkboxes = document.querySelectorAll('input[type="checkbox"]');
        const checkedOne = Array.prototype.slice.call(checkboxes).some(x => x.checked);

        if (!checkedOne) {
            validationMessageNonSelected.innerHTML = '<?= __('At least one permission must be selected to delete!') ?>';
            return false;
        }

        if (formBeingSubmitted) {
            alert("<?= __('Deleting Selected Permissions, please wait a moment...') ?>");
            form.deleteSelectedPermissions.disabled = true;
            return false;
        }

        form.deleteSelectedPermissions.value = '<?= __('Deleting Selected Permissions...') ?>';
        formBeingSubmitted = true;
        return true;
    }

    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }
</script>

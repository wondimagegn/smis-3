<?php
use Cake\I18n\Time;
use Cake\I18n\I18n;
use Cake\Core\Configure;
?>

<script type="text/javascript">
    const inst_role = '<?= ROLE_INSTRUCTOR; ?>';
    $(document).ready(function() {
        $('#SearchRoleId').change(function() {
            if ($(this).val() == inst_role) {
                $('#showDepartmentDropDown').show();
            } else {
                $('#showDepartmentDropDown').hide();
                $('#SearchStaffDepartmentId').val('');
            }
        });
        $('#getUsers').click(function() {
            $('#getUsers').val('Searching...');
            $("#show_list_of_users").hide();
        });
        $('#select-all').click(function(event) {
            $('.checkbox1').prop('checked', $(this).prop('checked'));
        });
        $('.checkbox1').click(function(event) {
            if (!this.checked) {
                $('#select-all').prop('checked', false);
            }
        });
        $('#delete-form').on('submit', function() {
            var checkboxes = $('input.checkbox1:checked').length;
            if (checkboxes === 0) {
                alert('<?= __('At least one user must be selected to delete!') ?>');
                return false;
            }
            return true;
        });
    });
</script>

<div class="box" style="display: block;">
    <div class="box-header bg-transparent">
        <div class="box-title" style="margin-top: 10px;">
            <i class="fontello-th-list" style="font-size: larger; font-weight: bold;"></i>
            <span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('List of Users') ?></span>
        </div>
    </div>
    <div class="box-body">
        <div style="margin-top: -30px;">
            <hr>
            <blockquote>
                <h6><i class="fa fa-info"></i> &nbsp; Important Note:</h6>
                <p style="text-align:justify;" class="fs16 text-black">
                    Clicking on the <strong>Construct Menu</strong> link will run an expensive process that will consume extensive system resources. Please click on <strong>Construct Menu</strong> only if there is a change in user privileges, assignment of new privilege(s) to the user, or revoked privilege(s) from the user.
                </p>
            </blockquote>
            <hr>

            <!-- Search Form -->
            <?= $this->Form->create(null, ['type' => 'get', 'url' => ['action' => 'index'], 'class' => 'form-horizontal']) ?>
            <fieldset style="padding-bottom: 0px;">
                <div class="row align-items-center">
                    <div class="large-4 columns">
                        <?= $this->Form->control('Search.name', [
                            'label' => ['text' => __('Search Key'), 'class' => 'control-label'],
                            'placeholder' => __('name, username or email'),
                            'value' => $selected_search,
                            'style' => 'width:90%',
                            'class' => 'form-control'
                        ]) ?>
                    </div>
                    <div class="large-4 columns">
                        <?= $this->Form->control('Search.role_id', [
                            'label' => ['text' => __('Role'), 'class' => 'control-label'],
                            'type' => 'select',
                            'options' => $roles,
                            'value' => $selected_role,
                            'style' => 'width:90%',
                            'class' => 'form-control'
                        ]) ?>
                    </div>
                    <div class="large-4 columns align-self-center">
                        <?= $this->Form->control('Search.limit', [
                            'id' => 'limit',
                            'type' => 'number',
                            'min' => 100,
                            'max' => 1000,
                            'value' => $selected_limit,
                            'step' => 100,
                            'label' => ['text' => __('Limit'), 'class' => 'control-label'],
                            'style' => 'width:30%',
                            'class' => 'fs14'
                        ]) ?>
                    </div>
                </div>
                <div class="row align-items-center justify-content-center">
                    <div class="large-4 columns">
                        <?= $this->Form->control('Search.orderby', [
                            'label' => ['text' => __('Order By'), 'class' => 'control-label'],
                            'id' => 'orderby',
                            'type' => 'select',
                            'options' => [
                                'full_name' => __('Full name'),
                                'username' => __('Username'),
                                'email' => __('Email'),
                                'last_login' => __('Last Login'),
                                'active' => __('Active'),
                                'created' => __('Created Date'),
                                'modified' => __('Modified Date')
                            ],
                            'value' => $order_by,
                            'style' => 'width:90%',
                            'class' => 'fs14'
                        ]) ?>
                    </div>
                    <div class="large-4 columns">
                        <?= $this->Form->control('Search.sortorder', [
                            'label' => ['text' => __('Sort'), 'class' => 'control-label'],
                            'id' => 'sortorder',
                            'type' => 'select',
                            'options' => ['asc' => __('Ascending'), 'desc' => __('Descending')],
                            'value' => $sort_order,
                            'style' => 'width:90%',
                            'class' => 'fs14'
                        ]) ?>
                    </div>
                    <div class="large-4 columns">
                        <br>
                        <?= $this->Form->control('Search.Staff.active', [
                            'label' => ['text' => __('Active Staff'), 'class' => 'control-label'],
                            'type' => 'checkbox',
                            'value' => 1,
                            'checked' => $selected_staff_active == 1
                        ]) ?>
                        <?= $this->Form->control('Search.active', [
                            'label' => ['text' => __('Active User'), 'class' => 'control-label'],
                            'type' => 'checkbox',
                            'value' => 1,
                            'checked' => $selected_user_active == 1
                        ]) ?>
                        <?= $this->Form->hidden('page', ['value' => $this->request->getQuery('page')]) ?>
                        <?= $this->Form->hidden('sort', ['value' => $this->request->getQuery('sort')]) ?>
                        <?= $this->Form->hidden('direction', ['value' => $this->request->getQuery('direction')]) ?>
                        <br>
                    </div>
                </div>
                <?php if ($this->request->getSession()->read('Auth.User.role_id') == ROLE_SYSADMIN): ?>
                    <div id="showDepartmentDropDown" style="display: <?= $selected_role == ROLE_INSTRUCTOR ? 'block' : 'none'; ?>">
                        <div class="row align-items-center justify-content-center">
                            <div class="large-4 columns">
                                <?= $this->Form->control('Search.Staff.department_id', [
                                    'label' => ['text' => __('College/Department'), 'class' => 'control-label'],
                                    'type' => 'select',
                                    'options' => $departments,
                                    'empty' => __('[ Any Department ]'),
                                    'value' => $selected_staff_department_id,
                                    'style' => 'width:90%',
                                    'class' => 'form-control'
                                ]) ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                <hr>
                <?= $this->Form->button(__('Search'), [
                    'name' => 'getUsers',
                    'id' => 'getUsers',
                    'class' => 'tiny radius button bg-blue'
                ]) ?>
            </fieldset>
            <?= $this->Form->end() ?>
        </div>
        <hr>
        <div id="show_list_of_users">
            <?php if (!empty($users)): ?>
                <br>
                <div style="overflow-x:auto;">
                    <?= $this->Form->create(null, ['id' => 'delete-form', 'url' => ['action' => 'delete'], 'type' => 'post', 'onsubmit' => 'return checkForm(this);']) ?>
                    <table cellpadding="0" cellspacing="0" class="table">
                        <thead>
                        <tr>
                            <th scope="col" class="center"><?= $this->Form->checkbox('select-all', ['id' => 'select-all']) ?></th>
                            <th scope="col" class="center"><?= $this->Paginator->sort('id', __('#')) ?></th>
                            <th scope="col" style="padding-left: 2%;"><?= $this->Paginator->sort('full_name', __('Full Name (Username | Email)')) ?></th>
                            <th scope="col" class="center"><?= $this->Paginator->sort('role_id', __('Role')) ?></th>
                            <th scope="col" class="center"><?= $this->Paginator->sort('last_login', __('Last Login')) ?></th>
                            <th scope="col" class="center"><?= $this->Paginator->sort('active', __('Active')) ?></th>
                            <th scope="col" class="actions center"><?= __('Actions') ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $count = $this->Paginator->counter(['format' => '{{start}}']); ?>
                        <?php foreach ($users as $user):

                            ?>
                            <tr>
                                <td class="center">
                                    <?= $this->Form->checkbox("User.delete.{$user->id}", ['class' => 'checkbox1']) ?>
                                </td>
                                <td class="center"><?= $count++ ?></td>
                                <td style="padding-left: 1%; padding-right:1%">
                                    <strong><?= h($user->full_name) ?></strong>&nbsp;<br>
                                    <i><?= h($user->username) . ' | ' . (empty($user->email) ? '---' : h($user->email)) ?></i>
                                </td>
                                <td class="center">
                                    <?= h($user->role->name ?? '') ?>
                                    <?php if ($user->is_admin == 1): ?>
                                        <?php if ($this->request->getSession()->read('Auth.User.role_id') == ROLE_COLLEGE): ?>
                                            <br><span class="alert-box success radius">Dean</span>
                                        <?php elseif ($this->request->getSession()->read('Auth.User.role_id') == ROLE_DEPARTMENT): ?>
                                            <br><span class="alert-box success radius">Head</span>
                                        <?php else: ?>
                                            <br><span class="alert-box success radius">Admin</span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                                <td class="center">
                                    <?php
                                    if (empty($user->last_login) || $user->last_login == '0000-00-00 00:00:00') {
                                        echo '<span class="alert-box alert radius">' . __('Never Logged In') . '</span><br>';
                                        echo __('Created on: ') . (new Time($user->created))->format('M j, Y');
                                    } else {
                                        echo (new Time($user->last_login))->timeAgoInWords(['format' => 'M j, Y', 'end' => '1 year', 'accuracy' => ['month' => 'month']]);
                                    }
                                    ?>
                                </td>
                                <td class="center">
                                    <?php
                                    $canBeDeactivated = (new Time())->modify('-' .
                                        Configure::read('Users.AccountDeactivation.yearstoLookGivenLastLogin') . ' years');
                                    if ($user->active == 1) {
                                        echo __('Yes');
                                        $lastLogin = $user->last_login instanceof \Cake\I18n\Time ? $user->last_login : null;
                                        $created = $user->created instanceof \Cake\I18n\Time ? $user->created : null;
                                        if ($lastLogin && $created && $canBeDeactivated->gt($lastLogin) && $canBeDeactivated->gt($created) && $this->request->getSession()->read('Auth.User.id') != $user->id) {
                                            echo '<br>' . $this->Html->link(
                                                    __('Deactivate'),
                                                    ['action' => 'deactivate_account', $user->id],
                                                    ['confirm' => __('Are you sure you want to send account deactivation request to system administrators for {0}?', $user->full_name . ' (' . $user->username . ')')]
                                                );
                                        }
                                    } else {
                                        echo '<span class="alert-box alert radius">' . __('No') . '</span>';
                                        echo '<br>' . $this->Html->link(
                                                __('Activate'),
                                                ['action' => 'activate_account', $user->id],
                                                ['confirm' => __('Are you sure you want to send account activation request to system administrators for {0}?', $user->full_name . ' (' . $user->username . ')')]
                                            );
                                    }
                                    if ($this->request->getSession()->read('Auth.User.id') == $user->id) {
                                        echo '<br><span class="alert-box success radius">' . __('own account') . '</span>';
                                    }
                                    ?>
                                </td>
                                <td class="actions center">
                                    <?= $this->Html->link('', ['action' => 'view', $user->id], ['class' => 'fontello-eye', 'title' => __('View')]) ?>
                                    <?php if ($user->id == $this->request->getSession()->read('Auth.User.id') || $this->request->getSession()->read('Auth.User.role_id') == ROLE_SYSADMIN || (Configure::read('ENABLE_INSTRUCTOR_USER_EDIT_COLLEGE_DEPARTMENT') && $this->request->getSession()->read('Auth.User.is_admin') && in_array($this->request->getSession()->read('Auth.User.role_id'), [ROLE_DEPARTMENT, ROLE_COLLEGE]))): ?>
                                        <?= $this->Html->link('', ['action' => 'edit', $user->id], ['class' => 'fontello-pencil', 'title' => __('Edit')]) ?>
                                    <?php endif; ?>
                                    <?php if ($user->role_id != ROLE_INSTRUCTOR): ?>
                                        <?= $this->Html->link('', ['action' => 'build_user_menu', $user->id], ['class' => 'fontello-clockwise', 'title' => __('Construct Menu')]) ?>
                                    <?php endif; ?>
                                    <?php if ($user->active == 0 && $this->request->getSession()->read('Auth.User.role_id') == $user->role_id): ?>
                                        <!-- Disabled reset password link -->
                                    <?php elseif ($this->request->getSession()->read('Auth.User.role_id') == ROLE_INSTRUCTOR): ?>
                                        <!-- Disabled reset password link -->
                                    <?php elseif ($this->request->getSession()->read('Auth.User.role_id')
                                        == $user->role_id &&
                                        $this->request->getSession()->read('Auth.User.role_id')
                                        != ROLE_SYSADMIN &&
                                        $this->request->getSession()->read('Auth.User.is_admin')
                                        == 1 && $this->request->getSession()->read('Auth.User.id')
                                        != $user->id && $user->role_id != ROLE_INSTRUCTOR): ?>
                                        <?= $this->Html->link('', ['action' => 'resetpassword',
                                            $user->id], ['class' => 'fontello-key',
                                            'title' => __('Reset Password')]) ?>
                                    <?php endif; ?>
                                    <?php if (in_array($this->request->getSession()->read('Auth.User.role_id'), [ROLE_REGISTRAR, $user->Role->parent_id])): ?>
                                        <?php if ($user->active == 0): ?>
                                            <!-- Disabled assign link -->
                                        <?php else: ?>
                                            <?= $this->Html->link('', ['action' => 'assign', $user->id], ['class' => 'fontello-users', 'title' => __('Assign')]) ?>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                    <?php if ($this->request->getSession()->read('Auth.User.role_id') == ROLE_ACCOMODATION): ?>
                                        <?php if ($user->active == 0): ?>
                                            <!-- Disabled assign dorm block link -->
                                        <?php else: ?>
                                            <?= $this->Html->link('', ['action' => 'assign_user_dorm_block', $user->id], ['class' => 'fontello-users', 'title' => __('Assign Dorm Block')]) ?>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                    <?php if ($this->request->getSession()->read('Auth.User.role_id') == ROLE_MEAL): ?>
                                        <?php if ($user->active == 0): ?>
                                            <!-- Disabled assign meal hall link -->
                                        <?php else: ?>
                                            <?= $this->Html->link('', ['action' => 'assign_user_meal_hall', $user->id], ['class' => 'fontello-users', 'title' => __('Assign Meal Hall')]) ?>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                    <?php if ($this->request->getSession()->read('Auth.User.role_id') == ROLE_SYSADMIN): ?>
                                        <!-- Delete link disabled as per original -->
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                    <div class="form-group">
                        <?= $this->Form->button(__('Delete Selected'), ['id' => 'delete-selected', 'class' => 'tiny radius button bg-blue']) ?>
                    </div>
                    <div class="row">
                        <div class="large-5 columns">
                            <?= $this->Paginator->counter(['format' => __('Page {{page}} of {{pages}}, showing {{current}} records out of {{count}} total')]) ?>
                        </div>
                        <div class="large-7 columns">
                            <div class="pagination-centered">
                                <ul class="pagination">
                                    <?= $this->Paginator->prev('<< ' . __('Previous'), ['tag' => 'li'], null, ['class' => 'arrow unavailable']) ?>
                                    <?= $this->Paginator->numbers(['tag' => 'li', 'currentClass' => 'current', 'separator' => '']) ?>
                                    <?= $this->Paginator->next(__('Next') . ' >>', ['tag' => 'li'], null, ['class' => 'arrow unavailable']) ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <?= $this->Form->end() ?>
                </div>
            <?php else: ?>
                <div class="alert-box info">
                    <span></span> <?= __('No users found with the given search criteria') ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

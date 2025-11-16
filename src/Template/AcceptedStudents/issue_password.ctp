<?php
use Cake\I18n\I18n;

$this->set('title', __('Password Issue/Reset to a Student'));
$this->Html->script(['jquery-1.6.2.min', 'generatepassword'], ['block' => 'script']);
?>

<script type="text/javascript">
    function validatePasswordJs() {
        var password = $("#password").val();
        if (!password) {
            alert('<?= __('Please provide a password') ?>');
            return false;
        } else if (password.length < 6) {
            alert('<?= __('The minimum password length is 6') ?>');
            return false;
        }
        return true;
    }
</script>

<div class="container">
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <h2><?= __('Password Issue/Reset to a Student') ?></h2>
                    <?= $this->Form->create(null, ['type' => 'post', 'url' => ['action' => 'issuePassword'], 'class' => 'form-horizontal', 'onsubmit' => 'return validatePasswordJs()']) ?>
                    <div class="students-index">
                        <?php if (empty($hideSearch)): ?>
                            <table class="table">
                                <tbody>
                                <tr>
                                    <td>
                                        <?= $this->Form->control('AcceptedStudent.studentnumber', [
                                            'label' => ['text' => __('Student Number'), 'class' => 'control-label'],
                                            'class' => 'form-control'
                                        ]) ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <?= $this->Form->button(__('Continue'), ['name' => 'issuestudentidsearch', 'class' => 'btn btn-primary']) ?>
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <table class="table">
                                <tr>
                                    <td style="font-weight: bold;">
                                        <?= __('Student Name: {0}', h($students->full_name)) ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold;">
                                        <?= __('College: {0}', h($students->College->name)) ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <?= $this->Form->hidden('AcceptedStudent.id', ['value' => $students->id]) ?>
                                        <?= $this->Form->hidden('User.id', ['value' => $students->user_id]) ?>
                                        <?= $this->Form->hidden('User.role_id', ['value' => $students->User->role_id]) ?>
                                        <?= $this->Form->control('User.username', [
                                            'value' => $students->studentnumber,
                                            'readonly' => true,
                                            'class' => 'form-control'
                                        ]) ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <?= $this->Form->control('User.passwd', [
                                            'label' => ['text' => __('Password'), 'class' => 'control-label'],
                                            'type' => 'password',
                                            'id' => 'password',
                                            'class' => 'form-control'
                                        ]) ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <?= $this->Form->control('text', [
                                            'label' => ['text' => __('Generate Password'), 'class' => 'control-label'],
                                            'id' => 'text',
                                            'class' => 'form-control'
                                        ]) ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding-left: 350px;">
                                        <input type="button" id="button-generate-password" value="<?= __('Generate') ?>" onclick="suggestPassword(this.form)" class="btn btn-secondary">
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <?= $this->Form->button(__('Set Password'), ['name' => 'issuepasswordtostudent', 'class' => 'btn btn-primary']) ?>
                                        </div>
                                    </td>
                                </tr>
                                <tr><td></td></tr>
                            </table>
                        <?php endif; ?>
                    </div>
                    <?= $this->Form->end() ?>
                </div>
            </div>
        </div>
    </div>
</div>

<h5 class="text-dark mb-3">Login to SMiS</h5>
<hr>
<?php
$flashMessages = $this->Flash->render('flash');
if (!empty($flashMessages)) {
    // Assuming flash messages are set with params like ['class' => 'type', 'delay' => 5000] in the controller
    $flashData = $this->getRequest()->getSession()->read('Flash.flash');
    $msg = h($flashData[0]['message'] ?? '');
    $type = h($flashData[0]['params']['class'] ?? 'info');
    $delay = (int)($flashData[0]['params']['delay'] ?? 5000);
    ?>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof showToast === 'function') {
                showToast("<?= $msg ?>", "<?= $type ?>", <?= $delay ?>);
            }
        });
    </script>
    <?php
} else {
    echo $this->Flash->render();
}
?>
<?= $this->Form->create(null, ['url' => ['controller' => 'Users', 'action' => 'login']]) ?>
<div class="mb-3 input-group">
    <span class="input-group-text bg-transparent text-dark">
        <i class="fas fa-user"></i>
    </span>
    <?= $this->Form->control('username', [
        'placeholder' => 'Username',
        'class' => 'form-control',
        'label' => false,
        'autocomplete' => 'off',
        'id' => 'Text1',
        'required' => true
    ]) ?>
</div>
<div class="mb-3 input-group">
    <span class="input-group-text bg-transparent text-dark">
        <i class="fas fa-key"></i>
    </span>
    <?= $this->Form->control('password', [
        'type' => 'password',
        'placeholder' => 'Password',
        'class' => 'form-control',
        'label' => false,
        'autocomplete' => 'off',
        'id' => 'Text2',
        'required' => true
    ]) ?>
</div>
<?php if (isset($mathCaptcha)) { ?>
    <div class="mb-3 input-group">
    <span class="input-group-text bg-transparent text-dark">
        <i class="fas fa-shield-alt"></i>
    </span>
        <?= $this->Form->control('security_code', [
            'type' => 'number',
            'label' => false,
            'class' => 'form-control',
            'autocomplete' => 'off',
            'id' => 'securityCode',
            'min' => 0,
            'max' => 100,
            'placeholder' => 'Enter the sum of ' . h($mathCaptcha),
            'required' => true
        ]) ?>
    </div>
<?php } ?>
<?= $this->Form->button('Login', ['id' => 'loginButton', 'class' => 'btn btn-primary w-100']) ?>
<div class="mt-3">
    <?= $this->Html->link(__('Forgot Password?'), ['controller' => 'Users', 'action' => 'forget'], ['class' => 'btn btn-secondary w-100', 'target' => '_blank']) ?>
</div>
<?= $this->Form->end() ?>
<script>
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }
</script>

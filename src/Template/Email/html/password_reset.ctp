<!-- src/Template/Email/html/password_reset.ctp -->
<p>Dear <?= h($message['first_name'] ?? 'User') ?>,</p>
<p>Your SMiS account has been created with the following details:</p>
<ul>
    <li>Email: <?= h($message['email'] ?? 'N/A') ?></li>
    <li>Username: <?= h($message['username'] ?? 'N/A') ?></li>
    <li>Password: <?= h($message['password'] ?? 'N/A') ?></li>
</ul>
<p>Please log in at <a href="<?= $this->Url->build('/', ['fullBase' => true]) ?>">SMiS Portal</a> and change your password.</p>
<p>Contact support at <?= h($message['support_email'] ?? 'support@university.edu') ?>
    for assistance.</p>

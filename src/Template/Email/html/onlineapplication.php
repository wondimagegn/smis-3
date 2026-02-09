<?php
?>
<!-- templates/email/html/onlineapplication.php -->
<div style="font-family: Arial, sans-serif; color: #333;">
    <h2>Online Admission Application Received</h2>
    <p>Dear <?= $applicant_name ?>,</p>
    <p><?= $message ?></p>
    <p>Track your application: <a href="<?= $this->Url->build('/pages/online_admission_tracking', ['fullBase' => true]) ?>">Click Here</a></p>
    <hr>
    <small>University Registrar Office</small>
</div>

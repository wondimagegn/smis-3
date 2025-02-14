<!DOCTYPE html>
<html lang="en">

<head>
    <?= $this->Html->charset(); ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Management Information System</title>

    <?= $this->Html->css(['default', 'common1']); ?>
    <?= $this->fetch('script'); ?>
</head>

<body>

<div id="header">
    <div id="logo">
        <h1>Student Management Information System</h1>
        <p>Arba Minch University</p>
    </div>
</div>

<div id="page">
    <div id="content">
        <?= $this->fetch('content'); ?>
    </div>
</div>

<div id="footer">
    <?= __('This is a restricted network. Use of this network, its equipment, and resources is monitored at all times and requires explicit permission from the network administrator. If you do not have this permission in writing, you are violating the regulations of this network and can and will be prosecuted to the fullest extent of the law. By continuing into this system, you are acknowledging that you are aware of and agree to these terms.') ?>
</div>

</body>

</html>

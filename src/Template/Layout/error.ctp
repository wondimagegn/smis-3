<!DOCTYPE html>
<html lang="en">

<head>
    <?= $this->Html->charset(); ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 Error Page</title>

    <?= $this->Html->css([
        'reset', 'datePicker', 'common1', 'text', '960', 'layout', 'nav'
    ]); ?>

    <!--[if IE 6]><?= $this->Html->css('ie6'); ?><![endif]-->
    <!--[if IE 7]><?= $this->Html->css('ie'); ?><![endif]-->
</head>

<body>

<div class="container_16">
    <div id="ajax_div" class="grid_16" style="text-align:center">
        <?= $this->Flash->render(); ?>
        <?= $this->fetch('content'); ?>
    </div>

    <div class="grid_16" id="site_info">
        <div class="footerbox">
            <p style="margin:0px; padding:0px">
                <strong>&copy; <?= date("Y"); ?> Arba Minch University</strong><br />
                Designed and Developed By IT and T Solutions PLC
                <a href="http://www.itandts.com" style="color:#ebad05">itandts.com</a>
            </p>
        </div>
    </div>
</div>

</body>

</html>

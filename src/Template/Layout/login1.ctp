<!DOCTYPE html>
<html lang="en">

<head>
    <?= $this->Html->charset(); ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= __('SMIS Sign In'); ?></title>

    <?= $this->Html->css(['reset', 'common1', 'text', '960', 'layout', 'nav']); ?>
    <?= $this->fetch('script'); ?>
</head>

<body>
<div class="container_16">
    <div class="container_16">
        <?= $this->Html->image('AMU-SMIS-Banner.jpg', ['id' => 'AMU-SMIS-Banner']); ?>
    </div>
    <div class="clear"></div>

    <div class="prefix_3 suffix_4" style="padding-top:30px">
        <?= $this->Flash->render(); ?>

        <?php
        $session = $this->request->getSession();
        if ($session->check('Message.auth')) {
            echo $this->Flash->render('auth');
        }
        if ($session->check('Message.flash')) {
            echo $this->Flash->render();
        }
        ?>
    </div>

    <div id="ajax_div" class="prefix_4" style="padding-bottom:10px">
        <?= $this->fetch('content'); ?>
    </div>

    <div class="clear"></div>

    <div class="grid_16">
        <p style="text-align:center; font-size:12px">
            <?= __('This is a restricted network. Use of this network, its equipment, and resources is monitored at all times and requires explicit permission from the system administrator. If you do not have this permission in writing, you are violating the regulations of this network and can and will be prosecuted to the fullest extent of law. By continuing into this system, you are acknowledging that you are aware of and agree to these terms.'); ?>
        </p>

        <p class="info-box info-message">
            <span></span>
            Notice: This software is under development and you may face bugs, see incomplete features, and the already running features may get changed.
            Please report any bugs you face to <a href="mailto:bugs@itandts.com" style="color:#ebad05">bugs@itandts.com</a>
        </p>
    </div>

    <div class="grid_16" id="site_info">
        <div class="footerbox">
            <p style="margin:0px; padding:0px">
                <strong>&copy; <?= date("Y"); ?> Arba Minch University</strong>
            </p>
        </div>
    </div>

    <div class="clear"></div>
</div>
</body>
</html>

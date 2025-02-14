<!DOCTYPE html>
<html class="no-js" lang="en">

<head>
    <?= $this->Html->charset(); ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="<?= h(Configure::read('ApplicationMetaDescription')); ?>" />
    <meta name="keywords" content="<?= h(Configure::read('ApplicationMetaKeywords')); ?>">
    <meta name="author" content="<?= h(Configure::read('ApplicationMetaAuthor')); ?>">

    <!-- Refresh the page every 30 minutes (in seconds) -->
    <meta http-equiv="refresh" content="1800">

    <title>
        <?= h(Configure::read('ApplicationShortName')) . ' ' . h(Configure::read('ApplicationVersionShort')) ?>
        <?= !empty($this->fetch('title_details'))
            ? ' | ' . h($this->fetch('title_details'))
            : (!empty($this->request->getParam('controller'))
                ? ' | ' . Inflector::humanize(Inflector::underscore($this->request->getParam('controller')))
                . (!empty($this->request->getParam('action')) && $this->request->getParam('action') !== 'index'
                    ? ' | ' . ucwords(str_replace('_', ' ', $this->request->getParam('action')))
                    : '')
                : '');
        ?>
        <?= ' - ' . h(Configure::read('ApplicationTitleExtra')); ?>
    </title>

    <?= $this->Html->css(['foundation.min']); ?>

    <?php if (Configure::read('debug') || true): ?>
        <?= $this->Html->css([
            'dripicon', 'typicons', 'font-awesome',
            'pace-theme-flash', 'theme', 'login', 'style',
            'slicknav', 'sass/css/theme', 'common1'
        ]) ?>
    <?php else: ?>
        <?= $this->AssetCompress->css('login.css', ['full' => true]) ?>
    <?php endif; ?>

    <?= $this->Html->script('vendor/modernizr'); ?>

    <style>
        .center { text-align: center; vertical-align: middle; }
        .vcenter { vertical-align: middle; }
        .hcenter { text-align: center; }
    </style>
</head>

<body class="fullbackground">
<!-- Preloader -->
<div id="preloader">
    <div id="status">&nbsp;</div>
</div>

<div class="inner-wrap">
    <div class="wrap-fluid">
        <?php if ($this->Flash->render('Message.auth')): ?>
            <?= $this->Flash->render('auth'); ?>
        <?php endif; ?>

        <?php if ($this->request->getSession()->check('Message.flash')): ?>
            <?= $this->Flash->render(); ?>
        <?php endif; ?>

        <?= $this->fetch('content'); ?>
    </div>
</div>

<?php if (Configure::read('debug') || true): ?>
    <?= $this->Html->script([
        'jquery', 'waypoints.min', 'preloader-script',
        'pace/pace', 'foundation.min',
        'foundation/foundation.abide', 'inputMask/jquery.maskedinput',
        'circle-progress/jquery.circliful'
    ]) ?>
<?php else: ?>
    <?= $this->AssetCompress->script('login.js', ['full' => true]) ?>
<?php endif; ?>

<!-- Foundation Validation -->
<script type="text/javascript">
    $(document).foundation({
        abide: {
            live_validate: true,
            validate_on_blur: true,
            focus_on_invalid: true,
            error_labels: true,
            timeout: 1000
        }
    });
</script>

<!-- Masked Input -->
<script type="text/javascript">
    $(document).ready(function() {
        $("#intPhone").mask("+999 999 999999");
        $("#intPhoneSpaceFormatted").mask("(+999) 999 999999");
        $("#intPhoneHyphenFormatted").mask("(+999) 999-999999");

        $("#etPhone").mask("+251999999999");
        $("#etPhoneSpaceFormatted").mask("+251 999 999999");
        $("#etPhoneHyphenFormatted").mask("+251 999-999999");

        $("#phonemobile").mask("+251999999999");
        $("#phoneoffice").mask("+251999999999");
        $("#staffid").mask("AMU/9999/9999", { placeholder: "_" });
        $("#ssn").mask("99--AAA--9999", { placeholder: "*" });
    });
</script>

</body>

</html>

<?php $this->assign('title', __('Forget Password'));
use Cake\Core\Configure;
use Cake\Utility\Inflector;
?>


<!DOCTYPE html>
<html class="no-js" lang="en">
<head>
    <?= $this->Html->charset(); ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= h(Configure::read('ApplicationMetaDescription')); ?>">
    <meta name="keywords" content="<?= h(Configure::read('ApplicationMetaKeywords')); ?>">
    <meta name="author" content="<?= h(Configure::read('ApplicationMetaAuthor')); ?>">

    <!-- Refresh the page every 30 minutes -->
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
            'slicknav'
        ]) ?>
    <?php else: ?>
        <?= $this->AssetCompress->css('login.css', ['full' => true]) ?>
    <?php endif; ?>

    <?= $this->Html->script('vendor/modernizr'); ?>

    <?php
    $login_page_background = Configure::read('Image.login_background');
    $bg_count = count($login_page_background) - 1;
    $bg_index = rand(0, $bg_count);
    ?>
</head>

<body class="fullbackground">
<!-- Preloader -->
<div id="preloader">
    <div id="status">&nbsp;</div>
</div>

<div class="inner-wrap">
    <div class="wrap-fluid">
        <br><br>
        <?php if ($this->request->getSession()->check('Flash.flash')): ?>
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
        'date-dropdown/jquery.date-dropdowns.min', 'date-dropdown/jquery.datetimepicker'
    ]) ?>
<?php else: ?>
    <?= $this->AssetCompress->script('login.js', ['full' => true]) ?>
<?php endif; ?>

<script type="text/javascript">
    $(document).ready(function() {
        // Background Image Handling
        var screenWidth = screen.width;
        var screenHeight = screen.height;
        var backgroundImages = <?= json_encode($login_page_background); ?>;
        var bgIndex = <?= $bg_index; ?>;

        if (screenWidth >= 1366 && screenHeight >= 768) {
            $('body').css("background-image", "url('/img/login-background/" + backgroundImages[bgIndex]['1366_768'] + "')");
        } else if (screenWidth >= 1280 && screenHeight >= 800) {
            $('body').css("background-image", "url('/img/login-background/" + backgroundImages[bgIndex]['1280_800'] + "')");
        } else if (screenWidth >= 1280 && screenHeight >= 768) {
            $('body').css("background-image", "url('/img/login-background/" + backgroundImages[bgIndex]['1280_768'] + "')");
        } else if (screenWidth >= 1280 && screenHeight >= 720) {
            $('body').css("background-image", "url('/img/login-background/" + backgroundImages[bgIndex]['1280_720'] + "')");
            $('body').css("background-position", "top left");
        } else if (screenWidth >= 1024 && screenHeight >= 768) {
            $('body').css("background-image", "url('/img/login-background/" + backgroundImages[bgIndex]['1024_768'] + "')");
        } else if (screenWidth >= 800 && screenHeight >= 600) {
            $('body').css("background-image", "url('/img/login-background/" + backgroundImages[bgIndex]['800_600'] + "')");
        }

        if (($(window).height() - 500) > 0) {
            $('#upper_table').css("margin-top", ($(window).height() - 500) + "px");
        } else {
            $('#upper_table').css("margin-top", (screenHeight - 700) + "px");
        }
    });

    // Form Validation
    $(document).foundation({
        abide: {
            live_validate: true,
            validate_on_blur: true,
            focus_on_invalid: true,
            error_labels: true,
            timeout: 1000
        }
    });

    // Masked Input
    $(document).ready(function() {
        $("#intPhone").mask("+999 999 999999");
        $("#etPhone").mask("+251999999999");
        $("#studentID").mask("AAAA/9999/99", { placeholder: "_" });
        $("#staffid").mask("AMU/9999/9999", { placeholder: "_" });
    });

    // Date & Time Picker
    $("#date-dropdown").dateDropdowns();
    $('#datetimepicker').datetimepicker({ dayOfWeekStart: 1, lang: 'en' });
    $('#datetimepicker1').datetimepicker({ datepicker: false, format: 'H:i', step: 5 });
</script>

</body>

</html>

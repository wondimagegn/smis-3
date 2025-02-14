
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

    <?= $this->Html->css([
        'foundation.min', 'js/tip/tooltipster', 'common1', 'dashboard', 'style',
        'dripicon', 'typicons', 'font-awesome', 'sass/css/theme',
        'pace-theme-flash', 'slicknav'
    ]); ?>

    <?= $this->Html->script('vendor/modernizr'); ?>

    <?php
    $login_page_background = Configure::read('Image.login_background');
    $bg_index = rand(0, 9);
    ?>
</head>

<body>
<!-- Preloader -->
<div id="preloader">
    <div id="status">&nbsp;</div>
</div>

<div class="off-canvas-wrap" data-offcanvas>
    <div class="inner-wrap">
        <div class="top-bar-nest">
            <nav class="top-bar" data-topbar role="navigation">
                <ul class="title-area left">
                    <li class="toggle-topbar menu-icon"><a href="#"><span></span></a></li>
                </ul>

                <div class="left top-bar-section menu-margin-front">
                    <div class="left hide-banner">
                        <a class="logo-link-bg" href="/">
                            <?= $this->Html->image('amu.png', ['style' => 'width:100px;height:84px;']); ?>
                        </a>
                    </div>

                    <div class="left hide-banner">
                        <div class='centeralign_smallheading'>
                                <span style="color:gray;">
                                    <?= h(Configure::read('CompanyName')); ?> | Office of the University Registrar
                                </span>
                        </div>
                    </div>
                </div>

                <section class="top-bar-section">
                    <ul class="right menu menu-margin-front">
                        <li><a class="show-menu" href="#">Menu</a></li>
                        <li><a href="/pages/academic_calender">Academic Calendar</a></li>
                        <li><a href="/pages/official_transcript_request">Transcript Request</a></li>
                        <li><a href="/pages/admission">Admission</a></li>
                        <li><a href="#">Alumni Registration</a></li>
                    </ul>
                </section>
            </nav>
        </div>
    </div>
</div>

<div class="inner-wrap container">
    <div class="wrap-fluid">
        <div class="row">
            <div class="medium-3 large-3 columns">
                <?= $this->element('leftmenu/leftmenu'); ?>
            </div>
            <div class="medium-9 large-9 columns">
                <div class="row">
                    <?= $this->Flash->render(); ?>
                    <?= $this->fetch('content'); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="footer">
    <p>&copy; <?= h(Configure::read('Calendar.applicationStartYear')) . ' - ' . date('Y'); ?>
        <?= h(Configure::read('CopyRightCompany')); ?></p>
</div>

<?= $this->Html->script([
    'jquery', 'waypoints.min', 'preloader-script', 'foundation.min',
    'foundation/foundation.dropdown', 'slimscroll/jquery.slimscroll',
    'sliding-menu', 'scriptbreaker-multiple-accordion-1',
    'number/jquery.counterup.min', 'circle-progress/jquery.circliful',
    'app', 'foundation/foundation.abide', 'slicknav/jquery.slicknav',
    'inputMask/jquery.maskedinput'
]); ?>

<script>
    // Disable all tabs
    $('[data-toggle=tab]').click(function() {
        return false;
    }).addClass("disabledTab");

    var validated = function(tab) {
        tab.unbind('click').removeClass('disabledTab').addClass('active');
    };

    $('.btnNext').click(function() {
        var allValid = true;
        $(this).parents('.tab-pane').find('.form-control').each(function(i, e) {
            if ($(e).val() !== "") {
                allValid = true;
            } else {
                allValid = false;
            }
        });

        if (allValid) {
            var tabIndex = $(this).parents('.tab-pane').index();
            validated($('[data-toggle]').eq(tabIndex + 1));
            $('#ListOfTab  > .active').next('li').find('a').trigger('click');
        }
    });

    $('.btnPrevious').click(function() {
        $('#ListOfTab > .active').prev('li').find('a').trigger('click');
    });

    validated($('[data-toggle]').eq(0));
</script>

<style>
    .disabledTab {
        pointer-events: none;
    }
</style>

<script>
    $(document).foundation({
        abide: {
            live_validate: true,
            validate_on_blur: true,
            focus_on_invalid: true,
            error_labels: true,
            timeout: 1000
        }
    });

    $(document).ready(function() {
        $('.tooltipster-top').tooltipster({ position: "top" });
        $('.tooltipster-left').tooltipster({ position: "left" });
        $('.tooltipster-right').tooltipster({ position: "right" });
        $('.tooltipster-bottom').tooltipster({ position: "bottom" });
    });

    $("#OfficialTranscriptRequestMobilePhone, #phonemobile, #phoneoffice")
        .mask("+251999999999");

    $("#staffid").mask("AMU/9999/9999", { placeholder: "_" });
    $("#ssn").mask("99--AAA--9999", { placeholder: "*" });
</script>
</body>

</html>

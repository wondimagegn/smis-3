<!doctype html>
<html class="no-js" lang="en">
<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="<?= h(\Cake\Core\Configure::read('ApplicationMetaDescription')) ?>" />
    <meta name="keywords" content="<?= h(\Cake\Core\Configure::read('ApplicationMetaKeywords')) ?>">
    <meta name="author" content="<?= h(\Cake\Core\Configure::read('ApplicationMetaAuthor')) ?>">
    <meta http-equiv="refresh" content="1800">

    <title>
        <?= h(\Cake\Core\Configure::read('ApplicationShortName')) . ' ' . h(\Cake\Core\Configure::read('ApplicationVersionShort')) ?>
        <?= !empty($this->fetch('title_details')) ? ' | ' . h($this->fetch('title_details')) : (!empty($this->request->getParam('controller')) ? ' | ' . \Cake\Utility\Inflector::humanize(\Cake\Utility\Inflector::underscore($this->request->getParam('controller'))) . (!empty($this->request->getParam('action')) && $this->request->getParam('action') !== 'index' ? ' | ' . ucwords(str_replace('_', ' ', $this->request->getParam('action'))) : '') : '') ?>
        <?= ' - ' . h(\Cake\Core\Configure::read('ApplicationTitleExtra')) ?>
    </title>

    <?= $this->Html->css([
        'foundation.css',
        'dashboard.css',
        'style.css',
        'dripicon.css',
        'typicons.css',
        'font-awesome.css',
        '/sass/css/theme.css',
        'pace-theme-flash.css',
        'slicknav.css',
        'common1.css',
        'responsive-tables.css',
        'jquery-customselect-1.9.1.css'
    ]) ?>

    <?= $this->Html->script([
        'jquery.js',
        'vendor/modernizr.js',
        'jquery-customselect-1.9.1.min.js'
    ]) ?>
</head>
<body>
<div id="preloader">
    <div id="status">&nbsp;</div>
</div>

<div id="myModal" class="reveal-modal" data-reveal></div>
<div id="busy_indicator">
    <img src="/img/busy.gif" alt="" class="displayed" />
</div>

<div class="off-canvas-wrap" data-offcanvas>
    <div class="inner-wrap">
        <div id="skin-select">
            <a id="toggle"><span class="fa icon-menu"></span></a>
            <div class="skin-part">
                <div id="tree-wrap">
                    <div class="profile">
                        <a href="/">
                            <img alt="" class="" src="/img/<?= h(\Cake\Core\Configure::read('logo')) ?>">
                            <h3><?= h(\Cake\Core\Configure::read('ApplicationShortName')) ?>
                                <small><?= h(\Cake\Core\Configure::read('ApplicationVersionShort')) ?></small>
                            </h3>
                        </a>
                    </div>
                    <div class="side-bar">
                        <?= $this->element('leftmenu/leftmenu') ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="wrap-fluid" id="paper-bg">
            <div class="top-bar-nest">
                <nav class="top-bar" data-topbar role="navigation" data-options="is_hover: false">
                    <ul class="title-area left">
                        <li class="toggle-topbar menu-icon"><a href="#"><span></span></a></li>
                    </ul>
                    <section class="top-bar-section">
                        <div class="centeralign_smallheading">
                                <span style="color:gray;">
                                    <?= h(\Cake\Core\Configure::read('CompanyName')) ?> | Office of the Registrar
                                </span>
                        </div>
                    </section>
                </nav>
            </div>

            <div class="row" style="margin-top:-20px;">
                <div class="large-12 columns">
                    <div class="row">
                        <div class="large-12 columns">
                            <div class="box">
                                <?php
                                if ($this->Flash->render('auth')) {
                                    echo '<div style="margin-top: 40px;">' . $this->Flash->render('auth') . '</div>';
                                }
                                if ($this->Flash->render()) {
                                    echo '<div style="margin-top: 40px;">' . $this->Flash->render() . '</div>';
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    <?= $this->fetch('content') ?>
                </div>
            </div>

            <footer>
                <div id="footer">
                    Copyright &copy; <?= h(\Cake\Core\Configure::read('Calendar.applicationStartYear')) . ' - ' . date('Y') ?>
                    <?= h(\Cake\Core\Configure::read('CopyRightCompany')) ?>
                </div>
            </footer>
        </div>
    </div>
</div>

<?php if (\Cake\Core\Configure::read('debug') || true): ?>
    <?= $this->Html->script([
        'waypoints.min.js',
        'preloader-script.js',
        'foundation.min.js',
        'slimscroll/jquery.slimscroll.js',
        'slicknav/jquery.slicknav.js',
        'sliding-menu.js',
        'scriptbreaker-multiple-accordion-1.js',
        'number/jquery.counterup.min.js',
        'circle-progress/jquery.circliful.js',
        'number-progress-bar/jquery.velocity.min.js',
        'number-progress-bar/number-pb.js',
        'app.js',
        'loader/loader.js',
        'loader/demo.js',
        'jquery-department_placement'
    ]) ?>
<?php else: ?>
    <?= $this->AssetCompress->script('mainjslib.js', ['full' => true]) ?>
    <?= $this->AssetCompress->script('foundation.js', ['full' => true]) ?>
    <?= $this->AssetCompress->script('maininternaledu.js', ['full' => true]) ?>
    <?= $this->AssetCompress->script('additionaljavascript.js', ['full' => true]) ?>
    <?= $this->AssetCompress->script('floatjavascript.js', ['full' => true]) ?>
<?php endif; ?>

<script type="text/javascript">
    $(function() {
        $(document).foundation();
    });
</script>

<?= $this->Html->script([
    'angular.min.js',
    'chart.js',
    'angular-chart.min.js',
    'angular-route.min.js',
    'responsive-tables.js'
]) ?>

<style>
    .disabledTab { pointer-events: none; }
</style>

<script>
    $('[data-toggle=tab]').click(function() { return false; }).addClass("disabledTab");
    var validated = function(tab) {
        tab.unbind('click').removeClass('disabledTab').addClass('active');
    };
    $('.btnNext').click(function() {
        var allValid = true;
        $(this).parents('.tab-pane').find('.form-control').each(function(i, e) {
            if ($(e).val() != "") { allValid = true; } else { allValid = false; }
        });
        if (allValid) {
            var tabIndex = $(this).parents('.tab-pane').index();
            validated($('[data-toggle]').eq(tabIndex + 1));
            $('#ListOfTab > .active').next('li').find('a').trigger('click');
        }
    });
    $('.btnPrevious').click(function() {
        $('#ListOfTab > .active').prev('li').find('a').trigger('click');
    });
    validated($('[data-toggle]').eq(0));
</script>
</body>
</html>

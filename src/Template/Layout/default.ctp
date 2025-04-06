<?php
use Cake\Core\Configure;
?>
<!DOCTYPE html>
<html class="no-js" lang="en">
<head>
    <?= $this->Html->charset(); ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= h(Configure::read('ApplicationShortName') . ' ' . Configure::read('ApplicationVersionShort')) . (!empty($this->fetch('title_details')) ? ' | ' . h($this->fetch('title_details')) : ''); ?></title>

    <?= $this->Html->css(['foundation', 'dashboard', 'style', 'dripicon', 'typicons', 'font-awesome', 'theme', 'pace-theme-flash', 'slicknav', 'common1', 'responsive-tables']); ?>
    <?= $this->Html->script(['jquery', 'vendor/modernizr', 'jquery-customselect-1.9.1.min','amharictyping','admit_student']); ?>
    <?= $this->Html->css('jquery-customselect-1.9.1'); ?>

</head>

<body>
<!-- Preloader -->
<div id="preloader">
    <div id="status">&nbsp;</div>
</div>
<!-- End of Preloader -->


<?= $this->Html->script(['waypoints.min', 'preloader-script', 'foundation.min', 'foundation/foundation.abide', 'slimscroll/jquery.slimscroll', 'slicknav/jquery.slicknav', 'sliding-menu', 'scriptbreaker-multiple-accordion-1', 'number/jquery.counterup.min', 'circle-progress/jquery.circliful', 'number-progress-bar/jquery.velocity.min', 'number-progress-bar/number-pb', 'app', 'loader/loader']); ?>

<script>
    $(document).ready(function() {
        $('#select-all').click(function() {
            $('.checkbox1').prop('checked', this.checked);
        });
        $('.checkbox1').click(function() {
            if (!this.checked) $('#select-all').prop('checked', false);
        });
    });
</script>

<div id="myModal" class="reveal-modal" data-reveal></div>

<div id="busy_indicator">
    <img src="<?= $this->Url->image('busy.gif'); ?>" alt="Loading..." class="displayed" />
</div>

<div class="off-canvas-wrap" data-offcanvas>
    <!-- Right sidebar wrapper -->
    <div class="inner-wrap">
        <!-- Right sidemenu -->
        <div id="skin-select">
            <!-- Toggle sidemenu icon button -->
            <a id="toggle">
                <span class="fa icon-menu"></span>
            </a>
            <!-- End of Toggle sidemenu icon button -->

            <div class="skin-part">
                <div id="tree-wrap">
                    <!-- Profile -->
                    <div class="profile">
                        <a href="<?= $this->Url->build('/'); ?>">
                            <img alt="" class="" src="<?= $this->Url->image(Configure::read('logo')); ?>">
                            <h3>
                                <?= Configure::read('ApplicationShortName'); ?>
                                <small><?= Configure::read('ApplicationVersionShort'); ?></small>
                            </h3>
                        </a>
                    </div>
                    <!-- End of Profile -->

                    <!-- Menu Sidebar Begin -->
                    <div class="side-bar">
                        <?= $this->element('mainmenu/mainmenuOptimized'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Right Sidebar Wrapper -->

    <div class="wrap-fluid" id="paper-bg">
        <!-- Top Nav -->
        <div class="top-bar-nest">
            <nav class="top-bar" data-topbar role="navigation" data-options="is_hover: false">
                <ul class="title-area left">
                    <li class="toggle-topbar menu-icon"><a href="#"><span></span></a></li>
                </ul>
                <section class="top-bar-section">
                    <?= $this->element('mainmenu/top-menu'); ?>
                </section>
            </nav>
        </div>

        <!-- Container Begin -->
        <div class="row" style="margin-top:-20px;">
            <div class="large-12 columns">
                <div class="row">
                    <div class="large-12 columns">
                        <div class="box">

                            <?php $flash = $this->Flash->render(); ?>
                            <?php if (!empty($flash)): ?>
                                <div style="margin-top: 40px;">
                                    <?= $flash ?>
                                </div>
                            <?php endif; ?>

                            <?php $authFlash = $this->Flash->render('auth'); ?>
                            <?php if (!empty($authFlash)): ?>
                                <div style="margin-top: 40px;">
                                    <?= $authFlash ?>
                                </div>
                            <?php endif; ?>

                        </div>
                    </div>
                </div>
                <?= $this->fetch('content'); ?>

            </div>
        </div>
        <footer>
            <div id="footer">
                Copyright &copy;
                <?= Configure::read('Calendar.applicationStartYear') . ' - ' . date('Y'); ?>
                <?= Configure::read('CopyRightCompany'); ?>
            </div>
        </footer>
    </div>
</div>
</body>
</html>

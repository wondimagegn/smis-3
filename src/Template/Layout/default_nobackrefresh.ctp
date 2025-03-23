<!DOCTYPE html>
<html class="no-js" lang="en">

<head>
    <?= $this->Html->charset(); ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

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

    <!-- STYLESHEETS -->
    <?= $this->Html->css(['foundation', 'js/tip/tooltipster', 'js/footable/css/footable-demos']) ?>
    <?= $this->Html->css([
        'dashboard',
        'style',
        'dripicon',
        'typicons',
        'font-awesome',
        'sass/css/theme',
        'css/pace-theme-flash',
        'slicknav',
        'jquery.dataTables.min',
        'js/footable/css/footable.core',
        'js/footable/css/footable.standalone',
        'js/footable/css/footable-demos',
        'common1',
        'responsive-tables'
    ]) ?>

    <?= $this->Html->script('js/pace/pace') ?>
    <?= $this->Html->css('js/pace/themes/orange/pace-theme-flash') ?>


    <?= $this->Html->script([
        'jquery', 'vendor/modernizr', 'jquery-customselect-1.9.1.min'
    ]) ?>
    <?= $this->Html->css('jquery-customselect-1.9.1') ?>

    <script type="text/javascript">
        window.history.forward();
        function noBack() { window.history.forward(); }
    </script>

    <style>
        .center { text-align: center; vertical-align: middle; }
        .vcenter { vertical-align: middle; }
        .hcenter { text-align: center; }
    </style>

    <!-- Check and reload tabs when session expires -->
    <?php
    $session = $this->request->getSession();
    $user_id = $session->read('Auth.User.id');
    $is_logged_in = $session->read('User.is_logged_in');
    ?>

    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function() {
            var isLoggedIn = <?= json_encode($is_logged_in); ?>;
            var userId = <?= json_encode($user_id); ?>;
            var checkInterval = 10000; // Check every 10 seconds

            function checkSession() {
                fetch('/users/check-session')
                    .then(response => response.json())
                    .then(data => {
                        if (!data.is_logged_in) {
                            window.location.reload();
                        }
                    });
            }

            if (isLoggedIn) {
                setInterval(checkSession, checkInterval);
            }
        });
    </script>

</head>


<body onload="noBack();" onpageshow="if (event.persisted) noBack();" onunload="">
<!-- Preloader -->
<div id="preloader">
    <div id="status">&nbsp;</div>
</div>

<!-- Modal -->
<div id="myModal" class="reveal-modal" data-reveal></div>

<!-- Busy Indicator -->
<div id="busy_indicator">
    <img src="/img/busy.gif" alt="" class="displayed" />
</div>

<div class="off-canvas-wrap" data-offcanvas>
    <div class="inner-wrap">
        <div id="skin-select">
            <a id="toggle">
                <span class="fa icon-menu"></span>
            </a>

            <div class="skin-part">
                <div id="tree-wrap">
                    <!-- Profile -->
                    <div class="profile">
                        <a href="/">
                            <img alt="" src="/img/<?= h(Configure::read('logo')); ?>">
                            <h3><?= h(Configure::read('ApplicationShortName')); ?>
                                <small><?= h(Configure::read('ApplicationVersionShort')); ?></small>
                            </h3>
                        </a>
                    </div>
                    <!-- End Profile -->

                    <!-- Sidebar Menu -->
                    <div class="side-bar">
                        <?= $this->element('mainmenu/mainmenuOptimized'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Page Content -->
    <div class="wrap-fluid" id="paper-bg">
        <!-- Top Navigation -->
        <div class="top-bar-nest">
            <nav class="top-bar" data-topbar role="navigation">
                <ul class="title-area left">
                    <li class="toggle-topbar menu-icon"><a href="#"><span></span></a></li>
                </ul>
                <section class="top-bar-section">
                    <?= $this->element('mainmenu/top-menu'); ?>
                </section>
            </nav>
        </div>

        <!-- Main Container -->
        <div class="row" style="margin-top:-20px;">
            <div class="large-12 columns">
                <div class="row">
                    <div class="large-12 columns">
                        <div class="box">
                            <?php if ($this->Flash->render('auth')): ?>
                                <div style="margin-top: 40px;"><?= $this->Flash->render('auth'); ?></div>
                            <?php endif; ?>

                            <?php if ($this->request->getSession()->check('Message.flash')): ?>
                                <div style="margin-top: 40px;"><?= $this->Flash->render(); ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <?= $this->fetch('content'); ?>
            </div>
        </div>

        <!-- Footer -->
        <footer>
            <div id="footer">
                Copyright &copy; <?= h(Configure::read('Calendar.applicationStartYear')) . ' - ' . date('Y'); ?>
                <?= h(Configure::read('CopyRightCompany')); ?>
            </div>
        </footer>
    </div>
</div>

<!-- JavaScript Files -->
    <?= $this->Html->script([
        'waypoints.min', 'preloader-script', 'foundation.min',
        'foundation/foundation.abide', 'slimscroll/jquery.slimscroll',
        'slicknav/jquery.slicknav', 'sliding-menu',
        'scriptbreaker-multiple-accordion-1', 'number/jquery.counterup.min',
        'circle-progress/jquery.circliful', 'number-progress-bar/jquery.velocity.min',
        'number-progress-bar/number-pb', 'app', 'loader/loader',
        'loader/demo', 'datatables/jquery.dataTables',
        'footable/js/footable', 'footable/js/footable.sort',
        'footable/js/footable.filter', 'footable/js/footable.paginate',
        'jquery-department_placement'
    ]); ?>

<!-- Form Validation -->
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

<!-- Checkbox Select All -->
<script type="text/javascript">
    $(document).ready(function() {
        $('#select-all').click(function() {
            $('.checkbox1').prop('checked', this.checked);
        });

        $('.checkbox1').click(function() {
            if (!this.checked) {
                $('#select-all').prop('checked', false);
            }
        });
    });
</script>

<!-- Additional Scripts -->
<?= $this->Html->script(['chart', 'responsive-tables', 'tip/jquery.tooltipster']); ?>

<!-- Tooltip Setup -->
<script>
    $(document).ready(function() {
        $('.tooltipster-top').tooltipster({ position: "top" });
        $('.tooltipster-left').tooltipster({ position: "left" });
        $('.tooltipster-right').tooltipster({ position: "right" });
        $('.tooltipster-bottom').tooltipster({ position: "bottom" });
        $('.tooltipster-fadein').tooltipster({ animation: "fade" });
        $('.tooltipster-growing').tooltipster({ animation: "grow" });
        $('.tooltipster-swinging').tooltipster({ animation: "swing" });
        $('.tooltipster-sliding').tooltipster({ animation: "slide" });
        $('.tooltipster-falling').tooltipster({ animation: "fall" });
    });
</script>

<!-- Masked Input -->
<?= $this->Html->script('inputMask/jquery.maskedinput'); ?>
<script>
    $(document).ready(function() {
        $("#intPhone").mask("+999 999 999999");
        $("#phonemobile").mask("+251999999999");
        $("#staffid").mask("AMU/9999/9999", { placeholder: "_" });
    });
</script>

<!-- DataTables -->
<script>
    $(document).ready(function() {
        $('#example').dataTable();
        $('#footable-res2').footable().on('footable_filtering', function(e) {
            var selected = $('.filter').find();
            if (selected && selected.length > 0) {
                e.filter += e.filter ? ' ' + selected : selected;
                e.clear = !e.filter;
            }
        });

        $('.clear-filter').click(function(e) {
            e.preventDefault();
            $('.filter').val('');
            $('table.demo').trigger('footable_clear_filter');
        });

        $('.filter').change(function(e) {
            e.preventDefault();
            $('table.demo').trigger('footable_filter', { filter: $('#filter').val() });
        });
    });
</script>

<?= $this->fetch('script'); ?>
<?= $this->element('sql_dump'); ?>
</body>
</html>

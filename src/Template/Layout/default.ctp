<?php
use Cake\Core\Configure;
?>
<!DOCTYPE html>
<html class="no-js" lang="en">
<head>
    <?= $this->Html->charset(); ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= h(Configure::read('ApplicationShortName') . ' ' . Configure::read('ApplicationVersionShort')) . (!empty($this->fetch('title_details')) ? ' | ' . h($this->fetch('title_details')) : ''); ?></title>

    <?= $this->Html->css(['foundation', 'dashboard', 'style', 'dripicon', 'typicons', 'font-awesome', 'slicknav',
        'common1', 'responsive-tables','theme']); ?>
    <?= $this->Html->script(['jquery', 'vendor/modernizr', 'jquery-customselect-1.9.1.min','amharictyping']); ?>
    <?= $this->Html->css('jquery-customselect-1.9.1'); ?>

    <!-- Pace Loader CSS -->
    <?= $this->Html->css('/js/pace/themes/orange/pace-theme-flash.css', ['block' => true]) ?>
    <!-- Pace Loader JavaScript -->
    <?= $this->Html->script('/js/pace/pace.js', ['block' => true]) ?>


</head>

<body>
<!-- Preloader -->
<div id="preloader">
    <div id="status">&nbsp;</div>
</div>
<!-- End of Preloader -->


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
<!-- Footable and data table -->



<?= $this->Html->script(['waypoints.min', 'preloader-script', 'foundation.min', 'foundation/foundation.abide', 'slimscroll/jquery.slimscroll',
    'slicknav/jquery.slicknav', 'sliding-menu', 'scriptbreaker-multiple-accordion-1', 'number/jquery.counterup.min',
    'circle-progress/jquery.circliful', 'number-progress-bar/jquery.velocity.min', 'number-progress-bar/number-pb', 'app',
    'loader/loader','loader/demo','datatables/jquery.dataTables','footable/js/footable','footable/js/footable.sort','footable/js/footable.filter',
    'footable/js/footable.paginate','jquery-department_placement','chart','responsive-tables','tip/jquery.tooltipster']); ?>

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

<script type="text/javascript">
    (function($) {
        "use strict";
        $('#example').dataTable({
            /* "order": [
                [3, "desc"]
            ] */
        });
    })(jQuery);


    (function($) {
        "use strict";
        $('#footable-res2').footable().bind('footable_filtering', function(e) {
            //var selected = $('.filter').find(':selected').text();
            var selected = $('.filter').find();
            if (selected && selected.length > 0) {
                e.filter += (e.filter && e.filter.length > 0) ? ' ' + selected : selected;
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
            $('table.demo').trigger('footable_filter', {
                filter: $('#filter').val()
            });
        });
    })(jQuery);

    console.log($.fn.foundation);

    $(document).foundation()
</script>

<?= $this->Html->scriptBlock(<<<'JS'
    document.addEventListener('DOMContentLoaded', () => {
        $(document).foundation({
            abide: {
                live_validate: true,
                validate_on_blur: true,
                focus_on_invalid: true,
                error_labels: true,
                timeout: 1000,
                patterns: {
                    alpha: /^[a-zA-Z]+$/,
                    alpha_numeric: /^[a-zA-Z0-9]+$/,
                    id_number: /^[a-zA-Z0-9_/]+$/,
                    course_code: /^[A-Z][a-zA-Z]{1,4}-\d{3,4}$/,
                    alpha_with_space_only: /^[A-Za-z\s]+$/,
                    minute_number: /^[A-Z][a-zA-Z]*\/(January|February|March|April|May|June|July|August|September|October|November|December)\/\d{4}$/,
                    institution_code: /^[a-zA-Z_-]+$/,
                    integer: /^[-+]?\d+$/,
                    number: /^[-+]?[1-9]\d*$/,
                    whole_number: /^[0-9]\d*$/,
                    strong_password: /^(?=.*[0-9])(?=.*[!@#$%^&*~<>{}()+-`'"?/|=_.:,:;])[a-zA-Z0-9!@#$%^&*~<>{}()+-`'"?/|=_.:,:;]{8,20}$/,
                    card: /^(?:4[0-9]{12}(?:[0-9]{3})?|5[1-5][0-9]{14}|6(?:011|5[0-9][0-9])[0-9]{12}|3[47][0-9]{13}|3(?:0[0-5]|[68][0-9])[0-9]{11}|(?:2131|1800|35\d{3})\d{11})$/,
                    cvv: /^([0-9]){3,4}$/,
                    email: /^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/,
                    url: /(https?|ftp|file|ssh):\/\/(((([a-zA-Z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-zA-Z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-zA-Z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-zA-Z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-zA-Z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-zA-Z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-zA-Z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-zA-Z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-zA-Z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/((([a-zA-Z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-zA-Z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-zA-Z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(\#((([a-zA-Z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?/,
                    domain: /^([a-zA-Z0-9]([a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?\.)+[a-zA-Z]{2,6}$/,
                    datetime: /([0-2][0-9]{3})\-([0-1][0-9])\-([0-3][0-9])T([0-5][0-9])\:([0-5][0-9])\:([0-5][0-9])(Z|([\-\+]([0-1][0-9])\:00))/,
                    date: /(?:19|20)[0-9]{2}-(?:(?:0[1-9]|1[0-2])-(?:0[1-9]|1[0-9]|2[0-9])|(?:(?!02)(?:0[1-9]|1[0-2])-(?:30))|(?:(?:0[13578]|1[02])-31))/,
                    time: /(0[0-9]|1[0-9]|2[0-3])(:[0-5][0-9]){2}/,
                    dateISO: /\d{4}[\/\-]\d{1,2}[\/\-]\d{1,2}/,
                    month_day_year: /(0[1-9]|1[012])[- \/.](0[1-9]|[12][0-9]|3[01])[- \/.](19|20)\d\d/,
                    color: /^#?([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$/
                }
            }
        });

        // Checkbox Select All
        const selectAll = document.getElementById('select-all');
        if (selectAll) {
            selectAll.addEventListener('click', (event) => {
                const checkboxes = document.querySelectorAll('.checkbox1');
                checkboxes.forEach(checkbox => {
                    checkbox.checked = selectAll.checked;
                });
            });
        }

        const checkboxes = document.querySelectorAll('.checkbox1');
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('click', () => {
                if (!checkbox.checked && selectAll) {
                    selectAll.checked = false;
                }
            });
        });

        // Tooltips
        $('.tooltipster-top').tooltipster({ position: 'top' });
        $('.tooltipster-left').tooltipster({ position: 'left' });
        $('.tooltipster-right').tooltipster({ position: 'right' });
        $('.tooltipster-bottom').tooltipster({ position: 'bottom' });
        $('.tooltipster-fadein').tooltipster({ animation: 'fade' });
        $('.tooltipster-growing').tooltipster({ animation: 'grow' });
        $('.tooltipster-swinging').tooltipster({ animation: 'swing' });
        $('.tooltipster-sliding').tooltipster({ animation: 'slide' });
        $('.tooltipster-falling').tooltipster({ animation: 'fall' });

        // Masked Input
        $("#intPhone").mask("+999 999 999999");
        $("#intPhone1").mask("+999999999999");
        $("#intPhone2").mask("+999999999999");
        $("#intPhoneSpaceFormatted").mask("(+999) 999 999999");
        $("#intPhoneHyphenFormatted").mask("(+999) 999-999999");
        $("#etPhone").mask("+251999999999");
        $("#etPhone1").mask("+251999999999");
        $("#etPhoneSpaceFormatted").mask("+251 999 999999");
        $("#etPhoneHyphenFormatted").mask("+251 999-999999");
        $("#phonemobile").mask("+251999999999");
        $("#phoneoffice").mask("+251999999999");
        $("#staffid").mask("AMU/9999/9999", { placeholder: "_" });
        $("#ssn").mask("99--AAA--9999", { placeholder: "*" });

        // DataTables
        $('#example').DataTable();

        // Footable
        $('#footable-res2').footable().on('footable_filtering', function(e) {
            const selected = $('.filter').find(':selected').text();
            if (selected && selected.length > 0) {
                e.filter += (e.filter && e.filter.length > 0) ? ' ' + selected : selected;
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
JS
    , ['block' => true]) ?>
</body>
</html>

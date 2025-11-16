<?php
use Cake\Core\Configure;
use Cake\Utility\Inflector;
use Cake\I18n\Time;
?>

<!DOCTYPE html>
<html class="no-js" lang="en">
<head>
    <!-- Meta Tags -->
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Page Title -->
    <title>
        <?= Configure::read('ApplicationShortName') . ' ' . Configure::read('ApplicationVersionShort') ?>
        <?= $this->fetch('title_details') ? ' | ' . $this->fetch('title_details') : (
        $this->request->getParam('controller') ? ' | ' . Inflector::humanize(Inflector::underscore($this->request->getParam('controller'))) .
            ($this->request->getParam('action') && $this->request->getParam('action') !== 'index' ? ' | ' . ucwords(str_replace('_', ' ', $this->request->getParam('action'))) : '') : ''
        ) ?>
        <?= ' - ' . Configure::read('ApplicationTitleExtra') ?>
    </title>

    <!-- Stylesheets -->
    <?= $this->Html->css([
        '/css/foundation.css',
        '/js/tip/tooltipster.css',
        '/js/footable/css/footable-demos.css',
        '/css/dashboard.css',
        '/css/style.css',
        '/css/dripicon.css',
        '/css/typicons.css',
        '/css/font-awesome.css',
        '/sass/css/theme.css',
        '/css/pace-theme-flash.css',
        '/css/slicknav.css',
        '/css/jquery.dataTables.min.css',
        '/js/footable/css/footable.core.css?v=2-0-1',
        '/js/footable/css/footable.standalone.css',
        '/js/footable/css/footable-demos.css',
        '/css/common1.css',
        '/css/responsive-tables.css',
        '/css/jquery-customselect-1.9.1.css'
    ]) ?>

    <!-- Pace Loader -->
    <?= $this->Html->script('/js/pace/pace.js') ?>
    <?= $this->Html->css('/js/pace/themes/orange/pace-theme-flash.css') ?>
    <?= $this->Html->css('/js/slicknav/slicknav.css') ?>

    <!-- JavaScript -->
    <?= $this->Html->script([
        '/js/jquery.js',
        '/js/vendor/modernizr.js',
        '/js/jquery-customselect-1.9.1.min.js'
    ]) ?>

    <style>
        .center { text-align: center; vertical-align: middle; }
        .vcenter { vertical-align: middle; }
        .hcenter { text-align: center; }
    </style>

    <!-- Session Check -->
    <?php
    $user_id = $this->request->getSession()->read('Auth.User.id');
    $is_logged_in = $this->request->getSession()->read('User.is_logged_in');
    ?>

    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function() {
            var userId = <?= json_encode($user_id); ?>;
            var checkInterval = 10000;

            function checkSession() {
                fetch('<?= $this->Url->build(["controller" => "Users", "action" => "checkSession"]) ?>')
                    .then(response => response.json())
                    .then(data => {
                        if (!data.is_logged_in) {
                            window.location.reload();
                        }
                    });
            }

            if (userId) {
                setInterval(checkSession, checkInterval);
            }
        });
    </script>

</head>
<body>
<!-- Preloader -->
<div id="preloader">
    <div id="status">&nbsp;</div>
</div>

<!-- Reveal Modal -->
<div id="myModal" class="reveal-modal" data-reveal></div>

<!-- Busy Indicator -->
<div id="busy_indicator">
    <img src="/img/busy.gif" alt="" class="displayed" />
</div>

<!-- Off-Canvas Wrapper -->
<div class="off-canvas-wrap" data-offcanvas>
    <div class="inner-wrap">
        <!-- Right Sidebar -->
        <div id="skin-select">
            <!-- Toggle Sidebar -->
            <a id="toggle">
                <span class="fa icon-menu"></span>
            </a>
            <div class="skin-part">
                <div id="tree-wrap">
                    <!-- Profile -->
                    <div class="profile">
                        <a href="/">
                            <img alt="" src="/img/<?= h(Configure::read('logo')) ?>">
                            <h3>
                                <?= h(Configure::read('ApplicationShortName')) ?>
                                <small><?= h(Configure::read('ApplicationVersionShort')) ?></small>
                            </h3>
                        </a>
                    </div>
                    <!-- Sidebar Menu -->
                    <div class="side-bar">
                        <?= $this->element('mainmenu/mainmenuOptimized') ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="wrap-fluid" id="paper-bg">
            <!-- Top Navigation -->
            <div class="top-bar-nest">
                <nav class="top-bar" data-topbar role="navigation" data-options="is_hover: false">
                    <ul class="title-area left">
                        <li class="toggle-topbar menu-icon"><a href="#"><span></span></a></li>
                    </ul>
                    <section class="top-bar-section">
                        <?= $this->element('mainmenu/top-menu') ?>
                    </section>
                </nav>
            </div>

            <!-- Content -->
            <div class="row" style="margin-top:-20px;">
                <div class="large-12 columns">
                    <div class="row">
                        <div class="large-12 columns">
                            <div class="box">

                                    <div style="margin-top: 40px;">
                                        <?= $this->Flash->render('auth') ?>
                                    </div>
                                    <div style="margin-top: 40px;">
                                        <?= $this->Flash->render() ?>
                                    </div>
                            </div>
                        </div>
                    </div>
                    <?= $this->fetch('content') ?>
                </div>
            </div>

            <!-- Footer -->
            <footer>
                <div id="footer">
                    Copyright &copy; <?= Configure::read('Calendar.applicationStartYear') . ' - ' . date('Y') ?>
                    <?= h(Configure::read('CopyRightCompany')) ?>
                </div>
            </footer>
        </div>
    </div>
</div>

<!-- JavaScript Libraries -->
<?= $this->Html->script([
    '/js/waypoints.min.js',
    '/js/preloader-script.js',
    '/js/foundation.min.js',
    '/js/foundation/foundation.abide.js',
    '/js/slimscroll/jquery.slimscroll.js',
    '/js/slicknav/jquery.slicknav.js',
    '/js/sliding-menu.js',
    '/js/scriptbreaker-multiple-accordion-1.js',
    '/js/number/jquery.counterup.min.js',
    '/js/circle-progress/jquery.circliful.js',
    '/js/number-progress-bar/jquery.velocity.min.js',
    '/js/number-progress-bar/number-pb.js',
    '/js/app.js',
    '/js/loader/loader.js',
    '/js/loader/demo.js',
    '/js/datatables/jquery.dataTables.js',
    '/js/footable/js/footable.js?v=2-0-1',
    '/js/footable/js/footable.sort.js?v=2-0-1',
    '/js/footable/js/footable.filter.js?v=2-0-1',
    '/js/footable/js/footable.paginate.js?v=2-0-1',
    '/js/jquery-department_placement.js',
    '/js/chart.js',
    '/js/responsive-tables.js',
    '/js/tip/jquery.tooltipster.js',
    '/js/inputMask/jquery.maskedinput.js'
]) ?>

<!-- Foundation Initialization -->

<script type="text/javascript">
    $(document).foundation({
        abide: {
            live_validate: true, // validate the form as you go
            validate_on_blur: true, // validate whenever you focus/blur on an input field
            focus_on_invalid: true, // automatically bring the focus to an invalid input field
            error_labels: true, // labels with a for="inputId" will recieve an `error` class
            // the amount of time Abide will take before it validates the form (in ms).
            // smaller time will result in faster validation
            timeout: 1000,
            patterns: {
                alpha: /^[a-zA-Z]+$/,
                alpha_numeric: /^[a-zA-Z0-9]+$/,
                id_number: /^[a-zA-Z0-9_/]+$/,
                //course_code: /^[a-zA-Z0-9_-]+$/,
                //course_code: /^[A-Z][a-zA-Z]{1,4}-\d{3,4}$/, //^[A-Z]: Ensures the string starts with a capital letter. [a-zA-Z]{2,3}: Matches 1 or 4 additional characters (either uppercase or lowercase). -: Matches the hyphen. \d{3,4}$: Matches 3 or 4 digits. ^ and $: Ensure the entire string matches this pattern.
                course_code: /^[A-Z][a-zA-Z]{1,4}-([A-Z]?\d{3,4}|\d{2}[A-Z]\d{1,2})$/, // // ^[A-Z] → Starts with uppercase, [a-zA-Z]{1,4} → 1–4 letters, - → Hyphen, ( → Begin segment match: [A-Z]?\d{3,4} → Optional uppercase followed by 3–4 digits OR, \d{2}[A-Z]\d{1,2} → Two digits, one uppercase, then 1–2 digits, $ → End, // Passes These Examples CIT-82D11, CIT-82A2, PEng-3994, CS-124, MEngP-2238, Law-M3045, CSIT-3045
                alpha_with_space_only: /^[A-Za-z\s]+$/,
                //minute_number: /^[a-zA-Z0-9_/]+$/,
                minute_number: /^[A-Z][a-zA-Z]*\/(January|February|March|April|May|June|July|August|September|October|November|December)\/\d{4}$/,
                institution_code: /^[a-zA-Z_-]+$/,
                integer: /^[-+]?\d+$/,
                number: /^[-+]?[1-9]\d*$/,
                whole_number: /^[0-9]\d*$/,
                strong_password: /^(?=.*[0-9])(?=.*[!@#$%^&*~<>{}()+-`'"?/|=_.:,:;])[a-zA-Z0-9!@#$%^&*~<>{}()+-`'"?/|=_.:,:;]{8,20}$/,

                // amex, visa, diners
                card: /^(?:4[0-9]{12}(?:[0-9]{3})?|5[1-5][0-9]{14}|6(?:011|5[0-9][0-9])[0-9]{12}|3[47][0-9]{13}|3(?:0[0-5]|[68][0-9])[0-9]{11}|(?:2131|1800|35\d{3})\d{11})$/,
                cvv: /^([0-9]){3,4}$/,

                // http://www.whatwg.org/specs/web-apps/current-work/multipage/states-of-the-type-attribute.html#valid-e-mail-address
                email: /^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/,

                url: /(https?|ftp|file|ssh):\/\/(((([a-zA-Z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-zA-Z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-zA-Z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-zA-Z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-zA-Z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-zA-Z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-zA-Z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-zA-Z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-zA-Z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/((([a-zA-Z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-zA-Z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-zA-Z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(\#((([a-zA-Z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?/,
                // abc.de
                domain: /^([a-zA-Z0-9]([a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?\.)+[a-zA-Z]{2,6}$/,

                datetime: /([0-2][0-9]{3})\-([0-1][0-9])\-([0-3][0-9])T([0-5][0-9])\:([0-5][0-9])\:([0-5][0-9])(Z|([\-\+]([0-1][0-9])\:00))/,
                // YYYY-MM-DD
                date: /(?:19|20)[0-9]{2}-(?:(?:0[1-9]|1[0-2])-(?:0[1-9]|1[0-9]|2[0-9])|(?:(?!02)(?:0[1-9]|1[0-2])-(?:30))|(?:(?:0[13578]|1[02])-31))/,
                // HH:MM:SS
                time: /(0[0-9]|1[0-9]|2[0-3])(:[0-5][0-9]){2}/,
                dateISO: /\d{4}[\/\-]\d{1,2}[\/\-]\d{1,2}/,
                // MM/DD/YYYY
                month_day_year: /(0[1-9]|1[012])[- \/.](0[1-9]|[12][0-9]|3[01])[- \/.](19|20)\d\d/,

                // #FFF or #FFFFFF
                color: /^#?([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$/
            },

        }
    });
</script>

<!-- Checkbox Logic -->
<script type="text/javascript">
    $(document).ready(function() {
        $('#select-all').click(function(event) {
            $('.checkbox1').prop('checked', this.checked);
        });
        $('.checkbox1').click(function(event) {
            if (!this.checked) {
                $('#select-all').prop('checked', false);
            }
        });
    });
</script>

<!-- Tooltipster -->
<script>
    $(document).ready(function() {
        $('.tooltipster-top').tooltipster({ position: 'top' });
        $('.tooltipster-left').tooltipster({ position: 'left' });
        $('.tooltipster-right').tooltipster({ position: 'right' });
        $('.tooltipster-bottom').tooltipster({ position: 'bottom' });
        $('.tooltipster-fadein').tooltipster({ animation: 'fade' });
        $('.tooltipster-growing').tooltipster({ animation: 'grow' });
        $('.tooltipster-swinging').tooltipster({ animation: 'swing' });
        $('.tooltipster-sliding').tooltipster({ animation: 'slide' });
        $('.tooltipster-falling').tooltipster({ animation: 'fall' });
    });
</script>

<!-- Masked Input -->
<script type="text/javascript">
    $(document).ready(function() {
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
    });
</script>

<!-- Footable and DataTables -->
<script type="text/javascript">
    $(document).ready(function() {
        $('#example').dataTable();
        $('#footable-res2').footable().bind('footable_filtering', function(e) {
            var selected = $('.filter').val();
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
</script>

<!-- Custom Scripts -->
<?= $this->fetch('script') ?>
</body>
</html>

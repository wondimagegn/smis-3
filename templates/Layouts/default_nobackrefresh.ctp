<!doctype html>
<html class="no-js" lang="en">

<head>
    <!-- META CHARS -->
    <?= $this->Html->charset(); ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    
    <!-- PAGE TITLE -->
    <title><?= Configure::read('ApplicationShortName') . ' ' . Configure::read('ApplicationVersionShort'); ?><?= !empty($this->fetch('title_details')) ? ' |'. $this->fetch('title_details') : (!empty($this->request->params['controller']) ? ' | ' . Inflector::humanize(Inflector::underscore($this->request->params['controller'])) . (!empty($this->request->params['action']) && $this->request->params['action'] != 'index' ? ' | ' . ucwords(str_replace('_', ' ', $this->request->params['action'])) : '') : ''); ?><?= ' - '. Configure::read('ApplicationTitleExtra'); ?></title>

    <!-- STYLESHEETS -->
    <link rel="stylesheet" href="/css/foundation.css" />

    <!-- for tooltips  -->
    <link rel="stylesheet" href="/js/tip/tooltipster.css">

    <link href="/js/footable/css/footable-demos.css" rel="stylesheet" type="text/css" />

    <?php
    if (Configure::read('debug') || true) { ?>
        <link rel="stylesheet" href="/css/dashboard.css">
        <link rel="stylesheet" href="/css/style.css">
        <link rel="stylesheet" href="/css/dripicon.css">
        <link rel="stylesheet" href="/css/typicons.css" />
        <link rel="stylesheet" href="/css/font-awesome.css" />
        <link rel="stylesheet" href="/sass/css/theme.css">
        <link href="/css/pace-theme-flash.css" rel="stylesheet" />
        <link rel="stylesheet" href="/css/slicknav.css" />

        <!-- <link rel="stylesheet" href="/css/datatables/dataTables.bootstrap.css" /> -->
        <link href="/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />

        <link href="/js/footable/css/footable.core.css?v=2-0-1" rel="stylesheet" type="text/css" />
        <link href="/js/footable/css/footable.standalone.css" rel="stylesheet" type="text/css" />
        <link href="/js/footable/css/footable-demos.css" rel="stylesheet" type="text/css" />

        <!-- pace loader -->
        <script src="/js/pace/pace.js"></script>
        <link href="/js/pace/themes/orange/pace-theme-flash.css" rel="stylesheet" />
        <link rel="stylesheet" href="/js/slicknav/slicknav.css" />


        <link rel="stylesheet" href="/css/common1.css" />
        <link rel="stylesheet" href="/css/responsive-tables.css" />
        <!-- <link href="https://cdn.datatables.net/1.10.7/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css" /> -->
    <?php
    } else {
        echo $this->AssetCompress->css('internal.css', array('full' => true));
    } ?>

    <script type="text/javascript" src="/js/jquery.js"></script>
    <script src="/js/vendor/modernizr.js"></script>
    <script src='/js/jquery-customselect-1.9.1.min.js'></script>
    <link href='/css/jquery-customselect-1.9.1.css' rel='stylesheet' />
    
    <script type="text/javascript">
      window.history.forward();
      function noBack() { window.history.forward(); }
    </script>

    <style>
        .center {
            text-align:center;
            vertical-align:middle;
        }
        .vcenter {
            vertical-align:middle;
        }
        .hcenter {
            text-align:center;
        }
    </style>

    <!-- To ckeck and reload opened tabs when the user session is no longer active -->
	<?php 
	$user_id = $this->Session->read('Auth.User.id');
	$is_logged_in = $this->Session->read('User.is_logged_in'); 
    ?>

	<script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function() {
            var isLoggedIn = <?php echo json_encode($is_logged_in); ?>;
            var userId = <?php echo json_encode($user_id); ?>;
            var checkInterval = 10000; // Check every 10 seconds to minimize server checks

            function checkSession() {
                fetch('/users/check_session')
                    .then(response => response.json())
                    .then(data => {
                        if (!data.is_logged_in) {
                            // User logged out in another tab, reload this tab
                            window.location.reload();
                        }
                    });
            }

            if (isLoggedIn) {
                setInterval(checkSession, checkInterval);
            }
        });

        // more efficient: Increased Interval and Page Visibility Check but still there is a probability of submitting not submitted forms

       	/*  document.addEventListener('DOMContentLoaded', function() {
            var userId = <?php echo json_encode($user_id); ?>;
            var checkInterval = 10000; // Check every 10 seconds

            function checkSession() {
                if (document.hidden) {
                    return; // Skip check if the page is not visible
                }
                fetch('/users/check_session')
                    .then(response => response.json())
                    .then(data => {
                        if (!data.is_logged_in) {
                            // User logged out in another tab, reload this tab
                            window.location.reload();
                        }
                    });
            }

            setInterval(checkSession, checkInterval);
        }); */
    </script>
	<!-- End ckeck and reload opened tabs when the user session is no longer active -->

</head>

<body onload="noBack();" onpageshow="if (event.persisted) noBack();" onunload="">
    <!-- preloader -->
    <div id="preloader">
        <div id="status">&nbsp;</div>
    </div>

    <!-- End of preloader -->
    <div id="myModal" class="reveal-modal" data-reveal>

    </div>

    <div id="busy_indicator">
        <img src="/img/busy.gif" alt="" class="displayed" />
    </div>


    <div class="off-canvas-wrap" data-offcanvas>
        <!-- right sidebar wrapper -->
        <div class="inner-wrap">
            <!-- Right sidemenu -->
            <div id="skin-select">

                <!--      Toggle sidemenu icon button -->
                <a id="toggle">
                    <span class="fa icon-menu"></span>
                </a>
                <!--  End of Toggle sidemenu icon button -->

                <div class="skin-part">
                    <div id="tree-wrap">

                        <!-- Profile -->
                        <div class="profile">
                            <a href="/">
                                <img alt="" class="" src="/img/<?php echo Configure::read('logo'); ?>">
                                <h3><?= Configure::read('ApplicationShortName'); ?> <small><?= Configure::read('ApplicationVersionShort'); ?></small></h3>
                            </a>

                        </div>
                        <!-- end of profile -->

                        <!-- Menu sidebar begin-->
                        <div class="side-bar">
                            <?= $this->element('mainmenu/mainmenuOptimized'); ?>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <!-- end right sidebar wrapper -->

        <div class="wrap-fluid" id="paper-bg">

            <!-- top nav -->
            <div class="top-bar-nest">
                <nav class="top-bar" data-topbar role="navigation" data-options="is_hover: false">
                    <ul class="title-area left">

                        <!-- Remove the class "menu-icon" to get rid of menu icon. Take out "Menu" to just have icon alone -->
                        <li class="toggle-topbar menu-icon"><a href="#"><span></span></a>
                        </li>
                    </ul>

                    <section class="top-bar-section ">
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
                                <?php
                                if ($this->Session->flash('Message.auth')) {
                                    echo '<div style="margin-top: 40px;">' . $this->Session->flash('auth') . '</div>';
                                }
                                if ($this->Session->check('Message.flash')) {
                                    echo '<div style="margin-top: 40px;">' . $this->Session->flash() . '</div>';
                                }
                                ?>
                            </div>
                        </div>
                    </div>

                    <?= $this->fetch('content'); ?>

                </div>
            </div>
            <footer>
                <div id="footer">
                    Copyright &copy; <?= Configure::read('Calendar.applicationStartYear') . ' - ' . date('Y'); ?> <?= Configure::read('CopyRightCompany'); ?>
                </div>
            </footer>
        </div>
    </div>


    <?php
    if (Configure::read('debug') || true) {  ?>
        <!-- main javascript library -->
        <script type="text/javascript" src="/js/waypoints.min.js"></script>
        <script type='text/javascript' src='/js/preloader-script.js'></script>

        <!-- foundation javascript -->
        <script type='text/javascript' src="/js/foundation.min.js"></script>

        <script type='text/javascript' src="/js/foundation/foundation.abide.js"></script>

        <!-- main edumix javascript -->
        <script type='text/javascript' src='/js/slimscroll/jquery.slimscroll.js'></script>
        <script type='text/javascript' src='/js/slicknav/jquery.slicknav.js'></script>
        <script type='text/javascript' src='/js/sliding-menu.js'></script>
        <script type='text/javascript' src='/js/scriptbreaker-multiple-accordion-1.js'></script>
        <script type="text/javascript" src="/js/number/jquery.counterup.min.js"></script>
        <script type="text/javascript" src="/js/circle-progress/jquery.circliful.js"></script>

        <!-- additional javascript -->
        <script type='text/javascript' src="/js/number-progress-bar/jquery.velocity.min.js"></script>
        <script type='text/javascript' src="/js/number-progress-bar/number-pb.js"></script>

        <script type='text/javascript' src='/js/app.js'></script>
        <script type='text/javascript' src="/js/loader/loader.js"></script>
        <script type='text/javascript' src="/js/loader/demo.js"></script>

        <script src="/js/datatables/jquery.dataTables.js" type="text/javascript"></script>

        <script src="/js/footable/js/footable.js?v=2-0-1" type="text/javascript"></script>
        <script src="/js/footable/js/footable.sort.js?v=2-0-1" type="text/javascript"></script>
        <script src="/js/footable/js/footable.filter.js?v=2-0-1" type="text/javascript"></script>
        <script src="/js/footable/js/footable.paginate.js?v=2-0-1" type="text/javascript"></script>

        <?= $this->Html->script('jquery-department_placement'); ?>

    <?php
    } else {
        echo $this->AssetCompress->script('mainjslib.js', array('full' => true));
        echo $this->AssetCompress->script('foundation.js', array('full' => true));
        echo $this->AssetCompress->script('maininternaledu.js', array('full' => true));
        echo $this->AssetCompress->script('additionaljavascript.js', array('full' => true));
        echo $this->AssetCompress->script('floatjavascript.js', array('full' => true));
    } ?>

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
                    course_code: /^[A-Z][a-zA-Z]{1,4}-\d{3,4}$/, //^[A-Z]: Ensures the string starts with a capital letter. [a-zA-Z]{2,3}: Matches 1 or 4 additional characters (either uppercase or lowercase). -: Matches the hyphen. \d{3,4}$: Matches 3 or 4 digits. ^ and $: Ensure the entire string matches this pattern.
                    minute_number: /^[a-zA-Z0-9_/]+$/,
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
                /* validators: {
                    diceRoll: function(el, required, parent) {
                        var possibilities = [true, false];
                        return possibilities[Math.round(Math.random())];
                    },
                    isAllowed: function(el, required, parent) {
                        var possibilities = ['a@zurb.com', 'b.zurb.com'];
                        return possibilities.indexOf(el.val) > -1;
                    }
                } */
            }
        });
    </script>

    <script type="text/javascript">
        /* $(function() {
            $(document).foundation();
        }); */

        $(document).ready(function() {
            $('#select-all').click(function(event) { //on click 
                //alert($('#select-all').prop('checked'));        
                if (this.checked) { // check select status
                    $('.checkbox1').each(function() { //loop through each checkbox
                        this.checked = true; //select all checkboxes with class "checkbox1"               
                    });
                } else {
                    $('.checkbox1').each(function() { //loop through each checkbox
                        this.checked = false; //deselect all checkboxes with class "checkbox1"                       
                    });
                }

            });

            $('.checkbox1').click(function(event) { //on click 
                if (!this.checked) { // check select status    
                    $('#select-all').attr('checked', false);
                }
            });
        });
    </script>

    <script src="/js/chart.js"></script>
    <!-- <script src="/js/angular.min.js"></script> -->
    <!-- <script src="/js/smisangularapp.js"></script> -->
    
    <!-- <script src="/js/angular-chart.min.js"></script> -->
    <!-- <script src="/js/angular-route.min.js"></script> -->

    <script src="/js/responsive-tables.js"></script>

    <!-- ToolTip -->

    <script type='text/javascript' src='/js/tip/jquery.tooltipster.js'></script>
    <script>
        $(document).ready(function() {
            $('.tooltipster-top').tooltipster({
                position: "top"
            });
            $('.tooltipster-left').tooltipster({
                position: "left"
            });
            $('.tooltipster-right').tooltipster({
                position: "right"
            });
            $('.tooltipster-bottom').tooltipster({
                position: "bottom"
            });
            $('.tooltipster-fadein').tooltipster({
                animation: "fade"
            });
            $('.tooltipster-growing').tooltipster({
                animation: "grow"
            });
            $('.tooltipster-swinging').tooltipster({
                animation: "swing"
            });
            $('.tooltipster-sliding').tooltipster({
                animation: "slide"
            });
            $('.tooltipster-falling').tooltipster({
                animation: "fall"
            });
        });
    </script>
    <!-- End ToolTip -->

    <!--  Masked Input -->
    <script type="text/javascript" src="/js/inputMask/jquery.maskedinput.js"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            // MASKED INPUT
            (function($) {
                "use strict";

                $("#intPhone").mask("+999 999 999999");
                $("#intPhoneSpaceFormatted").mask("(+999) 999 999999");
                $("#intPhoneHyphenFormatted").mask("(+999) 999-999999");

                $("#etPhone").mask("+251999999999");
                $("#etPhoneSpaceFormatted").mask("+251 999 999999");
                $("#etPhoneHyphenFormatted").mask("+251 999-999999");

                $("#phonemobile").mask("+251999999999");
                $("#phoneoffice").mask("+251999999999");
                $("#staffid").mask("AMU/9999/9999", {
                    placeholder: "_"
                });
                $("#ssn").mask("99--AAA--9999", {
                    placeholder: "*"
                });
            })(jQuery);
        });
    </script>
    <!--  End Masked Input -->

    <!-- Footable and data table -->

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
    </script>

    <!-- Footable and data table -->

    <?php //echo $this->Js->writeBuffer();  
    ?>
    <?= $this->fetch('script'); ?>
    <?= $this->element('sql_dump'); ?>
</body>

</html>
<!doctype html>
<html class="no-js" lang="en">

<head>
    <!-- META CHARS -->
    <?= $this->Html->charset(); ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="<?= Configure::read('ApplicationMetaDescription'); ?>" />
    <meta name="keywords" content="<?= Configure::read('ApplicationMetaKeywords'); ?>">
    <meta name="author" content="<?= Configure::read('ApplicationMetaAuthor'); ?>">

    <!-- Refresh the page every  30 MINUTES (in seconds) -->
    <meta http-equiv="refresh" content="1800">
    
    <!-- PAGE TITLE -->
    <title><?= Configure::read('ApplicationShortName') . ' ' . Configure::read('ApplicationVersionShort'); ?><?= !empty($this->fetch('title_details')) ? ' |'. $this->fetch('title_details') : (!empty($this->request->params['controller']) ? ' | ' . Inflector::humanize(Inflector::underscore($this->request->params['controller'])) . (!empty($this->request->params['action']) && $this->request->params['action'] != 'index' ? ' | ' . ucwords(str_replace('_', ' ', $this->request->params['action'])) : '') : ''); ?><?= ' - '. Configure::read('ApplicationTitleExtra'); ?></title>

    <link rel="stylesheet" type="text/css" href="/css/foundation.min.css" media="screen" />

    <?php

    $login_page_background = Configure::read('Image.login_background');
    $bg_index = rand(0, 9);

    if (Configure::read('debug') || true) { ?>
        <link rel="stylesheet" type="text/css" href="/css/dripicon.css" media="screen" />
        <link rel="stylesheet" type="text/css" href="/css/typicons.css" media="screen" />
        <link rel="stylesheet" type="text/css" href="/css/font-awesome.css" media="screen" />
        <link rel="stylesheet" type="text/css" href="/css/pace-theme-flash.css" media="screen" />
        <link rel="stylesheet" type="text/css" href="/css/theme.css" media="screen" />
        <link rel="stylesheet" type="text/css" href="/css/login.css" media="screen" />
        <link rel="stylesheet" type="text/css" href="/css/style.css" media="screen" />
        <link rel="stylesheet" href="/css/slicknav.css" />
        <link rel="stylesheet" href="/sass/css/theme.css">

        <link rel="stylesheet" href="/css/common1.css" />
        
        <?php
    } else {
        echo $this->AssetCompress->css('login.css', array('full' => true));
    } ?>
    <script src="/js/vendor/modernizr.js"></script>

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
</head>

<body class="fullbackground">
    <!-- preloader -->
    <div id="preloader">
        <div id="status">&nbsp;</div>
    </div>

    <div class="inner-wrap">
        <div class="wrap-fluid">
            <?php
            if ($this->Session->flash('Message.auth')) {
                echo $this->Session->flash('auth');
            }
            if ($this->Session->check('Message.flash')) {
                echo $this->Session->flash();
            }
            ?>
            <?= $content_for_layout; ?>
        </div>
    </div>

    <?php
    if (Configure::read('debug') || true) { ?>
        <script src="/js/jquery.js"></script>
        <script src="/js/waypoints.min.js"></script>
        <script src="/js/preloader-script.js"></script>
        <script src="/js/pace/pace.js"></script>

        <!-- foundation javascript -->
        <script type='text/javascript' src="/js/foundation.min.js"></script>
        <script type='text/javascript' src="js/foundation/foundation.abide.js"></script>

        <script type="text/javascript" src="/js/inputMask/jquery.maskedinput.js"></script>
        <script type="text/javascript" src="/js/circle-progress/jquery.circliful.js"></script>

        <?php
    } else {
        echo $this->AssetCompress->script('login.js', array('full' => true));
    } ?>

    <!-- <script type="text/javascript">
		$(function() {
			$(document).foundation();
		});
    </script> -->

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
            }
        });
    </script>

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

    <?= $this->Js->writeBuffer(); // Any Buffered Scripts ?>

</body>

</html>
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

	<!-- <link rel="stylesheet" type="text/css" href="/css/home/theme.css" media="screen" />
	<link rel="stylesheet" type="text/css" href="/css/home/login.css" media="screen" />
	<link rel="stylesheet" type="text/css" href="/css/home/style_p.css" media="screen" />
	<link rel="stylesheet" type="text/css" href="/css/home/blog.css" media="screen" /> -->

	<!-- for tooltips  -->
    <link rel="stylesheet" href="/js/tip/tooltipster.css">
	<link rel="stylesheet" href="/css/common1.css" />
	<link rel="stylesheet" href="/css/dashboard.css">
    <link rel="stylesheet" href="/css/style.css"> 
    <link rel="stylesheet" href="/css/dripicon.css">
    <link rel="stylesheet" href="/css/typicons.css" />
    <link rel="stylesheet" href="/css/font-awesome.css" />
    <link rel="stylesheet" href="/sass/css/theme.css">
    <link href="/css/pace-theme-flash.css" rel="stylesheet" />
    <link rel="stylesheet" href="/css/slicknav.css" />
	<script src="/js/vendor/modernizr.js"></script>

	<?php
		$login_page_background = Configure::read('Image.login_background');
		$bg_index = rand(0, 9);
	?>
</head>


<body>
	<!-- preloader -->
	<div id="preloader">
		<div id="status">&nbsp;</div>
	</div>
	<!-- End of preloader -->
	<div class="off-canvas-wrap" data-offcanvas>
		<div class="inner-wrap">
			<div class="top-bar-nest">
				<nav class="top-bar " data-topbar role="navigation" data-options="is_hover: false">
					<ul class="title-area left">
						<!-- Remove the class "menu-icon" to get rid of menu icon. Take out "Menu" to just have icon alone -->
						<li class="toggle-topbar menu-icon"><a href="#"><span></span></a>

						</li>
					</ul>

					<div class="left top-bar-section menu-margin-front">
						<div class="left  hide-banner">
							<a class="logo-link-bg" href="/">
								<img style="width:100px;height: 84px;" src="/img/amu.png">
							</a>
						</div>

						<div class="left  hide-banner">
							<div class='centeralign_smallheading'>
								<span style="color:gray;"> <?= Configure::read('CompanyName'); ?>  |  Office of the University Registrar </span>
							</div>
						</div>

					</div>

					<section class="top-bar-section">
						<ul class="right menu menu-margin-front">
							<li>
								<a class="show-menu" href="#"> Menu </a>
							</li>
							<li>

								<a href="/pages/academic_calender">Academic Calendar</a>
							</li>
							<li>
								<a href="/pages/official_transcript_request">Transcript Request</a>
							</li>
							<li>
								<a href="/pages/admission">Admission</a>
							</li>
							<li>
								<a href="#">Alumni Registration</a>
							</li>

						</ul>
					</section>
				</nav>
			</div>
		</div>
	</div>

	<!-- right sidebar wrapper -->
	<div class="inner-wrap container">
		<div class="wrap-fluid">
			<div class="row">
				<div class="medium-3 large-3 columns">
					<?= $this->element('leftmenu/leftmenu'); ?>
				</div>
				<div class="medium-9 large-9 columns">
					<div class="row">
						<?php
						if ($this->Session->check('Message.flash')) {
							echo $this->Session->flash();
						}
						?>

						<?= $content_for_layout; ?>

					</div>

				</div>
			</div>
		</div>
	</div>

	<div id="footer">
		<p>Copyright &copy; <?= Configure::read('Calendar.applicationStartYear') . ' - ' . date('Y'); ?> <?= Configure::read('CopyRightCompany'); ?></p>
	</div>
	
	<!-- main javascript library -->
	<script type='text/javascript' src="/js/jquery.js"></script>
	<script type="text/javascript" src="/js/waypoints.min.js"></script>
	<script type='text/javascript' src='/js/preloader-script.js'></script>
	<!-- foundation javascript -->
	<script type='text/javascript' src="/js/foundation.min.js"></script>
	<script type='text/javascript' src="/js/foundation/foundation.dropdown.js"></script>
	<!-- main edumix javascript -->
	<script type='text/javascript' src='/js/slimscroll/jquery.slimscroll.js'></script>

	<script type='text/javascript' src='/js/sliding-menu.js'></script>

	<script type='text/javascript' src='/js/scriptbreaker-multiple-accordion-1.js'></script>
	<script type="text/javascript" src="/js/number/jquery.counterup.min.js"></script>
	<script type="text/javascript" src="/js/circle-progress/jquery.circliful.js"></script>
	<script type='text/javascript' src='/js/app.js'></script>

	<script type='text/javascript' src="/js/foundation/foundation.abide.js"></script>
	<script type='text/javascript' src='/js/slicknav/jquery.slicknav.js'></script>
	<script type='text/javascript' src='/js/sliding-menu.js'></script>

	<script type="text/javascript" src="/js/inputMask/jquery.maskedinput.js"></script>

	<?= $this->Js->writeBuffer(); // Any Buffered Scripts ?>

	<script>
		// disable all tabs
		//// disable all tabs
		$('[data-toggle=tab]').click(function() {
			return false;
		}).addClass("disabledTab");

		var validated = function(tab) {
			//alert(tab);
			tab.unbind('click').removeClass('disabledTab').addClass('active');
			//$('[data-toggle=tab]').addClass("disabledTab");
		};

		/*
		function disableOtherTab(){
		    var listTab=[];
		    $("#ListOfTab ").find('li').each(function(i,e){
		    	listTab.push(i);
		    });
		    //alert(listTab);
			$("#ListOfTab").tabs({disabled:[listTab]});
		}

		$("#ListOfTab ").find('li').not('.active').each(function(i,e){
				$(this).addClass('disabledTab');
		});
		*/
		$('.btnNext').click(function() {
			var allValid = true;
			// get each input in this tab pane and validate
			$(this).parents('.tab-pane').find('.form-control').each(function(i, e) {
				// some condition(s) to validate each input
				if ($(e).val() != "") {
					// validation passed
					allValid = true;
				} else {
					// validation failed
					allValid = false;
				}
			});

			if (allValid) {
				var tabIndex = $(this).parents('.tab-pane').index();
				validated($('[data-toggle]').eq(tabIndex + 1));
				$('#ListOfTab  > .active').next('li').find('a').trigger('click');
			} else {
				//alert(allValid);
				//$('[data-toggle=tab]').addClass("disabledTab");
			}
		});

		$('.btnPrevious').click(function() {
			$('#ListOfTab > .active').prev('li').find('a').trigger('click');
		});

		// always validate first tab
		validated($('[data-toggle]').eq(0));
		//disableOtherTab();
		/*
		$(".nav-tabs a[data-toggle=tab]").on("click", function(e) {
		  if ($(this).hasClass("disable")) {
		    e.preventDefault();
		    return false;
		  }
		});
		*/
	</script>
	<style>
		.disabledTab {
			pointer-events: none;
		}
	</style>

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
                    id_number: /^[a-zA-Z0-9\/]+$/,
                    //course_code: /^[a-zA-Z0-9_-]+$/,
					course_code: /^[A-Z][a-zA-Z]{1,4}-\d{3,4}$/, //^[A-Z]: Ensures the string starts with a capital letter. [a-zA-Z]{2,3}: Matches 1 or 4 additional characters (either uppercase or lowercase). -: Matches the hyphen. \d{3,4}$: Matches 3 or 4 digits. ^ and $: Ensure the entire string matches this pattern.
					minute_number: /^[a-zA-Z0-9_/]+$/,
					id_number: /^[a-zA-Z0-9_/]+$/,
                    integer: /^[-+]?\d+$/,
                    number: /^[-+]?[1-9]\d*$/,
					valid_phone: /^\d{9,15}$/,
					et_phone: /^(?:\d{3}|\(\d{3}\))([-\/\.])\d{3}\1\d{4}$/, // ###-###-####
                    strong_password: /^(?=.*[0-9])(?=.*[!@#$%^&*])[a-zA-Z0-9!@#$%^&*]{6,16}$/,

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

    <script type="text/javascript">
        $(document).ready(function() {
            // MASKED INPUT
            (function($) {
                "use strict";
				$("#OfficialTranscriptRequestMobilePhone").mask("+251999999999");
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

</body>

</html>
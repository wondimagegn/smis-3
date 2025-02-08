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

    <!-- STYLESHEETS -->
    <link rel="stylesheet" href="/css/foundation.css" />

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

        <!-- <link href="/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css" /> -->
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

</head>

<body>
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
                <!--      End of Toggle sidemenu icon button -->

                <div class="skin-part">
                    <div id="tree-wrap">

                        <!-- Profile -->
                        <div class="profile">
                            <a href="/">
                                <img alt="" class="" src="/img/<?= Configure::read('logo'); ?>">
                                <h3><?= Configure::read('ApplicationShortName'); ?> <small><?= Configure::read('ApplicationVersionShort'); ?></small></h3>
                            </a>

                        </div>
                        <!-- end of profile -->
                        <!-- Menu sidebar begin-->

                        <div class="side-bar">
                            <?= $this->element('leftmenu/leftmenu'); ?>
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
                        <li class="toggle-topbar menu-icon"><a href="#"><span></span></a> </li>
                    </ul>

                    <section class="top-bar-section ">
                        <div class='centeralign_smallheading'> <span style="color:gray;"> <?= Configure::read('CompanyName'); ?>  |  Office of the University Registrar </span> </div>
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
                                    echo '<div style="margin-top: 40px;">'. $this->Session->flash('auth') . '</div>';
                                }
                                if ($this->Session->check('Message.flash')) {
                                    echo '<div style="margin-top: 40px;">'. $this->Session->flash(). '</div>';
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
    if (Configure::read('debug') || true) { ?>
        <!-- main javascript library -->

        <script type="text/javascript" src="/js/waypoints.min.js"></script>
        <script type='text/javascript' src='/js/preloader-script.js'></script>


        <!-- foundation javascript -->
        <script type='text/javascript' src="/js/foundation.min.js"></script>

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
        $(function() {
            $(document).foundation();
        });
    </script>
    <script src="/js/angular.min.js"></script>
    <!-- <script src="/js/smisangularapp.js"></script> -->

    <script src="/js/chart.js"></script>
    <script src="/js/angular-chart.min.js"></script>
    <script src="/js/angular-route.min.js"></script>

    <script src="/js/responsive-tables.js"></script>
    <style>
        .disabledTab {
            pointer-events: none;
        }
    </style>


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
    <?php //echo $this->Js->writeBuffer(); ?>
    <?= $this->fetch('script'); ?>
    <?= $this->element('sql_dump'); ?>
</body>

</html>
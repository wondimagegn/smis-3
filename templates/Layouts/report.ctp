<!doctype html>
<html class="no-js" lang="en">

<head>
    <?= $this->Html->charset(); ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    
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

        <link rel="stylesheet" type="text/css" href="/css/common1.css" media="screen" />

        <link href="https://cdn.datatables.net/1.10.7/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />

    <?php 
    } else {
        echo $this->AssetCompress->css('internal.css', array('full' => true));
    } ?>

    <script type="text/javascript" src="/js/jquery.js"></script>
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

<body>
    <div id="preloader">
        <div id="status">&nbsp;</div>
    </div>
    <!--   
    <div id="busy_indicator">
	<img src="/img/busy.gif" alt="" class="displayed" />	     
    </div>
    -->
    <div class="off-canvas-wrap" data-offcanvas>
        <!-- right sidebar wrapper -->
        <div class="inner-wrap">
            <!-- Right sidemenu -->
            <div id="skin-select">
                <a id="toggle"> <span class="fa icon-menu"></span> </a>

                <div class="skin-part">
                    <div id="tree-wrap">
                        <!-- Profile -->
                        <div class="profile">
                            <a href="/">
                                <img alt="" class="" src="/img/<?= Configure::read('logo'); ?>">
                                <h3><?= Configure::read('ApplicationShortName'); ?> <small><?= Configure::read('ApplicationVersionShort'); ?></small></h3>
                            </a>
                        </div>
                        
                        <div class="side-bar">
                            <?= $this->element('mainmenu/mainmenuOptimized'); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

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

            <div class="row" style="margin-top:-20px">
                <div class="large-12 columns">
                    <div class="row">
                        <div class="large-12 columns">
                            <div class="box">
                                <?= $this->Session->flash(); ?>
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

        <script src="/js/datatables/jquery.dataTables.js" type="text/javascript"></script>
        <script src="/js/datatable-list.js" type="text/javascript"></script>

        <?php 
    } else {
        echo $this->AssetCompress->script('mainjslib.js', array('full' => true));

        echo $this->AssetCompress->script('foundation.js', array('full' => true));

        echo $this->AssetCompress->script('maininternaledu.js', array('full' => true));
        echo $this->AssetCompress->script('additionaljavascript.js', array('full' => true));
        echo $this->AssetCompress->script('floatjavascript.js', array('full' => true));
    } ?>
    <?php // echo $this->Html->script('jquery-selectall');  ?>

    <script type="text/javascript">
        $(function() {
            $(document).foundation();
        });

        $(document).ready(function() {
            $('#select-all').click(function(event) { //on click 
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

    <?php
    //echo $this->fetch('dataTableSettings');
    //echo $this->Js->writeBuffer(); 
    echo $this->fetch('script');
    ?>
    <?php //echo $this->element('sql_dump');   ?>
</body>

</html>
<?php

use Cake\Core\Configure;
use Cake\Utility\Inflector;

?>
<!doctype html>
<html class="no-js">
<head>
    <?= $this->Html->charset(); ?>

    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>
        <?= Configure::read('ApplicationShortName') . ' ' . Configure::read('ApplicationVersionShort'); ?>
        <?= !empty($this->fetch('title_details')) ? ' | ' . $this->fetch('title_details') : (!empty($this->request->getParam('controller')) ? ' | ' . Inflector::humanize(Inflector::underscore($this->request->getParam('controller'))) . (!empty($this->request->getParam('action')) && $this->request->getParam('action') != 'index' ? ' | ' . ucwords(str_replace('_', ' ', $this->request->getParam('action'))) : '') : ''); ?>
        <?= ' - ' . Configure::read('ApplicationTitleExtra'); ?>
    </title>

    <?= $this->Html->css([
        'foundation', 'dashboard', 'style', 'dripicon', 'typicons', 'font-awesome', 'theme',
        'pace-theme-flash', 'slicknav', 'common1'
    ]); ?>

    <?= $this->Html->script(['jquery', 'vendor/modernizr']); ?>

    <?php $user_id = $this->request->getSession()->read('Auth.User.id'); ?>

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

<div id="preloader">
    <div id="status">&nbsp;</div>
</div>
<div class="off-canvas-wrap" data-offcanvas>
    <div class="inner-wrap">
        <div id="skin-select">
            <a id="toggle"> <span class="fa icon-menu"></span> </a>
            <div class="skin-part">
                <div id="tree-wrap">
                    <div class="profile">
                        <a href="/">
                            <img alt="" src="/img/<?= Configure::read('logo'); ?>">
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

        <div class="row" style="margin-top:-20px">
            <div class="large-12 columns">
                <div class="row">
                    <div class="large-12 columns">
                        <div class="box">
                            <?= $this->Flash->render(); ?>
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

<?= $this->Html->script([
    'waypoints.min', 'preloader-script', 'foundation.min', 'foundation/foundation.abide',
    'slimscroll/jquery.slimscroll', 'slicknav/jquery.slicknav', 'sliding-menu',
    'scriptbreaker-multiple-accordion-1', 'number/jquery.counterup.min',
    'circle-progress/jquery.circliful', 'number-progress-bar/jquery.velocity.min',
    'number-progress-bar/number-pb',
    'app',
    'loader/loader',
    'loader/demo',
    'angular',
    'smisangularapp'
]); ?>

<script>
    $(function() {
        $(document).foundation();
    });
</script>
<script type="text/javascript">
    $(function () {
        $(document).foundation();
    });
    $(document).ready(function () {
        $('#select-all').click(function (event) { //on click
            if (this.checked) { // check select status
                $('.checkbox1').each(function () { //loop through each checkbox
                    this.checked = true; //select all checkboxes with class "checkbox1"
                });
            } else {
                $('.checkbox1').each(function () { //loop through each checkbox
                    this.checked = false; //deselect all checkboxes with class "checkbox1"
                });
            }
        });

        $('.checkbox1').click(function (event) {
            //on click
            if (!this.checked) {
                // check select status
                $('#select-all').attr('checked', false);
            }
        });
    });

</script>


<?= $this->fetch('script'); ?>
</body>
</html>

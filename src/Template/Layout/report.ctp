<!DOCTYPE html>
<html class="no-js" lang="en">

<head>
    <?= $this->Html->charset(); ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

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

    <?= $this->Html->css([
        'foundation', 'dashboard', 'style', 'dripicon', 'typicons',
        'font-awesome', 'sass/css/theme', 'pace-theme-flash', 'slicknav',
        'common1', 'responsive-tables'
    ]); ?>

    <link href="https://cdn.datatables.net/1.10.7/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />

    <?= $this->Html->script('vendor/modernizr'); ?>

    <style>
        .center {
            text-align: center;
            vertical-align: middle;
        }
        .vcenter {
            vertical-align: middle;
        }
        .hcenter {
            text-align: center;
        }
    </style>
</head>

<body>
<div id="preloader">
    <div id="status">&nbsp;</div>
</div>

<div class="off-canvas-wrap" data-offcanvas>
    <div class="inner-wrap">
        <div id="skin-select">
            <a id="toggle"><span class="fa icon-menu"></span></a>
            <div class="skin-part">
                <div id="tree-wrap">
                    <div class="profile">
                        <a href="/">
                            <?= $this->Html->image(h(Configure::read('logo')), ['alt' => 'Logo']); ?>
                            <h3><?= h(Configure::read('ApplicationShortName')); ?>
                                <small><?= h(Configure::read('ApplicationVersionShort')); ?></small>
                            </h3>
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
            <nav class="top-bar" data-topbar role="navigation">
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
                &copy; <?= h(Configure::read('Calendar.applicationStartYear')) . ' - ' . date('Y'); ?>
                <?= h(Configure::read('CopyRightCompany')); ?>
            </div>
        </footer>
    </div>
</div>

<?= $this->Html->script([
    'jquery', 'waypoints.min', 'preloader-script', 'foundation.min',
    'slimscroll/jquery.slimscroll', 'slicknav/jquery.slicknav',
    'sliding-menu', 'scriptbreaker-multiple-accordion-1',
    'number/jquery.counterup.min', 'circle-progress/jquery.circliful',
    'app', 'foundation/foundation.abide',
    'datatables/jquery.dataTables', 'datatable-list'
]); ?>

<script>
    $(document).foundation();

    // Checkbox select all functionality
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

<?= $this->fetch('script'); ?>

</body>

</html>

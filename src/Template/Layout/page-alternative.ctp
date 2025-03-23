<!DOCTYPE html>
<html class="no-js" lang="en">

<head>
    <?= $this->Html->charset(); ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= h(Configure::read('ApplicationMetaDescription')); ?>">
    <meta name="keywords" content="<?= h(Configure::read('ApplicationMetaKeywords')); ?>">
    <meta name="author" content="<?= h(Configure::read('ApplicationMetaAuthor')); ?>">

    <!-- Refresh the page every 30 minutes -->
    <meta http-equiv="refresh" content="1800">

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
        'foundation', 'common1', 'dashboard', 'style', 'dripicon', 'typicons',
        'font-awesome', 'sass/css/theme', 'pace-theme-flash', 'slicknav',
        'responsive-tables'
    ]); ?>

    <?= $this->Html->script('vendor/modernizr'); ?>
</head>

<body>
<!-- Preloader -->
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
                            <?= $this->Html->image(Configure::read('logo'), ['alt' => 'Logo']); ?>
                            <h3><?= h(Configure::read('ApplicationShortName')); ?>
                                <small><?= h(Configure::read('ApplicationVersionShort')); ?></small>
                            </h3>
                        </a>
                    </div>

                    <div class="side-bar">
                        <?= $this->element('leftmenu/leftmenu'); ?>
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
                        <div class='centeralign_smallheading'>
                                <span style="color:gray;">
                                    <?= h(Configure::read('CompanyName')); ?>  | Office of the University Registrar
                                </span>
                        </div>
                    </section>
                </nav>
            </div>

            <div class="row" style="margin-top:-20px;">
                <div class="large-12 columns">
                    <div class="box">
                        <?= $this->Flash->render('auth'); ?>
                        <?= $this->Flash->render(); ?>
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
</div>

<?= $this->Html->script([
    'jquery', 'waypoints.min', 'preloader-script', 'foundation.min',
    'slimscroll/jquery.slimscroll', 'slicknav/jquery.slicknav',
    'sliding-menu', 'scriptbreaker-multiple-accordion-1',
    'number/jquery.counterup.min', 'circle-progress/jquery.circliful',
    'app', 'foundation/foundation.abide'
]); ?>

<script>
    $(document).foundation();

    // Disable all tabs initially
    $('[data-toggle=tab]').click(function() {
        return false;
    }).addClass("disabledTab");

    var validated = function(tab) {
        tab.unbind('click').removeClass('disabledTab').addClass('active');
    };

    $('.btnNext').click(function() {
        var allValid = true;
        $(this).parents('.tab-pane').find('.form-control').each(function(i, e) {
            if ($(e).val() !== "") {
                allValid = true;
            } else {
                allValid = false;
            }
        });

        if (allValid) {
            var tabIndex = $(this).parents('.tab-pane').index();
            validated($('[data-toggle]').eq(tabIndex + 1));
            $('#ListOfTab  > .active').next('li').find('a').trigger('click');
        }
    });

    $('.btnPrevious').click(function() {
        $('#ListOfTab > .active').prev('li').find('a').trigger('click');
    });

    validated($('[data-toggle]').eq(0));
</script>

<style>
    .disabledTab {
        pointer-events: none;
    }
</style>

</body>

</html>

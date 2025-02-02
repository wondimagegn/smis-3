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

    <title>Login<?= ' - '. Configure::read('ApplicationTitleExtra'); ?></title>

    <link rel="stylesheet" type="text/css" href="/css/foundation.min.css" media="screen" />
    <link href="/css/home/style.css" rel="stylesheet" />
    <link href="/css/home/flaticon.css" rel="stylesheet" />
    <link href="/css/home/login.css" rel="stylesheet" />
    <link rel="stylesheet" href="/css/common1.css" />
    <link rel="stylesheet" href="/css/dripicon.css">
    <link rel="stylesheet" href="/css/typicons.css" />
    <link rel="stylesheet" href="/css/font-awesome.css" />
    <link href="/css/pace-theme-flash.css" rel="stylesheet" />

    <script src="/js/vendor/modernizr.js"></script>
    
    <?php
        $login_page_background = Configure::read('Image.login_background');
        //debug(count($login_page_background));
        $bg_count = count($login_page_background)-1;
        $bg_index = rand(0, $bg_count);
    ?>

</head>

<body>
    <div id="intro">
        <div class="row">
            <div class="large-6 medium-6 columns">
                <img src="/img/amulogo.png" alt="logo" />
                <h3 class="color-white heading"><?= Configure::read('CompanyShortName'); ?> | Office of the University Registrar</h3>
                <hr />
                <h5 class="color-white " style="line-height: 27px;"> This is our registrar portal for students, academic staffs and alumni to access different registrar services offered by the office of the university registrar. </h5>
            </div>

            <div class="large-6 medium-6 columns">
                <?php
                if ($this->Session->check('Message.flash')) {
                    //echo $this->Session->flash();
                }
                
                ?>
                <?= $this->Flash->render() ?>
                

                <?= $content_for_layout; ?>
            </div>
        </div>
    </div>

    <div class="auto-grid">
        
        <div class="featured-item-grid">
            <a href="/pages/academic_calender">
                <div class="glyph-icon flaticon-calendar23"></div>
                <h6 class="text-center">Academic <br /> Calendar</h6>
            </a>
        </div>
        
        <div class="featured-item-grid">
            <a href="/pages/announcement">
                <div class="glyph-icon flaticon-speech7" style="color: rgb(23, 199, 85);"></div>
                <h6 class="text-center">Registrar <br />Announcemnts</h6>
            </a>
        </div>
        
        <div class="featured-item-grid">
            <a href="/pages/official_request_tracking">
                <div class="glyph-icon flaticon-laptop10" style="color: rgb(8, 161, 181);"></div>
                <h6 class="text-center"> Official <br /> Transcript</h6>
            </a>
        </div>

        <div class="featured-item-grid">
            <a href="/pages/admission">
                <div class="glyph-icon flaticon-cloud47" style="color: rgb(255, 136, 0);"></div>
                <h6 class="text-center">Online <br />Admission</h6>
            </a>
        </div>

        <div class="featured-item-grid">
            <a href="/pages/online_admission_tracking">
                <div class="glyph-icon flaticon-cloud47" style="color: rgb(255, 136, 0);"></div>
                <h6 class="text-center">Online <br />Admission Tracking</h6>
            </a>
        </div>

        <div class="featured-item-grid">
            <a href="/alumni/member_registration">
                <div class="glyph-icon flaticon-user20" style="color: rgb(255, 136, 0);"></div>
                <h6 class="text-center">Alumni Registration</h6>
            </a>
        </div>

        <div class="featured-item-grid">
            <a href="/pages/check_graduate">
                <div class="glyph-icon flaticon-cloud47" style="color: rgb(255, 136, 0);"></div>
                <h6 class="text-center">Forgery <br />Check</h6>
            </a>
        </div>

    </div>


    <div id="footer">
        <p style="padding:5px;">
            Copyright &copy; <?= Configure::read('Calendar.applicationStartYear') . ' - ' . date('Y'); ?> <?= Configure::read('CopyRightCompany'); ?>
        </p>
    </div>

    <script src="/js/jquery.js"></script>

    <script>
        var backgroundImage = Array();
        $(document).ready(function() {

            <?php
            foreach ($login_page_background as $dck => $dcv) { ?>
                if (screen.width >= 1366 && screen.height >= 768) {
                    backgroundImage[<?= $dck; ?>] = "<?= $dcv['1366_768']; ?>";
                } else if (screen.width >= 1280 && screen.height >= 800) {
                    backgroundImage[<?= $dck; ?>] = "<?= $dcv['1280_800']; ?>";
                } else if (screen.width >= 1280 && screen.height >= 768) {
                    backgroundImage[<?= $dck; ?>] = "<?= $dcv['1280_768']; ?>";
                } else if (screen.width >= 1280 && screen.height >= 720) {
                    backgroundImage[<?= $dck; ?>] = "<?= $dcv['1280_720']; ?>";
                } else if (screen.width >= 1024 && screen.height >= 768) {
                    backgroundImage[<?= $dck; ?>] = "<?= $dcv['1024_768']; ?>";
                } else if (screen.width >= 800 && screen.height >= 600) {
                    backgroundImage[<?= $dck; ?>] = "<?= $dcv['800_600']; ?>";
                }
                <?php
            }
            ?>
        });
    </script>

    <script>
        function change() {
            var index = Math.round(Math.random() * <?= $bg_count;?>);
            var bg_index = backgroundImage[index];
            var imgUrl = "url(\'/img/login-background/" + bg_index + "\')";
            $("#intro").css("background-image", imgUrl);
        }
        setInterval(change, 6000);
    </script>

</body>

</html>
<?php
/**
 * Default CakePHP 3.8 Layout
 */

use Cake\Core\Configure;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= Configure::read('ApplicationMetaDescription') ?>">
    <meta name="keywords" content="<?= Configure::read('ApplicationMetaKeywords') ?>">
    <meta name="author" content="<?= Configure::read('ApplicationMetaAuthor') ?>">
    <meta http-equiv="refresh" content="1800">
    <title>Login<?= ' - ' . Configure::read('ApplicationTitleExtra') ?></title>

    <?= $this->Html->css([
        'foundation.min',
        'home/style',
        'home/flaticon',
        'home/login',
        'common1',
        'dripicon',
        'typicons',
        'font-awesome',
        'pace-theme-flash'
    ]) ?>

    <?= $this->Html->script('vendor/modernizr') ?>
</head>
<body>
<div id="intro">
    <div class="row">
        <div class="large-6 medium-6 columns">
            <?= $this->Html->image('amulogo.png', ['alt' => 'logo']) ?>
            <h3 class="color-white heading"> <?= Configure::read('CompanyShortName') ?> | Office of the University Registrar</h3>
            <hr>
            <h5 class="color-white">This is our registrar portal for students, academic staff, and alumni to access different registrar services offered by the office of the university registrar.</h5>
        </div>
        <div class="large-6 medium-6 columns">
            <?= $this->Flash->render() ?>
            <?= $this->fetch('content') ?>
        </div>
    </div>
</div>

<div class="auto-grid">
    <?php
    $items = [
        ['url' => 'pages/academic_calender', 'icon' => 'flaticon-calendar23', 'title' => 'Academic Calendar'],
        ['url' => 'pages/announcement', 'icon' => 'flaticon-speech7', 'color' => 'rgb(23, 199, 85)', 'title' => 'Registrar Announcements'],
        ['url' => 'pages/official_request_tracking', 'icon' => 'flaticon-laptop10', 'color' => 'rgb(8, 161, 181)', 'title' => 'Official Transcript'],
        ['url' => 'pages/admission', 'icon' => 'flaticon-cloud47', 'color' => 'rgb(255, 136, 0)', 'title' => 'Online Admission'],
        ['url' => 'pages/online_admission_tracking', 'icon' => 'flaticon-cloud47', 'color' => 'rgb(255, 136, 0)', 'title' => 'Online Admission Tracking'],
        ['url' => 'alumni/member_registration', 'icon' => 'flaticon-user20', 'color' => 'rgb(255, 136, 0)', 'title' => 'Alumni Registration'],
        ['url' => 'pages/check_graduate', 'icon' => 'flaticon-cloud47', 'color' => 'rgb(255, 136, 0)', 'title' => 'Forgery Check']
    ];
    foreach ($items as $item): ?>
        <div class="featured-item-grid">
            <a href="<?= $this->Url->build($item['url']) ?>">
                <div class="glyph-icon <?= $item['icon'] ?>" style="color: <?= $item['color'] ?? '' ?>;"></div>
                <h6 class="text-center"> <?= $item['title'] ?> </h6>
            </a>
        </div>
    <?php endforeach; ?>
</div>

<div id="footer">
    <p>Copyright &copy; <?= Configure::read('Calendar.applicationStartYear') . ' - ' . date('Y') . ' ' . Configure::read('CopyRightCompany') ?></p>
</div>

<?= $this->Html->script('jquery') ?>

<script>
    var backgroundImage = [];
    $(document).ready(function() {
        <?php foreach (Configure::read('Image.login_background') as $index => $image): ?>
        if (screen.width >= 1366 && screen.height >= 768) backgroundImage[<?= $index ?>] = "<?= $image['1366_768'] ?>";
        else if (screen.width >= 1280 && screen.height >= 800) backgroundImage[<?= $index ?>] = "<?= $image['1280_800'] ?>";
        else if (screen.width >= 1280 && screen.height >= 768) backgroundImage[<?= $index ?>] = "<?= $image['1280_768'] ?>";
        else if (screen.width >= 1280 && screen.height >= 720) backgroundImage[<?= $index ?>] = "<?= $image['1280_720'] ?>";
        else if (screen.width >= 1024 && screen.height >= 768) backgroundImage[<?= $index ?>] = "<?= $image['1024_768'] ?>";
        else if (screen.width >= 800 && screen.height >= 600) backgroundImage[<?= $index ?>] = "<?= $image['800_600'] ?>";
        <?php endforeach; ?>
    });

    function change() {
        var index = Math.floor(Math.random() * backgroundImage.length);
        var imgUrl = "url('/img/login-background/" + backgroundImage[index] + "')";
        $("#intro").css("background-image", imgUrl);
    }
    setInterval(change, 6000);
</script>
</body>
</html>

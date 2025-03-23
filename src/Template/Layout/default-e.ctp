<!DOCTYPE html>
<html class="no-js" lang="en">

<head>
    <?= $this->Html->charset(); ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <title><?= h(Configure::read('ApplicationShortName')) ?> <?= h(Configure::read('ApplicationVersionShort')) ?></title>

    <?= $this->Html->css(['es', 'ep'], ['media' => 'screen']) ?>
    <?= $this->Html->css('ep', ['media' => 'print']) ?>

    <!--[if lt IE 7]>
    <?= $this->Html->css('ielt7', ['media' => 'screen']) ?>
    <![endif]-->

    <?= $this->Html->script('es') ?>
</head>

<body id="e404">
<div id="root">
    <?= $this->fetch('content'); ?>
</div>
</body>

</html>

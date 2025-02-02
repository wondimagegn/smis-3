<!doctype html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <link rel="stylesheet" type="text/css" href="/css/common1.css" media="screen" />
    <?=  $this->Html->charset(); ?>
    <title><?=  $page_title; ?></title>

    <?php if (Configure::read('debug') == 0) { ?>
        <meta http-equiv="Refresh" content="<?=  $pause; ?>;url=<?=  $url; ?>" />
    <?php } ?>
    <style>
        p {
            text-align: center;
            font: bold 1.1em sans-serif
        }
        a {
            color: #444;
            text-decoration: none
        }
        a:hover {
            text-decoration: underline;
            color: #44E
        }
    </style>
</head>

<body>
    <div class="flash"><?=  $message; ?></div>
</body>

</html>
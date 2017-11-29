<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Красная звезда</title>

    <!-- env:prod --#>
        <link rel="stylesheet" href="views/css/term.min.css?<?= filemtime("views/css/term.min.css")?>">
    <!-- env:prod:end -->

    <!-- env:dev -->
        <link href='../bower_components/bootstrap/dist/css/bootstrap.css' rel="stylesheet">
        <link href='views/css/style-sass.css?<?= filemtime(ROOT.'/views/css/style-sass.css')?>' rel="stylesheet">
        <link href='views/css/term-sass.css?<?= filemtime(ROOT.'/views/css/term-sass.css')?>' rel="stylesheet">
    <!-- env:dev:end -->

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>
    <input type="hidden" id="idScreen" value="<?= $firstScreen;?>">
    <input type="hidden" id="action" value="<?= $firstAction;?>">
    <input type="hidden" id="sid" value="<?= $sid;?>">
    <div class="container-fluid">
        <div class="header">
            <div class="logos">
                <!-- <div class="lines line1">Бюджетное учреждение города Омска</div> -->
                <div class="lines line2">БУ города Омска Спортивный комплекс</div>
                <div class="row">
                    <div class="col-md-3 logo1 text-center"><img src='views/img/logo.png' alt=""></div>
                    <div class="col-md-6 title-logo text-center"><img src='views/img/title.png' alt=""></div>
                    <div class="col-md-3 logo2 text-center"><img src='views/img/logo2.png' alt=""></div>
                </div>
            </div>

            <div class="row time">
                <div class='col-md-6'>
                    <div class="currDate"></div>
                </div>
                <div class='col-md-6 text-right'>
                    <div style='display: inline; width: 100px; text-align: right' class='currHour'></div>
                    <div style='display: inline; width: 100px; text-align: center' class='currDelim'>:</div>
                    <div style='display: inline; width: 100px; text-align: left' class='currMinute'></div>
                </div>
            </div>
        </div>

        <div id="main">
        </div>
    </div>

    <!-- env:prod --#>
        <script src='views/js/terminal.min.js?<?= filemtime("views/js/terminal.min.js")?>'></script>
    <!-- env:prod:end -->

    <!-- env:dev -->
        <script src='../bower_components/jquery/dist/jquery.js'></script>
        <script src='../bower_components/bootstrap/dist/js/bootstrap.js'></script>
        <script src='views/js/terminal.js?<?= filemtime("views/js/terminal.js")?>'></script>
        <script src='views/js/cashcode.js?<?= filemtime("views/js/cashcode.js")?>'></script>
        <!-- <script src='views/js/rfid.js?<?= filemtime("views/js/rfid.js")?>'></script> -->
    <!-- env:dev:end -->
</body>
</html>
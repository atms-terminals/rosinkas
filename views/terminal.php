<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <link rel="shortcut icon" href='img/favicon.ico' type="image/x-icon" />
    <title>Terminal</title>

    <!-- env:prod --#>
        <link rel="stylesheet" href="views/css/style.min.css?<?= filemtime("views/css/style.min.css")?>">
    <!-- env:prod:end -->

    <!-- env:dev -->
        <link rel="stylesheet" href="../bower_components/bootstrap/dist/css/bootstrap.min.css" />
        <link rel="stylesheet" href="../bower_components/leaflet/dist/leaflet.css" />
        <link rel="stylesheet" href="views/css/style-sass.css?<?= filemtime("views/css/style-sass.css")?>" />
    <!-- env:dev:end -->

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>

    <div id="mapid"></div>

    <!-- env:prod --#>
        <script src='views/js/index.min.js?<?= filemtime("views/js/index.min.js")?>'></script>
    <!-- env:prod:end -->

    <!-- env:dev -->
        <script src="../bower_components/jquery/dist/jquery.js"></script>
        <script src="../bower_components/bootstrap/dist/js/bootstrap.js"></script>
        <script src='views/js/index.js?<?= filemtime("views/js/index.js")?>'></script>
    <!-- env:dev:end -->
</body>
</html>
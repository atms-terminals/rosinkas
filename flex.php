<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Document</title>
    <link rel="stylesheet" href="views/css/style-sass.css">
    <link rel="stylesheet" href="views/css/flex-sass.css">
    <link href='bower_components/bootstrap/dist/css/bootstrap.css' rel="stylesheet">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-1"></div>
            <div class="col-sm-10">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-12 text-center">
                        <input type="text" id="target" value='____-__/__'>
                        <input type="hidden" class="mask" value='****-**/**'>
                    </div>
                </div>
                <div class="row">
                    <?php
                    // include 'views/kbd/kbdRus.php';
                    // include 'views/kbd/kbdRusNum.php';
                    include 'views/kbd/kbdNum.php'; ?>
                </div>
            </div>

            </div>
            <div class="col-sm-1"></div>
        </div>
    </div>
</body>
        <script src='bower_components/jquery/dist/jquery.js'></script>
        <script src='bower_components/bootstrap/dist/js/bootstrap.js'></script>
        <script src='views/kbd/kbd.js'></script>
</html>
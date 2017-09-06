<?php
    define('ROOT', __DIR__);
?>
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
        <h1>Загрузка файла со списком услуг</h1>
        <?php
            include 'views/uploadPriceXls.php';
        ?>

    </div>
</body>
        <script src='bower_components/jquery/dist/jquery.js'></script>
        <script src='bower_components/bootstrap/dist/js/bootstrap.js'></script>
        <script src='views/kbd/kbd.js'></script>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <link rel="shortcut icon" href='img/favicon.ico' type="image/x-icon" />
    <title>ServiceDesc</title>

    <!-- env:prod --#>
        <link rel="stylesheet" href="views/css/login.min.css?<?= filemtime("views/css/login.min.css")?>">
    <!-- env:prod:end -->

    <!-- env:dev -->
        <link href="bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="views/css/login-sass.css?<?= filemtime("views/css/login-sass.css")?>" rel="stylesheet">
        <link href="views/css/style-sass.css?<?= filemtime("views/css/style-sass.css")?>" rel="stylesheet">
    <!-- env:dev:end -->

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>
    <div class="container">
        <form class="form-signin" method="post">
            <label for="inputEmail" class="sr-only">Логин</label>
            <input type="email" name="login" class="form-control" placeholder="Логин" required autofocus>
            <label for="inputPassword" class="sr-only">Пароль</label>
            <input type="password" name="password" class="form-control" placeholder="Пароль" required>
            <div class="checkbox">
<!--             <label>
                <input type="checkbox" value="remember-me"> Запомнить меня
            </label>
 -->            </div>
            <button class="btn btn-lg btn-primary btn-block" type="submit">Войти</button>
        </form>
    </div>
</body>
</html>
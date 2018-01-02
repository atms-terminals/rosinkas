<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon" />
    <title>Росинкас. Панель администратора</title>

    <!-- env:prod --#>
        <link rel="stylesheet" href="views/css/style.min.css?<?= filemtime("views/css/login.min.css")?>">
    <!-- env:prod:end -->

    <!-- env:dev -->
        <link href='../bower_components/bootstrap/dist/css/bootstrap.css' rel="stylesheet" />
        <link href="../bower_components/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css" rel="stylesheet" />
        <link href='views/css/style-sass.css?<?= filemtime(ROOT.'/views/css/style-sass.css')?>' rel="stylesheet" />
    <!-- env:dev:end -->

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>
    <input type="hidden" id="sid" value="<?= $sid;?>">
    <div class="container">
        <!-- Nav tabs -->
        <ul class="nav nav-tabs" role="tablist" id='mainTabs'>
            <li role="presentation" class="active"><a href="#hws" aria-controls="hws" role="tab" data-toggle="tab">Оборудование</a></li>
            <li role="presentation"><a href="#admin" aria-controls="admin" role="tab" data-toggle="tab">Администрирование</a></li>
            <!-- <li role="presentation"><a href="#schedule" aria-controls="schedule" role="tab" data-toggle="tab">Настройка дней</a></li> -->
            <!-- <li role="presentation"><a href="#priceGroup" aria-controls="priceGroup" role="tab" data-toggle="tab">Настройка меню</a></li> -->
            <!-- <li role="presentation"><a href="#collections" aria-controls="collections" role="tab" data-toggle="tab">Инкассации</a></li> -->
        </ul>


        <div class="tab-content">
            <div role="tabpanel" id="schedule" class="tab-pane fade">
                <div class="resultArea">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-1">
                                <select class="year">
                                    <?php
                                    $year = date('Y');
                                    for ($i = 0; $i < 3; $i++) {
                                        echo "<option value='$year'>$year</option>";
                                        $year++;
                                    }
                                    ?>
                                </select> 
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <div class='input-group date dt'>
                                        <input type='text' class="form-control" />
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-7">
                                <label><input type="checkbox" class="dt-type"> Выходной</label>&nbsp;
                                <button class="btn btn-primary add-dt">Добавить дату</button>
                            </div>
                        </div>
                        <div class="row dates">
                            <?php require_once(ROOT.'/views/dates.php') ?>
                        </div>
                    </div>
                </div>
            </div>

            <div role="tabpanel" id="collections" class="tab-pane fade">
                <div class="resultArea">
                </div>
            </div>

            <div role="tabpanel" id="hws" class="tab-pane fade in active">
                <a class="btn btn-primary" id='refreshHwsStatus'>Обновить</a>
                <div class="resultArea">
                    <?php require_once(ROOT.'/views/hwsState.php'); ?>
                </div>
            </div>

            <div role="tabpanel" id="admin" class="tab-pane fade">
                <h2>Терминалы</h2>
                <button type='button' class='btn btn-primary changeUser add terminal' data-toggle='modal' data-target='#changeUserDialog'>Добавить</button>
                <div id="terminals">
                    <div class="resultArea">
                    </div>
                </div>
                <h2>Пользователи</h2>
                <button type='button' class='btn btn-primary changeUser add user' data-toggle='modal' data-target='#changeUserDialog'>Добавить</button>
                <div id="users">
                    <div class="resultArea">
                    </div>
                </div>
            </div>

            <div role="tabpanel" id="priceGroup" class="tab-pane fade">
                <div>
                    <div class="btn-group day-type" role="group" aria-label="...">
                        <button type="button" class="btn btn-default active" value="2">Рабочий</button>
                        <button type="button" class="btn btn-default" value="1">Выходной или праздничный</button>
                    </div>
                </div>
                <input type="checkbox" id="priceStatus" checked> Только активные услуги
                <div class="resultArea"></div>
            </div>
        </div>

    </div>

    <?php
    include 'include/confirmDeleteDialog.php';
    include 'include/prepaidDialog.php';
    include 'include/editUserDialog.php';
    include 'include/changePasswordDialog.php';
    ?>

    <!-- env:prod --#>
        <script src='views/js/admin.min.js?<?= filemtime("views/js/admin.min.js")?>'></script>
    <!-- env:prod:end -->

    <!-- env:dev -->
        <script src='../bower_components/jquery/dist/jquery.js'></script>
        <script src="../bower_components/moment/min/moment-with-locales.min.js"></script>
        <script src="../bower_components/moment/locale/ru.js"></script>
        <script src='../bower_components/bootstrap/dist/js/bootstrap.js'></script>
        <script src="../bower_components/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>

        <script src='views/js/admin.js?<?= filemtime(ROOT.'/views/js/admin.js')?>'></script>
    <!-- env:dev:end -->
</body>
</html>
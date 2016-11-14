<h1>Текущая наличность терминала</h1>
<table class='table table-striped'>
    <thead>
        <tr>
            <th>Адрес</th>
            <th>Подтвержденная</th>
            <th>Не подтвержденная</th>
            <th>Итого</th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($collections['money'] as $address => $current) {
            echo "<tr>
                    <td class=''>$address</td>
                    <td class='' align='center'>{$current['confirmed']}</td>
                    <td class='' align='center'>{$current['notConfirmed']}</td>
                    <td class='' align='center'>".($current['notConfirmed'] + $current['confirmed'])."</td>
                </tr>";
        }
        ?>
    </tbody>
</table>

<h1>Инкассации</h1>
<table class='table table-striped'>
    <thead>
        <tr>
            <th>Адрес</th>
            <th>Дата</th>
            <th>Сумма</th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($collections['collections'] as $collection) {
            echo "<tr>
                    <td class=''>{$collection['address']}</td>
                    <td class='' align='center'>{$collection['dt']}</td>
                    <td class='' align='center'>{$collection['action']}</td>
                </tr>";
        }
        ?>
    </tbody>
</table>

<h1>Текущая наличность терминала</h1>
<table class='table table-striped'>
    <thead>
        <tr>
            <th>Адрес</th>
            <th>Подтвержденная</th>
            <th>Не подтвержденная</th>
            <th>Депозит</th>
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
                    <td class='' align='center'>{$current['deposit']}</td>
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
            <th>Услуг на сумму</th>
            <th>Депозит</th>
            <th>Сумма (наличные)</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($collections['collections'] as $collection) {
            echo "<tr>
                    <td class=''>{$collection['address']}</td>
                    <td class='' align='center'>{$collection['dt']}</td>
                    <td class='' align='center'>{$collection['summ']}</td>
                    <td class='' align='center'>{$collection['deposit']}</td>
                    <td class='' align='center'>{$collection['amount']}</td>
                    <td class='' align='center'>
                        <button class='getCollectionDetails btn btn-default'>Детализация</button>
                        <input type='hidden' class='id' value='{$collection['id_collection']}'>
                    </td>
                </tr>";
        }
        ?>
    </tbody>
</table>

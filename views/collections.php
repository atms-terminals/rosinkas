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
        foreach ($collections as $collection) {
            echo "<tr>
                    <td class=''>{$collection['address']}</td>
                    <td class='' align='center'>{$collection['dt']}</td>
                    <td class='' align='center'>{$collection['action']}</td>
                </tr>";
        }
        ?>
    </tbody>
</table>

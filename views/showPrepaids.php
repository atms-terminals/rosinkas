<table class='table table-striped'>
    <thead>
        <tr>
            <th>Карта</th>
            <th>ФИО</th>
            <th>Аванс</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($statuses as $row) {
            echo "<tr>
                    <td class='col-md-6' align='center'><span class='card'>{$row['card']}</span></td>
                    <td class='col-md-6' align='center'>{$row['name']}</td>
                    <td class='col-md-6' align='center'><span class='amount'>{$row['amount']}</span></td>
                    <td class='col-md-6' align='center'>
                        <button type='button' class='btn btn-primary changePrepayment' data-toggle='modal' data-target='#changePrepaymentDialog'>Изменить</button>
                    </td>
                </tr>";
        }
        ?>
    </tbody>
</table>

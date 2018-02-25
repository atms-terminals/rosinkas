<table id="cards-table" class="display" data-order='[[0, "asc"]]'>
    <thead>
        <tr>
            <th>Номер</th>
            <th>Организация</th>
            <th>Адрес</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($list as $card) {
            echo "<tr>
                    <td class='num'>{$card['num']}</td>
                    <td class='org'>{$card['org']}</td>
                    <td class='address'>{$card['address']}</td>
                    <td class='control' align='right'>
                        <button type='button' class='btn btn-primary changeCard edit card' data-toggle='modal' data-target='#changeCardDialog'
                            data-id='{$card['id']}'>Изменить</button>&nbsp;
                        <button type='button' class='btn btn-primary confirmDelete card' data-toggle='modal' data-target='#confirmDeleteDialog'
                            data-id='{$card['id']}'>Удалить</button>
                    </td>
                </tr>";
        }
        ?>
    </tbody>
</table>


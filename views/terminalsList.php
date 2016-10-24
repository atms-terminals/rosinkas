<table class='table'>
    <thead>
        <tr>
            <th>ip-адрес</th>
            <th>Адрес</th>
            <th>Статус</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($list as $terminal) {
            if ($terminal['status'] == 1) {
                $class = 'bg-success';
                $btn = "<button type='button' class='btn btn-primary disable changeStatus terminal'>Запретить</button>";
            } else {
                $class = "bg-danger";
                $btn = "<button type='button' class='btn btn-primary enable changeStatus terminal'>Разрешить</button>";
            }
            echo "<tr class='$class'>
                    <td class='ip'>{$terminal['ip']}</td>
                    <td class='address'>{$terminal['address']}</td>
                    <td class='' align='center'>$btn</td>
                    <td class='' align='right'>
                        <input type='hidden' class='id' value='{$terminal['id']}'>
                        <button type='button' class='btn btn-primary changeUser edit terminal' data-toggle='modal' data-target='#changeUserDialog'>Изменить</button>
                        <button type='button' class='btn btn-primary confirmDelete terminal' data-toggle='modal' data-target='#confirmDeleteDialog'>Удалить</button>
                    </td>
                </tr>";
        }
        ?>
    </tbody>
</table>

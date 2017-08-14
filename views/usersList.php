<table class='table'>
    <thead>
        <tr>
            <th>Логин</th>
            <th>Статус</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($list as $user) {
            if ($user['status'] == 1) {
                $class = 'bg-success';
                $btn = "<button type='button' class='btn btn-primary disable changeStatus user'>Запретить</button>";
            } else {
                $class = "bg-danger";
                $btn = "<button type='button' class='btn btn-primary enable changeStatus user'>Разрешить</button>";
            }

            echo "<tr class='$class'>
                    <td class='login'>{$user['login']}</td>
                    <td class='' align='center'>
                        $btn
                        <button type='button' class='btn btn-primary changeUserPassword' data-toggle='modal' data-target='#changePasswordDialog'>Изменить пароль</button>
                    </td>
                    <td class='' align='right'>
                        <input type='hidden' class='id' value='{$user['id']}'>
                        <button type='button' class='btn btn-primary changeUser edit user' data-toggle='modal' data-target='#changeUserDialog'>Изменить</button>
                        <button type='button' class='btn btn-primary confirmDelete user' data-toggle='modal' data-target='#confirmDeleteDialog'>Удалить</button>
                    </td>
                </tr>";
        }
        ?>
    </tbody>
</table>

<table class='table table-striped'>
    <thead>
        <tr>
            <th>№</th>
            <th>Адрес</th>
            <?php
            foreach ($devices as $value) {
                echo "<th>$value</th>";
            }
            ?>
            <th>Текущая наличность</th>
            <th>Последняя инкассация</th>
        </tr>
    </thead>
    <tbody>
        <?php
        // echo "<pre>"; print_r($money); echo "</pre>";
        foreach ($statuses as $id => $states) {
            echo "<tr>
                    <td>$id</td>
                    <td>{$states['address']}</td>";
            foreach ($devices as $key => $value) {
                if ($states['status'][$key]['isError'] == 0) {
                    $status = "<span class='glyphicon glyphicon-ok-circle green' title='{$states['status'][$key]['dt']}: {$states['status'][$key]['message']}'>";
                } elseif ($states['status'][$key]['isError'] == 1) {
                    $status = "<span class='glyphicon glyphicon-remove-circle red' title='{$states['status'][$key]['dt']}: {$states['status'][$key]['message']}'>";
                } else {
                    $status = "<span class='glyphicon glyphicon-ban-circle blue' title='Нет данных'>";
                }
                echo "<td class='status $key'>$status</td>";
            }

            $free = isset($money['free'][$id]) ? number_format($money['free'][$id], 2, '.', ' ') : '0.00';
            echo "<td align='center'>$free</td>";

            $last = isset($money['collections'][$id]) ? "{$money['collections'][$id]['dt']}" : 'н/д';
            $title = isset($money['collections'][$id]) ? number_format($money['collections'][$id]['amount'], 2, '.', ' ') : '';
            echo "<td align='center' title='$title'>$last</td>";

            echo '</tr>';
        }
        ?>
    </tbody>
</table>

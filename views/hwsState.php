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
            <th>Заполненность</th>
            <th>Последняя инкассация</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $problemOnly = !isset($_GET['problemOnly']) ? true : (empty($_GET['problemOnly']) ? false : true);
        // echo "<pre>"; print_r($money); echo "</pre>";
        foreach ($statuses as $id => $states) {
            $html = "<td class='pointer' data-target='#historyDialog' data-toggle='modal'>$id<input type='hidden' value='$id' class='id'></td>
                    <td class='pointer' data-target='#historyDialog' data-toggle='modal'>{$states['address']}</td>";
            
            $isProblem = false;
            foreach ($devices as $key => $value) {
                if ($states['status'][$key]['isError'] == 0) {
                    $status = "<span class='glyphicon glyphicon-ok-circle green' title='{$states['status'][$key]['dt']}: {$states['status'][$key]['message']}'>";
                } elseif ($states['status'][$key]['isError'] == 1) {
                    $status = "<span class='glyphicon glyphicon-remove-circle red' title='{$states['status'][$key]['dt']}: {$states['status'][$key]['message']}'>";
                    $isProblem = true;
                } else {
                    $status = "<span class='glyphicon glyphicon-ban-circle blue' title='Нет данных'>";
                    $isProblem = true;
                }
                $html .= "<td class='status $key'>$status</td>";
            }

            $free = isset($money['free'][$id]) ? number_format($money['free'][$id], 2, '.', ' ') : '0.00';
            $notes = isset($money['nominals'][$id]['total']) ? $money['nominals'][$id]['total'] : '0';

            $class = '';
            $p = $notes / MAX_CASS_CAPASITY;
            if ($p > 0.5 && $p < 0.8) {
                $class = 'notes-warning';
                $isProblem = true;
            } elseif ($p >= 0.8) {
                $class = 'notes-danger';
                $isProblem = true;
            } 

            $html .= "<td align='center' class='pointer $class' data-target='#notesDetailDialog' data-toggle='modal'>$free ($notes листов)</td>";

            $title = isset($money['collections'][$id][0]) ? number_format($money['collections'][$id][0]['amount'], 2, '.', ' ') : '';
            $last = isset($money['collections'][$id][0]) ? "{$money['collections'][$id][0]['dt']} ($title руб.)" : 'н/д';
            $html .= "<td align='center' class='pointer' data-target='#collectionDetailDialog' data-toggle='modal'>$last</td>";

            $rowClass = $problemOnly ? ($isProblem ? '' : 'hidden') : '';
            echo "<tr class='$rowClass'>$html</tr>";
        }
        ?>
    </tbody>
</table>

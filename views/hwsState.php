<table class='table table-striped'>
    <thead>
        <tr>
            <th>Адрес</th>
            <?php
            foreach ($devices as $value) {
                echo "<th>$value</th>";
            }
            ?>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($statuses as $address => $states) {
            echo "<tr>
                    <td class='col-md-6'>$address</td>";
            foreach ($devices as $key => $value) {
                if ($states[$key]['isError'] == 0) {
                    $class = 'bg-success';
                } elseif ($states[$key]['isError'] == 1) {
                    $class = "bg-danger";
                } else {
                    $class = '';
                }

                echo "<td class='col-md-2 $class'>{$states[$key]['dt']}<br>{$states[$key]['message']}</td>";
            }
            echo '</tr>';
        }
        ?>
    </tbody>
</table>

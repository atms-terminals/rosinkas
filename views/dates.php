<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Дополнительные рабочие дни</h3>
    </div>
    <div class="panel-body extra-work">
        <table class="table table-striped">
            <?php
            foreach ($dates['worked'] as $day) {
                echo "<tr>";
                echo "<td>{$day['dt']}</td>";
                echo "<td><button type='button' class='btn btn-primary del-date' value='{$day['id']}'>Удалить</button></td>";
                echo "</tr>";
            }
            ?>
        </table>
    </div>
</div>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Дополнительные выходные дни</h3>
    </div>
    <div class="panel-body extra-holiday">
        <table class="table table-striped">
            <?php
            foreach ($dates['holidays'] as $day) {
                echo "<tr>";
                echo "<td>{$day['dt']}</td>";
                echo "<td><button type='button' class='btn btn-primary del-date' value='{$day['id']}'>Удалить</button></td>";
                echo "</tr>";
            }
            ?>
        </table>
    </div>
</div>

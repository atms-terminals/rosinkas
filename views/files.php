<h1>Файлы</h1>
<table class='table table-striped'>
    <thead>
        <tr>
            <th>Дата</th>
            <th>Файл</th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($files as $file) {
            echo "<tr>
                    <td class=''>{$file['dt']}</td>
                    <td class='' align='center'>{$file['path']}</td>
                    <td class='' align='center'>
                        <a href='./".FILES_PATH."{$file['path']}'>Скачать</a>
                    </td>
                </tr>";
        }
        ?>
    </tbody>
</table>

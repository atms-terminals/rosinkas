<?php
/**
 * Получение уровня в меню
 *
 * @param int $id Уровень меню
 * @return string html-код списка;
 */
function getMenuLevel($menu, $id)
{
    $html = '';
    if (!empty($menu[$id])) {
        foreach ($menu[$id] as $key => $item) {
            $status = $item['status'] ? 'checked' : '';
            if (empty($menu[$item['id']])) {
                $html .= "<li><input class='serviceItem id' type='checkbox' id='{$item['id']}' $status title='запретить/разрешить'>
                    {$item['desc']} (id={$item['id']}) 
                    <button class='confirmDelete price'><span class='glyphicon glyphicon-remove' title='Удалить' data-toggle='modal' data-target='#confirmDeleteDialog'></span></button><br>
                    <input type='text' value='{$item['clients_desc']}' class='clientsDesc' size='100' placeholder='Название для терминала' title='Название для терминала' />
                    </li>";
            } else {
                $html .= "<li><input class='serviceItem id' type='checkbox' id='{$item['id']}' $status title='запретить/разрешить'>
                    {$item['desc']} (id={$item['id']}) 
                    <button class='confirmDelete price'><span class='glyphicon glyphicon-remove' title='Удалить' data-toggle='modal' data-target='#confirmDeleteDialog'></span></button><br>
                    <input type='text' value='{$item['clients_desc']}' class='clientsDesc' size='100' placeholder='Название для терминала' title='Название для терминала' />
                    ";
                $html .= "<ul>";
                $html .= getMenuLevel($menu, $item['id']);
                $html .= "</ul>";
                $html .= "</li>";
            }
        }
    }
    return $html;
}

// создаем структуру меню
?>
<ul>
<?=getMenuLevel($list, 0);?>
</ul>
<?php
function printButton($key)
{
    $hidden = $key ? '' : 'hidden';
    $half = $key ? '' : 'half';
    if ($key == 'bs') {
        return "<div class='rus-block'>
                <button class='btn btn-danger small-font-size kbd rus-btn wide backspace wide-btn'>Стереть</button>
                <input type='hidden' class='target' value='target'>
            </div>";
    } elseif ($key == 'ok') {
        return "<div class='rus-block'>
                <button class='btn btn-primary medium-font-size kbd rus-btn wide wide-btn'>OK</button>
            </div>";
    } elseif ($key == 'space') {
        return "<div class='rus-block'>
                <button class='btn btn-default medium-font-size kbd rus-btn space-btn'></button>
                <input type='hidden' class='target' value='target'>
                <input type='hidden' class='char' value=' '>
            </div>";
    }

    return "<div class='rus-block $half'>
                <button class='btn btn-default medium-font-size kbd rus-btn $hidden'>$key</button>
                <input type='hidden' class='target' value='target'>
                <input type='hidden' class='char' value='$key'>
            </div>";
}
?>

<div class="my-flex-container">
    <?php
    $keySeq = [
                'й', 'ц', 'у', 'к', 'е', 'н', 'г', 'ш', 'щ', 'з', 'х', 'ъ', 'bs',
                '-', 'ф', 'ы', 'в', 'а', 'п', 'р', 'о', 'л', 'д', 'ж', 'э', '/',
                'я', 'ч', 'с', 'м', 'и', 'т', 'ь', 'б', 'ю', '.', 'ok',
                'space'];
    foreach ($keySeq as $char) {
        echo printButton($char);
    }
    ?>
</div>

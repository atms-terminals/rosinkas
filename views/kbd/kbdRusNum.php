<?php
function printButton($key)
{
    $hidden = '';
    $half = '';
    if ($key === '') {
        $hidden = 'hidden';
        $half = 'half';
    }

    if ($key == 'bs') {
        return "<div class='rus-num-block'>
                <button class='btn btn-danger small-font-size kbd rus-num-btn wide backspace wide-btn'>Стереть</button>
                <input type='hidden' class='target' value='target'>
            </div>";
    } elseif ($key == 'ok') {
        return "<div class='rus-num-block'>
                <button class='btn btn-primary medium-font-size kbd rus-num-btn wide wide-btn'>OK</button>
            </div>";
    } elseif ($key == 'space') {
        return "<div class='rus-num-block'>
                <button class='btn btn-default medium-font-size kbd rus-num-btn space-btn'></button>
                <input type='hidden' class='target' value='target'>
                <input type='hidden' class='char' value=' '>
            </div>";
    }

    return "<div class='rus-num-block $half'>
                <button class='btn btn-default medium-font-size kbd rus-num-btn $hidden'>$key</button>
                <input type='hidden' class='target' value='target'>
                <input type='hidden' class='char' value='$key'>
            </div>";
}
?>

<div class="my-flex-container">
    <?php
    $keySeq = [
                '1', '2', '3', '4', '5', '6', '7', '8', '9', '0', '-', 'bs',
                'й', 'ц', 'у', 'к', 'е', 'н', 'г', 'ш', 'щ', 'з', 'х', 'ъ',
                'ф', 'ы', 'в', 'а', 'п', 'р', 'о', 'л', 'д', 'ж', 'э', '/',
                'я', 'ч', 'с', 'м', 'и', 'т', 'ь', 'б', 'ю', '.', 'ok',
                'space'];
    foreach ($keySeq as $char) {
        echo printButton($char);
    }
    ?>
</div>

<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Localization\Loc as Loc;

Loc::loadMessages(__FILE__);
$this->setFrameMode(false);
?>
<?
    //Без верстки
    foreach($arResult['CALCULATE_PERCENTS_FOR_CURRENCIES'] as $currency => $values)
    {
        echo $currency . ':'; echo '<br/>';
        foreach($values as $value) {
            echo $value; echo '<br/>';
        }
    }
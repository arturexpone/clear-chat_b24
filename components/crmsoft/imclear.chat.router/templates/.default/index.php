<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
?>

<?php
$APPLICATION->IncludeComponent('crmsoft:imclear.chat.list','',['SEF_FOLDER' => $arResult['FOLDER'],'URL_TEMPLATES' => $arResult['URL_TEMPLATES'],'VARIABLES' => $arResult['VARIABLES']],false);
?>

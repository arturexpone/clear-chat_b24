<?php

$MODULE_ID = 'crmsoft.imclear';

use Bitrix\Main\Config\Option;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Application;
use Bitrix\Main\Loader;

$app = Application::getInstance();
$context = $app->getContext();
$request = $context->getRequest();
Loc::loadMessages($context->getServer()->getDocumentRoot()."/bitrix/modules/main/options.php");
Loc::loadMessages(__FILE__);

global $USER;
if (!$USER->CanDoOperation($MODULE_ID . '_settings')) {
    $APPLICATION->AuthForm(Loc::getMessage("ACCESS_DENIED"));
}

if(!Loader::includeModule('iblock')) {
    ShowError(Loc::GetMessage($MODULE_ID."_MODULE_iblock_NOT_INSTALLED"));
    return;
}

if(!Loader::includeModule('crm')) {
    ShowError(Loc::GetMessage($MODULE_ID."_MODULE_crm_NOT_INSTALLED"));
    return;
}

$arIblocks = [0 => 'не выбрано'];
$dbIblock = \Bitrix\Iblock\IblockTable::query()
    ->setSelect(['ID','NAME'])
    ->exec();
while($arIBlock = $dbIblock->fetch()) {
    $arIblocks[$arIBlock['ID']] = '['.$arIBlock['ID'].']: '.$arIBlock['NAME'];
}

$arAllOptions = [
    'common' => [
        [
            'iic_users_fullaccess',
            Loc::getMessage($MODULE_ID.'_iic_users_fullaccess'),
            Option::get($MODULE_ID, 'iic_users_fullaccess'),
            ['hidden']
        ]
    ]
];

if(isset($request["save"]) && check_bitrix_sessid()) {
    foreach ($arAllOptions as $tab=>$part) {
        foreach($part as $okey=>$arOption) {
            if(is_array($arOption)) {
                if($arOption[0] === 'iic_users_fullaccess') {
                    $val = $_REQUEST['iic_users_fullaccess'];
                    if(is_array($val)) {
                        $val = serialize($val);
                    } else {
                        $val = '';
                    }
                    Option::set($MODULE_ID, 'iic_users_fullaccess', $val);
                    $arAllOptions[$tab][$okey][2] = $val;
                } else {
                    __AdmSettingsSaveOption($MODULE_ID, $arOption);
                }
            }
        }
    }
}

$arTabs = [
    [
        'DIV' => 'common',
        'TAB' => Loc::getMessage($MODULE_ID.'_common_tab_title'),
        'TITLE' => Loc::getMessage($MODULE_ID.'_common_tab_subtitle')
    ]
];

$tabControl = new CAdminTabControl("tabControl", $arTabs);

$tabControl->Begin();
?>
<form method="POST" action="<?= $APPLICATION->GetCurPage() ?>?mid=<?= htmlspecialcharsbx($mid) ?>&amp;lang=<?= LANG ?>"
      name="<?= $MODULE_ID ?>_settings">
    <?= bitrix_sessid_post() ?>
    <?php
    foreach ($arTabs as $tab) {
        $tabControl->BeginNextTab();
        foreach($arAllOptions[$tab['DIV']] as $Option)
        {
            if($Option[0] === 'iic_users_fullaccess') {
                \CRMsoft\ImClear\Helpers\OptionsHelper::drawRightsControl($Option);
            } else {
                __AdmSettingsDrawRow($MODULE_ID, $Option);
            }
        }
    }?>
    <?php $tabControl->Buttons();?>
    <input type="submit" class="adm-btn-save" name="save" value="<?=Loc::getMessage($MODULE_ID.'_save')?>">
    <?=bitrix_sessid_post()?>
    <?php $tabControl->End(); ?>
</form>
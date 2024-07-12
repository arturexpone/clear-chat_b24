<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
$this->addExternalCss('/bitrix/js/im/css/im.css');
$mess = \Bitrix\Main\Localization\Loc::loadLanguageFile(__FILE__);
$bodyClass = $APPLICATION->getPageProperty('BodyClass', false);
$APPLICATION->setPageProperty('BodyClass', trim(sprintf('%s %s', $bodyClass, ' pagetitle-toolbar-field-view no-background')));
?>
    <div class="pagetitle-container toolbar__container_space-b">
        <div class="pagetitle-container page-title-align-left-container">
            <?php
            $APPLICATION->IncludeComponent(
                'bitrix:main.ui.filter',
                '',
                [
                    'FILTER_ID' => $arParams['LIST_ID'],
                    'GRID_ID' => $arParams['LIST_ID'],
                    'FILTER' => $arResult['FILTER_FIELDS'],
                    'ENABLE_LIVE_SEARCH' => true,
                    'ENABLE_LABEL' => true
                ],
                false
            );?>
        </div>
        <div class="pagetitle-container page-title-align-right-container">
            <a href="<?=$arParams['SEF_FOLDER'];?>" class="ui-btn ui-btn-light-border">
                К списку чатов
            </a>
        </div>
    </div>
<?php
$snippets = new \Bitrix\Main\Grid\Panel\Snippet();
$APPLICATION->IncludeComponent(
    'bitrix:main.ui.grid',
    '',
    [
        'GRID_ID' => $arParams['LIST_ID'],
        'COLUMNS' => $arResult['COLUMNS'],
        'ROWS' => $arResult['ITEMS'],
        'SHOW_ROW_CHECKBOXES' => true,
        'NAV_OBJECT' => $arResult['NAV_OBJECT'],
        'AJAX_MODE' => 'Y',
        'AJAX_ID' => \CAjax::getComponentID('bitrix:main.ui.grid','.default',''),
        'PAGE_SIZES' => [
            ['NAME' => '5', 'VALUE' => '5'],
            ['NAME' => '10', 'VALUE' => '10'],
            ['NAME' => '20','VALUE' => '20'],
            ['NAME' => '50', 'VALUE' => '50'],
            ['NAME' => '100', 'VALUE' => '100']
        ],
        'AJAX_OPTION_JUMP' => 'N',
        'SHOW_CHECK_ALL_CHECKBOXES' => true,
        'SHOW_ROW_ACTIONS_MENU'     => true,
        'SHOW_GRID_SETTINGS_MENU'   => true,
        'SHOW_NAVIGATION_PANEL'     => true,
        'SHOW_PAGINATION'           => true,
        'SHOW_SELECTED_COUNTER'     => true,
        'SHOW_TOTAL_COUNTER'        => true,
        'SHOW_PAGESIZE'             => true,
        'SHOW_ACTION_PANEL'         => true,
        'ALLOW_COLUMNS_SORT'        => true,
        'ALLOW_COLUMNS_RESIZE'      => true,
        'ALLOW_HORIZONTAL_SCROLL'   => true,
        'ALLOW_SORT'                => true,
        'ALLOW_PIN_HEADER'          => true,
        'AJAX_OPTION_HISTORY'       => 'N',
        'TOTAL_ROWS_COUNT' => $arResult['TOTAL_ROWS_COUNT'],
        'ENABLE_COLLAPSIBLE_ROWS' => true,
        'ACTION_PANEL'=>[
            'GROUPS' => [[
                'ITEMS' => [
                    $snippets->getRemoveButton()
                ]
            ]]
        ]
    ],
    false
);
?>

<script>
	BX.message(<?=\CUtil::PhpToJSObject($mess);?>);
	BX.CRMsoft.ImClear.Detail.grid_id = '<?=$arParams['LIST_ID'];?>';
</script>

<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
\Bitrix\Main\UI\Extension::load("ui.forms");
\Bitrix\Main\UI\Extension::load("ui.hint");

$mess = \Bitrix\Main\Localization\Loc::loadLanguageFile(__FILE__);
$bodyClass = $APPLICATION->getPageProperty('BodyClass', false);
$APPLICATION->setPageProperty('BodyClass', trim(sprintf('%s %s', $bodyClass, ' pagetitle-toolbar-field-view no-background')));
?>


<div class="page-toolbar">
    <div id="uiToolbarContainer" class="ui-toolbar">
        <div id="pagetitleContainer" class="ui-toolbar-title-box">
            <div class="ui-toolbar-title-inner">
                <div class="ui-toolbar-title-item-box">
                    <span id="pagetitle" class="ui-toolbar-title-item">Чаты</span>
                    <span class="ui-toolbar-star" id="uiToolbarStar" data-bx-title-template="" data-bx-url=""
                          title="Добавить текущую страницу в левое меню"></span></div>
            </div>
        </div>
        <div class="ui-toolbar-after-title-buttons">

        </div>
        <div class="ui-toolbar-filter-box">
            <!-- Final :: Search -->
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
            ); ?>
            <!--'start_frame_cache_xIeC97'-->

            <!--'end_frame_cache_xIeC97'--></div>
        <div class="ui-toolbar-right-buttons">
            <button class="ui-btn ui-btn-light-border ui-btn-icon-setting ui-btn-themes"
                    data-btn-uniqid="uibtn-8p6ztejd"
                    data-json-options="{&quot;menu&quot;:{&quot;id&quot;:&quot;toolbar_deal_list_settings_menu&quot;,&quot;items&quot;:[{&quot;id&quot;:&quot;crm-kanban-settings-sub-menu&quot;,&quot;text&quot;:&quot;Настройка канбана&quot;,&quot;items&quot;:[{&quot;text&quot;:&quot;Настроить карточку просмотра&quot;,&quot;onclick&quot;:{&quot;event&quot;:&quot;crm-kanban-settings-fields-view&quot;}},{&quot;text&quot;:&quot;Настроить карточку создания&quot;,&quot;onclick&quot;:{&quot;event&quot;:&quot;crm-kanban-settings-fields-edit&quot;}}]},{&quot;text&quot;:&quot;Импорт сделок&quot;,&quot;title&quot;:&quot;Импорт сделок&quot;,&quot;icon&quot;:&quot;btn-import&quot;,&quot;href&quot;:&quot;\/crm\/deal\/import\/?category_id=0&quot;},{&quot;text&quot;:&quot;Миграция из других CRM&quot;,&quot;title&quot;:&quot;Миграция из других CRM&quot;,&quot;icon&quot;:&quot;btn-migration&quot;,&quot;href&quot;:&quot;\/market\/category\/migration\/&quot;},{&quot;text&quot;:&quot;Шаблоны регулярных сделок&quot;,&quot;title&quot;:&quot;Шаблоны регулярных сделок&quot;,&quot;onclick&quot;:{&quot;code&quot;:&quot;BX.Crm.Page.openSlider(\u0022\/crm\/deal\/recur\/category\/0\/\u0022);&quot;}},{&quot;text&quot;:&quot;Отраслевые сценарии&quot;,&quot;title&quot;:&quot;Отраслевые сценарии&quot;,&quot;onclick&quot;:{&quot;code&quot;:&quot;BX.SidePanel.Instance.open(\u0027\/marketplace\/configuration\/placement\/crm_deal\/?from=setting_list\u0027);&quot;}},{&quot;id&quot;:&quot;crm-type-button&quot;,&quot;text&quot;:&quot;Режим работы CRM&quot;,&quot;title&quot;:&quot;Режим работы CRM&quot;,&quot;onclick&quot;:{&quot;code&quot;:&quot;BX.CrmLeadMode.init({\u0027ajaxPath\u0027:\u0027\/bitrix\/tools\/crm_lead_mode.php\u0027,\u0027dealPath\u0027:\u0027\/crm\/deal\/kanban\/\u0027,\u0027leadPath\u0027:\u0027\/crm\/lead\/kanban\/\u0027,\u0027isAdmin\u0027:\u0027Y\u0027,\u0027isLeadEnabled\u0027:\u0027N\u0027,\u0027messages\u0027:{\u0027CRM_TYPE_TITLE\u0027:\u0027Выберите удобный способ работы с CRM\u0027,\u0027CRM_TYPE_SAVE\u0027:\u0027Сохранить\u0027,\u0027CRM_TYPE_CANCEL\u0027:\u0027Отменить\u0027,\u0027CRM_TYPE_TURN_ON\u0027:\u0027Включить\u0027,\u0027CRM_LEAD_CONVERT_TITLE\u0027:\u0027Конвертация лидов\u0027,\u0027CRM_LEAD_CONVERT_TEXT\u0027:\u0027Простой режим работы с CRM автоматически конвертирует каждый лид в сделку и клиента. \u003Cbr\/\u003E\u003Cbr\/\u003E\\nЕсли у вас остались незакрытые лиды, при включении этого режима они будут сконвертированы в сделки и клиентов. \u003Cbr\/\u003E\u003Cbr\/\u003E\\nВсе роботы и бизнес-процессы, настроенные на создание сделок и клиентов, сразу отработают для созданных элементов.\u0027,\u0027CRM_TYPE_CONTINUE\u0027:\u0027Продолжить\u0027,\u0027CRM_LEAD_BATCH_CONVERSION_STATE\u0027:\u0027#processed# из #total#\u0027,\u0027CRM_LEAD_BATCH_CONVERSION_TITLE\u0027:\u0027Конвертация лидов\u0027,\u0027CRM_LEAD_BATCH_CONVERSION_COMPLETED\u0027:\u0027Конвертация лидов завершена.\u0027,\u0027CRM_LEAD_BATCH_CONVERSION_COUNT_SUCCEEDED\u0027:\u0027Успешно сконвертировано: #number_leads#.\u0027,\u0027CRM_LEAD_BATCH_CONVERSION_COUNT_FAILED\u0027:\u0027Не удалось сконвертировать: #number_leads#.\u0027,\u0027CRM_LEAD_BATCH_CONVERSION_NO_NAME\u0027:\u0027Без имени\u0027}}); BX.CrmLeadMode.preparePopup();&quot;}}],&quot;offsetLeft&quot;:20,&quot;closeByEsc&quot;:true,&quot;angle&quot;:true},&quot;dropdown&quot;:false}"></button>
        </div>
    </div>

</div>
<?php
$APPLICATION->IncludeComponent(
    'bitrix:main.ui.grid',
    '',
    [
        'GRID_ID' => $arParams['LIST_ID'],
        'COLUMNS' => $arResult['COLUMNS'],
        'ROWS' => $arResult['ITEMS'],
        'SHOW_ROW_CHECKBOXES' => false,
        'NAV_OBJECT' => $arResult['NAV_OBJECT'],
        'AJAX_MODE' => 'Y',
        'AJAX_ID' => \CAjax::getComponentID('bitrix:main.ui.grid', '.default', ''),
        'PAGE_SIZES' => [
            ['NAME' => '5', 'VALUE' => '5'],
            ['NAME' => '10', 'VALUE' => '10'],
            ['NAME' => '20', 'VALUE' => '20'],
            ['NAME' => '50', 'VALUE' => '50'],
            ['NAME' => '100', 'VALUE' => '100']
        ],
        'AJAX_OPTION_JUMP' => 'N',
        'SHOW_CHECK_ALL_CHECKBOXES' => false,
        'SHOW_ROW_ACTIONS_MENU' => true,
        'SHOW_GRID_SETTINGS_MENU' => true,
        'SHOW_NAVIGATION_PANEL' => true,
        'SHOW_PAGINATION' => true,
        'SHOW_SELECTED_COUNTER' => false,
        'SHOW_TOTAL_COUNTER' => true,
        'SHOW_PAGESIZE' => true,
        'SHOW_ACTION_PANEL' => true,
        'ALLOW_COLUMNS_SORT' => true,
        'ALLOW_COLUMNS_RESIZE' => true,
        'ALLOW_HORIZONTAL_SCROLL' => true,
        'ALLOW_SORT' => true,
        'ALLOW_PIN_HEADER' => true,
        'AJAX_OPTION_HISTORY' => 'N',
        'TOTAL_ROWS_COUNT' => $arResult['TOTAL_ROWS_COUNT'],
        'ENABLE_COLLAPSIBLE_ROWS' => true,
        'ACTION_PANEL' => [
        ]
    ],
    false
);
?>

<script>
    BX.message(<?=\CUtil::PhpToJSObject($mess)?>);
    BX.CRMsoft.ImClear.List.grid_id = '<?=$arParams['LIST_ID']?>';
</script>

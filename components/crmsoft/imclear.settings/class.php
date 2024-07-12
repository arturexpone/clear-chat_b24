<?php

use Bitrix\Main\Config\Option;

class CCrmsoftImClearSettings extends \CBitrixComponent
{
    protected $obGridOptions;
    protected $arSort;
    protected $obNav;
    protected $modules = ['crmsoft.imclear', 'im'];

    public function onPrepareComponentParams($arParams)
    {
        $this->includeModules();

        if(empty($arParams['ID'])) {
            $this->set404();
        }

        $arParams['LIST_ID'] = 'iic_chat_detail_'.$arParams['ID'];

        $arParams['PATH_TO_USER'] = Option::get('intranet', 'search_user_url', SITE_DIR.'company/personal/user/#ID#/');
        return $arParams;
    }

    public function executeComponent()
    {
        $this->includeModules();
        if(!\CRMsoft\ImClear\PermissionsManager::getInstance()->checkPerms()) {
            ShowError('Access denied');
            return;
        }

        $this->processGridActions();
        $this->prepareMeta();
        $this->prepareResult();

        $this->includeComponentTemplate();
    }

    protected function includeModules()
    {
        foreach ($this->modules as $moduleId) {
            if(!\Bitrix\Main\Loader::includeModule($moduleId)) {
                ShowError("Module $moduleId not included");
                exit();
            }
        }
    }

    protected function prepareMeta()
    {
        $dbChat = \Bitrix\Im\Model\ChatTable::query()
            ->setSelect(['ID', 'TYPE', 'TITLE'])
            ->where('ID',$this->arParams['ID'])
            ->exec();
        if($arChat = $dbChat->fetch()) {
            global $APPLICATION;
            $APPLICATION->SetTitle($arChat['TITLE']);
        } else {
            $this->set404();
        }
    }

    protected function prepareResult()
    {
        $this->obGridOptions = new \Bitrix\Main\Grid\Options($this->arParams['LIST_ID']);
        $this->obGridOptions->resetExpandedRows();
        $this->arSort = $this->obGridOptions->getSorting(['sort' => ['DATE_CREATE' => 'DESC'], 'vars' => ['by' => 'by', 'order' => 'order']]);

        $this->obNav = new \Bitrix\Main\UI\PageNavigation($this->arParams['LIST_ID']);
        $this->obNav
            //->allowAllRecords(true)
            ->setPageSize($this->obGridOptions->GetNavParams()['nPageSize'])
            ->initFromUri();

        $obFilterOption = new \Bitrix\Main\UI\Filter\Options($this->arParams['LIST_ID']);
        $arFilterData = $obFilterOption->getFilter([]);

        $this->arResult['COLUMNS'] = [
            ['id' => 'ID', 'name' => 'Ид', 'type' => 'int', 'default' => true, 'sort' => 'ID'],
            ['id' => 'AUTHOR_NAME', 'name' => 'Автор сообщения', 'type' => 'text', 'default' => true, 'sort' => 'AUTHOR_NAME'],
            ['id' => 'MESSAGE', 'name' => 'Сообщение', 'type' => 'text', 'default' => true, 'sort' => 'DESCRIPTION'],
            ['id' => 'DATE_CREATE', 'name' => 'Дата создания', 'type' => 'date', 'default' => true, 'sort' => 'DATE_CREATE']
        ];

        $this->arResult['FILTER_FIELDS'] = [
            ['id' => 'ID', 'name' => 'ID', 'type' => 'int', 'default' => true],
            ['id' => 'DATE_CREATE', 'name' => 'Дата создания', 'type' => 'date', 'default' => true],
            ['id' => 'MESSAGE', 'name' => 'Текст сообщения', 'type' => 'text', 'default' => true]
        ];

        $dbItems = \Bitrix\Im\Model\MessageTable::query()
            ->setSelect([
                'ID',
                'AUTHOR_ID',
                'AUTHOR_NAME' => 'AUTHOR.NAME',
                'AUTHOR_LAST_NAME' => 'AUTHOR.LAST_NAME',
                'AUTHOR_SECOND_NAME' => 'AUTHOR.SECOND_NAME',
                'DATE_CREATE',
                'MESSAGE',
                'MESSAGE_OUT',
				//'MESSAGE_UUID' => 'UUID.UUID',
            ])
            ->setFilter($this->makeFilter($arFilterData))
            ->countTotal(true)
            ->setOrder($this->arSort['sort'])
            ->setOffset($this->obNav->getOffset())
            ->setLimit($this->obNav->getLimit())
            ->exec();

        while($item = $dbItems->fetch()) {
            $message = \Bitrix\Im\Text::parse($item['MESSAGE']);

            if(strpos($message, '------------------------------------------------------') !== false) {
                $message = preg_replace_callback(
                    '/------------------------------------------------------<br \/>(.*?)\[(.*?)\]<br \/>(.*?)------------------------------------------------------(<br \/>)?/',
                    static function($matches) {
                        return "<div class=\"bx-messenger-content-quote\">
                                    <div class=\"bx-messenger-content-quote-wrap\">
                                        <div class=\"bx-messenger-content-quote-name\">
                                            <span class=\"bx-messenger-content-quote-name-text\">".$matches[1]."</span>
                                            <span class=\"bx-messenger-content-quote-name-time\">".$matches[2]."</span>
                                        </div>"
                                        .$matches[3]
                                    ."</div>
                                </div><br />";
                    },
                    $message
                );
                $message = preg_replace_callback(
                    '/------------------------------------------------------<br \/>(.*?)------------------------------------------------------(<br \/>)?/',
                    static function($matches) {
                        return "<div class=\"bx-messenger-content-quote\"><div class=\"bx-messenger-content-quote-wrap\">".$matches[1]."</div></div><br />";
                    },
                    $message
                );
            }

            $params = \CIMMessageParam::Get([$item['ID']]);
            $fileIds = Array();
            foreach ($params as $messageId => $param) {
                if (isset($param['FILE_ID'])) {
                    foreach ($param['FILE_ID'] as $fileId) {
                        $fileIds[$fileId] = $fileId;
                    }
                }
            }

            $files = \CIMDisk::GetFiles($this->arParams['ID'], $fileIds, false);

            foreach ($files as $file) {
                $strFile = '<a href="'.$file['urlShow'].'" ';
                foreach ($file['viewerAttrs'] as $attrCode=>$attrValue) {
                    $convertCode = mb_strtolower(preg_replace('/([A-Z])/','-$1',ucfirst($attrCode)));
                    $strFile .= 'data'.$convertCode.'=\''.$attrValue.'\' ';
                }
                $strFile .= '>';
                if($file['type'] === 'image') {
                    $strFile .= '<img src="'.$file['urlPreview'].'" alt="'.$file['name'].'" />';

                } else {
                    $strFile .= $file['name'];
                }
                $strFile .= '</a>';
                $message .= $strFile;
            }

            if((int)$item['AUTHOR_ID'] > 0 ) {
                $authorName = '<a href="'.str_replace(['#ID#', '#USER_ID#'], $item['AUTHOR_ID'], $this->arParams['PATH_TO_USER']).'">'
                    .implode(' ', [$item['AUTHOR_LAST_NAME'], $item['AUTHOR_NAME'], $item['AUTHOR_SECOND_NAME']])
                    .'</a>';
                // изначально был ExpressionField с использованием CONCAT по референсу AUTHOR.NAME/LAST_NAME/SECOND_NAME, но он почему-то возвращает пустую строку для админа, при этом для прочих пользователей ок
            } else {
                $authorName = 'Системный пользователь';
            }

            $resultItem = [
                'data' => $item,
                'columns' => [
                    'ID' => $item['ID'],
                    'MESSAGE' => $message,
                    'DATE_CREATE' => $item['DATE_CREATE']->format('d.m.Y H:i:s'),
                    'AUTHOR_NAME' => $authorName
                ],
                'actions' => [
                    [
                        'ICONCLASS' => 'delete',
                        'TEXT' => 'Удалить сообщение',
                        'ONCLICK' => 'BX.CRMsoft.ImClear.Detail.deleteMessage('.$item['ID'].', "'.$item['MESSAGE_OUT'].'");'
                    ]
                ],
            ];

            $this->arResult['ITEMS'][] = $resultItem;
        }

        $this->obNav->setRecordCount($dbItems->getCount());
        $this->arResult['TOTAL_ROWS_COUNT'] = $dbItems->getCount();
        $this->arResult['NAV_OBJECT'] = $this->obNav;
    }

    protected function makeFilter($arPostFilterData)
    {
        $arFilter = [
            '=CHAT_ID' => $this->arParams['ID']
        ];

        if (!empty($arPostFilterData['MESSAGE']) || !empty($arPostFilterData['FIND'])) {
            if (!empty($arPostFilterData['MESSAGE']) && !empty($arPostFilterData['FIND'])) {
                $arSubFilter = [
                    [
                        'LOGIC' => 'OR',
                        ['%MESSAGE' => $arPostFilterData['MESSAGE']],
                        ['%MESSAGE' => $arPostFilterData['FIND']]
                    ]
                ];
                $arFilter[] = $arSubFilter;
            } else {
                if (!empty($arPostFilterData['FIND'])) {
                    $arFilter[] = [
                        'LOGIC' => 'OR',
                        '%MESSAGE' => $arPostFilterData['FIND']
                    ];
                } else {
                    $arFilter['%MESSAGE'] = $arPostFilterData['MESSAGE'];
                }
            }
        }

        if (isset($arPostFilterData['DATE_CREATE_from']) && $arPostFilterData['DATE_CREATE_from']) {
            $arFilter['>=DATE_CREATE'] = $arPostFilterData['DATE_CREATE_from'];
        }
        if (isset($arPostFilterData['DATE_CREATE_to']) && $arPostFilterData['DATE_CREATE_to']) {
            $arFilter['<=DATE_CREATE'] = $arPostFilterData['DATE_CREATE_to'];
        }

        return $arFilter;
    }

    protected function set404()
    {
        define("ERROR_404", "Y");

        \CHTTP::setStatus("404 Not Found");

        global $APPLICATION;
        if ($APPLICATION->RestartWorkarea()) {
            require(\Bitrix\Main\Application::getDocumentRoot()."/404.php");
            die();
        }
    }

    protected function processGridActions(): void
    {
        $request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
        if (
            $request->getRequestMethod() !== 'POST'
            || !check_bitrix_sessid()
        )
        {
            return;
        }
        $removeActionButtonParamName = 'action_button_' . $this->arParams['LIST_ID'];
        if ($request->getPost($removeActionButtonParamName) === 'delete')
        {
            $ids = $request->getPost('ID');
            if (!is_array($ids))
            {
                return;
            }
            \Bitrix\Main\Type\Collection::normalizeArrayValuesByInt($ids);
            if (empty($ids))
            {
                return;
            }

            $dbItems = \Bitrix\Im\Model\MessageTable::query()
                ->whereIn('ID',$ids)
                ->where('CHAT_ID',$this->arParams['ID'])
                ->setSelect(['ID'])
                ->exec();
            while($arItem = $dbItems->fetch()) {
                \CIMMessenger::DisableMessageCheck();
                $result = \CIMMessenger::Delete($arItem['ID'], null, true);
            }
        }
    }
}
<?php

class CCrmsoftImClearChatList extends \CBitrixComponent
{
    protected $obGridOptions;
    protected $arSort;
    protected $obNav;
    protected $modules = ['crmsoft.imclear','im'];
    protected $chatTypes = [
        \Bitrix\Im\Chat::TYPE_GROUP => 'Чат группы',
        \Bitrix\Im\Chat::TYPE_OPEN => 'Открытый чат',
		//\Bitrix\Im\Chat::TYPE_OPEN_LINE => 'Чат открытой линии',
        \Bitrix\Im\Chat::TYPE_PRIVATE => 'Приватный чат'
        //\Bitrix\Im\Chat::TYPE_THREAD => ''
    ];

    public function onPrepareComponentParams($arParams)
    {
        $this->includeModules();

        $arParams['LIST_ID'] = 'iic_chat_list';
        return $arParams;
    }

    public function executeComponent()
    {
        $this->includeModules();
        if(!\CRMsoft\ImClear\PermissionsManager::getInstance()->checkPerms()) {
            ShowError('Access denied');
            return;
        }

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

    protected function getAgentInfo(array $agent)
    {
        $isPeriod = $agent['IS_PERIOD'] === 'Y' ? 'Да' : 'Нет';

        preg_match_all('/\((.+)\)/', $agent['NAME'], $matches);

        $agentParams = explode(',', $matches[1][0]);

        if (count($agentParams) > 1)
        {
            $periodValue = preg_replace('/[^0-9]+/', '', $agentParams[1]);
            $periodType = $agentParams[2];
            $moreOrLess = $agentParams[3];

            $moreOrLess = match($moreOrLess){
                'M' => 'Больше',
                'L' => 'Меньше',
                default => 'Больше'
            };

            if ($periodValue && $periodType && $moreOrLess)
            {

                $hint = "Удаление сообщений ";

                if ($moreOrLess === 'L')
                {
                    $hint .= "ранее \n";
                } else
                {
                    $hint .= "старше \n";
                }

                $hint .= ($periodValue . " ");

                if (str_contains($periodType, 'M'))
                {
                    $hint .= 'месяцев';

                    $periodType = 'Месяцы';
                }

                if (str_contains($periodType, 'D'))
                {
                    $hint .= 'дней';

                    $periodType = 'Дни';
                }

                if (str_contains($periodType, 'Y'))
                {
                    $hint .= 'лет / годов';

                    $periodType = 'Год(ы)';
                }

                //$hint .= "\nс даты запуска очистки";

                return
                '<div class="ui-form ui-form-section">
                    <div class="ui-form-content">
                            <div class="ui-form-row-group">
                                <div class="ui-form-row">
                                        <div class="ui-form-label">
                                            <div class="ui-ctl-label-text">
                                                <b>Активные настройки очистки чата</b>:
                                                <span data-hint="' . $hint .'" data-hint-init="y" class="ui-hint"><span class="ui-hint-icon"></span></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="ui-form-row-group">
                                        <div class="ui-form-row">
                                            <div class="ui-form-label">
                                                <div class="ui-ctl-label-text">
                                                    <b>Период (значение)</b>:
                                                </div>
                                            </div>
                                            <div class="ui-ctl ui-ctl-after-icon ui-ctl-dropdown ui-ctl-w100">
                                                <input type="text" value="' . $periodValue .'" class="ui-ctl-element" placeholder="">
                                            </div>
                                        </div>
                                        <div class="ui-form-row">
                                                <div class="ui-form-label">
                                                    <div class="ui-ctl-label-text">
                                                        <b>Период (тип)</b>:</div>
                                                </div>
                                                <div class="ui-ctl ui-ctl-after-icon ui-ctl-dropdown ui-ctl-w100">
                                                    <input type="text" value="' . $periodType .'" class="ui-ctl-element" placeholder="">
                                                </div>
                                            </div>
                                        <div class="ui-form-row">
                                                <div class="ui-form-label">
                                                    <div class="ui-ctl-label-text">
                                                        <b>Больше / меньше</b>:
                                                     </div>
                                                </div>
                                                <div class="ui-ctl ui-ctl-after-icon ui-ctl-dropdown ui-ctl-w100">
                                                    <input type="text" value="' . $moreOrLess .'" class="ui-ctl-element" placeholder="">
                                                </div>
                                            </div>
                                        <div class="ui-form-row">
                                                <div class="ui-form-label">
                                                    <div class="ui-ctl-label-text">
                                                        <b>Следующий запуск</b>:
                                                    </div>
                                                </div>
                                                <div class="ui-ctl ui-ctl-after-icon ui-ctl-dropdown ui-ctl-w100">
                                                    <div class="ui-ctl ui-ctl-textbox ui-ctl-w100">
                                                        <input type="text" value="' . $agent['NEXT_EXEC'] .'" class="ui-ctl-element" placeholder="">
                                                    </div>
                                                </div>
                                        </div>
                                        <div class="ui-form-row">
                                                <div class="ui-form-label">
                                                    <div class="ui-ctl-label-text">
                                                        <b>Регулярная очистка</b>:
                                                    </div>
                                                </div>
                                                <div class="ui-ctl ui-ctl-after-icon ui-ctl-dropdown ui-ctl-w100">
                                                    <div class="ui-ctl ui-ctl-textbox ui-ctl-w100">
                                                        <input type="text" value="Да" class="ui-ctl-element" placeholder="">
                                                    </div>
                                                </div>
                                        </div>
                                        <div class="ui-form-row">
                                                <div class="ui-form-label">
                                                    <div class="ui-ctl-label-text">
                                                        <b>Общая информация</b>:
                                                    </div>
                                                </div>
                                                <div class="ui-ctl ui-ctl-after-icon ui-ctl-dropdown ui-ctl-w100">
                                                    <div class="ui-ctl ui-ctl-textbox ui-ctl-w100">
                                                        <textarea wrap="hard" class="ui-ctl-element" rows="5" cols="33">'.$hint.'</textarea>
                                                    </div>
                                                </div>
                                        </div>
                                    </div>
                            </div>
                        </div>
                </div>';
//                return "Агент с моими параметрами" . print_r([
//                        'Период (значение)' => $periodValue,
//                        'Период (тип)' => $periodType,
//                        'Больше / меньше' => $moreOrLess,
//                        'Следующий запуск' => '11.02.2024'
//                    ], true);
            }

        } else
        {
            return
                '<div class="ui-form ui-form-section">
                    <div class="ui-form-content">
                            <div class="ui-form-row-group">
                                <div class="ui-form-row">
                                        <div class="ui-form-label">
                                            <div class="ui-ctl-label-text">
                                                <b>Активные настройки очистки чата</b>:
                                                <span data-hint="Моя первая подсказка" data-hint-init="y" class="ui-hint"><span class="ui-hint-icon"></span></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="ui-form-row-group">
                                        <div class="ui-form-row">
                                                <div class="ui-form-label">
                                                    <div class="ui-ctl-label-text">
                                                        <b>Следующий запуск</b>:
                                                    </div>
                                                </div>
                                                <div class="ui-ctl ui-ctl-after-icon ui-ctl-dropdown ui-ctl-w100">
                                                    <div class="ui-ctl ui-ctl-textbox ui-ctl-w100">
                                                        <input type="text" value="' . $agent['NEXT_EXEC'] .'" class="ui-ctl-element" placeholder="">
                                                    </div>
                                                </div>
                                            </div>
                                            
                                        <div class="ui-form-row">
                                                <div class="ui-form-label">
                                                    <div class="ui-ctl-label-text">
                                                        <b>Регулярная очистка</b>:
                                                    </div>
                                                </div>
                                                <div class="ui-ctl ui-ctl-after-icon ui-ctl-dropdown ui-ctl-w100">
                                                    <div class="ui-ctl ui-ctl-textbox ui-ctl-w100">
                                                        <input type="text" value="Да" class="ui-ctl-element" placeholder="">
                                                    </div>
                                                </div>
                                        </div>
                                    </div>
                            </div>
                        </div>
                </div>';
        }
    }

    protected function prepareResult()
    {
        $this->obGridOptions = new \Bitrix\Main\Grid\Options($this->arParams['LIST_ID']);
        $this->obGridOptions->resetExpandedRows();
        $this->arSort = $this->obGridOptions->getSorting(['sort' => ['ID' => 'DESC'], 'vars' => ['by' => 'by', 'order' => 'order']]);

        $this->obNav = new \Bitrix\Main\UI\PageNavigation($this->arParams['LIST_ID']);
        $this->obNav
            //->allowAllRecords(true)
            ->setPageSize($this->obGridOptions->GetNavParams()['nPageSize'])
            ->initFromUri();

        $obFilterOption = new \Bitrix\Main\UI\Filter\Options($this->arParams['LIST_ID']);
        $arFilterData = $obFilterOption->getFilter([]);

        $this->arResult['COLUMNS'] = [
            ['id' => 'ID', 'name' => 'ID', 'type' => 'int', 'default' => true, 'sort' => 'ID'],
            ['id' => 'TITLE', 'name' => 'Название', 'type' => 'text', 'default' => true, 'sort' => 'TITLE'],
            ['id' => 'TYPE', 'name' => 'Тип', 'type' => 'text', 'default' => true, 'sort' => 'TYPE'],
            ['id' => 'DESCRIPTION', 'name' => 'Описание', 'type' => 'text', 'default' => false, 'sort' => 'DESCRIPTION'],
            /*['id' => 'ENTITY_TYPE', 'name' => 'Тип связанной сущности', 'type' => 'text', 'default' => false, 'sort' => 'ENTITY_TYPE'],
            ['id' => 'ENTITY_ID', 'name' => 'Связанная сущность', 'type' => 'text', 'default' => false, 'sort' => 'ENTITY_ID'],*/
            ['id' => 'AUTHOR_NAME', 'name' => 'Владелец чата', 'type' => 'text', 'default' => true, 'sort' => 'AUTHOR_NAME'],
            ['id' => 'MESSAGE_COUNT', 'name' => 'Кол-во сообщений', 'type' => 'text', 'default' => true, 'sort' => 'MESSAGE_COUNT'],
            ['id' => 'USER_COUNT', 'name' => 'Кол-во пользователей', 'type' => 'text', 'default' => true, 'sort' => 'USER_COUNT'],
            ['id' => 'DATE_CREATE', 'name' => 'Дата создания', 'type' => 'date', 'default' => true, 'sort' => 'DATE_CREATE'],
            ['id' => 'TIMER_SET', 'name' => 'Очистка по таймеру', 'type' => 'date'],
            ['id' => 'INFORMATION', 'name' => 'Информация', 'type' => 'html'],
        ];

        $this->arResult['FILTER_FIELDS'] = [
            ['id' => 'ID', 'name' => 'ID', 'type' => 'int', 'default' => true],
            ['id' => 'TYPE', 'name' => 'Тип', 'type' => 'list', 'items' => $this->chatTypes, 'default' => true, 'sort' => 'TYPE'],
            ['id' => 'TITLE','name'=> 'Название', 'type' => 'text', 'default' => true],
            [
                'id' => 'AUTHOR_ID',
                'name' => 'Владелец',
                'type' => 'dest_selector',
                'params' => [
                    'apiVersion' => '3',
                    'context' => 'IMC_CHAT_LIST_FILTER_AUTHOR_ID',
                    'multiple' => 'Y',
                    'contextCode' => 'U',
                    'enableAll' => 'N',
                    'enableEmpty' => 'Y',
                    'enableSonetgroups' => 'N',
                    'allowEmailInvitation' => 'Y',
                    'departmentSelectDisable' => 'Y',
                    'isNumeric' => 'Y',
                    'prefix' => 'U',
                    'allowBots' => 'Y'
                ],
                'default' => true
            ],
            ['id' => 'DATE_CREATE', 'name' => 'Дата создания', 'type' => 'date', 'default' => true],
        ];

        $dbItems = \Bitrix\Im\Model\ChatTable::query()
            ->setSelect([
                'ID',
                'TYPE',
                'TITLE',
                'DESCRIPTION',
                'ENTITY_TYPE',
                'ENTITY_ID',
                'AUTHOR_ID',
                'AUTHOR_NAME' => 'AUTHOR.NAME',
                'AUTHOR_LAST_NAME' => 'AUTHOR.LAST_NAME',
                'AUTHOR_SECOND_NAME' => 'AUTHOR.SECOND_NAME',
                'MESSAGE_COUNT',
                'USER_COUNT',
                'DATE_CREATE'
            ])
            //->registerRuntimeField(new \Bitrix\Main\ORM\Fields\ExpressionField('AUTHOR_NAME','CONCAT(%s, \' \', %s, \' \', %s)', ['AUTHOR.LAST_NAME','AUTHOR.NAME','AUTHOR.SECOND_NAME']))
            ->setFilter($this->makeFilter($arFilterData))
            ->countTotal(true)
            ->setOrder($this->arSort['sort'])
            ->setOffset($this->obNav->getOffset())
            ->setLimit($this->obNav->getLimit())
            ->exec();

        $dbAgent = \CAgent::GetList(
            [],
            ['MODULE_ID' => 'crmsoft.imclear']
        );

        $allAgents = [];
        while($arAgent = $dbAgent->Fetch()) {
            $allAgents[] = $arAgent;
        }

        while($item = $dbItems->fetch()) {
            $title = $item['TITLE'];
            if(empty($title) && $item['TYPE'] === \Bitrix\Im\Chat::TYPE_PRIVATE) {
                $title = 'Персональный чат';

                $dbRelation = \Bitrix\Im\Model\RelationTable::query()
                    ->where('CHAT_ID', $item['ID'])
                    ->whereNot('USER_ID', $item['AUTHOR_ID'])
                    ->setSelect(['ID','USER_ID','USER_NAME' => 'USER.NAME', 'USER_LASTNAME' => 'USER.LAST_NAME', 'USER_SECONDNAME' => 'USER.SECOND_NAME'])
                    ->exec();
                $arUsers = [];
                while ($arRelation = $dbRelation->fetch()) {
                    $arUsers[] = trim($arRelation['USER_LASTNAME'].' '.$arRelation['USER_NAME'].' '.$arRelation['USER_SECONDNAME']);
                }
                if(!empty($arUsers)) {
                    $title .= ' с '.implode(', ', $arUsers);
                }
            }
            $urlDetail = $this->arParams['SEF_FOLDER'].str_replace("#CHAT_ID#",$item['ID'],$this->arParams['URL_TEMPLATES']['detail']);
            if((int)$item['AUTHOR_ID'] > 0 ) {
                $authorName = '<a href="'.str_replace(['#ID#', '#USER_ID#'], $item['AUTHOR_ID'], $this->arParams['PATH_TO_USER']).'">'
                    .trim(implode(' ', [$item['AUTHOR_LAST_NAME'], $item['AUTHOR_NAME'], $item['AUTHOR_SECOND_NAME']]))
                    .'</a>';
                // изначально был ExpressionField с использованием CONCAT по референсу AUTHOR.NAME/LAST_NAME/SECOND_NAME, но он почему-то возвращает пустую строку для админа, при этом для прочих пользователей ок
            } else {
                $authorName = 'Системный пользователь';
            }

            $actions = [];
            if((int)$item['MESSAGE_COUNT'] > 0) {
                $actions[] = [
                    //'ICONCLASS' => 'delete',
                    'TEXT' => 'Очистить чат',
                    'ONCLICK' => 'BX.CRMsoft.ImClear.List.clearChat('.$item['ID'].', "'.htmlspecialcharsbx($title).'");'
                ];
            }
//            $dbAgent = \CAgent::GetList([], ['NAME' => '\CRMsoft\ImClear\Agent::clean(\'' . $item['ID'] . '\');', 'MODULE_ID' => 'crmsoft.imclear']);


            $currentAgent = array_filter($allAgents, function ($agent) use ($item) {
                $chatId = $item['ID'];
                return str_contains($agent['NAME'], "\CRMsoft\ImClear\Agent::clean('{$chatId}'");
            });

            $resultAgent = reset($currentAgent);

            $agentInfo = '<b>Удаление не настроено</b>';

            $timerValue = '';
            if($resultAgent) {
                $timerValue = $resultAgent['NEXT_EXEC'];
                $actions[] = [
                    'TEXT' => 'Изменить таймер',
                    'ONCLICK' => 'BX.CRMsoft.ImClear.List.changeTimer('.$resultAgent['ID'].','.$item['ID'].');'
                ];
                $actions[] = [
                    'TEXT' => 'Удалить таймер',
                    'ONCLICK' => 'BX.CRMsoft.ImClear.List.clearTimer('.$resultAgent['ID'].');'
                ];

                $agentInfo = $this->getAgentInfo($resultAgent);
            } else {
                $actions[] = [
                    'TEXT' => 'Установить таймер',
                    'ONCLICK' => 'BX.CRMsoft.ImClear.List.setTimer('.$item['ID'].');'
                ];
            }

            $resultItem = [
                'data' => $item,
                'columns' => [
                    'ID' => $item['ID'],
                    'TYPE' => $this->chatTypes[$item['TYPE']],
                    'TITLE' => "<a href=\"$urlDetail\">$title</a>",
                    'DESCRIPTION' => $item['DESCRIPTION'],
                    'DATE_CREATE' => $item['DATE_CREATE']->format('d.m.Y H:i:s'),
                    'AUTHOR_NAME' => $authorName,
                    'MESSAGE_COUNT' => $item['MESSAGE_COUNT'],
                    'USER_COUNT' => $item['USER_COUNT'],
                    'TIMER_SET' => $timerValue,
                    'INFORMATION' => $agentInfo
                ],
                'actions' => $actions,
            ];

            $this->arResult['ITEMS'][] = $resultItem;
        }

        $this->obNav->setRecordCount($dbItems->getCount());
        $this->arResult['TOTAL_ROWS_COUNT'] = $dbItems->getCount();
        $this->arResult['NAV_OBJECT'] = $this->obNav;
    }

    protected function makeFilter($arPostFilterData)
    {
        $arFilter = [];

        if (!empty($arPostFilterData['TITLE']) || !empty($arPostFilterData['FIND'])) {
            if (!empty($arPostFilterData['TITLE']) && !empty($arPostFilterData['FIND'])) {
                $arSubFilter = [
                    [
                        'LOGIC' => 'OR',
                        ['%TITLE' => $arPostFilterData['TITLE']],
                        ['%TITLE' => $arPostFilterData['FIND']]
                    ]
                ];
                $arFilter[] = $arSubFilter;
            } else {
                if (!empty($arPostFilterData['FIND'])) {
                    $arFilter[] = [
                        'LOGIC' => 'OR',
                        ['%TITLE' => $arPostFilterData['FIND']],
                        ['%AUTHOR_NAME' => $arPostFilterData['FIND']],
                        ['%AUTHOR_LAST_NAME' => $arPostFilterData['FIND']],
                        ['%AUTHOR_SECOND_NAME' => $arPostFilterData['FIND']],
                    ];
                } else {
                    $arFilter['%TITLE'] = $arPostFilterData['TITLE'];
                }
            }
        }

        if (isset($arPostFilterData['DATE_CREATE_from']) && $arPostFilterData['DATE_CREATE_from']) {
            $arFilter['>=CREATE_TIME'] = $arPostFilterData['DATE_CREATE_from'];
        }
        if (isset($arPostFilterData['DATE_CREATE_to']) && $arPostFilterData['DATE_CREATE_to']) {
            $arFilter['<=CREATE_TIME'] = $arPostFilterData['DATE_CREATE_to'];
        }

        foreach ($arPostFilterData as $key => $value) {
            if(preg_match('/[=%><!]+ID/', $key, $m)) {
                $arFilter[$key] = $value;
            }
        }

        if(empty($arPostFilterData['TYPE'])) {
			$arFilter[] = [
				'LOGIC' => 'OR',
				[
					'=TYPE' => array_keys($this->chatTypes),
					'=ENTITY_TYPE' => ['PERSONAL','TASKS','SONET_GROUP']
				],
				[
					'=TYPE' => [\Bitrix\Im\Chat::TYPE_PRIVATE, \Bitrix\Im\Chat::TYPE_OPEN],
					'ENTITY_TYPE' => false
				]
			];
			//$arFilter['=TYPE'] = array_keys($this->chatTypes);
        } else {
            $arFilter['=TYPE'] = $arPostFilterData['TYPE'];
			if($arPostFilterData['TYPE'] === 'C') {
				$arFilter['!ENTITY_TYPE'] = 'LIVECHAT';
			}
        }
		//$arFilter['=ENTITY_TYPE'] = ['PERSONAL','TASKS','SONET_GROUP',false];

        if(!empty($arPostFilterData['AUTHOR_ID'])) {
            $arFilter['AUTHOR_ID'] = $arPostFilterData['AUTHOR_ID'];
        }

        return $arFilter;
    }
}
<?php

namespace CRMsoft\ImClear;

use Bitrix\Main\Error;
use Bitrix\Main\Loader;

class Agent
{

    public static function getFileInMessage(int $messageId): ?array
    {
        Loader::includeModule('im');

        return \Bitrix\Im\Model\MessageParamTable::getList([
            'filter' => [
                'MESSAGE_ID' => $messageId,
                'PARAM_NAME' => 'FILE_ID'
            ]
        ])->fetchAll();
    }
    public static function clean(
        $chatId,
        $imcTimerIntervalType,
        $imcTimerIntervalValue,
        $imcTimerDeleteDaysValue
    )
    {
        try {
            if((int)$chatId > 0) {
                Loader::includeModule('im');
                Loader::includeModule('disk');

                \CIMMessenger::DisableMessageCheck();
                $logger = new Logger('agent.txt');

                $dbQuery = \Bitrix\Im\Model\MessageTable::query();

                if ($imcTimerDeleteDaysValue)
                {
                    $currentDate = new \DateTime();
                    $currentDate->sub(new \DateInterval("P{$imcTimerDeleteDaysValue}D"));

                    $dbQuery->where(
                        'DATE_CREATE',
                        '>=',
                        new \Bitrix\Main\Type\DateTime($currentDate->format('d.m.Y 23:59:59'))
                    );
                }

                $dbQuery->where('CHAT_ID',$chatId)
                    ->setSelect(['ID','MESSAGE','MESSAGE_OUT', 'DATE_CREATE']);

                print_r($dbQuery->getQuery());

                $dbItems = $dbQuery->exec();
                $messages = [];
                while($item = $dbItems->fetch()) {

                    $messages[] = [
                        'message' => $item['MESSAGE'],
                        'message_out' => $item['MESSAGE_OUT']
                    ];

                    $params = \CIMMessageParam::Get([$item['ID']]);
                    if($params[$item['ID']]['IS_DELETED'] === 'Y') {
                        continue;
                    }
                    $result = \CIMMessenger::Delete($item['ID'], 1, true);
                    if(!$result) {
                        $logger->error('Error delete message '.$item['ID'].' from chat '.$chatId.': "'.($item['MESSAGE'] ?? $item['MESSAGE_OUT']));
                    } else
                    {
                        $files = self::getFileInMessage($item['ID']);
                        if ($files)
                        {
                            foreach ($files as $file)
                            {
                                $file = \Bitrix\Disk\File::getById($file['PARAM_VALUE']);
                                if (!$file) continue;
                                \CFile::Delete($file->getFileId());
                            }

                        }
                    }
                }

                print_r($messages);

//            $dbAgent = \CAgent::GetList([], ['NAME' => '\CRMsoft\ImClear\Agent::clean(\'' . $item['ID'] . '\');', 'MODULE_ID' => 'crmsoft.imclear']);

                $dbAgent = \CAgent::GetList([], ['MODULE_ID' => 'crmsoft.imclear']);

                $allAgents = [];
                if($arAgent = $dbAgent->Fetch()) {
                    $allAgents[] = $arAgent;
                }

                $allAgents = array_filter($allAgents, function ($agent) use ($item) {
                    $chatId = $item['ID'];
                    return str_contains($agent['NAME'], "\CRMsoft\ImClear\Agent::clean('{$chatId}'");
                });

                if($arAgent = reset($allAgents)) {
                    $logger->info('Чат '.$chatId.' успешно очищен');
                    //@\CAgent::Delete($arAgent['ID']);
                    return true;
                }
            }
        } catch (\Error | \Exception $exception)
        {
            print_r([
                'date' => date('d.m.Y H:i:s'),
                '$chatId' => $chatId,
                '$daysValue' => $imcTimerDeleteDaysValue,
                'error' => $exception->getMessage()
            ], true);
        } finally {
            return "\CRMsoft\ImClear\Agent::clean('{$chatId}', '{$imcTimerIntervalType}', '{$imcTimerIntervalValue}', '{$imcTimerDeleteDaysValue}');";
        }
    }
}
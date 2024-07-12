<?php

namespace CRMsoft\ImClear\Controller;

use Bitrix\Im\Model\ChatTable;
use Bitrix\Main\Engine\Controller;
use Bitrix\Main\Error;
use Bitrix\Main\Loader;

class Message extends Controller
{
    public function configureActions()
    {
        return [
            'delete' => []
        ];
    }

    public function deleteAction($id)
    {
        if(!\CRMsoft\ImClear\PermissionsManager::getInstance()->checkPerms()) {
            $this->addError(new Error('access denied'));
            return [];
        }

        Loader::includeModule('im');
        \CIMMessenger::DisableMessageCheck();
        $result = \CIMMessenger::Delete($id, $this->getCurrentUser()->getId(), true);
        if(!$result) {
            $this->addError(new Error('Delete error'));
        }

        return [];
    }

    public function clearChatAction($id)
    {
        if(!\CRMsoft\ImClear\PermissionsManager::getInstance()->checkPerms()) {
            $this->addError(new Error('access denied'));
            return [];
        }

        Loader::includeModule('im');

        $dbChat = ChatTable::query()->where('ID', $id)->setSelect(['ID'])->exec();
        if($dbChat->getSelectedRowsCount() === 0) {
            $this->addError(new Error('Chat not found'));
            return [];
        }

        \CIMMessenger::DisableMessageCheck();
        $dbItems = \Bitrix\Im\Model\MessageTable::query()
            ->where('CHAT_ID',$id)
            ->setSelect(['ID','MESSAGE','MESSAGE_OUT'])
            ->exec();
        while($item = $dbItems->fetch()) {
            $params = \CIMMessageParam::Get([$item['ID']]);
            if($params[$item['ID']]['IS_DELETED'] === 'Y') {
                continue;
            }
            $result = \CIMMessenger::Delete($item['ID'], $this->getCurrentUser()->getId(), true);
            if(!$result) {
                $this->addError(new Error('Error delete message '.$item['ID'].': "'.($item['MESSAGE'] ?? $item['MESSAGE_OUT'])));
            }
        }

        return [];
    }

    public function setTimerAction($chatId, $imcTimerIntervalType = '', $imcTimerIntervalValue = '', $imcTimerDeleteDaysValue = '')
    {
        if(!\CRMsoft\ImClear\PermissionsManager::getInstance()->checkPerms()) {
            $this->addError(new Error('access denied'));
            return [];
        }

        if ($imcTimerDeleteDaysValue)
        {
            $agent = "\CRMsoft\ImClear\Agent::clean('{$chatId}', '{$imcTimerIntervalType}', '{$imcTimerIntervalValue}', '{$imcTimerDeleteDaysValue}');";
        } else
        {
            $agent = '\CRMsoft\ImClear\Agent::clean(\''.$chatId.'\');';
        }

//        return [
//            '$chatId' => $chatId,
//            '$imcTimerType' => $imcTimerType,
//            '$imcTimerDateValue' => $imcTimerDateValue,
//            '$imcTimerIntervalValue' => $imcTimerIntervalValue,
//            '$imcTimerIntervalType' => $imcTimerIntervalType,
//            '$imcTimerPeriodValue' => $imcTimerPeriodValue,
//            '$imcTimerPeriodType' => $imcTimerPeriodType,
//            'imcTimerPeriodToggle' => $imcTimerPeriodToggle,
//            'agent' => $agent,
//            '$imcTimerIntervalTypeRepeat' => $imcTimerIntervalTypeRepeat,
//        ];

        if((int)$chatId > 0) {
            if((int)$imcTimerIntervalValue > 0) {
                $seconds = 0;
                switch ($imcTimerIntervalType) {
                    case 'minute':
                        $seconds = 60;
                        break;
                    case 'hour':
                        $seconds = 3600;
                        break;
                    case 'day':
                        $seconds = 86400;
                        break;
                    default:
                        $this->addError(new Error('Wrong interval type'));
                        return [];
                }
                $date = new \DateTime();
                $inervalValue = (int)$imcTimerIntervalValue*$seconds;
                $date->setTimestamp($date->getTimestamp() + $inervalValue);

                \CAgent::AddAgent(
                    $agent,
                    'crmsoft.imclear',
                    'N',
                    (int)$imcTimerIntervalValue*$seconds,
                    $date->format('d.m.Y H:i:s'),
                    'Y',
                    $date->format('d.m.Y H:i:s')
                );


            } else {
                $this->addError(new Error('Wrong type'));
            }
        } else {
            $this->addError(new Error('Wrong chatId'));
        }

        return [];
    }

    public function clearTimerAction($id)
    {
        if(!\CRMsoft\ImClear\PermissionsManager::getInstance()->checkPerms()) {
            $this->addError(new Error('access denied'));
            return [];
        }

        $dbAgent = \CAgent::GetList([], ['ID' => $id]);
        if($arAgent = $dbAgent->Fetch()) {
            @\CAgent::Delete($arAgent['ID']);
        } else {
            $this->addError(new Error('Clean agent not found'));
        }

        return [];
    }
    public function changeTimerAction(
        $agentId,
        $chatId,
        $imcTimerIntervalValue = '',
        $imcTimerIntervalType = '',
        $imcTimerDeleteDaysValue = '',
    )
    {
        if(!\CRMsoft\ImClear\PermissionsManager::getInstance()->checkPerms()) {
            $this->addError(new Error('access denied'));
            return [];
        }

        $dbAgent = \CAgent::GetList([], ['ID' => $agentId]);
        if($arAgent = $dbAgent->Fetch()) {
            @\CAgent::Delete($arAgent['ID']);
        } else {
            $this->addError(new Error('Clean agent not found'));
        }

        $agent = "\CRMsoft\ImClear\Agent::clean('{$chatId}', '{$imcTimerIntervalType}', '{$imcTimerIntervalValue}', '{$imcTimerDeleteDaysValue}');";

        $seconds = 0;
        switch ($imcTimerIntervalType) {
            case 'minute':
                $seconds = 60;
                break;
            case 'hour':
                $seconds = 3600;
                break;
            case 'day':
                $seconds = 86400;
                break;
            default:
                $this->addError(new Error('Wrong interval type'));
                return [];
        }
        $date = new \DateTime();
        $inervalValue = (int)$imcTimerIntervalValue*$seconds;
        $date->setTimestamp($date->getTimestamp() + $inervalValue);

//        return [
//            'result' => true,
//            'data' => [
//                '$agent' => $agent,
//                '$chatId' => $chatId,
//                '$imcTimerDeleteDaysValue' => $imcTimerDeleteDaysValue,
//                '$imcTimerIntervalValue' => $imcTimerIntervalValue,
//                '$imcTimerIntervalType' => $imcTimerIntervalType,
//                '$agentId' => $agentId,
//            ]
//        ];

        $res = \CAgent::AddAgent(
            $agent,
            'crmsoft.imclear',
            'N',
            (int)$imcTimerIntervalValue*$seconds,
            $date->format('d.m.Y H:i:s'),
            'Y',
            $date->format('d.m.Y H:i:s')
        );


        return [
            'agentId'       => $res,
            'agentMethod'   => $agent
        ];
    }
}
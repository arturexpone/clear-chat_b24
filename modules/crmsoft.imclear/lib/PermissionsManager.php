<?php

namespace CRMsoft\ImClear;

use Bitrix\Main\Application;
use Bitrix\Main\Config\Option;

class PermissionsManager
{
    private static $instance;
    private $arUserCodes = [];

    /**
     * @return self
     */
    public static function getInstance()
    {
        if (!isset(static::$instance)) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    public function checkPerms(int $userId = null) : bool
    {
        if(!$userId) {
            global $USER;
            $userId = $USER->GetID();
            if($userId > 0) {
                return !empty(array_intersect($this->arUserCodes, \CAccess::getUserCodesArray($userId)));
            }
        }
        return false;
    }

    private function __construct()
    {
        $opt = Option::get('crmsoft.imclear','iic_users_fullaccess','');
        if(!empty($opt)) {
            $codes = unserialize($opt,['allowed_classes' => true]);
            if(!empty($codes) && is_array($codes)) {
                $this->arUserCodes = $codes;
            }
        }
    }

    private function __clone()
    {
    }

    private function __wakeup()
    {
    }
}
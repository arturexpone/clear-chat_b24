<?php

namespace CRMsoft\ImClear\ORM;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\IntegerField;

/**
 * Class ClearSettingsTable
 *
 * Fields:
 * <ul>
 * <li> ID int mandatory
 * <li> ALL_CHATS int optional
 * <li> INTERVAL_DAYS int mandatory
 * </ul>
 *
 * @package Bitrix\Im
 **/

class ClearSettingsTable extends DataManager
{
    /**
     * Returns DB table name for entity.
     *
     * @return string
     */
    public static function getTableName()
    {
        return 'ilaita_im_clear_settings';
    }

    /**
     * Returns entity map definition.
     *
     * @return array
     */
    public static function getMap()
    {
        return [
            'ID' => (new IntegerField('ID',
                []
            ))->configureTitle(Loc::getMessage('CLEAR_SETTINGS_ENTITY_ID_FIELD'))
                ->configurePrimary(true)
                ->configureAutocomplete(true)
            ,
            'ALL_CHATS' => (new IntegerField('ALL_CHATS',
                []
            ))->configureTitle(Loc::getMessage('CLEAR_SETTINGS_ENTITY_ALL_CHATS_FIELD'))
            ,
            'INTERVAL_DAYS' => (new IntegerField('INTERVAL_DAYS',
                []
            ))->configureTitle(Loc::getMessage('CLEAR_SETTINGS_ENTITY_INTERVAL_DAYS_FIELD'))
                ->configureRequired(true)
            ,
        ];
    }
}
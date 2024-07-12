<?php

namespace CRMsoft\ImClear\Controller;

use Bitrix\Main\Engine\Controller;
use Bitrix\Main\Loader;

Loader::includeModule('calendar');

class Test extends \Bitrix\Calendar\Controller\CalendarAjax
{
    public function configureActions()
    {
        return [];
    }

    public function getViewEventSliderAction()
    {
        return parent::getViewEventSliderAction();
    }
}
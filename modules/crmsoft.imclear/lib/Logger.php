<?php

namespace CRMsoft\ImClear;

use Psr\Log\LogLevel;

class Logger extends \Bitrix\Main\Diag\FileLogger
{
    public function __construct(string $fileName, int $maxSize = null)
    {
        $prefix = $_SERVER['DOCUMENT_ROOT'].'/local/modules/crmsoft.imclear/logs/';
        if(strpos($fileName, $prefix) === false) {
            $fileName = $prefix.$fileName;
        }
        parent::__construct($fileName, $maxSize);
    }

    protected function logMessage(string $level, string $message)
    {
        $message = date(DATE_COOKIE).'['.$level.']: '.$message.PHP_EOL;
        parent::logMessage($level, $message);
        if(static::$supportedLevels[$level] < LOG_WARNING) {
            \CEventLog::Add(array(
                "SEVERITY" => "ERROR",
                "AUDIT_TYPE_ID" => "CRMSOFT_IMCLEAR_ERROR",
                "MODULE_ID" => "cmsoft.imclear",
                "DESCRIPTION" => $message.PHP_EOL.'log: '.$this->fileName,
            ));
        }
    }
}
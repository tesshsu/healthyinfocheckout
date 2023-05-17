<?php
/**
 * @author    TessHsu
 */
class Logger
{
    const DEFAULT_LOG_FILE = 'dev-healthyInfo.log';

    public static function log($message, $level = 'debug', $fileName = null)
    {
        $fileDir = _PS_MODULE_DIR_ . 'healthyinfocheckout/var/logs/';

        if (!is_dir($fileDir)) {
            // Create the directory recursively
            mkdir($fileDir, 0777, true);
        }

        if (!$fileName) {
            $fileName = self::DEFAULT_LOG_FILE;
        }

        if (is_array($message) || is_object($message)) {
            $message = print_r($message, true);
        }

        $formatted_message = '*' . $level . '* ' . " -- " . date('Y/m/d - H:i:s') . ': ' . $message . "\r\n";

        return file_put_contents($fileDir . $fileName, $formatted_message, FILE_APPEND);
    }
}

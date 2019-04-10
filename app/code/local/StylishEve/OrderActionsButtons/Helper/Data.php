<?php
class StylishEve_OrderActionsButtons_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * write exception in log file of module
     *
     * @param String $pLogFileName
     * @param Array $pLogData
     *
     */
    public function logException($pLogFileName, $pLogData)
    {
        Mage::log(
            json_encode([
                "Code"          => $pLogData['exceptionObj']->getCode(),
                "Message"       => $pLogData['exceptionObj']->getMessage(),
                "Line"          => $pLogData['exceptionObj']->getLine(),
                "File"          => $pLogData['exceptionObj']->getFile(),
                "Class"         => $pLogData['className'],
                "Method"        => $pLogData['methodName'],
                "TraceAsString" => $pLogData['exceptionObj']->getTraceAsString()
            ]),
            null, $pLogFileName, true
        );
    }
}
	 
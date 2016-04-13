<?php
/*******************************************************************************
********************************************************************************
**** System: ORION                                                          ****
**** Module: N/A Generic class                                              ****
**** File Name: logger.class.php                                            ****
**** File type: PHP4 class                                                  ****
**** Called by: several files                                               ****
**** ---------------------------------------------------------------------- ****
**** Author: Víctor Hugo García Harzbecher                                  ****
**** Contact: mail to VictorHugo.Garcia@ge.com                              ****
**** Created on : October, 2012                                             ****
**** Description: This class allow to create a log manager, you can use     ****
****    the objects created to trace a php script execution                 ****
********************************************************************************
*******************************************************************************/

namespace Mapache;

class Logger
{
    private $MAILNOTOFICATIONS = false;
    private $OUTPUTLOG = false;
    private $APPENDTIME = false;
    private $scriptLog = "";
    private $mailReceivers = "";
    private $mailSubject = ""; 
    private $ccMailReceivers = "";
    private $bccMailReceivers = "";
    // Constant formats
    private $_HTML = "html";
    private $_TXT = "txt";
    
    private $infoCount = 0;
    private $errorCount = 0;
    private $fatalCount = 0;
    
    /**
     * Function logMessage
     *  add a log entry
     *  @param String $title "Log entry title"
     *  @param String $detail "Define the log entry detail"
     *  @param Boolean $error "true: the log entry is an error"
     *  @param Boolean $die "Define if the Script should stops"
     **/
    public function logMessage($title, $detail, $error = false, $die = false)
    {
        if($die){$this->fatalCount++;}
        elseif($error){$this->errorCount++;}
        else{$this->infoCount++;}
        // Set message color indicator
        $titleColor = ($error)?(($die)?"#D94736":"#FAA305"):"#3771B5";
        // Add message to log
        $this->scriptLog .= '
        <strong style="color: '.$titleColor.'">'.$title.'</strong><br/>
        <small style="color: #919191; margin-left: 15px;">
        '.$detail.
            (($this->APPENDTIME)?' <br/><small>'.date('Y-m-d H:i:s').'</small>':"").
        '</small><br/><hr/>';
        // If script stop send notification
        if($die)
        {
            if($this->MAILNOTOFICATIONS)$this->sendLogByMail();
            if($this->OUTPUTLOG)$this->outputLog();
            exit();
        }
    }
    
    /*
     * Return true if log is empty, false otherwise
     */
    public function isEmpty(){
        if($this->infoCount > 0 || $this->errorCount > 0 || $this->fatalCount > 0){return true;}
        return false;
    }
    
    /**
     * get Error Count
     * @return Int
     */
    public function getErrorCount(){
        return $this->errorCount;
    }
    
    /**
     * tells whether a fatal error occur
     * Returns true if a fatal error occur
     * @return boolean
     */
    public function fatalOccur(){
        if($this->fatalCount > 0){return true;}
        return false;
    }
    
    /**
     * get Information message count
     * @return Int
     */
    public function getInfoCount(){
        return $this->infoCount;
    }
    
    /**
     * get number of messages stored in log including information, errors and fatal errors
     */
    public function getMessageCount(){
        return $this->infoCount + $this->errorCount + $this->fatalCount;
    }

    public function outputLog($format="html")
    {
        $output = ($format == "txt")? $this->getTxt() : $this->getHtml ();
        echo $output;
    }
    
    public function sendLogByMail()
    {
        if($this->scriptLog != "")
        {
            $headers  = "From: ORION <orion-noreply@ciat.ge.com>\n";
            $headers .= "X-Mailer: PHP\n";
            $headers .= "Content-Type: text/html; charset=iso-8859-1\n";
            $headers .= "Content-Transfer-Encoding: 8bit\n";
            if($this->ccMailReceivers != "")$headers .= "cc: ".$this->ccMailReceivers."\r\n";
            if($this->bccMailReceivers != "")$headers .= "bcc: ".$this->bccMailReceivers."\r\n";
            $message = $this->getHtml()."";
            mail($this->mailReceivers, $this->mailSubject, $message, $headers);
        }
    }
    
    public function getTxt()
    {
        $entrySeparator = "";
        for($i = 1; $i <= 25; $i++)$entrySeparator .= "-";
        $text = str_replace("<br/>", "\n", $this->scriptLog);
        $text = str_replace("<hr/>", $entrySeparator."\n", $this->mailSubject);
        $text = strip_tags($text);
        return $text;
    }
    
    public function getHtml()
    {
        $html = '<div style="font-family: GE Inspira">
            '.$this->scriptLog.'
        </div>';
        return $html;
    }
    
    public function setMailParameters($subject, $to, $cc = "", $bcc = "")
    {
        $this->mailSubject = $subject;
        $this->mailReceivers = $to;
        $this->ccMailReceivers = $cc;
        $this->bccMailReceivers = $bcc;
        $this->MAILNOTOFICATIONS = true;
    }
}
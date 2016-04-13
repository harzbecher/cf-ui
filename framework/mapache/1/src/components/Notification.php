<?php

class Notification {
    
    public static $DBUG_NONE = 0;
    public static $DBUG_ADMIN_ONLY = 1;
    public static $DBUG_SUPPORT_ONLY = 2;
    public static $DBUG_BOTH = 3;
    
    private $debugLevel = 0;

    function __construct() {
        
    }
    
    private function getDebugMails()
    {
        $mailList = '';
        switch($this->debugLevel)
        {
            case self::$DBUG_ADMIN_ONLY:
                $mailList = ADMIN_MAIL;
                break;
            case self::$DBUG_SUPPORT_ONLY:
                $mailList = SUPPORT_MAIL;
                break;
            case self::$DBUG_BOTH:
                $mailList = ADMIN_MAIL.','.SUPPORT_MAIL;
                break;
        }
        return $mailList;
    }
    
    public function sendMail($to, $subject, $title, $message)
    {
        $mailNews = new Mail($to, $subject, $title, $message,' Is sending old things ');
        if($this->debugLevel > self::$DBUG_NONE)$mailNews->setBcc ( "Bcc: ".$this->getDebugMails()."\r\n");
        $mailNews->send();
    }
    
    public function setDebugLevel($level){$this->debugLevel = $level;}

}
?>

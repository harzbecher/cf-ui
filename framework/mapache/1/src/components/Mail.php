<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Mail
 *
 * @author Víctor Hugo García Harzbecher <victorhugo.garcia@ge.com>
 */
class Mail {
    
    private $to;
    private $subject;
    private $title;
    private $message;
    private $bccList;
    private $ccList;
    
    function __construct($to, $subject, $title, $message) {
        $this->to = $to;
        $this->subject = $subject;
        $this->title = $title;
        $this->message = $message;
    }
    
    private function assembleMessage()
    {
        $message = '
            <!DOCTYPE html>
                <html>
                    <body style="font-family: \'GE inspira\', \'Helvetica Neue\', \'Helvetica\', \'Arial\', \'Sans-serif\';">
                        <div style="background-color:#FFFFFF; padding: 10px;">
                            <!--div style="background-color: #13161A; padding: 10px">
                                <span class="ge-logo" style="height: 45px; width: 45px; vertical-align: middle">
                                    <img src="http://oriondev.mtc.ge.com/public/iids/components/ge-bootstrap/img/bitmap/ge-monogram.png" />
                                </span>
                                <span style="color: #FFFFFF; vertical-align: middle; font-size: 18px; line-height: 20px; font-weight: normal;">
                                    ORION Notifications
                                </span>
                            </div-->
                            <div style="font-size: 14px; color:#313337">
                                <h3 style="font-size: 18px;">ORION :: '.$this->title.'</h3>
                            </div>
                            <hr style="margin: 10px 0px; border-width: 1px 0px; border-style: solid none; border-color: rgb(225, 226, 229)" />
                            <!-- Mail body -->
                            <div>'.$this->message.'</div>
                            <!-- Mail footer -->
                            <br/>
                            <div style="color: rgb(153, 156, 159); font-size: 14px;">
                                <p>
                                    This is an automatic e-mail, please do not reply directly, if you have any question, send an e-mail to
                                    '.self::toA("mailto:".SUPPORT_MAIL,SUPPORT_NAME).'
                                </p>
                            </div>
                        </div>
                    </body>
                </html>
            ';
        return $message;
    }
    
    public static function toA($href, $innerHtml)
    {
        return '<a style="color: rgb(59, 115, 185); text-decoration: none" href="'.$href.'">'.$innerHtml.'</a>';
    }
    
    public static function toBadge($badge, $innerHtml)
    {
        switch($badge)
        {
            case 'info':
                $color = 'rgb(8, 165, 225)';
                break;
            case 'warning':
                $color = '#ED8000';
                break;
            case 'success':
                $color = '#76B900';
                break;
        }
        return '<strong><span style="background-color: '.$color.'; line-height: 7px; padding: 6px 4px; font-size: 12px; color:#FFFFFF;">&nbsp;'.$innerHtml.'&nbsp;</span></strong>';
    }
    
    public function setBcc($bccList)
    {$this->bccList = $bccList;}
    public function setCc($bccList)
    {$this->ccList = $bccList;}
    
    public function send()
    {
        $headers  = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
        $headers .= "From:ORION NOTIFICATIONS <noreply@orion.mtc.ge.com>\r\n";
        if($this->ccList)$headers .= "Cc: ".$this->ccList."\r\n";
        if($this->bccList)$headers .= "Bcc: ".$this->bccList."\r\n";
        
        if(!mail($this->to, $this->subject, $this->assembleMessage(), $headers))throw new Exception('CAN NOT SEND MAIL');
    }
}

?>

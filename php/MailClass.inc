<?php

/**
 * Created by PhpStorm.
 * User: Alexander
 * Date: 08.04.2016
 * Time: 16:28
 */
require_once('../lib/PHPMailer-master/class.phpmailer.php');

class MedServMailer extends PHPMailer
{
    var $priority = 3;
    var $to_name;
    var $to_email;
    var $From = null;
    var $FromName = null;
    var $Sender = null;

    function MedServMailer()
    {
        global $medserv;

        // Берем из файла config.php массив $site

        if($medserv['smtp_mode'] == 'enabled')
        {
            $this->Host = $medserv['smtp_host'];
            $this->Port = $medserv['smtp_port'];
            $this->Sendmail = $medserv['Sendmail'];
            if($medserv['smtp_username'] != '')
            {
                $this->SMTPAuth  = true;
                $this->Username  = $medserv['smtp_username'];
                $this->Password  =  $medserv['smtp_password'];
            }
            $this->Mailer = "smtp";
        }
        if(!$this->From)
        {
            $this->From = $medserv['from_email'];
        }
        if(!$this->FromName)
        {
            $this-> FromName = $medserv['from_name'];
        }
        if(!$this->Sender)
        {
            $this->Sender = $medserv['from_email'];
        }

        $this->Priority = $this->priority;
    }

}

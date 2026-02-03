<?php
namespace mailer;
use mailer\PHPMailer;
use Storage;
class WebMail{
public static function send($info){

    $config["EmailName"]=Storage::setting("mailer_EmailName");
    $config["EmailAddress"]=Storage::setting("mailer_EmailAddress");
    $config["EmailPassword"]=Storage::setting("mailer_EmailPassword");
	try{
    $mail = new PHPMailer(true);                              // Passing `true` enables exceptions
    //Server settings
    $mail->SMTPDebug = 0;                                 // Enable verbose debug output
    $mail->isSMTP();
    $mail->CharSet    = "utf-8";                                  // Set mailer to use SMTP
    $mail->Host       = "smtp.gmail.com";  // Specify main and backup SMTP servers
    $mail->SMTPAuth   = true;                               // Enable SMTP authentication
    $mail->Username   = $config["EmailAddress"];                 // SMTP username
    $mail->Password   = $config["EmailPassword"];                           // SMTP password
    $mail->SMTPSecure = "tls";                            // Enable TLS encryption, `ssl` also accepted or tls
    $mail->Port       = 587;                                    // TCP port to connect to

    //Recipients
    $mail->setFrom($config["EmailAddress"],$config["EmailName"]);
    if(!empty($info["ReplyTo"]) ){ $mail->addReplyTo($info["ReplyTo"]); }
    //$mail->addCC('info@example.com');
    if(count($info["To"])>1){
        foreach ($info["To"] as $id=>$email) {
            $mail->addBCC($email);
        }
    }else{
        $mail->addAddress($info["To"][0]); 
    }
    //Đính kèm
    //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
    if(is_array($info["Attachments"])){
        foreach ($info["Attachments"] as $path) {
            $mail->addAttachment($path);
        }
    }
    //Content
    $mail->isHTML(true);                                  // Set email format to HTML
    $mail->Subject = $info["Subject"];
    $mail->Body    = '
    <div style="font-family:\'Segoe UI\',Arial,sans-serif;font-size:16px;background:#eaeaea; padding: 50px 10px" bgcolor="#eaeaea">
        <table cellspacing="0" align="center" style="width: 100%; max-width:700px;border-radius:2px;border-spacing:0px;margin-left:auto;margin-right:auto;background:#fff;border:1px solid #dddddd" bgcolor="#FFF">
            <tr>
                <td style="padding: 15px; text-align: center; background-color: '.Storage::option("theme_header_bg", "#FFF").'">
                    <a href="'.HOME.'">
                        <img style="max-height: 100px" src="'. ( empty(Storage::option("theme_header_logo")) ? "".HOME."/assets/general/images/logo/".DOMAIN.".png" : HOME.Storage::option("theme_header_logo") ) .'" alt="'.ucwords(DOMAIN).'" />
                    </a>
                </td>
            </tr>
            <tr>
                <td style="text-align:left;color:#666;font-size:15px;line-height:26px;padding:25px">
                    '.$info["Body"].'
                </td>
            </tr>
            '.( empty( Storage::setting("mailer_footer") ) ? '' : '
	            <tr>
	                <td style="text-align:left;color:#666;font-size:15px;line-height:26px;padding: 0 25px 25px 25px">
	                    '.nl2br( Storage::setting("mailer_footer") ).'
	                </td>
	            </tr>
	            ').'
        </table>
    </div>
    ';
    $mail->AltBody = strip_tags($info["Body"]);

    $mail->send();
    	return;
    } catch (Exception $e) {
    	return $mail->ErrorInfo;
	}
    
    }

}
<?php

require_once("sendgrid/birthday-email.php");
require_once("sendgrid/dayoff-accepted-email.php");
require_once("sendgrid/dayoff-rejected-email.php");
require_once("sendgrid/feedback-email.php");
require_once("sendgrid/password-recovery-email.php");
require_once("sendgrid/profile-confirmation-email.php");
require_once("sendgrid/vacation-accepted-email.php");
require_once("sendgrid/vacation-rejected-email.php");
require_once("sendgrid/vacation-request-email.php");
require_once("sendgrid/verification-email.php");
require_once("sendgrid/unaccepted-vacations-email.php");
require_once("sendgrid/added-deleted-collaborator-email.php");
require_once("sendgrid/provider-oc-email.php");
require_once("sendgrid/costo-enviar-factura.php");
require_once("sendgrid/low-rating-email.php");
require_once("sendgrid/plus-eigth-hours-email.php");
require_once("sendgrid/califica-proveedor.php");
require_once("sendgrid/budget-email.php");

function SG_SendMail($name, $email_from, $email_to, $body, $subject, $code = "", $user = 0)
{ 
    if (function_exists('CheckIfSendable')) {
        if(!CheckIfSendable($code . "_M", $user)) return;
    }

    $headers = array(
    "Authorization: Bearer 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",
    "Content-Type: application/json"
    );
        
    $data = array(
    "personalizations" => array(
        array(
            "to" => array(
                array(
                    "email" => $email_to,
                    "name" => $name
                    )
                )
        )
        ),
            "from" => array(
                    "email" => $email_from
                ),
                "subject" => $subject,
                "content" => array(
                    array(
                        "type" => "text/html",
                        "value" => $body
                    )
                )
            );
        
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api.sendgrid.com/v3/mail/send");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    
    // FIX PROVISORIO: Deshabilitar verificación SSL para MAMP/Localhost
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);
        
    if ($httpCode >= 200 && $httpCode < 300) {
        error_log("DEVELOPER: Email has been sent to user: " . $email_to . "!");
        return true;
    } else {
        error_log("DEVELOPER: Email FAILED to user: " . $email_to . " (HTTP " . $httpCode . ")");
        return false;
    }
}

function SG_SendMail_Batch($emails)
{
    if (empty($emails)) return true;

    $mh = curl_multi_init();
    $curl_handles = array();

    $headers = array(
        "Authorization: Bearer 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",
        "Content-Type: application/json"
    );

    foreach ($emails as $key => $email_data) {
        $name = $email_data['name'];
        $email_from = $email_data['email_from'];
        $email_to = $email_data['email_to'];
        $body = $email_data['body'];
        $subject = $email_data['subject'];

        $data = array(
            "personalizations" => array(
                array(
                    "to" => array(
                        array(
                            "email" => $email_to,
                            "name" => $name
                        )
                    )
                )
            ),
            "from" => array(
                "email" => $email_from
            ),
            "subject" => $subject,
            "content" => array(
                array(
                    "type" => "text/html",
                    "value" => $body
                )
            )
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.sendgrid.com/v3/mail/send");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        
        // FIX PROVISORIO: Deshabilitar verificación SSL para MAMP/Localhost
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

        curl_multi_add_handle($mh, $ch);
        $curl_handles[$key] = $ch;
    }

    $active = null;
    do {
        $mrc = curl_multi_exec($mh, $active);
    } while ($mrc == CURLM_CALL_MULTI_PERFORM);

    while ($active && $mrc == CURLM_OK) {
        if (curl_multi_select($mh) != -1) {
            do {
                $mrc = curl_multi_exec($mh, $active);
            } while ($mrc == CURLM_CALL_MULTI_PERFORM);
        }
    }

    // Cleanup
    foreach ($curl_handles as $ch) {
        curl_multi_remove_handle($mh, $ch);
        curl_close($ch);
    }

    curl_multi_close($mh);
    return true;
}

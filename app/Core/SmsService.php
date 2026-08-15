<?php
namespace App\Core;

class SmsService
{
    private const BASE_URL = 'https://api2.dream-digital.info/api/SendSMS';

    private static function isDebug(): bool
    {
        return filter_var(Env::get('SMS_DEBUG', 'false'), FILTER_VALIDATE_BOOLEAN);
    }

    private static function log(string $msg, array $ctx = []): void {
        if (self::isDebug()) {
            $s = '[SMS] ' . $msg . ' ' . json_encode($ctx);
            error_log($s);
            $logFile = defined('ROOT_PATH') ? ROOT_PATH . '/storage/logs/sms_debug.log' : __DIR__ . '/../../storage/logs/sms_debug.log';
            @file_put_contents($logFile, date('Y-m-d H:i:s') . ' - ' . $s . "\n", FILE_APPEND);
        }
    }

    /**
     * Normalise un numéro de téléphone RDC (retourne les 9 chiffres nationaux).
     */
    public static function normaliserTelephone(?string $phone): string
    {
        $p = preg_replace('/\D+/', '', (string)$phone);
        if (strpos($p, '243') === 0) {
            $p = substr($p, 3);
        }
        if (strpos($p, '0') === 0) {
            $p = substr($p, 1);
        }
        if (strlen($p) > 9) {
            $p = substr($p, -9);
        }
        self::log('Normalisé', ['input' => $phone, 'output' => $p]);
        return $p;
    }

    /**
     * Envoie un SMS via l'API Dream Digital.
     *
     * @param string $phoneNational  Les 9 chiffres nationaux (sans 243 ni 0)
     * @param string $message        Le contenu du SMS
     * @return bool true si l'envoi a réussi
     */
    public static function envoyer(string $phoneNational, string $message): bool
    {
        self::log('Tentative SMS', ['phone' => $phoneNational, 'msg' => $message, 'len' => strlen($phoneNational)]);
        
        if (strlen($phoneNational) !== 9) {
            self::log('ERREUR: longueur invalide', ['phone' => $phoneNational]);
            return false;
        }

        $apiId = Env::get('SMS_API_ID', '');
        $apiPassword = Env::get('SMS_API_PASSWORD', '');
        if ($apiId === '' || $apiPassword === '') {
            self::log('ERREUR: SMS_API_ID / SMS_API_PASSWORD non configurés (.env)');
            return false;
        }

        $query = http_build_query([
            'api_id'       => $apiId,
            'api_password' => $apiPassword,
            'sms_type'     => 'T',
            'encoding'     => 'T',
            'sender_id'    => Env::get('SMS_SENDER_ID', 'CNPR-TSHOPO'),
            'phonenumber'  => '243' . $phoneNational,
            'textmessage'  => $message,
        ]);

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => self::BASE_URL . '?' . $query,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT        => 15,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_USERAGENT      => 'MIN-TRANSPORT/1.0',
        ]);
        $res    = curl_exec($ch);
        $error  = curl_error($ch);
        $status = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        curl_close($ch);

        self::log('Réponse API', ['status' => $status, 'error' => $error, 'res' => substr($res, 0, 200)]);

        return $res !== false && $status < 400;
    }
}

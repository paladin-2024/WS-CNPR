<?php
namespace App\Core;

class SmsService
{
    private const BASE_URL = 'https://api2.smsala.com/SendSmsV2';

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
     * Envoie un SMS via l'API Africala (SendSmsV2, api2.smsala.com).
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

        $apiToken = Env::get('SMS_API_TOKEN', '');
        if ($apiToken === '') {
            self::log('ERREUR: SMS_API_TOKEN non configuré (.env)');
            return false;
        }

        // SendSmsV2 deserializes the body as a List<SendSmsRequestModelV2> -
        // it expects a JSON array of message objects, not a single object.
        $payload = [[
            'apiToken'          => $apiToken,
            'messageType'       => '1',
            'messageEncoding'   => '1',
            'destinationAddress' => '243' . $phoneNational,
            'sourceAddress'     => Env::get('SMS_SENDER_ID', 'CNPR-TSHOPO'),
            'messageText'       => $message,
        ]];

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => self::BASE_URL,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($payload),
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
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

        self::log('Réponse API', ['status' => $status, 'error' => $error, 'res' => substr((string)$res, 0, 200)]);

        if ($res === false || $status >= 400) {
            return false;
        }

        // Réponse attendue : {"Status":"Success","Remarks":"...", ...}, ou un
        // tableau du même objet (l'endpoint accepte un lot de messages, donc
        // peut répondre avec un résultat par message). Statut différent
        // ("Failed", etc.) en cas d'échec.
        $data = json_decode($res, true);
        if (is_array($data) && array_is_list($data) && isset($data[0])) {
            $data = $data[0];
        }
        if (!is_array($data) || !isset($data['Status'])) {
            self::log('ERREUR: réponse API inattendue', ['res' => substr($res, 0, 200)]);
            return false;
        }

        if (strcasecmp((string)$data['Status'], 'Success') !== 0) {
            self::log('ERREUR: échec API', ['status' => $data['Status'], 'remarks' => $data['Remarks'] ?? null]);
            return false;
        }

        return true;
    }
}

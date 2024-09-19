<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\MessagingException;

class FirebaseService
{
    protected $messaging;

    public function __construct()
    {
        // Replace this with your actual Firebase credentials JSON data
     $firebaseCredentialsJson = '{
    "type": "service_account",
    "project_id": "cactus-5cbab",
    "private_key_id": "c6bff781f729493ad42b30eb574c57bbe0057b0c",
    "private_key": "-----BEGIN PRIVATE KEY-----\\nMIIEvAIBADANBgkqhkiG9w0BAQEFAASCBKYwggSiAgEAAoIBAQCwW2gsOVXWQ/c5\\nw2Kid2ujNjQY/geviHIEYpEq2BFKzBYbXb1TvveUNzPkRxl9ri2ByBZ+/KGSeu2h\\n1QAr3XQ29BiAjuwSBi6TL0RJHkX9j9modY0KHAwXZelByZIvR5is/w4nQ+sBKk/h\\ndDq70DLpvwVJKpAumP8EFpPxYGykCPsvLW9Aj1TqQYmArHADoSEmkWbAXsTdYmGr\\nssXm0X73LgoSoLTxjspbxzTHTtAUHeWTlcNU5IfG8/8KlKzKY2Ck4Hyb7g+CPVCf\\nb89899j8XqXtx6JH080mIWu8ksaFaN7MRSWKitf8KGxmwhY9gV120Sqp5FFoA477\\nNMhPcdQxAgMBAAECggEAEcKi0xFTG6jslNygakwsgttKk9nBXGQZV2e1xuLLmwTC\\nlS7ziiOJO1vz1eFzJ70KyC8mVNOMUfwfQlT5F3HN6lravNen3ojQ1/HCprptwgNi\\noBx2f7YjYZfZzc5G8ov7TDDDdCDa4dspAEN8Rr3I4tFh7uRicM7nn7nGXGChkENM\\nfJVdtXtIOegAkhNQtZ6zKISd8qO8TtoW+2OInhk4XRpPdYrPAMP7anLK9mmqWmzo\\nDk/bDYqhCBVm89On1gy2DiHh6xcdfouAhexWK/C0WEEPr34DXjMjRIFxVVPH5afw\\nD2E95E4Nbm3+JdkUKienDVgacVPqCQFX+bdUmduHgQKBgQDUqxoaQZqQ2EZeuJoM\\nLE1+4Al8MqXTaVdG+G6UgLANj7q0O1gSPmCd4/PHkTZfoHOcGljGJNar9pV2LRSi\\nVgES+Oat+2OGWU8Ui6cZ/4o3IFh2KdolI9UjzZm/CktxAtkUVxBuKti42dQwg8BM\\n92dTjP3/KVZ4WfIKkLbGL/xVjQKBgQDUSkmLzyGJ16KielhpFHqzrs0sXd7XSZgQ\\n3k0JTnnwYhJYBM9w6a2Lyr8MCinJWaDhtSFRsOt4aWjP4ICE9Usuh/uqv9d336kS\\nwG8uzsVv3Q5Bvc+Wu3sJPkglc7WZaR+Mu+eOgkv27mAof71xBbiOh33YSRLWYbgc\\np/WKTMIWNQKBgA6R8mXeHMLTrm6K4zL2ThDlNIEnzyiezPX1y02tS2KCeF4kurH1\\nSBVJKsh/cGQ4z/Lf8zkQCQ0bBb5k9Eby8XvlRZih4n2v3LUhpD8pvMRuGOFvmJx2\\nygF78o6eG/EQKuMz29JQaSyw79KrFB+xy4hz3mr+4Ae60dRbeco2O2l5AoGABp1R\\n8VNA3kSjwqBBRPToZdTae7lpChg27r+ect3JGt5TZ/6uX/xH9gUZMszWRHnQc3fr\\nU9pJW/Uc2O1L09i4wPhntQhJNGj5oaXxUOoMaNHgcdfyMeYhjUYthU03qVIHM6Ff\\nM6eHqmf/AwQH6Q+ekoJSP3z3Y0qkK/BnzClcGskCgYAI/KrL5qYlJwOPm/w4qAPO\\nAdyl6rpS3fUADX0yE1cYon1Z8QGWzCIvvjrtW2mD6ygd3P279cIi6WtEaolpeBwr\\n7MOopyfHBC0FVx81syInhpQyaCSIfnjXookQ9tfoUs1Q4TU3BnQAsboodn8X2Vvf\\n1mvvR2AojJyqPw4T/2JfdA==\\n-----END PRIVATE KEY-----\\n",
    "client_email": "firebase-adminsdk-4wge4@cactus-5cbab.iam.gserviceaccount.com",
    "client_id": "117436710406358581063",
    "auth_uri": "https://accounts.google.com/o/oauth2/auth",
    "token_uri": "https://oauth2.googleapis.com/token",
    "auth_provider_x509_cert_url": "https://www.googleapis.com/oauth2/v1/certs",
    "client_x509_cert_url": "https://www.googleapis.com/robot/v1/metadata/x509/firebase-adminsdk-4wge4%40cactus-5cbab.iam.gserviceaccount.com",
    "universe_domain": "googleapis.com"
  }';


        // Decode JSON data to an array
        $credentials = json_decode($firebaseCredentialsJson, true);

        if (!$credentials) {
            throw new \Exception('Invalid Firebase credentials JSON');
        }

        $factory = (new Factory)
            ->withServiceAccount($credentials);

        $this->messaging = $factory->createMessaging();
    }

    public function sendNotification($deviceToken, $title, $body, $data = [])
    {
        try {
            $message = CloudMessage::withTarget('token', $deviceToken)
                ->withNotification(['title' => $title, 'body' => $body])
                ->withData($data);

            // Send notification and capture the response
            $messageId = $this->messaging->send($message);

            // Return success message or the message ID
            return [
                'status' => 'success',
                'message_id' => $messageId
            ];
        } catch (MessagingException $e) {
            // Handle the exception and capture error details
            return [
                'status' => 'error',
                'error' => $e->getMessage()
            ];
        }
    }
}

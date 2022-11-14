<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CinetPayController extends Controller
{
    public function index()
    {
        return view('cinetpay');
    }
    public function Payment(Request $request)
    {
        $transaction_id = date("YmdHis");// Generer votre identifiant de transaction
        $cinetpay_data =  [
            "amount"=> $request['amount'],
            "currency"=> $request['currency'],
            "apikey"=> env("APIKEY"),
            "site_id"=> env("SITE_ID"),
            "transaction_id"=> "sdkLaravel-".$transaction_id,
            "description"=> "TEST-Laravel",
            "return_url"=> route('return_url'),
            "notify_url"=> route('notify_url'),
            "metadata"=> "user001",
            'customer_surname'=> "",
            'customer_name'=> "" ,
            'customer_email'=> "",
            'customer_phone_number'=> '',
            'customer_address'=> '',
            'customer_city'=> '',
            'customer_country'=> '' ,
            'customer_state'=> '',
            'customer_zip_code'=> ''
        ];
        //Sequence d'initialisation du lien de paiement
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api-checkout.cinetpay.com/v2/payment',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 45,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($cinetpay_data),
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_HTTPHEADER => array(
                "content-type:application/json"
            ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        //On recupère la réponse de CinetPay
        $response_body = json_decode($response,true);
        if($response_body['code'] == '201')
        {
            $payment_link = $response_body["data"]["payment_url"]; // Recuperation de l'url de paiement
            //Enregistrement des informations dans la base de donnée
            //Ensuite redirection vers la page de paiement
            return redirect($payment_link);
        }
        else
        {
            return back()->with('info', 'Une erreur est survenue.  Description : '. $response_body["description"]);
        }

    }

    //configuration de l'api la notification
    public function notify_url (Request $request)
    {
        /* 1- Recuperation des paramètres postés sur l'url par CinetPay
         * https://docs.cinetpay.com/api/1.0-fr/checkout/notification#les-etapes-pour-configurer-lurl-de-notification
         * */
        if (isset($request->cpm_trans_id))
        {
            // A l'aide de l'identifiant de votre transaction, vérifier que la commande n'a pas encore été traité
            $VerifyStatusCmd = "1"; // valeur du statut à récupérer dans votre base de donnée
            if ($VerifyStatusCmd == '00') {
                // La commande a été déjà traité
                // Arret du script
                die();
            }

           /* 2- Dans le cas contrait, on vérifie l'état de la transaction en cas de tentative de paiement sur CinetPay
            * https://docs.cinetpay.com/api/1.0-fr/checkout/notification#2-verifier-letat-de-la-transaction */
            $cinetpay_check = [
                "apikey" => env("APIKEY"),
                "site_id" => $request->cpm_site_id,
                "transaction_id" => $request->cpm_trans_id
            ];

            $response = $this->getPayStatus($cinetpay_check); // appel fonction de requête pour récupérer le statut

            //On recupère la réponse de CinetPay
            $response_body = json_decode($response,true);
            if($response_body['code'] == '00')
            {
                /* correct, on délivre le service
                 * https://docs.cinetpay.com/api/1.0-fr/checkout/notification#3-delivrer-un-service*/
                echo 'Felicitation, votre paiement a été effectué avec succès';

            }
            else
            {
                // transaction a échoué
                echo 'Echec, code:' . $response_body['code'] . ' Description' . $response_body['description'] . ' Message: ' .$response_body['message'];
            }
            // Mettez à jour la transaction dans votre base de donnée
            /*  $commande->update(); */
        }
        else{
            print("cpm_trans_id non fourni");
        }
    }

    //configuration de l'api de retour
    public function return_url (Request $request)
    {
        /* 1- recuperation des données postées par CinetPay
         * https://docs.cinetpay.com/api/1.0-fr/checkout/retour#les-etapes-pour-configurer-lurl-de-retour */
        if (isset($request->transaction_id) || isset($request->token))
        {
            /* 2- on vérifie l'état de la transaction sur CinetPay ou dans notre base de donnée
            * https://docs.cinetpay.com/api/1.0-fr/checkout/notification#2-verifier-letat-de-la-transaction */
            $cinetpay_check = [
                "apikey" => env("APIKEY"),
                "site_id" => env("SITE_ID"),
                "transaction_id" => $request->transaction_id
            ];
            // appel fonction de requête pour récupérer le statut chez CinetPay
            $response = $this->getPayStatus($cinetpay_check);
            //On recupère la réponse de CinetPay
            $response_body = json_decode($response,true);
            if($response_body['code'] == '00')
            {
                /* correct, on redirige le client vers la page souhaité */
                return back()->with('info', 'Felicitation, votre paiement a été effectué avec succès');
            }
            else
            {
                /* correct, on redirige le client vers la page souhaité */
                return back()->with('info', 'Echec, votre paiement a échoué');
            }
        }
        else{
            print("transaction non fourni");
        }
    }

    public function getPayStatus($data)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api-checkout.cinetpay.com/v2/payment/check',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 45,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_HTTPHEADER => array(
                "content-type:application/json"
            ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err)
         print ($err);
        else
        return ($response);
    }
}

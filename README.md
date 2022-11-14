# CinetPay SDK Laravel Integration

CinetPay SDK Laravel Integration permet d'intégrer rapidement CinetPay à un site en ligne fait avec PHP.

## 1) Prérequis
Avant de procéder à l’intégration du module de paiement, veuillez-vous assurer d’être en possession des éléments suivants :

    - Un compte marchand sur  [cinetpay.com](https://cinetpay.com/)
    - Votre api key, site ID (disponible dans votre interface administration backend)

## 2) Configuration

- Saisissez vos identifiants dans le fichier ``.env``

```php
APIKEY = votre apikey
SITE_ID = Votre site id
```

- Pour l'exemple, récupérer le fichier ``views/cinetpay.blade.php``

- Définissez les routes suivantes dans ``routes/web.php``

```php
Route::get('/', [\App\Http\Controllers\CinetPayController::class, 'index']);
Route::post('/', [\App\Http\Controllers\CinetPayController::class, 'Payment']);
Route::match(['get','post'],'/notify_url', [\App\Http\Controllers\CinetPayController::class, 'notify_url'])->name('notify_url');
Route::match(['get','post'],'/return_url', [\App\Http\Controllers\CinetPayController::class, 'return_url'])->name('return_url');
```

- Importer le fichier ``CinetPayController.php`` dans votre dossier ``Http/Controllers``
- Ajouter ces lignes au tableau `$except` situé dans le fichier ``app/http/middleware/VerifyCsrfToken.php``
```php
'return_url',
'notify_url'
```

## 3) Paramétrage du fichier CinetPayController.php
- La fonction `Payment` permet de rediriger l'utilisateur sur un lien de paiement 
 Vous trouverez plus d'informations sur la documentation:
```url 
 https://docs.cinetpay.com/api/1.0-fr/checkout/initialisation
```

```php
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
...
```
- La fonction `notify_url` vous permet de recevoir des notifications et mettre à jour votre système. <br/> Pour ceux qui possèdent des services qui ne nécessitent pas un traitement des notifications de paiement de CinetPay,
vous pouvez passer directement à la phase suivante, par exemple les services de don.

- La fonction de `return_url` permet de rediriger le client après une transaction sur CinetPay (quelque soit le statut de la transaction). <br>
Aucune mise à jour de la base de données ne doit être traité sur cette page.




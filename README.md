Tpp One4All Library for PHP
=======================
```php
use MyOne4All\TppClient;
use MyOne4All\Exceptions\TppException;
use MyOne4All\Models\DataBundle;
use MyOne4All\NetworkCodes;

$tppClient = new \MyOne4All\Client("apikey", "apisecret", "retailer");

echo $tppClient->getBalance(); # 0.0

// send airtime implementation
$airtime_response = $tppClient->sendAirtime("0245667942", 1, "trans03423423", NetworkCodes::AUTO_DETECT);
if($airtime_response->isSuccessful()){
    echo "airtime sent";
}else{
    echo "Failed: ".$airtime_response->getErrorMessage();
}

// send internet data implementation
$data_code = "DAILY_20MB";
$transaction_reference = "trans03423423";
$bundle_response = $tppClient->sendDataBundle("0245667942", $data_code, $transaction_reference, NetworkCodes::MTN_GH);
if($bundle_response->isSuccessful()){
    echo "internet bundle sent";
}else{
    echo "Failed: ".$bundle_response->getErrorMessage();
}


// send mobile money implementation
$transaction_reference = "trans03423423";
$amount = 1;
$momo_response = $tppClient->sendMobileMoney("0245667942", $amount, $transaction_reference);
if($momo_response->isSuccessful()){
    echo "mobile money sent";
}else if($momo_response->isPending()){
    // this is very important, do not treat as an error.
    // you must check transaction status later to know if it was successful or not
    echo "mobile money request is being processed.";
}else{
    echo "Failed: ".$momo_response->getErrorMessage();
}

// receive mobile money implementation
$transaction_reference = "trans03423423";
$amount = 1;
$payer_number = "0245667XXX";
$momo_response = $tppClient->receiveMobileMoney($payer_number, $amount, $transaction_reference);
if($momo_response->isSuccessful()){
    // check transaction status later to confirm receipt
    echo "mobile money payment request initiated";
}else{
    echo "Failed: ".$momo_response->getErrorMessage();
}

$sms_message = "hello world";
$sms_sender_id = "One4All";
$transaction_reference = "sms11";
$sms_response = $tppClient->sendSms("0572180376", $sms_message, $sms_sender_id, $transaction_reference);
if($sms_response->isSuccessful()){
    echo "sms message queued";
}else{
    echo "Failed: ".$sms_response->getErrorMessage();
}



echo NetworkCodes::AUTO_DETECT; # 0
echo NetworkCodes::MTN_GH; # 4
echo NetworkCodes::AIRTEL_DRC; # 2

// fetch available data packages for all networks
$data_packages = $tppClient->getDataBundleList();

// only fetch data packages for mtn
$data_packages = $tppClient->getDataBundleList(NetworkCodes::MTN_GH);

foreach($data_pages as $data_package){
    echo $data_package->plan_id; # DAILY_20MB
    echo $data_package->plan_name; # DAILY_20MB
    echo $data_package->category; # DAILY
    echo $data_package->network_id; # 4
    echo $data_package->volume; # 20 MB
}
```

Install package using Composer
```bash
composer require jilenloa/tppmyone4all-php
```

After installing, you need to require Composer's autoloader:

```php
require 'vendor/autoload.php';
```

You can then later update the package later using composer:

 ```bash
composer update
 ```

In order to run unit tests for this package

 ```bash
npm install -g mockserver
 ```
<?php
/**
 * Created by PhpStorm.
 * User: macbookpro
 * Date: 10/27/19
 * Time: 3:00 PM
 */

namespace MyOne4All;


use GuzzleHttp\Exception\ClientException;
use MyOne4All\Exceptions\TppException;
use MyOne4All\Models\DataBundle;

class TppClient
{
    private $key;
    private $secret;
    private $retailer;
    private $country;
    protected $httpClient;

    private $mock;

    const COUNTRY_GHANA = 'gh';
    const COUNTRY_CONGO = 'drc';
    const WALLET_TYPE_ECREDIT = 'e-credit';
    const WALLET_TYPE_MOBILE_MONEY_CREDIT = 'mobile-money-credit-account';
    const WALLET_TYPE_MOBILE_MONEY_COLLECTION = 'mobile-money-collection-account';

    /**
     * Client constructor.
     * @param $key
     * @param $secret
     * @param $retailer
     * @param string $country
     */
    public function __construct($key, $secret, $retailer, $country = self::COUNTRY_GHANA)
    {
        $this->key = $key;
        $this->secret = $secret;
        $this->retailer = $retailer;
        $this->country = $country;
    }

    private function getHttpClient(){
        if($this->httpClient){
            return $this->httpClient;
        }

        $base_uri = "https://tpp{$this->country}.myone4all.com";
        if($this->mock){
            $base_uri = "http://127.0.0.1:9005";
        }

        $this->httpClient = new \GuzzleHttp\Client(
            array(
                'base_uri' => $base_uri,
                'headers' => array(
                    'ApiKey' => $this->key,
                    'ApiSecret' => $this->secret,
                    'User-Agent' => 'TppMyOne4All PHP Library',
                    'Accept' => 'application/json'
                ),
                'verify' => false
            )
        );

        return $this->httpClient;
    }

    /**
     * @return string|double
     * @throws ClientException
     */
    public function getBalance($wallet_type = self::WALLET_TYPE_ECREDIT){
        $response = $this->getHttpClient()->get("/api/TopUpApi/balance?type={$wallet_type}");
        $response_array = json_decode($response->getBody()->getContents(), true);
        return $response_array['balance'];
    }

    /**
     * @param $recipient
     * @param $amount
     * @param $transaction_reference
     * @param int $network
     * @return TppResponse
     * @throws ClientException
     */
    public function sendAirtime($recipient, $amount, $transaction_reference, $network = NetworkCodes::AUTO_DETECT){
        $params = array(
            'retailer' => $this->retailer,
            'recipient' => $recipient,
            'amount' => $amount,
            'network' => $network,
            'trxn' => $transaction_reference
        );
        $response = $this->getHttpClient()->get("/api/TopUpApi/airtime?".http_build_query($params));
        $response_array = json_decode($response->getBody()->getContents(), true);
        return new TppResponse($response_array);
    }

    /**
     * @param $recipient
     * @param $data_code
     * @param $transaction_reference
     * @param $network
     * @return TppResponse
     * @throws ClientException
     */
    public function sendDataBundle($recipient, $data_code, $transaction_reference, $network){
        $params = array(
            'retailer' => $this->retailer,
            'recipient' => $recipient,
            'data_code' => $data_code,
            'network' => $network,
            'trxn' => $transaction_reference
        );
        $response = $this->getHttpClient()->get("/api/TopUpApi/dataBundle?".http_build_query($params));
        $response_array = json_decode($response->getBody()->getContents(), true);
        return new TppResponse($response_array);
    }

    public function sendFlexiDataBundle($recipient, $amount, $data_code, $transaction_reference, $network){
        $params = array(
            'retailer' => $this->retailer,
            'recipient' => $recipient,
            'data_code' => $data_code,
            'network' => $network,
            'trxn' => $transaction_reference,
            'amount' => $amount
        );

        $response = $this->getHttpClient()->get("/api/TopUpApi/dataBundle?".http_build_query($params));
        $response_array = json_decode($response->getBody()->getContents(), true);
        return new TppResponse($response_array);
    }

    /**
     * @param $recipient
     * @param $amount
     * @param $transaction_reference
     * @return TppResponse
     * @throws ClientException
     */
    public function sendMobileMoney($recipient, $amount, $transaction_reference){
        $params = array(
            'amount' => $amount,
            'recipient' => $recipient,
            'trxn' => $transaction_reference
        );
        $response = $this->getHttpClient()->get("/api/TopUpApi/b2c?".http_build_query($params));
        $response_array = json_decode($response->getBody()->getContents(), true);
        return new TppResponse($response_array);
    }

    /**
     * @param $payer_number
     * @param $amount
     * @param $transaction_reference
     * @param int $delay_seconds
     * @return TppResponse
     * @throws ClientException
     */
    public function receiveMobileMoney($payer_number, $amount, $transaction_reference, $delay_seconds = 0){
        $params = array(
            'amount' => $amount,
            'recipient' => $payer_number,
            'trxn' => $transaction_reference
        );

        if($delay_seconds && $delay_seconds > 0){
            $params['delay'] = $delay_seconds;
        }
        $response = $this->getHttpClient()->get("/api/TopUpApi/c2b?".http_build_query($params));
        $response_array = json_decode($response->getBody()->getContents(), true);
        return new TppResponse($response_array);
    }

    /**
     * @param $transaction_reference
     * @return string
     * @throws ClientException
     * @throws TppException
     */
    public function getTransactionStatus($transaction_reference){
        $params = array(
            'trxn' => $transaction_reference
        );
        $response = $this->getHttpClient()->get("/api/TopUpApi/transactionStatus?".http_build_query($params));
        $response_array = json_decode($response->getBody()->getContents(), true);

        if(isset($response_array['transaction-state'])){
            return $response_array['transaction-state'];
        }else{
            if(isset($response_array['message'])){
                throw new TppException($response_array['message']);
            }else{
                throw new TppException("Unknown response from Tpp server");
            }
        }

    }

    /**
     * @param null $network
     * @return array|DataBundle[]
     * @throws ClientException
     */
    public function getDataBundleList($network = null){
        if($network){
            $params = array(
                'network' => $network
            );
        }else{
            $params = array();
        }

        $response = $this->getHttpClient()->get("/api/TopUpApi/dataBundleList?".http_build_query($params));
        $response_array = json_decode($response->getBody()->getContents(), true);

        $list = array();
        foreach ($response_array['bundles'] as $item) {
            $list[] = new DataBundle($item);
        }

        return $list;
    }

    public function sendSms($recipient, $message, $sender_id, $transaction_reference){
        $params = array(
            'message' => $message,
            'recipient' => $recipient,
            'sender_id' => $sender_id,
            'trxn' => $transaction_reference
        );
        $response = $this->getHttpClient()->get("/api/TopUpApi/sms?".http_build_query($params));
        $response_array = json_decode($response->getBody()->getContents(), true);
        return new TppResponse($response_array);
    }

    public function enableMock()
    {
        $this->mock = true;
    }


}
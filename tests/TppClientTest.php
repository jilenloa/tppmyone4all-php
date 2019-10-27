<?php
/**
 * Created by PhpStorm.
 * User: macbookpro
 * Date: 10/27/19
 * Time: 9:47 PM
 */

namespace MyOne4All\Tests;


use MyOne4All\TppClient;
use MyOne4All\Exceptions\TppException;
use MyOne4All\Models\DataBundle;
use MyOne4All\NetworkCodes;
use MyOne4All\TppResponse;
use PHPUnit\Framework\TestCase;

class TppClientTest extends TestCase
{
    /** @var TppClient */
    protected $tppClient;

    protected function setUp()
    {
        parent::setUp();
        $this->tppClient = new TppClient("testkey", "testsecret", "testretailer", TppClient::COUNTRY_GHANA);
        $this->tppClient->enableMock();
    }

    public function testAirtime(){
        $response = $this->tppClient->sendAirtime("0245667942", 1, "809003324634425623", NetworkCodes::AUTO_DETECT);
        $this->assertInstanceOf(TppResponse::class, $response);
        $this->assertTrue($response->isSuccessful());
    }


    public function testAirtimeFail(){
        $response = $this->tppClient->sendAirtime("02456679", 1, "809003324634425623", NetworkCodes::AUTO_DETECT);
        $this->assertInstanceOf(TppResponse::class, $response);
        $this->assertTrue($response->failed());
        $this->assertFalse($response->isSuccessful());
        $this->assertNotEmpty($response->getErrorMessage());
        $this->assertTrue(is_string($response->getErrorMessage()));
    }

    public function testBalance(){
        $response = $this->tppClient->getBalance();
        $this->assertNotNull($response);
        $this->assertTrue(is_numeric($response));
    }

    public function testSendDataBundle(){
        $response = $this->tppClient->sendDataBundle("0245667942", "DAILY_20MB", "809003324634425624", NetworkCodes::MTN_GH);
        $this->assertInstanceOf(TppResponse::class, $response);
        $this->assertTrue($response->isSuccessful());
    }

    public function testQueryBundle(){
        $response = $this->tppClient->getDataBundleList();
        $this->assertTrue(is_array($response));
        $this->assertNotNull(current($response));
        $this->assertInstanceOf(DataBundle::class, current($response));
    }

    /**
     * @throws TppException
     */
    public function testTransactionStatusCheck(){
        $response = $this->tppClient->getTransactionStatus("809003324634425623");
        $this->assertTrue(is_string($response));
        $this->assertNotEmpty($response);
    }

    /**
     * @throws TppException
     */
    public function testTransactionNotFound(){
        $this->setExpectedException(TppException::class, 'Transaction not Found');
        $this->tppClient->getTransactionStatus("8090034634425623");
    }

    public function testC2b(){
        $response = $this->tppClient->receiveMobileMoney("0245667942", 1, "809003324634425623");
        $this->assertInstanceOf(TppResponse::class, $response);
        $this->assertTrue($response->isSuccessful());
    }

    public function testB2cMTNGhana(){
        $response = $this->tppClient->sendMobileMoney("0245667942", 1, "809003324634425623");
        $this->assertInstanceOf(TppResponse::class, $response);
        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isPending());
    }

    public function testB2cAirtelTigoGhana(){
        $response = $this->tppClient->sendMobileMoney("0572180376", 1, "809003324634425623");
        $this->assertInstanceOf(TppResponse::class, $response);
        $this->assertTrue($response->isSuccessful() || $response->isPending());
        $this->assertFalse($response->isSuccessful());
        $this->assertTrue($response->isPending());
        $this->assertFalse($response->failed());
        $this->assertNull($response->getErrorMessage());
    }

    public function testB2cFail(){
        $response = $this->tppClient->sendMobileMoney("057218037", 1, "809003324634425623");
        $this->assertInstanceOf(TppResponse::class, $response);
        $this->assertFalse($response->isSuccessful() || $response->isPending());
        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isPending());
        $this->assertNotNull($response->getErrorMessage());
        $this->assertNotEmpty($response->getErrorMessage());
        $this->assertTrue($response->failed());
    }

    public function testSms(){
        $response = $this->tppClient->sendSms("057218037", "hello world", "One4All", "sms11");
        $this->assertInstanceOf(TppResponse::class, $response);
        $this->assertTrue($response->isSuccessful());
    }

}
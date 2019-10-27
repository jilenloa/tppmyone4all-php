<?php
/**
 * Created by PhpStorm.
 * User: macbookpro
 * Date: 10/27/19
 * Time: 3:40 PM
 */

namespace MyOne4All;


class TppResponse implements \JsonSerializable
{

    protected $response_array;
    /**
     * TppResponse constructor.
     * @param mixed $response_array
     */
    public function __construct($response_array)
    {
        $this->response_array = $response_array;
    }

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return $this->response_array;
    }

    public function isSuccessful(){
        if($this->response_array['status-code'] === "00"){
            if(isset($this->response_array['pending'])){
                return !$this->response_array['pending'];
            }
            return true;
        }else{
            return false;
        }
    }

    public function isPending(){
        if(isset($this->response_array['pending'])){
            return $this->response_array['pending'];
        }
        return false;
    }

    public function getErrorMessage(){
        if($this->failed()){
            return $this->response_array['message'];
        }
        return null;
    }

    public function getMessage(){
        return $this->response_array['message'];
    }

    public function failed(){
        return !$this->isSuccessful() && !$this->isPending();
    }

    public function getBalanceBefore(){
        if(isset($this->response_array['balance_before'])){
            return $this->response_array['balance_before'];
        }
        return null;
    }

    public function getBalanceAfter(){
        if(isset($this->response_array['balance_after'])){
            return $this->response_array['balance_after'];
        }
        return null;
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: macbookpro
 * Date: 10/27/19
 * Time: 4:20 PM
 */

namespace MyOne4All\Models;


class DataBundle
{
    protected $category;
    protected $network_id;
    protected $plan_id;
    protected $plan_name;
    protected $price;
    protected $type;
    protected $validity;
    protected $volume;

    /**
     * DataBundle constructor.
     * @param array $props
     */
    public function __construct($props = array())
    {
        foreach($props as $k => $v){
            if(property_exists($this, $k)){
                $this->{$k} = $v;
            }
        }
    }


}
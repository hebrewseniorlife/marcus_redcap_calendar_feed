<?php

namespace Model;

use Ramsey\Uuid\Uuid as Uuid;

/**
 * CalendarLink - Internet-accessible link to a calendar feed
 */
class CalendarLink {    
    /**
     * key
     *
     * @var string
     */
    public $key;    
    /**
     * name
     *
     * @var string
     */
    public $name;    
    /**
     * params
     *
     * @var string
     */
    public $params;    
    /**
     * enabled
     *
     * @var boolean
     */
    public $enabled;    
    /**
     * access_level
     *
     * @var string
     */
    public $access_level;    
    /**
     * url
     *
     * @var string
     */
    public $url;

    public const DEFAULT_ACCESS_LEVEL = "public";

    public function __construct(string $name = "", string $params = "", string $key = null){
        $this->name             = $name;
        $this->params           = $params;
        $this->access_level     = CalendarLink::DEFAULT_ACCESS_LEVEL;
        $this->key              = ($key == null) ?  Uuid::uuid4()->toString() : $key;
        $this->enabled          = true;
    }
    
    /**
     * getAccessLevels
     *
     * @return string[]
     */
    public function getAccessLevels() : array {
        return [
            "public",
            "private"
        ];
    }
}
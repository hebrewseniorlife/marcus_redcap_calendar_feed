<?php

namespace Model;

use Ramsey\Uuid\Uuid as Uuid;

class CalendarLink {
    public $key;
    public $name;
    public $params;
    public $enabled;
    public $access_level;
    public $url;

    public const DEFAULT_ACCESS_LEVEL = "public";

    public function __construct(string $name = "", string $params = "", string $key = null){
        $this->name             = $name;
        $this->params           = $params;
        $this->access_level     = CalendarLink::DEFAULT_ACCESS_LEVEL;
        $this->key              = ($key == null) ?  Uuid::uuid4()->toString() : $key;
        $this->enabled          = true;
    }

    public function getAccessLevels() : array {
        return [
            "public",
            "private"
        ];
    }
}
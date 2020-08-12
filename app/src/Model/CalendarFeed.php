<?php

namespace Model;

class CalendarFeed {
    public $key;
    public $name;
    public $title_template;
    public $description_template;
    public $location_template;
    public $fields;
    public $url;

    public const DEFAULT_KEY = "default";
    public const DEFAULT_DESCRIPTION_TEMPLATE = "Record: {{ record }}, Event: {{ event_descrip }}, Forms: {{ forms|join(', ')}}{{ notes|length > 0 ? ', Notes: ' ~ notes : '' }}";
    public const DEFAULT_TITLE_TEMPLATE = "{{ record }} @ {{ event_descrip }}{{ arm_name|length > 0 ? ' (' ~ arm_name ~ ')' : '' }}";
    public const DEFAULT_LOCATION_TEMPLATE = "N/A";

    public function __construct(string $key = CalendarFeed::DEFAULT_KEY, string $name = "Default")
    {
        $this->key = $key;
        $this->name = $name;
        $this->title_template = CalendarFeed::DEFAULT_TITLE_TEMPLATE;
        $this->description_template = CalendarFeed::DEFAULT_DESCRIPTION_TEMPLATE;
        $this->location_template = CalendarFeed::DEFAULT_LOCATION_TEMPLATE;
    }

    public function setTemplates(string $titleTemplate = null, string $descriptionTemplate = null, string $locationTemplate = null){
        if (isset($titleTemplate)){
            $this->title_template = $titleTemplate;
        }

        $this->description_template = (!empty($descriptionTemplate)) ? $descriptionTemplate : "";
        $this->location_template    = (!empty($locationTemplate)) ? $locationTemplate : "";
    }
}
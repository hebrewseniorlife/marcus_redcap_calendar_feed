<?php

namespace Model;

/**
 * CalendarFeed  - Basic attributes that correspond to a feed (iCal, CSV, etc.) of calendar data 
 */
class CalendarFeed {    
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
     * title_template
     *
     * @var string
     */
    public $title_template;    
    /**
     * description_template
     *
     * @var string
     */
    public $description_template;    
    /**
     * location_template
     *
     * @var string
     */
    public $location_template;    
    /**
     * data_fields
     *
     * @var string[]
     */
    public $data_fields;    
    /**
     * filter_fields
     *
     * @var string[]
     */
    public $filter_fields;    
    /**
     * url
     *
     * @var string
     */
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

        $this->data_fields   = [];
        $this->filter_fields = [];
    }
    
    /**
     * setTemplates
     *
     * @param  string $titleTemplate
     * @param  string $descriptionTemplate
     * @param  string $locationTemplate
     * @return void
     */
    public function setTemplates(string $titleTemplate = null, string $descriptionTemplate = null, string $locationTemplate = null){
        if (isset($titleTemplate)){
            $this->title_template = $titleTemplate;
        }

        $this->description_template = (!empty($descriptionTemplate)) ? $descriptionTemplate : "";
        $this->location_template    = (!empty($locationTemplate)) ? $locationTemplate : "";

        return $this;
    }
    
    /**
     * setDataFields
     *
     * @param  string $fields
     * @return void
     */
    public function setDataFields(? string $fields = ""){
        if (strlen($fields) > 0){
            $this->data_fields = explode(',', $fields);
        }

        return $this;
    }
}
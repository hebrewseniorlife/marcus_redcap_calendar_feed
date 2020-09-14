<?php

namespace Model;

/**
 * StudyEvent
 */
class StudyEvent {   
    /**
     * event_id
     *
     * @var int
     */
    public $event_id;    
    /**
     * unique_name
     *
     * @var string
     */
    public $unique_name;
    
    /**
     * name
     *
     * @var string
     */
    public $name;

    public function __construct(int $event_id, string $name, string $unique_name)
    {
        $this->event_id = $event_id;
        $this->name = $name;
        $this->unique_name = $unique_name;
    }
}


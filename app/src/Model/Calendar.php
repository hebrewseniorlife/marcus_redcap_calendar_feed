<?php

namespace Model;

use Model\CalendarItem as CalendarItem;

/**
 * Calendar  
 */
class Calendar{    
    /**
     * filter
     *
     * @var CalendarFeed
     */
    public $filter;    
    /**
     * items
     *
     * @var CalendarItem[]
     */
    public $items = [];
        
    /**
     * getItems
     *
     * @return CalendarItem[]
     */
    public function getItems(){
        return $this->items;
    }
    
    /**
     * addItem
     *
     * @param  CalendarItem $item
     * @return void
     */
    public function addItem(CalendarItem $item){
        array_push($this->items, $item);    
    }
        
    /**
     * setItems
     *
     * @param  array $items
     * @return void
     */
    public function setItems(array $items){
        if (count($items) > 0){
            $this->items = $items;
        }
    }
        
    /**
     * __construct
     *
     * @param  CalendarFilter $filter
     * @return void
     */
    public function __construct($filter){
        $this->filter = $filter;
    }
}
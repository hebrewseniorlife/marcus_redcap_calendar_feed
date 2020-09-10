<?php

/**
 * SystemDate
 * 
 * Utility class for DateTime operations
 */
class SystemDate{
    
    /**
     * getMonths - generates a list of named months by a zero-based index
     *
     * @return array
     */
    public static function getMonths() : array {
        return array(
                1 => "January"
                , 2 => "February"
                , 3 => "March"
                , 4 => "April"
                , 5 => "May"
                , 6 => "June"
                , 7 => "July"
                , 8 => "August"
                , 9 => "September"
                , 10 => "October"
                , 11 => "November"
                , 12 => "December");
    }
        
    
    /**
     * getCurrentMonth - Returns the current named month
     *
     * @return string
     */
    public static function getCurrentMonth() : string {
        return date("n");
    }
        
    /**
     * getYearRange - returns an array of years (within range) based on the current year
     *
     * @param  mixed $start
     * @param  mixed $stop
     * @param  mixed $bounds
     * @return array
     */
    public static function getYearRange(string $start = null, string $stop = null, int $bounds = 10) : array {
        $currentYear = SystemDate::getCurrentYear();

        if (!isset($start, $stop)){
            $start = $currentYear - $bounds;
            $stop  = $currentYear + $bounds;
        }
        
        $years = array();
        for ($i = $start; $i <= $stop; $i++){
            array_push($years, $i);
        }
        
        return $years;
    }
    
    /**
     * getCurrentYear - Returns the current named year
     *
     * @return string
     */
    public static function getCurrentYear() : string {
        return date("Y");
    }
}
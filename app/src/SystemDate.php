<?php

class SystemDate{
    public static function getMonths(){
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
    
    public static function getCurrentMonth(){
        return date("n");
    }
    
    public static function getYearRange(string $start = null, string $stop = null, int $bounds = 10){
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

    public static function getCurrentYear(){
        return date("Y");
    }
}
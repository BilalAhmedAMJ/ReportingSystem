<?php
/**
 *
 */
namespace Application\Entity;
 

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

use JMS\Serializer\Annotation\Exclude;

use DateTime;

 /**
 * Months array to eas in conversions etc.
 * @Exclude
 */     
Period::$months=array( 'Jan'=>'January','Feb'=>'February','Mar'=>'March','Apr'=>'April','May'=>'May','Jun'=>'June',
                             'Jul'=>'July','Aug'=>'August','Sep'=>'September','Oct'=>'October','Nov'=>'November','Dec'=>'December');    

/**
 * A Period represents smallest time period that we operate with,
 * on a celendar it represents a month and is identified by a 
 * three leter month and a 4 digit year. A month period in Jamaat will
 * be associated with a Jamaat Year code that may or may not allign with
 * a calendar year. Hence we have a year_code property that represents 
 * Jamaat year that include current Period.
 *
 * @ORM\Entity(readOnly=true)
 * @ORM\Table(name="periods")
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 *
 * @author Haroon
 */ 
class Period 
{
    
    
    /**
     * We assume first period was after year 2000
     */
    const START_YEAR=2000;
    
    const START_PERIOD = '2000-01-01';
    /**
     * Default Year start aligns with Jamaat year start, ie.e 1st July
     */
    const DEFAULT_YEAR_START='Jul';

    const ID_DELIM='-';
    
    const JAN='Jan';

    /**
     * Months array to ease in conversions etc.
     * @Exclude
     */     
    public static $months;
    
    /**
     * @var string
     * @ORM\Id
     * @ORM\Column(type="string", length=128)
     */
    protected $period_code;
    
    /**
     * Set period_code
     *
     * @param string 
     */
    public function setPeriodCode($period_code)
    {
        $this->period_code=$period_code;
        return $this;
    }


    /**
     * Get period_code
     *
     * @return string 
     */
    public function getPeriodCode()
    {
        return $this->period_code;
    }

    
 
     /**
     * Jamaat year to which current period belongs. This is a ID_DELIM separated set of 4 dig years,
      * unless year alligns with Calendar Year in that case it is a 4 digit year
     *
     * @var srtring
     * @ORM\Column(type="string", length=128)
     */
     protected $year_code;
     
    /**
     * Set year_code
     *
     * @param string 
     */
    public function setYearCode($year_code)
    {
        $this->year_code=$year_code;
        return $this;
    }


    /**
     * Get year_code
     *
     * @return string 
     */
    public function getYearCode()
    {
        return $this->year_code;
    }
     
 
     /**
     * Jamaat year to which current period belongs. This is a ID_DELIM separated set of 4 dig years,
      * unless year alligns with Calendar Year in that case it is a 4 digit year
     *
     * @var boolean
     * @ORM\Column(type="boolean")
     */
    protected $display_ui;
     
    /**
     * Set year_code
     *
     * @param boolean 
     */
    public function setDisplayUi($display_ui)
    {
        $this->display_ui=$display_ui;
        return $this;
    }


    /**
     * Get year_code
     *
     * @return boolean 
     */
    public function getDisplayUi()
    {
        return $this->display_ui;
    }
     
 



     public function getCalendarYear(){
         if(!$this->isPeriodCodeValid())
            return null;
         
         return $this->parseYear();
     }    

     private function parseYear(){
         return substr($this->period_code,4,4);
     }
     
     private function parseMonth(){
         return substr($this->period_code,0,3);
     }

    /**
     *  @var datetime
     *  @ORM\Column(type="datetime")
     */
     protected $period_start;
     public function getPeriodStart(){
        if($this->period_start === null){
            $this->setPeriodStart();
         }        
        return $this->period_start;
     }

     protected function setPeriodStart($start=null){
        if($start === null){
            #print_r($this->getCalendarMonth()." 01, ".$this->getCalendarYear());
            $this->period_start=new \DateTime($this->getCalendarMonth()." 01, ".$this->getCalendarYear());
         }else if($start instanceof \DateTime){
             $this->period_start=($start);
         }else{//try to parse given datetime
             $this->period_start=new \DateTime($start);
         }
     }
     
     protected function setPeriodEnd($end=null){
        if($end === null){
            $this->period_end= clone $this->getPeriodStart();
            $this->period_end->modify('+1 month')->modify('-1 sec');
         }else if($end instanceof \DateTime){
             $this->period_start=($end);
         }else{//try to parse given datetime
             $this->period_start=new \DateTime($end);
         }
     }

    /**
     *  @var datetime
     *  @ORM\Column(type="datetime")
     */
     protected $period_end;
     public function getPeriodEnd(){
        if($this->period_end === null){
            $this->period_end= clone $this->getPeriodStart();
            $this->period_end->modify('+1 month')->modify('-1 sec');
         }
        return $this->period_end;
     }


     public function getCalendarMonth(){
         if(!$this->isPeriodCodeValid())
            return null;
         
         return $this->parseMonth();
     }    
     
     /**
      * @return true if current id is valid otherwise false
      */
     public function isPeriodCodeValid(){

         
        $valid = ($this->period_code!==null && !empty($this->period_code));//assume it is valid as long as it is not empty

        $valid = $valid && (strlen($this->period_code)==8);//and have expected length
        
        $valid = $valid && key_exists(substr($this->period_code, 0,3), self::$months);

        $valid = $valid && ($this->parseYear() > $this::START_YEAR);

        #if(!key_exists(substr($this->period_code."", 0,3), self::$months))
        
        return $valid;
     }
     
     
     // public function getLastPeriodOfYear($year_start=Period::DEFAULT_YEAR_START)
     // {
         // $year_code=$this->getYearCode();
         // if(strlen($year_code)>4)
             // $end_year=substr($year_code, 4,4);//year code formate is xxxx or xxxx-xxxx
         // else
             // $end_year=$year_code;
         // $last_period_date=new \DateTime("$year_start 01, $end_year");
         // $last_period_date->modify('-1 month');
         // return $this::create($last_period_date->format('M-Y'),$year_start);
     // }
     
     /**
      *
      *  
      * @param id A valid id that consists of three letter month name in title case, followed by - 
      *           and a four digit year 
      * @param obj A Year object that will be populated to given id, if not provided a new obj is created
      * @param yearStartMonth start month for the year_code to be used for this period, 
      *        it shuld be a three letter month name in title case
      *        if not provided default is assumed
      * 
      */
     static public function create($period_code,Period &$obj=null,$yearStartMonth=Period::DEFAULT_YEAR_START){
         if($obj!==null && !is_object($obj)){
             throw new \Exception("Invalid Parameter given, expected either null or valid Year obj ".var_export($obj,true));
         }
         if($obj===null){
             $obj=new Period();
         }
         
         #set id and validate 
         $obj->setPeriodCode($period_code);
         if(! $obj->isPeriodCodeValid()){
             throw new \Exception("Invalid Parameter given, period_code must be a 3 letter month and 4 digit year separated with -, given was [$period_code] ");
         }
         
         #set Jamaat year as needed
         $given_year=$obj->getCalendarYear();//calendar year for the given period
         $period_start=DateTime::createFromFormat("d-M-Y","01".Period::ID_DELIM.$obj->getCalendarMonth().Period::ID_DELIM."$given_year");//first day of the period as date
         
         $year_start=Period::getYearStart($given_year, $yearStartMonth);//first day of the year_code as date

         $year_code=$given_year;
         
         if($yearStartMonth!==Period::JAN && $period_start >= $year_start){
             $year_code=$given_year.Period::ID_DELIM.($year_code+1);
         }
         if($yearStartMonth!==Period::JAN && $period_start < $year_start){
             $year_code=($given_year-1).Period::ID_DELIM.($given_year);
         }
         $obj->setYearCode($year_code);
         //set period_start and period_end to default values
         $obj->setPeriodStart();
         $obj->setPeriodEnd();
         
         return $obj;
     }

     public static function createCurrent(){
         $now = new DateTime();         
         return Period::create($now->format('M-Y'));
     }
     
     public static function createFromDate($date){
         return Period::create($date->format('M-Y'));
     }

     public static function createLast(){
         $month = new DateTime();
         $month->modify('-1 month');    
         return Period::create($month->format('M-Y'));
     }
     
     
     private static function getYearStart($given_year,$yearStartMonth){
         return DateTime::createFromFormat("d-M-Y","01".Period::ID_DELIM.$yearStartMonth.Period::ID_DELIM."$given_year");
     }

     /**
      * 
      * 
      * @param years_after years to add to this period
      * @param yeatStartMonth starting month for the year to use
      * @return return pariod that represents month that marks end of year after adding years_after to this period
      * 
      */
     public function addYears($years_after,$yearStartMonth=Period::DEFAULT_YEAR_START){
        $years=explode("-",$this->getYearCode());
        if($this->getCalendarYear()==$years[1]){//we are in second year deduct 1 from years_after
            $years_after--;
        } 
        $target_year_period_start_date = Period::getYearStart($this->getCalendarYear()+$years_after,$yearStartMonth);
        $target_year_period_start_date->modify('-1 month');
        
        return Period::create($target_year_period_start_date->format('M-Y'));
     }
     
     public function addMonths($months){
         
         $start_date=clone $this->getPeriodStart();
         $start_date->modify($months.' month');
         
         return static::create($start_date->format('M-Y'));
     }
     
     public function getLastPeriod($year_start=Period::DEFAULT_YEAR_START){
         throw new \Exception('Not imlemented yet,');
     }
     
     public function __toString(){
         return $this->getPeriodCode();
     }
     
     public function getMonthsList(){
             
         return Period::$months;
         
     }

    
    public static function getYearFromPeriod($period){

        if(is_string($period)){
            $period = Period::create($period);            
        }

        $period_code = $period->__toString();
        $year = $period->getCalendarYear();
        
        $year_start = Period::getStartOfYear($year);
        
        if($year_start > $period->getPeriodStart()){
            $year = $year-1;
        }
        return $year;
    }

    public static function  getStartOfYear($year){

        $month = '-07-01'; //Fix it 
        
        return new \DateTime($year.$month);
    }
    
    
    public static function  getEndOfYear($year){

        $month = '-06-30'; //Fix it //TODO FXME
        $year++;//end date is next year
        return new \DateTime($year.$month);
    }
    


    public static function  getTermStart($term){
        $date = getdate();
        $month = '-07-01'; //Fix it //TODO FXME
        $years = explode('-',$term);
        return new \DateTime($years[0].$month);
    }
    
    
    public static function  getTermEnd($term){
        $date = getdate();
        $month = '-06-30'; //Fix it //TODO FXME
        $years = explode('-',$term);
        return new \DateTime($years[1].$month);
    }
    
    public static function  getTermStartCurrent(){
        if( date('m') > 6 ) {
            return date('Y');
        }
        else {
            return date('Y') - 1;
        }
    }
    
    
}

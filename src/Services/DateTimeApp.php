<?php


namespace App\Services;


use DateTimeZone;

class DateTimeApp extends \DateTime
{
    const STR_DATE_FORMAT='Ymd';
    const STR_DATETIME_FORMAT='YmdHis';

    private $jours_feries;


    private function get_easter_datetime() {
        $base = new self(sprintf("%s-03-21", $this->format('Y')));
        $days = easter_days($this->format('Y'));

        return $base->add(new \DateInterval("P{$days}D"));
    }
    protected function setJoursFeries(){
        $this->jours_feries=['01-05','08-05','14-07','15-08','01-11','25-12','31-12'];
        $paques=$this->get_easter_datetime(); //Dim de Paques
        $this->jours_feries[]=$paques->add(new \DateInterval('P1D'))->format('d-m');
        $this->jours_feries[]=$paques->add(new \DateInterval('P38D'))->format('d-m');
        $this->jours_feries[]=$paques->add(new \DateInterval('P11D'))->format('d-m');
    }
    /**
     * @return string[]
     */
    public function getJoursFeries(): array
    {
        return $this->jours_feries;
    }

    public function toStrDate()
    {
        return $this->format(self::STR_DATE_FORMAT);
    }
    public function toStrDateTime()
    {
        return $this->format(self::STR_DATETIME_FORMAT);
    }


    public function addWorkingDays($n){
        $this->setJoursFeries();
        $i=0;
        while($i<$n){
            $this->add(new \DateInterval('P1D'));
            if($this->format('N')==6||$this->format('N')==7||in_array($this->format("d-m"), $this->jours_feries)){
                continue;
            }
            $i++;
        }
        return $this;
    }

}
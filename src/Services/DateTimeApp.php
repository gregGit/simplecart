<?php


namespace App\Services;


trait DateTimeUtilTrait
{
    public function currentDateTime()
    {
        return new \DateTime();
    }
    public function currentDateTimeStr($format='YmdHis'){
        return $this->dateTimeToStr($this->currentDateTime(), $format);
    }
    public function dateTimeToStr(\DateTime $dateTime, $format='YmdHis')
    {
        return $dateTime->format($format);
    }

}
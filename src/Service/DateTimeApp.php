<?php


namespace App\Service;

use DateInterval;
use DateTime;
use DateTimeZone;
use Exception;

class DateTimeApp extends DateTime
{
    const STR_DATE_FORMAT='Ymd'; //format par défaut pour l'écriture d'une date en chaine
    const STR_DATETIME_FORMAT='YmdHis';//format par défaut pour l'écriture d'une date/heure en chaine

    private $joursFeries; //Contient la liste des jours fériés sous la forme jj-mm

    public function __construct($datetime = 'now', DateTimeZone $timezone = null)
    {
        parent::__construct($datetime, $timezone);
    }


    /**
     * Détermine les jours fériés
     * Ajoute les jours passés en arguments + ceux à date variable(paques, ascencion pentecote)
     * @throws Exception
     */
    public function setJoursFeries(array $fixedDays=[]){
        $base = new parent(sprintf("%s-03-21", $this->format('Y')), $this->getTimezone());
        $days = easter_days($this->format('Y'));
        $base->add(new DateInterval("P{$days}D"));//Dim de paques
        $this->joursFeries[]=$base->add(new DateInterval('P1D'))->format('d-m');//Lundi de paques
        $this->joursFeries[]=$base->add(new DateInterval('P38D'))->format('d-m');//Ascension
        $this->joursFeries[]=$base->add(new DateInterval('P11D'))->format('d-m');//Pentecote
    }
    /**
     * @return string[]
     */
    public function getJoursFeries(): array
    {
        return $this->joursFeries;
    }

    public function toStrDate()
    {
        return $this->format(self::STR_DATE_FORMAT);
    }
    public function toStrDateTime()
    {
        return $this->format(self::STR_DATETIME_FORMAT);
    }


    /**
     * Ajoute $n jours ouvrés
     * Les Samedis, dimanches et jours fériés ne sont pas comptés
     * @param $n
     * @return $this
     */
    public function addWorkingDays($n){
        $i=0;
        while($i<$n){
            $this->add(new DateInterval('P1D'));
            if($this->estDimanche() || $this->estSamedi() ||$this->estFerie()){
                continue;
            }
            $i++;
        }
        return $this;
    }

    protected function estSamedi(){
        return $this->format('N')==6;
    }
    protected function estDimanche(){
        return $this->format('N')==7;
    }
    protected function estFerie(){
        return in_array($this->format("d-m"), $this->joursFeries);
    }
}
<?php

namespace Database\Object;

use Ouzo\Utilities\Clock;
use Database\Repository\School;
use Database\Interface\CustomObject;
use Helpers\Mapping;
use Helpers\CString;

class TemperatureRegistration extends CustomObject
{
    protected $objectAttributes = [
        "id",
        "schoolId",
        "creationDateTime",
        "person",
        "soupTemp",
        "potatoRicePastaTemp",
        "vegetablesTemp",
        "meatFishTemp",
        "description",
        "deleted"
    ];

    public function init()
    {
        $this->dayNumber = Clock::at($this->creationDateTime)->format("w");
        $this->day = Mapping::get("general/days/{$this->dayNumber}");
        $this->date = Clock::at($this->creationDateTime)->format("d-m-Y");
        $this->time = Clock::at($this->creationDateTime)->format("H:i:s");
        $this->personInitials = CString::firstLetterOfEachWord($this->person);
        $this->personWithInitials = "{$this->person} ({$this->personInitials})";

        $this->soupTempColor = !empty($this->soupTemp) ? ($this->soupTemp > 64 ? 'green' : ($this->soupTemp > 59 ? 'orange' : 'red')) : "transparent";
        $this->potatoRicePastaTempColor = !empty($this->potatoRicePastaTemp) ? ($this->potatoRicePastaTemp > 64 ? 'green' : ($this->potatoRicePastaTemp > 59 ? 'orange' : 'red')) : "transparent";
        $this->vegetablesTempColor = !empty($this->vegetablesTemp) ? ($this->vegetablesTemp < 6 || $this->vegetablesTemp > 64 ? 'green' : ($this->vegetablesTemp < 8 || $this->vegetablesTemp > 59 ? 'orange' : 'red')) : 'transparent';
        $this->meatFishTempColor = !empty($this->meatFishTemp) ? ($this->meatFishTemp > 64 ? 'green' : ($this->meatFishTemp > 59 ? 'orange' : 'red')) : "transparent";

        $this->_orderfield = $this->creationDateTime;
    }

    public function link()
    {
        $this->school = ($this->schoolId == 0 ? false : (new School)->get($this->schoolId)[0]);
    }
}

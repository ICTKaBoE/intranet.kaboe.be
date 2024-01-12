<?php

namespace Database\Object;

use Helpers\Mapping;
use Ouzo\Utilities\Clock;
use Database\Repository\School;
use Database\Interface\CustomObject;

class Reservation extends CustomObject
{
	protected $objectAttributes = [
		"id",
		"userId",
        "schoolId",
        "start",
        "end",
        "type",
        "assetId",
        "deleted"
	];

    public function init()
    {
        $this->asset?->link()?->init();
        switch ($this->type) {
            case "R":
                $this->extraInfo = "Aantal lokalen: ";
                break;
            case "L":
                $this->extraInfo = "Aantal laptops: ";
                break;
            case "D":
                $this->extraInfo = "Aantal desktops: ";
                break;
            case "I":
                $this->extraInfo = "Aantal ipads: ";
                break;
            case "LK":
                $this->extraInfo = "Aantal laptopkarren: ";
                break;
            case "IK":
                $this->extraInfo = "Aantal ipadkarren: ";
                break;
        }

        $this->typeBackgroundColor = Mapping::get("reservation/type/{$this->type}/color");
        $this->startTime = Clock::at($this->start)->format("H:i:s");
        $this->endTime = Clock::at($this->end)->format("H:i:s");
        $this->startDate = Clock::at($this->start)->format("Y-m-d");
        $this->endDate = Clock::at($this->end)->format("Y-m-d");
    }
    
    public function link()
	{
		$this->school = ($this->schoolId == 0 ? false : (new School)->get($this->schoolId)[0]);
        return $this;
    }
}

<?php

namespace Database\Object\Violence;

use stdClass;
use Ouzo\Utilities\Clock;
use Security\CustomObject;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;

class Violence extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_INTEGER,
        "guid" => self::TYPE_GUID,
        "creationDateTime" => self::TYPE_STRING,
        "creatorUserId" => self::TYPE_INTEGER,
        "schoolId" => self::TYPE_INTEGER,
        "anonymous" => self::TYPE_BOOLEAN,
        "factsDate" => self::TYPE_DATE,
        "identityParty" => self::TYPE_STRING,
        "ageParty" => self::TYPE_INTEGER,
        "workingHours" => self::TYPE_BOOLEAN,
        "form" => self::TYPE_STRING,
        "formOther" => self::TYPE_STRING,
        "out" => self::TYPE_STRING,
        "outOther" => self::TYPE_STRING,
        "intention" => self::TYPE_STRING,
        "intentionOther" => self::TYPE_STRING,
        "consequence" => self::TYPE_STRING,
        "cause" => self::TYPE_STRING,
        "causeOther" => self::TYPE_STRING,
        "damage" => self::TYPE_STRING,
        "damageKind" => self::TYPE_STRING,
        "police" => self::TYPE_BOOLEAN,
        "actionsTaken" => self::TYPE_STRING,
        "proposalEmployer" => self::TYPE_STRING,
        "proposalConfidant" => self::TYPE_STRING,
        "proposalPapsy" => self::TYPE_STRING,
        "proposalHead" => self::TYPE_STRING,
        "deleted" => self::TYPE_BOOLEAN
    ];

    protected $linkedAttributes = [
        "school" => ["schoolId" => \Database\Repository\School\School::class],
        "victim" => ["victimId" => \Database\Repository\Informat\Employee::class],
        "form" => ["form" => \Database\Repository\Violence\Form::class],
        "out" => ["out" => \Database\Repository\Violence\Out::class],
        "intention" => ["intention" => \Database\Repository\Violence\Intention::class],
        "consequence" => ["consequence" => \Database\Repository\Violence\Consequence::class],
        "cause" => ["cause" => \Database\Repository\Violence\Cause::class],
        "damage" => ["damage" => \Database\Repository\Violence\Damage::class],
        "damageKind" => ["damageKind" => \Database\Repository\Violence\DamageKind::class],
    ];

    public function init()
    {
        $this->formatted->creationDateTime = new stdClass;
        $this->formatted->creationDateTime->display = Clock::at($this->creationDateTime)->format("d/m/Y H:i:s");
        $this->formatted->creationDateTime->sort = Clock::at($this->creationDateTime)->format("U");

        $this->formatted->factsDate = new stdClass;
        $this->formatted->factsDate->display = Clock::at($this->factsDate)->format("d/m/Y");
        $this->formatted->factsDate->sort = Clock::at($this->factsDate)->format("U");

        if (!is_array($this->linked->form)) $this->linked->form = [$this->linked->form];
        if (!is_array($this->linked->out)) $this->linked->out = [$this->linked->out];
        if (!is_array($this->linked->intention)) $this->linked->intention = [$this->linked->intention];
        if (!is_array($this->linked->cause)) $this->linked->cause = [$this->linked->cause];
        if (!is_array($this->linked->damage)) $this->linked->damage = [$this->linked->damage];
        if (!is_array($this->linked->damageKind)) $this->linked->damageKind = [$this->linked->damageKind];

        $this->formatted->victim = $this->anonymous ? "Anoniem" : $this->linked->victim->formatted->fullNameReversed;
        $this->formatted->party = $this->identityParty ? "{$this->identityParty} ({$this->ageParty} jaar)" : "";
        $this->formatted->workingHours = $this->workingHours ? "Ja" : "Nee";
        $this->formatted->form = $this->linked->form ? implode("<br />", Arrays::map($this->linked->form, fn($i) => Strings::equal($i->id, "O") ? $this->formOther : $i->name)) : "";
        $this->formatted->out = $this->linked->out ? implode("<br />", Arrays::map($this->linked->out, fn($i) => Strings::equal($i->id, "O") ? $this->outOther : $i->name)) : "";
        $this->formatted->intention = $this->linked->intention ? implode("<br />", Arrays::map($this->linked->intention, fn($i) => Strings::equal($i->id, "O") ? $this->intentionOther : $i->name)) : "";
        $this->formatted->cause = $this->linked->cause ? implode("<br />", Arrays::map($this->linked->cause, fn($i) => Strings::equal($i->id, "O") ? $this->causeOther : $i->name)) : "";
        $this->formatted->damage = $this->linked->damage ? implode("<br />", Arrays::map($this->linked->damage, fn($i) => $i->name)) : "";
        $this->formatted->damageKind = $this->linked->damageKind ? implode("<br />", Arrays::map($this->linked->damageKind, fn($i) => $i->name)) : "";
        $this->formatted->police = $this->police ? "Ja" : "Nee";
    }
}

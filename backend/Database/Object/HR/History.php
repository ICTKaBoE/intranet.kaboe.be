<?php

namespace Database\Object\HR;

use Ouzo\Utilities\Clock;
use Ouzo\Utilities\Strings;
use Security\CustomObject;
use stdClass;

class History extends CustomObject
{

    protected $objectAttributes = [
        "id" => self::TYPE_INTEGER,
        "hrId" => self::TYPE_INTEGER,
        "action" => self::TYPE_STRING,
        "data" => self::TYPE_JSON,
        "datetime" => self::TYPE_DATETIME,
        "creatorUserId" => self::TYPE_INTEGER,
    ];

    protected $linkedAttributes = [
        "creatorUser" => ["creatorUserId" => \Database\Repository\User\User::class],
    ];

    public function init()
    {
        $this->formatted->datetime = new stdClass;
        $this->formatted->datetime->display = Clock::at($this->datetime)->format("d/m/Y H:i");
        $this->formatted->datetime->sort = Clock::at($this->datetime)->format("U");

        $this->formatted->action = Strings::equal($this->action, "CREATE") ? "Aanmaken" : (Strings::equal($this->action, "UPDATE") ? "Wijzigen" : "Verwijderen");
        $this->formatted->changedData = "";

        if ($this->data) {
            $this->changedData = (new HR($this->data));

            foreach ($this->data as $key => $value) {
                $this->formatted->changedData .= "<p class='mb-1'><b>" . HR::PARAMETERS[$key] . "</b><br />" . (($this->changedData->formatted->$key instanceof stdClass ? $this->changedData->formatted->$key->display : $this->changedData->formatted->$key) ?: $value) . "</p>";
                
            }
        }
    }
}

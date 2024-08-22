<?php

namespace Controllers\COMPONENT;

use Router\Helpers;
use Database\Repository\Module;
use Controllers\ComponentController;
use Database\Repository\GeneralMessage;
use Ouzo\Utilities\Arrays;

class GeneralMessageComponentController extends ComponentController
{
    private const TEMPLATE_MESSAGE = '<div class="mb-1 alert alert-important alert-{{message:alertType}}" role="alert">
                                        <div class="d-flex">
                                            <div class="me-3">
                                                <i class="icon ti ti-{{message:alertIcon}}"></i>
                                            </div>
                                            <div>
                                                {{message:content}}
                                            </div>
                                        </div>
                                    </div>
                                    {{messages:layout}}';

    public function __construct()
    {
        parent::__construct('generalMessage');
        $this->writeLayout();
    }

    private function writeLayout()
    {
        // $module = (new Module)->getByModule(Helpers::getModule());
        // $messages = (new GeneralMessage)->getByModuleId($module->id);

        // $messages = Arrays::filter($messages, fn ($m) => $m->show);

        // if (empty($messages)) $this->layout = str_replace("{{messages:style:display}}", "d-none", $this->layout);
        if (empty([])) $this->layout = str_replace("{{messages:style:display}}", "d-none", $this->layout);

        // foreach ($messages as $message) {
        //     $templateMessage = self::TEMPLATE_MESSAGE;

        //     foreach ($message->toArray() as $key => $value) $templateMessage = str_replace("{{message:{$key}}}", $value, $templateMessage);

        //     $this->layout = str_replace("{{messages:layout}}", $templateMessage, $this->layout);
        // }
    }
}

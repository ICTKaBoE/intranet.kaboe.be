<?php

namespace Controllers\COMPONENT;

use Router\Helpers;
use Security\Session;
use Ouzo\Utilities\Arrays;
use Database\Repository\Module;
use Database\Repository\Navigation\Navigation;
use Database\Repository\Route\Group;
use Controllers\ComponentController;
use Database\Repository\General\Message;

class GeneralMessageComponentController extends ComponentController
{
    private const TEMPLATE_MESSAGE = '<div class="mb-1 alert alert-important alert-{{message:formatted.type}}" role="alert">
                                        <div class="d-flex">
                                            <div class="me-3">
                                                <i class="icon ti ti-{{message:formatted.typeIcon}}"></i>
                                            </div>
                                            <div>
                                                {{message:content}}
                                            </div>
                                        </div>
                                    </div>
                                    {{messages:layout}}';

    public function __construct($arguments = [])
    {
        parent::__construct('generalMessage', $arguments);
        $this->writeLayout();
    }

    private function writeLayout()
    {
        $repo = new Navigation;
        $domain = Helpers::url()->getHost();
        $module = $repo->getByLinkAndType(Helpers::getModule(), "M");

        $messages = (new Message)->getByNavigationId($module->id ?: 0);
        $messages = Arrays::filter($messages, fn($m) => $m->show);

        if (empty($messages)) $this->layout = str_replace("{{messages:style:display}}", "d-none", $this->layout);

        foreach ($messages as $message) {
            $templateMessage = self::TEMPLATE_MESSAGE;

            foreach ($message->toArray(true) as $key => $value) $templateMessage = str_replace("{{message:{$key}}}", $value, $templateMessage);

            $this->layout = str_replace("{{messages:layout}}", $templateMessage, $this->layout);
        }
    }
}

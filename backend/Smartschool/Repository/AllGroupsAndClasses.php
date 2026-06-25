<?php

namespace Smartschool\Repository;

use Smartschool\Interface\Repository;

class AllGroupsAndClasses extends Repository
{
    public function __construct($sourceId)
    {
        parent::__construct($sourceId, "getAllGroupsAndClasses", \Smartschool\Object\AllGroupsAndClasses::class, [self::OUTPUT_BASE64, self::OUTPUT_XML, self::OUTPUT_EXTRACT]);
    }

    protected function extract($node, &$result)
    {
        if (!is_array($node)) return;

        // Als dit een groep is
        if (isset($node['name'])) {
            $path[] = $node['name'];

            $copy = $node;
            unset($copy['children']);
            $copy['path'] = implode(' > ', $path);

            $result[] = $copy;
        }

        // Kinderen verwerken
        if (isset($node['children']['group'])) {
            $children = $node['children']['group'];

            if (isset($children['name'])) $this->extract($children, $result);
            else {
                foreach ($children as $child) $this->extract($child, $result);
            }
        }
    }
}

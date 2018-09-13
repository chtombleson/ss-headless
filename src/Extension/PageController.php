<?php

namespace Chtombleson\SSHeadless\Extension;

use SilverStripe\Core\Extension;
use Chtombleson\SSHeadless\Output\Factory;

class PageController extends Extension
{
    public function StaticJSONUrl()
    {
        return Factory::getInstance()->getAbsoluteUrl($this->owner->GUID);
    }

    public function JSON()
    {
        return $this->owner->JSON ?: null;
    }
}

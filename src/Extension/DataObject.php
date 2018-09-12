<?php

namespace Chtombleson\SSHeadless\Extension;

use Ramsey\Uuid\Uuid;
use SilverStripe\Core\Config\Config;
use SilverStripe\ORM\DataExtension;
use SilverStripe\Versioned\Versioned;
use SilverStripe\Core\Config\Configurable;
use SilverStripe\View\Parsers\ShortcodeParser;
use Chtombleson\SSHeadless\Output\Factory;

class DataObject extends DataExtension implements \JsonSerializable
{
    use Configurable;

    private static $db = [
        'GUID' => 'Varchar(100)',
    ];

    public function onBeforeWrite()
    {
        parent::onBeforeWrite();

        if (!$this->owner->GUID) {
            try {
                $uuid = Uuid::uuid4();
                $this->owner->GUID = $uuid->toString();
            } catch (\Exception $e) {}
        }
    }

    public function onAfterWrite()
    {
        parent::onAfterWrite();
        Factory::getInstance()->write($this->owner->GUID, $this->owner->jsonSerialize());
    }

    public function onAfterDelete()
    {
        parent::onAfterDelete();
        Factory::getInstance()->remove($this->owner->GUID);
    }

    public function onAfterPublish()
    {
        parent::onAfterPublish();
        Factory::getInstance()->write($this->owner->GUID, $this->owner->jsonSerialize());
    }

    public function jsonSerialize()
    {
        $jsonFields = $this->config()->get('default_json_fields');
        $extraJsonFields = Config::inst()->get($this->owner->ClassName, 'json_fields');

        if (!empty($extraJsonFields)) {
            $jsonFields = array_merge_recursive($jsonFields, $extraJsonFields);
        }

        $data = [];

        foreach ($jsonFields as $field) {
            $dbField = $this->owner->dbObject($field['column']);
            $className = get_class($dbField);

            switch ($className) {
                case 'SilverStripe\ORM\FieldType\DBDatetime':
                    $data[$field['key']] = $this->owner->{$field['column']}
                        ? date(\DateTime::ISO8601, strtotime($this->owner->{$field['column']})) : null;
                    break;

                case 'SilverStripe\ORM\FieldType\DBBoolean':
                    $data[$field['key']] = (bool) $this->owner->{$field['column']};
                    break;

                case 'SilverStripe\ORM\FieldType\DBHTMLText':
                case 'SilverStripe\ORM\FieldType\DBHTMLVarchar':
                    $data[$field['key']] = ShortcodeParser::get_active()->parse($this->owner->{$field['column']});
                    break;

                default:
                    $data[$field['key']] = $this->owner->{$field['column']};
            }
        }

        return $data;
    }
}

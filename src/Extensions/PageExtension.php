<?php

namespace Heyimphil\Robotson\Extensions;

use Heyimphil\Robotson\Models\RobotRule;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use SilverStripe\ORM\DataExtension;

class PageExtension extends DataExtension
{
    private static $has_many = [
        'RobotRule' => RobotRule::class
    ];

    public function updateCMSFields(FieldList $fields)
    {
        $fields->addFieldToTab('Root.Robots', GridField::create(
            'RobotRule',
            'RobotRule',
            $this->owner->RobotRule(),
            GridFieldConfig_RecordEditor::create()
        ));

        return $fields;
    }
}

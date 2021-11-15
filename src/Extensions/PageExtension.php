<?php

namespace Heyimphil\Robotson\Extensions;

use Heyimphil\Robotson\Models\RobotRuleSiteTree;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use SilverStripe\ORM\DataExtension;

class PageExtension extends DataExtension
{
    private static $has_many = [
        'RobotRules' => RobotRuleSiteTree::class
    ];

    public function updateCMSFields(FieldList $fields)
    {
        $fields->addFieldToTab('Root.Robots', GridField::create(
            'RobotRules',
            'Robot Rules',
            $this->owner->RobotRules(),
            GridFieldConfig_RecordEditor::create()
        ));

        return $fields;
    }
}

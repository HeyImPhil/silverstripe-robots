<?php

namespace Heyimphil\Robotson\Extensions;

use Heyimphil\Robotson\Models\RobotRuleSiteConfig;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\CompositeField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use SilverStripe\Forms\LiteralField;
use Silverstripe\ORM\DataExtension;

class SiteConfigExtension extends DataExtension
{
    private static $db = [
        'IncludeDefaultRules' => 'Boolean',
        'IncludeSiteMap' => 'Boolean'
    ];

    private static $has_many = [
        'RobotRules' => RobotRuleSiteConfig::class,
    ];

    public function updateCMSFields(FieldList $fields)
    {
        $fields->addFieldsToTab(
            'Root.Robots',
            [
                GridField::create(
                    'RobotRules',
                    'Robot Rules',
                    $this->owner->RobotRules(),
                    GridFieldConfig_RecordEditor::create()
                ),
                CompositeField::create(
                    [
                        CheckboxField::create(
                            'IncludeDefaultRules',
                            'Include Default Rules',
                            $this->owner->IncludeDefaultRules
                        ),
                        LiteralField::create(
                            'DefaultRulesMessage',
                            sprintf('<div class="message good notice">%s</div>', _t(
                                __CLASS__ . '.Note',
                                'Default rules include disallow for /dev /admin.'
                            ))
                        )
                    ]
                ),
                CheckboxField::create(
                    'IncludeSiteMap',
                    'Include Site Map',
                    $this->owner->IncludeSiteMap
                )
            ]
        );

        return $fields;
    }
}

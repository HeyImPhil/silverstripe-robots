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
        'RobotRule' => RobotRuleSiteConfig::class,
    ];

    public function updateCMSFields(FieldList $fields)
    {
        $fields->addFieldToTab(
            'Root.Robots',
            GridField::create(
                'RobotRule',
                'Robot Rules',
                $this->owner->RobotRule(),
                GridFieldConfig_RecordEditor::create()
            )
        );

        $fields->addFieldToTab(
            'Root.Robots',
            CompositeField::create(
                array(
                    CheckboxField::create(
                        'IncludeDefaultRules',
                        'Include Default Rules',
                        $this->IncludeDefaultRules
                    ),
                    LiteralField::create(
                        'DefaultRulesMessage',
                        sprintf('<div class="message good notice">%s</div>', _t(
                            __CLASS__ . '.Note',
                            'Default rules include disallow for /dev /admin.'
                        ))
                    )
                )
            )
        );

        $fields->addFieldToTab(
            'Root.Robots',
            CheckboxField::create(
                'IncludeSiteMap',
                'Include Site Map',
                $this->IncludeSiteMap
            )
        );

        return $fields;
    }
}

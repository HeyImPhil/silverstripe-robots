<?php

namespace Heyimphil\Robotson\Extensions;

use Heyimphil\Robotson\Models\RobotRuleSiteTree;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Control\Controller;
use SilverStripe\Control\Director;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use SilverStripe\ORM\DataExtension;
use SilverStripe\ORM\DataObject;

class PageExtension extends DataExtension
{
    private static $has_many = [
        'RobotRules' => RobotRuleSiteTree::class,
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

    /**
     * Once the page is published checks to see if the relative URL changes.
     * When there are changes, duplicate these existing rules and save the original link
     *
     * @param SiteTree $original
     * @return void
     */
    public function onAfterPublish(SiteTree $original) {
        /** @var SiteTree $owner */
        $owner = $this->getOwner();
        $originalLink = Controller::join_links(Director::baseURL(), $original->RelativeLink());
        $targetLink = Controller::join_links(Director::baseURL(), $owner->RelativeLink());

        if ($originalLink === $targetLink) {
            return;
        }

        if ($owner->RobotRules()->find('OriginalLink', $targetLink)) {
            return;
        }

        $agents = [];
        /** @var RobotRuleSiteTree $rule */
        foreach($owner->RobotRules()->filter('OriginalLink:not', $targetLink) as $rule) {
            if (in_array($rule->UserAgent, $agents)) {
                continue;
            }

            $agents[] = $rule->UserAgent;
            $duplicate = $rule->duplicate();
            $duplicate->OriginalLink = $targetLink;
            $duplicate->write();
        }
    }
}

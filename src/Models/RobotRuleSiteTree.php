<?php

namespace Heyimphil\Robotson\Models;

use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Control\Controller;
use SilverStripe\Control\Director;
use SilverStripe\Forms\LiteralField;
use Silverstripe\ORM\DataObject;

class RobotRuleSiteTree extends DataObject
{
    private static $db = [
        'Enabled' => 'Boolean(1)',
        'UserAgent' => 'Varchar',
        'IncludeChildren' => 'Boolean',
        'IncludeQueryString' => 'Boolean(1)',
        'CrawlDelay' => 'Int'
    ];

    private static $has_one = [
        'SiteTree' => SiteTree::class
    ];

    private static $summary_fields = [
        'UserAgent',
        'IncludeChildren',
        'IncludeQueryString'
    ];

    private static $table_name = 'RobotRuleSiteTree';

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $fields->removeByName('SiteTreeID');
        $fields->insertAfter(
            'CrawlDelay',
            LiteralField::create(
                'DelayHeader',
                '<p>If you set a crawl delay to this user agent
                it will affect all Robot rules that contain this user agent</p>'
            )
        );

        return $fields;
    }

    public function Link()
    {
        $relativeLink = $this->SiteTree()->RelativeLink();
        return Controller::join_links(Director::baseURL(), $relativeLink);
    }

    public function validate()
    {
        $validate = parent::validate();

        if (!$this->UserAgent) {
            $validate->addError('You need to add a User agent to this rule');
        }

        return $validate;
    }
}

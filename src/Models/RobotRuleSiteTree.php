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
        // I think we should call this DisAllow so that it follows the naming convention of Robots.txt
        'Enabled' => 'Boolean(1)',
        'UserAgent' => 'Varchar',
        'IncludeChildren' => 'Boolean',
        'IncludeQueryString' => 'Boolean(1)',
        'CrawlDelay' => 'Int',
        'OriginalLink' => 'Varchar',
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
        $relativeLink = $this->OriginalLink ?: $this->SiteTree()->RelativeLink();

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

    /**
     * Make sure OriginalLink gets the value from RelativeLink()
     *
     * @inheritDoc
     */
    public function onBeforeWrite()
    {
        if (!$this->OriginalLink) {
            $this->OriginalLink = Controller::join_links(Director::baseURL(), $this->SiteTree()->RelativeLink());
        }

        parent::onBeforeWrite();
    }
}

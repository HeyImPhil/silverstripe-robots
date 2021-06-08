<?php

namespace Heyimphil\Robotson\Models;

use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Forms\LiteralField;
use Silverstripe\ORM\DataObject;
use SilverStripe\SiteConfig\SiteConfig;

class RobotRuleSiteConfig extends DataObject
{
    private static $db = [
        'Enabled' => 'Boolean(1)',
        'UserAgent' => 'Varchar',
        'LinkUrl' => 'Varchar',
        'CrawlDelay' => 'Int'
    ];

    private static $has_one = [
        'SiteConfig' => SiteConfig::class,
    ];

    private static $summary_fields = [
        'UserAgent',
        'LinkUrl'
    ];

    private static $table_name = 'RobotRuleSiteConfig';

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $fields->removeByName('SiteConfigID');
        $fields->insertAfter('CrawlDelay',
            LiteralField::create(
            'DelayHeader',
            '<p>If you set a crawl delay to this user agent it will affect all Robot rules that contain this user agent</p>'
            )
        );

        return $fields;
    }

    public function Link()
    {
        return $this->LinkUrl ?? '/';
    }

    public function validate()
    {
        $validate = parent::validate();

        if (!$this->LinkUrl) {
            $validate->addError('You need to add a link url to this rule');
        }

        if (!$this->UserAgent) {
            $validate->addError('You need to add a User agent to this rule');
        }

        return $validate;
    }
}

<?php

namespace Heyimphil\Robotson\Controllers;

use Heyimphil\Robotson\Models\RobotRuleSiteConfig;
use Heyimphil\Robotson\Models\RobotRuleSiteTree;
use SilverStripe\Control\Controller;
use SilverStripe\Control\Director;
use SilverStripe\ORM\GroupedList;
use SilverStripe\SiteConfig\SiteConfig;

class RobotsController extends Controller
{
    protected static $rules;

    public function index()
    {
        $this->getResponse()->addHeader(
            'Content-Type',
            'text/plain; charset="utf-8"'
        );

        if (!$this->isLive()) {
            return $this->renderWith('RobotsDisallowAll');
        }

        $this->getRules();
        return $this->renderWith('Robots');
    }

    /**
     * Determines if this is a public site
     *
     * @return boolean flag indicating if this robots is for a public site
     */
    public static function isLive()
    {
        return Director::isLive();
    }

    public function getRules()
    {
        $siteConfig = SiteConfig::current_site_config();
        //Site config rule. If set just use these and ignore SiteTree
        $rules = RobotRuleSiteConfig::get()->filter([
            'SiteConfigID:not' => [null, 0],
            'Enabled' => true
        ])->sort('UserAgent');

        if (!$rules->exists()) {
            $rules = RobotRuleSiteTree::get()->filter([
                'SiteTreeID:not' => [null, 0],
                'Enabled' => true
            ])->sort('UserAgent');
        }

        if ($rules->exists()) {
            // each user agent should have a list of arrays which contains the urls it disallows
            $rules = $this->consilidateRules($rules);
        }

        $this->extend('updateRules', $rules);
        $this->rules = $rules ?? [];
    }

    /** @param RobotRule $rules */
    public function consilidateRules($rules)
    {
        return GroupedList::create($rules)->groupBy('UserAgent');
    }

    public function getSiteMapURL()
    {
        $siteConfig = SiteConfig::current_site_config();
        if (!$this->isLive() || !$siteConfig->IncludeSiteMap) {
            return '';
        }

        if ($this->config()->get('site_map_url')) {
            return $this->config()->get('site_map_url');
        }

        return '/sitemap.xml';
    }

    public function getDisallowed()
    {
        foreach ($this->rules as $userAgent => $rules) {
            $crawlDelay = 0;
            echo 'User-agent: '.$userAgent . PHP_EOL;

            /** @var RobotRule $rule */
            foreach ($rules as $rule) {
                echo $this->generateDisallowRuleStrings($rule);
                if ($rule->CrawlDelay) {
                    $crawlDelay = $rule->CrawlDelay;
                }
            }
            if ($crawlDelay) {
                echo 'crawl-delay: '.$crawlDelay . PHP_EOL;
            }
            echo PHP_EOL;
        }
    }


    /**
     * Syntax: Add a disallow line for the url with a slash and without.
     * if we do not want to include the children of this page then we add a $ to the end of each string. Making a total of 4 rules
     * for one page.
     */
    public function generateDisallowRuleStrings(RobotRule $rule)
    {
        // if it's not a site config then we want to only do this one page
        if (!$rule->IncludeChildren && !$rule->SiteConfigID) {
            $suffix = '$';
        }

        $link = ($rule instanceof RobotRule) ? $rule->Link() : $rule->link;

        //Default rule for page
        $disallowRule = $this->constructDisallowString($link, $suffix);
        if ($link != '/') {
            $disallowRule .= $this->constructDisallowString(rtrim($link,'/'), $suffix);
        }

        if ($suffix) {
            // If we have a suffix then we need to add query strings to our disallow as they will be excluded
            $suffix = '?';
            $disallowRule .= $this->constructDisallowString($link, $suffix);
            if ($link != '/') {
                $disallowRule .= $this->constructDisallowString(rtrim($link,'/'), $suffix);
            }
        }

        return $disallowRule;
    }

    public function constructDisallowString($link, $suffix = null)
    {
        return 'disallow: ' . $link . $suffix . PHP_EOL;
    }
}

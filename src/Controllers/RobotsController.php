<?php

namespace Heyimphil\Robotson\Controllers;

use Heyimphil\Robotson\Models\RobotRuleSiteConfig;
use Heyimphil\Robotson\Models\RobotRuleSiteTree;
use SilverStripe\Control\Controller;
use SilverStripe\Control\Director;
use SilverStripe\ORM\ArrayList;
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
        //Site config rule. If set just use these and ignore SiteTree
        $rulesSiteconfig = RobotRuleSiteConfig::get()->filter([
            'Enabled' => true
        ])->sort('UserAgent');

        $rulesSitetree = RobotRuleSiteTree::get()->filter([
            'Enabled' => true
        ])->sort('UserAgent');

        $rules = new ArrayList();
        if ($rulesSiteconfig->exists()) {
            $rules->merge($rulesSiteconfig);
        }

        if ($rulesSitetree->exists()) {
            $rules->merge($rulesSitetree);
        }

        // each user agent should have a list of arrays which contains the urls it disallows
        $rules = $this->consolidateRules($rules);

        $this->extend('updateRules', $rules);
        $this->rules = $rules ?? [];

        return $rules;
    }

    public function consolidateRules($rules)
    {
        return GroupedList::create($rules)->groupBy('UserAgent');
    }

    public function getSiteMapURL()
    {
        $sitemap = '/sitemap.xml';

        $siteConfig = SiteConfig::current_site_config();
        if (!$this->isLive() || !$siteConfig->IncludeSiteMap) {
            return '';
        }

        if ($this->config()->get('site_map_url')) {
            $sitemap = $this->config()->get('site_map_url');
        }
        $sitemap .= PHP_EOL . PHP_EOL;

        return $sitemap;
    }

    public function getDisallowed()
    {
        $robotsTxt = '';
        foreach ($this->rules as $userAgent => $rules) {
            $crawlDelay = 0;
            $robotsTxt .= 'User-agent: ' . $userAgent . PHP_EOL;

            foreach ($rules as $rule) {
                $robotsTxt .= $this->generateDisallowRuleStrings($rule);
                if ($rule->CrawlDelay) {
                    $crawlDelay = $rule->CrawlDelay;
                }
            }
            if ($crawlDelay) {
                $robotsTxt .= 'crawl-delay: ' . $crawlDelay . PHP_EOL;
            }
            $robotsTxt .= PHP_EOL;
        }

        return $robotsTxt;
    }

    /**
     * Syntax: Add a disallow line for the url with a slash and without.
     * if we do not want to include the children of this page then we add a $ to the end of each string.
     * Making a total of 4 rules for one page.
     */
    public function generateDisallowRuleStrings($rule)
    {
        $link = $rule->Link();

        if ($rule->LinkUrl) {
            return $this->constructDisallowString($link);
        }

        $suffix = ($rule->IncludeChildren) ? '' : '$';

        //Default rule for page
        $disallowRule = $this->constructDisallowString($link, $suffix);
        if ($link != '/') {
            $disallowRule .= $this->constructDisallowString(rtrim($link, '/'), $suffix);
        }

        if ($suffix && $rule->IncludeQueryString) {
            // If we have a suffix then we need to add query strings to our disallow as they will be excluded
            $suffix = '?';
            $disallowRule .= $this->constructDisallowString($link, $suffix);
            if ($link != '/') {
                $disallowRule .= $this->constructDisallowString(rtrim($link, '/'), $suffix);
            }
        }

        return $disallowRule;
    }

    public function constructDisallowString($link, $suffix = null)
    {
        return 'disallow: ' . $link . $suffix . PHP_EOL;
    }
}

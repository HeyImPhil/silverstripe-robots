<?php

namespace Heyimphil\Robotson\Tests;

use Heyimphil\Robotson\Controllers\RobotsController;
use SilverStripe\CMS\Model\SiteTree;
use Heyimphil\Robotson\Models\RobotRuleSiteTree;
use SilverStripe\Core\Config\Config;
use SilverStripe\Core\CoreKernel;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Core\Kernel;
use SilverStripe\Dev\FunctionalTest;
use SilverStripe\SiteConfig\SiteConfig;

class RobotTest extends FunctionalTest
{
    protected static $fixture_file = [
        '../fixtures/pages.yml'
    ];

    protected static $use_draft_site = true;

    public function testRobotsRoute()
    {
        $page = $this->get('/robots');
        $this->assertEquals(200, $page->getStatusCode());
    }

    public function testEnvironmentDev()
    {
        Injector::inst()->get(Kernel::class)->setEnvironment(CoreKernel::DEV);
        $page = $this->get('/robots');
        $this->assertContains('User-agent: *', $page->getBody());
        $this->assertContains('Disallow: /', $page->getBody());
    }

    public function testEnvironmentLive()
    {
        Injector::inst()->get(Kernel::class)->setEnvironment(CoreKernel::LIVE);
        $page = $this->get('/robots');

        $robotsText = <<<robots
disallow: /example-parent/example-page/
disallow: /example-parent/example-page
robots;

        $this->assertContains($robotsText,$page->getBody());
    }

    public function testExcludeChildren()
    {
        Injector::inst()->get(Kernel::class)->setEnvironment(CoreKernel::LIVE);
        $rule = $this->objFromFixture(RobotRuleSiteTree::class,'ExampleRule');
        $rule->IncludeChildren = false;
        $rule->write();

        $page = $this->get('/robots');

        $robotsText = <<<robots
disallow: /example-parent/example-page/$
disallow: /example-parent/example-page$
robots;

        $this->assertContains($robotsText,$page->getBody());
    }

    public function testExcludeChildrenWithQueryString()
    {
        Injector::inst()->get(Kernel::class)->setEnvironment(CoreKernel::LIVE);
        $rule = $this->objFromFixture(RobotRuleSiteTree::class,'ExampleRule');
        $rule->IncludeChildren = false;
        $rule->IncludeQueryString = true;
        $rule->write();

        $page = $this->get('/robots');

        $robotsText = <<<robots
disallow: /example-parent/example-page/$
disallow: /example-parent/example-page$
disallow: /example-parent/example-page/?
disallow: /example-parent/example-page?
robots;

        $this->assertContains($robotsText,$page->getBody());
    }

    public function testSinglePageMultipleAgents()
    {
        Injector::inst()->get(Kernel::class)->setEnvironment(CoreKernel::LIVE);
        $rule = $this->objFromFixture(RobotRuleSiteTree::class,'ExampleRule');
        $page = $this->get('/robots');

        $robotsText = <<<robots
User-agent: *
disallow: /example-parent/example-page/
disallow: /example-parent/example-page

User-agent: googlebot
disallow: /example-parent/example-page/
disallow: /example-parent/example-page
robots;

        $this->assertContains($robotsText,$page->getBody());
    }

    public function testSiteMapUrl()
    {
        Injector::inst()->get(Kernel::class)->setEnvironment(CoreKernel::LIVE);
        $config = $this->objFromFixture(SiteConfig::class,'SiteConfig');
        $config->IncludeSiteMap = true;
        $config->write();

        $page = $this->get('/robots');
        $this->assertContains('Sitemap: /sitemap.xml',$page->getBody());
    }

    public function testSiteMapUrlExtended()
    {
        Injector::inst()->get(Kernel::class)->setEnvironment(CoreKernel::LIVE);
        $config = $this->objFromFixture(SiteConfig::class,'SiteConfig');
        $config->IncludeSiteMap = true;
        $config->write();

        RobotsController::config()->set('site_map_url','/sitemap-new.xml');

        $page = $this->get('/robots');
        $this->assertContains('Sitemap: /sitemap-new.xml',$page->getBody());
    }
}

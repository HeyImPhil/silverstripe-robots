# SilverStripe Robots Module

A Module to handle disallowing by user agent and individual pages.

One of the main pain points the client had was keeping a page disallowed after it had moved url. Hence the need to create this module.

The Robot rules can be set at the Site Config level or the page level. If site level config has been set it will ignore page specific config.

If we set the Robot rule to include children it will output the url to the page with and without the slash. This means all children currently require it to have the same URL pattern.
Since we are disallowing individual pages it uses the syntax with a $ at the end which will inform the robots to act that the $ is the end of the string. This unfortunately means we need to also add a line for query strings
to also be included.

In all non production environments will return a disallow all rule.

If the site has a physical file called robots.txt file it will take precendence over this module.

## Requirements

* Silverstripe/framework ^4.0
* Silverstripe/admin ^ 1.0

## Installation

```
composer require Heyimphil/robotson dev-master
```

## Maintainers
 * Phillip King <phillip.king@silverstripe.com>

## Bugtracker
Bugs are tracked in the issues section of this repository. Before submitting an issue please read over
existing issues to ensure yours is unique.

If the issue does look like a new bug:

 - Create a new issue
 - Describe the steps required to reproduce your issue, and the expected outcome. Unit tests, screenshots
 and screencasts can help here.
 - Describe your environment as detailed as possible: SilverStripe version, Browser, PHP version,
 Operating System, any installed SilverStripe modules.

Please report security issues to the module maintainers directly. Please don't file security issues in the bugtracker.

## Development and contribution
If you would like to make contributions to the module please ensure you raise a pull request and discuss with the module maintainers.

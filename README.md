# SilverStripe Robots Module

A Module for configuring the Robots.txt file.

One of the main pain points the client had was keeping a page disallowed after it had moved url. Hence the need to create this module.

The Robot rules can be set at the Site Config level or the page level.

Note: In non-live environments this will return a disallow all rule.

### Site Config

The site config robot rules should be used to disallow advanced url strings, assets or anything that is not a page.

### Site Tree

One of the main reasons to use the site tree robots settings is that the robot rule will stay disallowed even after the page has been moved to a new URL.

### Options:

#### User-agent:

This is the string and name of the bot to exclude. If all should be excluded use the *

##### Include Children:

If include is turned on it will output the url to the page with and without the slash. This currently requires that the children follow the same url pattern.

Example:
```text
User-agent: *
Disallow: /example/page/
Disallow: /example/page
```

If this setting is disabled it will output with a suffix of $ to inform that this is the end of the rule string.

```text
Example:
Disallow: /example/page/$
Disallow: /example/page$
```

#### Include Query String:

NOTE: This setting is not taken into account if the "Include children" is enabled as by default will include the query strings.

Due to the $ syntax we need to include two lines for the query string.
```text
Example:
Disallow: /example/page/$
Disallow: /example/page$
disallow: /tools/knowledge-base/?
disallow: /tools/knowledge-base?
```

#### Crawl Delay:

This option will delay the user agents rules and will affect any other rule that also uses the same user agent.

## Requirements

* Silverstripe/cms ^4.0

## Installation

```
composer require Heyimphil/robotson dev-master
```
## Server requirements

If the site has a physical file called robots.txt file it will take precendence over this module and will need to be handled in the setup of the server.


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

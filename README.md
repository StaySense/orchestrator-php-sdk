![alt text](http://staysense.com/img/staysense-logo.png "StaySense Orchestrator")

StaySense - Orchestrator PHP SDK
================================
PHP SDK for Orchestrator for full-stack deployments

Orchestrator is a machine-learning powered conversion optimization platform built specifically for the travel industry and designed to overcome the inherent challenges associated with traditional A/B testing, namely:

1. Large sample sizes (and therefore long testing periods for most websites) are generally required to achieve incremental improvement.
2. There is generally large conversion “cost” that accompanies point 1 with traffic spend on a losing variant.
3. Many frequentist testing approaches (traditional A/B/n) are not able to react to real-time seasonality/demographic changes effectively.
4. Difficulty (and/or great expense) with currently available commercial A/B testing tools to serve a large number of page optimizations simultaneously.

Orchestrator™ was designed specifically with these challenges in mind and over comes them in these ways:

1. A machine-learning based decision model that is capable of choosing a winning variant early on in the test cycle while continuing to test other options while minimizing conversion "cost".
  1. This allows meaningful gains to be produced in a much shorter amount of time than traditional A/B tests.
2. A decision model that is capable of running continuously (read: forever) and reacting to changes over time.
3. A model that is able to provide incremental improvement in real-time, even for sites with relatively low volume.
  1. This is especially important when considering the relatively low traffic levels on the typical vacation rental / travel website.
4. A platform to solve issues traditionally difficult to solve with A/B testing, E.g., “which ‘lead photo’ in a search results set of properties earns the most clicks to the listing out of all property photos?” at scale and cost effectively.
5. Delivers a SaaS platform that enables large-scale, micro-optimization testing and improvements that would have been difficult and/or cumbersome to accomplish in the past in the travel industry.

Orchestrator is not geared towards finding the absolute truth, but helping companies find the best choice to provide incremental gains at scale. This enables companies to test more ideas, faster, and to discover value where previously there wasn't any.

How To Use
----------

### Installation
The easiest way to install the PHP SDK is via Composer:

`composer require staysense/orchestrator`

### Usage

#### 1. Instantiate the library

```php
require 'vendor/autoload.php';

use StaySense\Orchestrator\Orchestrator;

$key = "API_KEY";

$orchestratorAPI = new Orchestrator();
$orchestratorAPI->setAPIKey($key);
```

#### 2. Create a customer Profile ID

The SDK comes with built-in user profile identification and tracking. To tag a user prior to experience delivery, simply call this method:

`$orchestratorAPI->setProfileID();`

The StaySense Orchestrator™ SDK manages user ID generation/management auto-magically. If a user returns after a session has expired, the SDK will attempt to rehydrate the user context from persistent methods before generating a new profileID. This helps ensure a higher level of test experience delivery consistency.

In customer facing scenarios, it is highly recommended to call `setProfileID()` immediately after instantiation to ensure the Profile ID is available for subsequent calls as well as user tracking.

#### 3. Request A Variant

Ask the API for the variant of the experiment you want to serve.

`$variantID = $orchestratorAPI->getVariant($campaignID, $experimentID);`

This requests the variation from the AI model to determine which experience to deliver to the end user for the experiment.

#### 4. Deliver Test Experience

Vary your View based on the Variant ID:

```php
if($variantID = '93a1jx')
{
  //Serve Experience 1
}elseif($variantID = 'a8j4aj')
{
  //Serve Experience 2
}elseif($variantID = 'hyz9ja')
{
  //Serve Experience 3
}
```

#### 5. Log a conversion

```php
$conversion = $orchestratorAPI->logConversionEvent($campaignID, $experimentID, $variantID);
```

#### 6. Make It Rain
![alt text](https://i.giphy.com/media/l41lZccR1oUigYeNa/giphy-downsized.gif)

### Important Notes

The StaySense Orchestrator™ platform manages correct conversion event logging to ensure additional logic isn't required by the implementor. This is necessary to assure correct statistical modeling by the recommendation engine while also easing the burden on the implementing team.

#### Performance Notes

The SDK and platform leverages significant use of http caching wherever possible. This ensures a high end-user delivery experience and speed even leveraging a back-end SDK. We generally aim to keep raw HTTP request responses at or below 20ms in total, even for complex testing scenarios. After initial request, response times should be instant on the local machine thanks to caching.


To Do
-----

Update the repo wiki with full documentation for usage.

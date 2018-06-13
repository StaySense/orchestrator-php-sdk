![alt text](http://staysense.com/img/staysense-logo.png "StaySense Orchestrator")

StaySense - Orchestrator PHP SDK
================================
PHP SDK for Orchestrator for full-stack deployments

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

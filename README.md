![alt text](http://staysense.com/img/staysense-logo.png "StaySense Orchestrator")

StaySense - Orchestrator PHP SDK
================================
PHP SDK for Orchestrator for full-stack deployments

How To Use
----------

### Installation
The easiest way to install the PHP SDK is via Composer:

`composer require staysense/orchestrator-php-sdk`

### Usage

#### 1. Instantiate the library

```php
require 'vendor/autoload.php';

use StaySense\Orchestrator\Orchestrator;

$key = "API_KEY";

$orchestratorAPI = new Orchestrator();
$orchestratorAPI->setAPIKey($key);
```

#### 2. Request A Variant

Ask the API for the variant of the experiment you want to serve.

`$variantID = $orchestratorAPI->getVariant($campaignID, $experimentID);`

This requests the variation from the AI model to determine which experience to deliver to the end user for the experiment.

#### 3. Deliver Test Experience

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

#### 4. Log a conversion

```php
$conversion = $orchestratorAPI->logConversionEvent($campaignID, $experimentID, $variantID);
```

### Important Notes

The StaySense Orchestrator™ SDK manages user ID generation/management auto-magically. The platform also manages conversion event logging to ensure additional logic isn't required by the implementor. This is necessary to assure correct statistical modeling by the recommendation engine while also easing the burden on the implementing team.

To Do
-----

Update the repo wiki with full documentation for usage.

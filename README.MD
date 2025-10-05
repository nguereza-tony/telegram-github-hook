## Telegram Github Hook
**This library is used to notify telegram chat on Github Webhook for push event**

[![Project Status](http://opensource.box.com/badges/active.svg)](http://opensource.box.com/badges)
[![Latest Stable Version](https://poser.pugx.org/nguereza-tony/telegram-github-hook/v)](https://packagist.org/packages/nguereza-tony/telegram-github-hook)
[![Latest Unstable Version](https://poser.pugx.org/nguereza-tony/telegram-github-hook/v/unstable)](https://packagist.org/packages/nguereza-tony/telegram-github-hook)
[![Total Downloads](https://poser.pugx.org/nguereza-tony/telegram-github-hook/downloads)](https://packagist.org/packages/nguereza-tony/telegram-github-hook)
[![License](https://poser.pugx.org/nguereza-tony/telegram-github-hook/license)](https://packagist.org/packages/nguereza-tony/telegram-github-hook)  
![Tests](https://github.com/nguereza-tony/telegram-github-hook/actions/workflows/ci.yml/badge.svg)
[![Quality Score](https://img.shields.io/scrutinizer/g/nguereza-tony/telegram-github-hook.svg?style=flat-square)](https://scrutinizer-ci.com/g/nguereza-tony/telegram-github-hook)
[![Codacy Badge](https://app.codacy.com/project/badge/Grade/7bbc17c8aff1430c8a52a84ba7ff2787)](https://app.codacy.com/gh/nguereza-tony/telegram-github-hook/dashboard?utm_source=gh&utm_medium=referral&utm_content=&utm_campaign=Badge_grade)
[![Codacy Badge](https://app.codacy.com/project/badge/Coverage/7bbc17c8aff1430c8a52a84ba7ff2787)](https://app.codacy.com/gh/nguereza-tony/telegram-github-hook/dashboard?utm_source=gh&utm_medium=referral&utm_content=&utm_campaign=Badge_coverage)

### Requirements 
- **PHP >= 8.0** 

### Installation
#### Using composer (recommended)
```bash
composer require nguereza-tony/telegram-github-hook
```

### Usage
1. **Create telegram bot (if don't have already)**
- Create the Bot with BotFather: Open Telegram and search for "@BotFather". Ensure it is the official, verified account (look for the blue checkmark).
- Initiate a chat with BotFather and then send the command `/newbot` to BotFather.
- BotFather will prompt you to choose a display name for your bot. This can be a friendly name and does not need to be unique
- Next, you will need to select a unique username for your bot. This username must end with "bot" or "_bot" (e.g., "MyAwesomeBot" or "My_Awesome_Bot").
- Upon successful creation, BotFather will provide you with an HTTP API token. This token is crucial for interacting with your bot programmatically and should be kept secure.

2. **Add bot to telegram group**
- Open your Telegram group.
- Tap the group's name at the top to open its info.
- Select "Administrators" from the options.
- Tap "Add administrator".
- Search for your bot's username in the search bar.
- Select the bot from the search results.
- Configure the bot's administrative rights as desired and tap "Finish" or "Save" to add it to the group.

3. **Add webhook in GitHub**
- Navigate to your repository and go to the main page of the GitHub repository where you want to add the webhook.
- Under your repository name, click on the Settings tab. If you don't see it, look for a dropdown menu and select Settings from there.
- In the left sidebar of the Settings page, click on Webhooks.
- Click the Add webhook button.
- Configure the webhook:
    - **Payload URL**: Enter the URL where you want GitHub to send the webhook payloads (the data about the events). This URL should point to your server or application that will process these payloads.
    - **Content type**: select the data format for the webhook payload. For our case it will be "application/json" to delivers the JSON payload directly in the request body.
    - **Secret (optional)**: keep empty for now.
    - **Events**: Choose which GitHub events should trigger the webhook. For now just select "Just the push event." 
    - **Active**: Ensure the "Active" checkbox is selected to enable the webhook immediately after creation.
- Click the Add webhook button to save your configuration.

4. **Create the application**  
To deploy the application you need to create the configuration file in root or sub folder of your project like:  
```php
<?php

declare(strict_types=1);

return [
    'organization' => [
        'organization-name-1' => [
            '*' => ['YOUR_CHAT_ID', 'YOUR_BOT_TOKEN'], // this is default for all organization
            'specific-repo-name' => ['YOUR_CHAT_ID', 'YOUR_BOT_TOKEN'],
        ],
        'organization-name-2' => [
            // ...
        ],
    ],
];
```
**Note**: the configuration file name should be `config.php` in order to work.  
Now create your main file like `index.php`:
```php
<?php
require 'vendor/autoload.php';

use Platine\Config\Config;
use Platine\Config\FileLoader;
use Quantum\Hub\WebHook;

$configFolder = '.'; // change to the path of your configuration folder
$loader = new FileLoader($configFolder);
$config = new Config($loader);
$wh = new WebHook($config);

$wh->handle();
```
That's, each time you push into the repository, you will receive the notify in the telegram group.

### License
MIT License See [LICENSE.MD](LICENSE.MD)

### Resources
Coming soon


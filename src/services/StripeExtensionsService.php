<?php
/**
 * Stripe Extensions plugin for Craft CMS 3.x
 *
 * Provides additional functionality to Enupal Stripe Payments plugin
 *
 * @link      https://springworks.co.uk
 * @copyright Copyright (c) 2021 Steve Rowling
 */

namespace elementworks\stripeextensions\services;

use elementworks\stripeextensions\StripeExtensions;

use Craft;
use craft\base\Component;

/**
 * StripeExtensionsService Service
 *
 * All of your plugin’s business logic should go in services, including saving data,
 * retrieving data, etc. They provide APIs that your controllers, template variables,
 * and other plugins can interact with.
 *
 * https://craftcms.com/docs/plugins/services
 *
 * @author    Steve Rowling
 * @package   StripeExtensions
 * @since     1.0.0
 */
class StripeExtensionsService extends Component
{
    // Public Methods
    // =========================================================================

    /**
     * This function can literally be anything you want, and you can have as many service
     * functions as you want
     *
     * From any other plugin file, call it like this:
     *
     *     StripeExtensions::$plugin->stripeExtensionsService->exampleService()
     *
     * @return mixed
     */
    public function exampleService()
    {
        $result = 'something';
        // Check our Plugin's settings for `someAttribute`
        if (StripeExtensions::$plugin->getSettings()->someAttribute) {
        }

        return $result;
    }
}

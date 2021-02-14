<?php
/**
 * Stripe Extensions plugin for Craft CMS 3.x
 *
 * Provides additional functionality to Enupal Stripe Payments plugin
 *
 * @link      https://springworks.co.uk
 * @copyright Copyright (c) 2021 Steve Rowling
 */

namespace elementworks\stripeextensions;

use Craft;
use craft\base\Plugin;
use craft\elements\User;
use craft\events\PluginEvent;
use craft\services\Plugins;

use elementworks\stripeextensions\models\Settings;
use elementworks\stripeextensions\services\StripeExtensionsService as StripeExtensionsServiceService;

use enupal\stripe\events\OrderCompleteEvent;
use enupal\stripe\events\WebhookEvent;
use enupal\stripe\services\Orders;
use enupal\stripe\Stripe;

use yii\base\Event;

/**
 * Craft plugins are very much like little applications in and of themselves. We’ve made
 * it as simple as we can, but the training wheels are off. A little prior knowledge is
 * going to be required to write a plugin.
 *
 * For the purposes of the plugin docs, we’re going to assume that you know PHP and SQL,
 * as well as some semi-advanced concepts like object-oriented programming and PHP namespaces.
 *
 * https://docs.craftcms.com/v3/extend/
 *
 * @author    Steve Rowling
 * @package   StripeExtensions
 * @since     1.0.0
 *
 * @property  StripeExtensionsServiceService $stripeExtensionsService
 * @property  Settings $settings
 * @method    Settings getSettings()
 */
class StripeExtensions extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * Static property that is an instance of this plugin class so that it can be accessed via
     * StripeExtensions::$plugin
     *
     * @var StripeExtensions
     */
    public static $plugin;

    // Public Properties
    // =========================================================================

    /**
     * To execute your plugin’s migrations, you’ll need to increase its schema version.
     *
     * @var string
     */
    public $schemaVersion = '1.0.0';

    /**
     * Set to `true` if the plugin should have a settings view in the control panel.
     *
     * @var bool
     */
    public $hasCpSettings = true;

    /**
     * Set to `true` if the plugin should have its own section (main nav item) in the control panel.
     *
     * @var bool
     */
    public $hasCpSection = false;

    // Public Methods
    // =========================================================================

    /**
     * Set our $plugin static property to this class so that it can be accessed via
     * StripeExtensions::$plugin
     *
     * Called after the plugin class is instantiated; do any one-time initialization
     * here such as hooks and events.
     *
     * If you have a '/vendor/autoload.php' file, it will be loaded for you automatically;
     * you do not need to load it in your init() method.
     *
     */
    public function init()
    {
        parent::init();
        self::$plugin = $this;

        // Do something after we're installed
        Event::on(
            Plugins::class,
            Plugins::EVENT_AFTER_INSTALL_PLUGIN,
            function(PluginEvent $event) {
                if ($event->plugin === $this) {
                    // We were just installed
                }
            }
        );

        /**
         * Logging in Craft involves using one of the following methods:
         *
         * Craft::trace(): record a message to trace how a piece of code runs. This is mainly for development use.
         * Craft::info(): record a message that conveys some useful information.
         * Craft::warning(): record a warning message that indicates something unexpected has happened.
         * Craft::error(): record a fatal error that should be investigated as soon as possible.
         *
         * Unless `devMode` is on, only Craft::warning() & Craft::error() will log to `craft/storage/logs/web.log`
         *
         * It's recommended that you pass in the magic constant `__METHOD__` as the second parameter, which sets
         * the category to the method (prefixed with the fully qualified class name) where the constant appears.
         *
         * To enable the Yii debug toolbar, go to your user account in the AdminCP and check the
         * [] Show the debug toolbar on the front end & [] Show the debug toolbar on the Control Panel
         *
         * http://www.yiiframework.com/doc-2.0/guide-runtime-logging.html
         */
        Craft::info(
            Craft::t(
                'stripe-extensions',
                '{name} plugin loaded',
                ['name' => $this->name]
            ),
            __METHOD__
        );

        Event::on(Orders::class, Orders::EVENT_AFTER_ORDER_COMPLETE, function(OrderCompleteEvent $e) {
            $order = $e->order;

            //  Check for existing user
            $user = Craft::$app->getUsers()->getUserByUsernameOrEmail($order->email);

            if (!$user) {
                $user = new User();
                $user->pending = false;
                $user->username = $order->email;
                $user->email = $order->email;
                $user->passwordResetRequired = false;
                $user->validate(null, false);
                Craft::$app->getElements()->saveElement($user, false);
                // Add user to subscriber userGroup
                Craft::$app->getUsers()->assignUserToGroups($user->id, [$this->getSettings()->subscriberUserGroup]);
                // Send activation email if desired
                if ($this->getSettings()->sendActivationEmail) {
                    Craft::$app->getUsers()->sendActivationEmail($user);
                }
                // Auto login new user if desired
                if ($this->getSettings()->autoLoginUser) {
                    $generalConfig = Craft::$app->getConfig()->getGeneral();
                    Craft::$app->getUser()->login($user, $generalConfig->userSessionDuration);
                }
                // Add new user to order
                $order->userId = $user->id;
                Stripe::$app->orders->saveOrder($order, false);
            }

            // Update subscription expiry date on user
            if ($this->getSettings()->setSubscriptionExpiryDate && $this->getSettings()->subscriptionExpiryDateField) {
                // Set the subscription expiry date here…
                $subscription = $order->getSubscription();
                if ($subscription) {
                    $user->setFieldValues([
                        $this->getSettings()->subscriptionExpiryDateField => $subscription->endDate
                    ]);
                    Craft::$app->getElements()->saveElement($user, false);
                }
            }
        });

        Event::on(Orders::class, Orders::EVENT_AFTER_PROCESS_WEBHOOK, function(WebhookEvent $e) {
            $data = $e->stripeData;
            $order = $e->order;

            if ($order) {
                $user = Craft::$app->getUsers()->getUserByUsernameOrEmail($order->email);

                if ($user) {
                    switch ($data['type']) {
                        //Occurs whenever a customer recurring invoice is paid
                        case 'invoice.paid':
                            // Update subscription expiry date
                            if ($this->getSettings()->setSubscriptionExpiryDate && $this->getSettings()->subscriptionExpiryDateField) {
                                // Set the subscription expiry date here…
                                /** @var \enupal\stripe\models\Subscription $subscription */
                                $subscription = $order->getSubscription();
                                if ($subscription) {
                                    $user->setFieldValues([
                                        $this->getSettings()->subscriptionExpiryDateField => $subscription->endDate
                                    ]);
                                    Craft::$app->getElements()->saveElement($user, false);
                                }
                            }
                            break;
                    }
                }
            }
        });
    }

    // Protected Methods
    // =========================================================================

    /**
     * Creates and returns the model used to store the plugin’s settings.
     *
     * @return \craft\base\Model|null
     */
    protected function createSettingsModel()
    {
        return new Settings();
    }

    /**
     * Returns the rendered settings HTML, which will be inserted into the content
     * block on the settings page.
     *
     * @return string The rendered settings HTML
     */
    protected function settingsHtml(): string
    {
        $userGroups = Craft::$app->getUserGroups();
        foreach ($userGroups->getAllGroups() as $group) {
            $groups[] = [
                'label' => $group->name,
                'value' => $group->id
            ];
        }
        $fields = Craft::$app->getFields();
        $userFields = $fields->getFieldsByElementType(User::class);
        $userFieldOptions = [
            'label' => 'Choose…',
            'value' => ''
        ];
        foreach ($userFields as $field) {
            if (get_class($field) === 'craft\fields\Date') {
                $userFieldOptions[] = [
                    'label' => $field->name,
                    'value' => $field->handle
                ];
            }
        }
        return Craft::$app->view->renderTemplate(
            'stripe-extensions/settings',
            [
                'settings' => $this->getSettings(),
                'userGroups' => $groups ?? null,
                'userFields' => $userFieldOptions ?? null
            ]
        );
    }
}

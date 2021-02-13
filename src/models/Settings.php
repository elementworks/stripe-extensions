<?php
/**
 * Stripe Extensions plugin for Craft CMS 3.x
 *
 * Provides additional functionality to Enupal Stripe Payments plugin
 *
 * @link      https://springworks.co.uk
 * @copyright Copyright (c) 2021 Steve Rowling
 */

namespace elementworks\stripeextensions\models;

use elementworks\stripeextensions\StripeExtensions;

use Craft;
use craft\base\Model;

/**
 * StripeExtensions Settings Model
 *
 * This is a model used to define the plugin's settings.
 *
 * Models are containers for data. Just about every time information is passed
 * between services, controllers, and templates in Craft, it’s passed via a model.
 *
 * https://craftcms.com/docs/plugins/models
 *
 * @author    Steve Rowling
 * @package   StripeExtensions
 * @since     1.0.0
 */
class Settings extends Model
{
    // Public Properties
    // =========================================================================

    /**
     * Some field model attribute
     *
     * @var string
     */
    public $subscriberUserGroup;

    /**
     * Some field model attribute
     *
     * @var bool
     */
    public $sendActivationEmail;

    // Public Methods
    // =========================================================================

    /**
     * Returns the validation rules for attributes.
     *
     * Validation rules are used by [[validate()]] to check if attribute values are valid.
     * Child classes may override this method to declare different validation rules.
     *
     * More info: http://www.yiiframework.com/doc-2.0/guide-input-validation.html
     *
     * @return array
     */
    public function rules()
    {
        return [
            ['subscriberUserGroup', 'required'],
            ['subscriberUserGroup', 'string'],
            ['subscriberUserGroup', 'default', 'value' => ''],
        ];
    }
}

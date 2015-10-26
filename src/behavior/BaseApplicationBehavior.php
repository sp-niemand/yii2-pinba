<?php
/**
 * Class BaseApplicationBehavior
 *
 * @author Dmitri Cherepovski <dmitrij.cherepovskij@murka.com>
 * @package yiiPinba\behavior
 */

namespace yiiPinba\behavior;

use yii\base\ActionEvent;
use yii\base\Application;
use yii\base\Behavior;
use yii\base\Event;
use yii\base\InvalidConfigException;

/**
 * Base application behavior
 *
 * @author Dmitri Cherepovski <dmitrij.cherepovskij@murka.com>
 * @package yiiPinba\behavior
 */
class BaseApplicationBehavior extends Behavior
{
    /**
     * @inheritdoc
     */
    public function attach($owner)
    {
        if (! $owner instanceof Application) {
            throw new InvalidConfigException('Owner must be an application class');
        }
        parent::attach($owner);
    }

    /**
     * @inheritdoc
     */
    public function events()
    {
        return [
            Application::EVENT_BEFORE_REQUEST => 'beforeRequest',
            Application::EVENT_AFTER_REQUEST => 'afterRequest',
            Application::EVENT_BEFORE_ACTION => 'beforeAction',
            Application::EVENT_AFTER_ACTION => 'afterAction',
        ];
    }

    /**
     * Gets run before request. Override this.
     *
     * @param Event $event
     */
    public function beforeRequest(Event $event)
    {
    }

    /**
     * Gets run after request. Override this.
     *
     * @param Event $event
     */
    public function afterRequest(Event $event)
    {
    }

    /**
     * Gets run before action. Override this.
     *
     * @param ActionEvent $event
     */
    public function beforeAction(ActionEvent $event)
    {
    }

    /**
     * Gets run after action. Override this.
     *
     * @param ActionEvent $event
     */
    public function afterAction(ActionEvent $event)
    {
    }
}
<?php
/**
 * Class ApplicationProfilingBehavior
 *
 * @author Dmitri Cherepovski <dmitrij.cherepovskij@murka.com>
 * @package yiiPinba\behavior
 */

namespace yiiPinba\behavior;
use yii\base\Action;
use yii\base\ActionEvent;
use yii\base\Event;

/**
 * Profiles Application::handleRequest() and each action run.
 *
 * Standard \Yii::beginProfile() and \Yii::endProfile() are used,
 * so should be used with \yiiPinba\log\Target to send data to pinba server.
 *
 * @author Dmitri Cherepovski <dmitrij.cherepovskij@murka.com>
 * @package yiiPinba\behavior
 */
class ApplicationProfilingBehavior extends BaseApplicationBehavior
{
    /** @var string Request profile token */
    public $requestToken;

    /** @var string Action profile token */
    public $actionToken;

    /** @var string Profiling log records' category suffix for request */
    public $requestCategoryPrefix = 'app::';

    /** @var string Profiling log records' category suffix for action */
    public $actionCategoryPrefix = 'action::';

    /**
     * @inheritdoc
     */
    public function attach($owner)
    {
        parent::attach($owner);
        if (! $this->requestToken) {
            $this->requestToken = __CLASS__ . '::request';
        }
        if (! $this->actionToken) {
            $this->actionToken = __CLASS__ . '::action';
        }
    }

    /**
     * @return string
     */
    private function getRequestLogCategory()
    {
        return $this->requestCategoryPrefix . $this->owner->className();
    }

    /**
     * @param Action $action
     *
     * @return string
     */
    private function getActionLogCategory(Action $action)
    {
        return $this->actionCategoryPrefix . $action->getUniqueId();
    }

    /**
     * @inheritdoc
     */
    public function beforeRequest(Event $event)
    {
        \Yii::beginProfile($this->requestToken, $this->getRequestLogCategory());
    }

    /**
     * @inheritdoc
     */
    public function afterRequest(Event $event)
    {
        \Yii::endProfile($this->requestToken, $this->getRequestLogCategory());
    }

    /**
     * @inheritdoc
     */
    public function beforeAction(ActionEvent $event)
    {
        \Yii::beginProfile($this->actionToken, $this->getActionLogCategory($event->action));
    }

    /**
     * @inheritdoc
     */
    public function afterAction(ActionEvent $event)
    {
        \Yii::endProfile($this->actionToken, $this->getActionLogCategory($event->action));
    }
}
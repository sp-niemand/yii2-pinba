<?php
/**
 * Class TimersRemindBehavior
 *
 * @author Dmitri Cherepovski <codernumber1@gmail.com>
 * @package yiiPinba\behavior
 */

namespace yiiPinba\behavior;

use yii\base\Application;
use yii\base\Behavior;
use yii\base\InvalidConfigException;
use yii\helpers\VarDumper;
use yiiPinba\component\Pinba;

/**
 * Reminds through logs about the Pinba timers still running after the request
 * handling
 *
 * @author Dmitri Cherepovski <codernumber1@gmail.com>
 * @package yiiPinba\behavior
 */
class TimersRemindBehavior extends Behavior
{
    /** @var Pinba */
    public $pinba;

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
        return [Application::EVENT_AFTER_REQUEST => 'checkTimers'];
    }

    /**
     * Checks if there some timers are still running after the request handling
     */
    public function checkTimers()
    {
        if ($this->pinba->hasRunningTimers()) {
            \Yii::warning(
                'There are still some timers running:' . PHP_EOL
                    . VarDumper::export($this->pinba->getRunningTimerTokens()),
                __CLASS__
            );
        }
    }
}
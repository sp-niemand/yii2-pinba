<?php
/**
 * Class TimersRemindBehavior
 *
 * @author Dmitri Cherepovski <codernumber1@gmail.com>
 * @package yiiPinba\behavior
 */

namespace yiiPinba\behavior;

use yii\base\Behavior;
use yii\base\Event;
use yii\base\InvalidConfigException;
use yii\helpers\VarDumper;
use yiiPinba\component\Pinba;

/**
 * Reminds through logs about the Pinba timers still running after the request
 * handling
 *
 * @author Dmitri Cherepovski <codernumber1@gmail.com>
 * @package yiiPinba\behavior
 *
 * @property-write Pinba|string $pinba
 */
class TimersRemindBehavior extends BaseApplicationBehavior
{
    /** @var Pinba */
    private $pinba;

    /**
     * @param string|Pinba $pinba
     * @throws InvalidConfigException If wrong Pinba component given
     */
    public function setPinba($pinba)
    {
        if (is_string($pinba)) {
            $this->pinba = \Yii::$app->get($pinba);
        } elseif ($pinba instanceof Pinba) {
            $this->pinba = $pinba;
        } else {
            throw new InvalidConfigException('Wrong pinba component given');
        }
    }

    /**
     * Checks if there some timers are still running after the request handling
     */
    public function afterRequest(Event $event)
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
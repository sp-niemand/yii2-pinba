<?php
/**
 * Class Pinba
 *
 * @author Dmitri Cherepovski <dmitrij.cherepovskij@murka.com>
 * @package yiiPinba\component
 */

namespace yiiPinba\component;

use yii\base\Component;

/**
 * Yii2 pinba wrapper
 *
 * @author Dmitri Cherepovski <dmitrij.cherepovskij@murka.com>
 * @package yiiPinba\component
 */
class Pinba extends Component
{
    const DEFAULT_MAX_TAG_LENGTH = 64;
    const TRUNCATION_PREFIX = '...';

    /** @var int Maximum length for tag strings */
    public $maxTagLength = self::DEFAULT_MAX_TAG_LENGTH;

    /** @var resource[] */
    private $runningTimers = [];

    /**
     * Returns the tags ready to be sent to Pinba
     *
     * @param string $tags
     *
     * @return array
     */
    private function formatTags($tags)
    {
        return array_map(function ($tag) {
            if (($len = strlen($tag)) <= $this->maxTagLength) {
                return $tag;
            }
            $truncationToken = '...' . $len;
            return substr_replace(
                $tag,
                $truncationToken,
                $this->maxTagLength - strlen($truncationToken)
            );
        }, $tags);
    }

    /**
     * Starts the timer
     *
     * @param string $token
     * @param array $tags
     *
     * @return bool Operation success
     */
    public function startTimer($token, $tags = [])
    {
        if (isset($this->runningTimers[$token])) {
            return false;
        }
        $actualTags = array_merge($tags, ['timerToken' => $token]);
        $this->runningTimers[$token] = pinba_timer_start($this->formatTags($actualTags));
        return true;
    }

    /**
     * Stops the timer
     *
     * @param string $token
     *
     * @return bool Operation success
     */
    public function stopTimer($token)
    {
        if (! isset($this->runningTimers[$token])) {
            return false;
        }
        $timer = $this->runningTimers[$token];
        unset($this->runningTimers[$token]);
        if (! pinba_timer_get_info($timer)['started']) {
            return false;
        }
        return pinba_timer_stop($timer);
    }

    /**
     * Stops and flushes all timers
     */
    public function flush()
    {
        pinba_timers_stop();
        pinba_flush();
    }

    /**
     * Creates stopped Pinba timer so it can be later flushed to Pinba
     *
     * @param array $tags
     * @param int $value
     */
    public function profile($tags, $value)
    {
        pinba_timer_add($this->formatTags($tags), $value);
    }
}
<?php
/**
 * Class Target
 *
 * @author Dmitri Cherepovski <dmitrij.cherepovskij@murka.com>
 * @package yiiPinba\log
 */

namespace yiiPinba\log;

use yii\log\Logger;
use yii\log\Target as BaseTarget;
use yiiPinba\component\Pinba;

/**
 * Pinba log target. Saves Yii2 profiling data to Pinba.
 *
 * Use \yii\BaseYii::beginProfile() and \yii\BaseYii::endProfile()
 * to profile your code
 *
 * @author Dmitri Cherepovski <dmitrij.cherepovskij@murka.com>
 * @package yiiPinba\log
 */
class Target extends BaseTarget
{
    /** @var array[] */
    private $profilingSessions = [];

    /** @var string Pinba component name */
    public $pinba = 'pinba';

    /** @var string Reporter name to be sent to Pinba */
    public $reporter = __CLASS__;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->setLevels(Logger::LEVEL_PROFILE);
        $this->exportInterval = 1;
    }

    /**
     * @return Pinba
     */
    private function getPinba()
    {
        return \Yii::$app->get($this->pinba);
    }

    /**
     * @param string $token
     *
     * @return null|array
     */
    private function getProfilingSession($token)
    {
        return isset($this->profilingSessions[$token])
            ? $this->profilingSessions[$token]
            : null;
    }

    /**
     * @param string $token
     */
    private function forgetProfiling($token)
    {
        unset($this->profilingSessions[$token]);
    }

    /**
     * @param array $logMessage
     * @see Logger::$messages
     */
    private function registerProfilingStart(array $logMessage)
    {
        $this->profilingSessions[$logMessage[0]] = [ // $logMessage[0] is a token
            'timestamp' => $logMessage[3],
            'category' => $logMessage[2],
        ];
    }

    /**
     * @param array $logMessage
     * @see Logger::$messages
     */
    private function registerProfilingStop(array $logMessage)
    {
        $token = $logMessage[0];
        if (! $profilingStartData = $this->getProfilingSession($token)) {
            return;
        }
        // Yii gets timestamps with utime()
        $timeDiff = $logMessage[3] - $profilingStartData['timestamp'];
        $this->getPinba()->profile(
            [
                'reporter' => $this->reporter,
                'category' => $profilingStartData['category'],
            ],
            $timeDiff
        );
        $this->forgetProfiling($token);
    }

    /**
     * @inheritdoc
     */
    public function collect($messages, $final)
    {
        $this->messages = array_merge(
            $this->messages,
            $this->filterMessages($messages, $this->getLevels(), $this->categories, $this->except)
        );
        $this->export();
        $this->messages = [];
    }

    /**
     * @inheritdoc
     */
    public function export()
    {
        foreach ($this->messages as $message) {
            switch ($message[1]) {
                case Logger::LEVEL_PROFILE_BEGIN:
                    $this->registerProfilingStart($message); break;
                case Logger::LEVEL_PROFILE_END:
                    $this->registerProfilingStop($message); break;
                default:
                    \Yii::warning('Only profiling logs allowed for this target', __METHOD__);
            }
        }
    }
}
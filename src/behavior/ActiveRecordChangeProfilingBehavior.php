<?php
/**
 * Class ActiveRecordChangeProfilingBehavior
 *
 * @author Dmitri Cherepovski <dmitrij.cherepovskij@murka.com>
 * @package yiiPinba\behavior
 */

namespace yiiPinba\behavior;
use yii\base\Event;
use yii\db\ActiveRecord;
use yii\db\BaseActiveRecord;
use yii\base\Behavior;

/**
 * This behavior wraps every change operation for ActiveRecord
 * in profiling logs
 *
 * @author Dmitri Cherepovski <dmitrij.cherepovskij@murka.com>
 * @package yiiPinba\behavior
 */
class ActiveRecordChangeProfilingBehavior extends Behavior
{
    const SEPARATOR = '/';
    
    const OP_NAME_INSERT = 'insert';
    const OP_NAME_UPDATE = 'update';
    const OP_NAME_DELETE = 'delete';
    
    /** @var string Profiling category prefix */
    public $prefix = 'arOperation::';
    
    /** @var array */
    private $categoryCache = [];

    /**
     * @var array
     */
    private static $eventToOperationMap = [
        BaseActiveRecord::EVENT_BEFORE_UPDATE => self::OP_NAME_UPDATE,
        BaseActiveRecord::EVENT_BEFORE_INSERT => self::OP_NAME_INSERT,
        BaseActiveRecord::EVENT_BEFORE_DELETE => self::OP_NAME_DELETE,

        BaseActiveRecord::EVENT_AFTER_UPDATE => self::OP_NAME_UPDATE,
        BaseActiveRecord::EVENT_AFTER_INSERT => self::OP_NAME_INSERT,
        BaseActiveRecord::EVENT_AFTER_DELETE => self::OP_NAME_DELETE,
    ];
    
    /**
     * Returns profile log record category
     * 
     * @param string $operation
     * 
     * @return string
     */
    protected function getCategory($operation)
    {
        if (isset($this->categoryCache[$operation])) {
            return $this->categoryCache[$operation];
        }
        
        $owner = $this->owner;
        /** @var ActiveRecord $owner */
        $tableName = $owner->getDb()->getSchema()->getRawTableName($owner->tableName());
        return $this->categoryCache[$operation] = $this->prefix . $tableName . self::SEPARATOR . $operation;
    }

    /**
     * @param Event $event
     */
    public function beginProfile(Event $event)
    {
        $category = $this->getCategory(static::$eventToOperationMap[$event->name]);
        \Yii::beginProfile($category, $category);
    }

    /**
     * @param Event $event
     */
    public function endProfile(Event $event)
    {
        $category = $this->getCategory(static::$eventToOperationMap[$event->name]);
        \Yii::endProfile($category, $category);
    }
    
    /**
     * {@inheritdoc}
     */
    public function events()
    {
        return [
            BaseActiveRecord::EVENT_BEFORE_INSERT => 'beginProfile',
            BaseActiveRecord::EVENT_BEFORE_UPDATE => 'beginProfile',
            BaseActiveRecord::EVENT_BEFORE_DELETE => 'beginProfile',

            BaseActiveRecord::EVENT_AFTER_INSERT => 'endProfile',
            BaseActiveRecord::EVENT_AFTER_UPDATE => 'endProfile',
            BaseActiveRecord::EVENT_AFTER_DELETE => 'endProfile',
        ];
    }
}

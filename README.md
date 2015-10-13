# yii2-pinba

## Description

Integrates [pinba](http://pinba.org/ "Pinba site") 
with [Yii2](https://github.com/yiisoft/yii2 "Yii2 repository")

## Usage

Add the main component and the log target to the config.

```php
    'components' => [
        // ...
        'pinba' => [
            'class' => \yiiPinba\component\Pinba::className(),
        ],
        // ...
        'log' => [
            'targets' => [
                // ...
                [
                    'class' => \yiiPinba\log\Target::className(),
                ],
                // ...
            ]
        ]
        // ...
    ]
```

The target handles export of the profile logs to Pinba. Use standard Yii2 method for profiling:

```php
\Yii::beginProfile($token, $category);

// ...

\Yii::endProfile($token, $category);
```

Or you can use methods from the component directly:

```php
$p = \Yii::$app->get('pinba');
/** @var Pinba $p */
$p->startTimer('timer1');

// ...

$p->stopTimer('timer1');
```

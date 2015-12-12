Yii2 Resque Enqueue
===================
Extension capable of enqueuing jobs to Resque backend.

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist janakawicks/yii2-resque-enqueue "*"
```

or add

```
"janakawicks/yii2-resque-enqueue": "*"
```

to the require section of your `composer.json` file.


Configuration
-------------

To use this extension, you have to configure the yii2-redis Connection class and ResqueEnqueue class in your application configuration:

```php
return [
    //....
    'components' => [
        'redis' => [
            'class' => 'yii\redis\Connection',
            'hostname' => 'localhost',
            'port' => 6379,
            'database' => 0,
        ],
        'resqueEnqueue' => [
          'class' => 'janakawicks\resque\ResqueEnqueue',
        ],
    ]
];
```

Usage
-----

Once the extension is installed, simply use it in your code by  :

```php
$resqueEnqueue = Yii::$app->resqueEnqueue;
$resqueEnqueue->queue = 'mysql_backup';
$resqueEnqueue->enqueue('MySQLBackup', 'mydb01'); // with single argument.
$resqueEnqueue->enqueue('MySQLBackup', 'mydb01', true, '2015-12-31'); //with multiple arguments.
$resqueEnqueue->enqueue('MySQLBackup', ['mydb01', 'mydb02']); // with single argument with array
```

Notes
-----
You can override the namespace at the configuration or in the code

```php
return [
    //....
    'components' => [
        'redis' => [
            'class' => 'yii\redis\Connection',
            'hostname' => 'localhost',
            'port' => 6379,
            'database' => 0,
        ],
        'resqueEnqueue' => [
          'class' => 'janakawicks\resque\ResqueEnqueue',
          'namespace' => 'my_engine:',
        ],
    ]
];
```

```php
$resqueEnqueue = Yii::$app->resqueEnqueue;
$resqueEnqueue->namespace = 'my_engine:';
```

Key is generated as follows
```
  $namespace:queue:$queue_name

  Example:
    resque:queue:mysql_backup
```

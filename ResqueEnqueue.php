<?php

namespace janakawicks\resque;

use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;

class ResqueEnqueue extends Component
{
  public $namespace = 'resque:';
  public $queue;

  public function enqueue()
  {
    $enqueue = [];

    //arg[0] => class
    //arg[1...] => arguments
    $args = func_get_args();

    $enqueue['class'] = $args[0];
    foreach($args as $v){
      if ($k < 1)
        continue;
      $enqueue['args'][] = $v;
    }
    if(!isset($enqueue['args']))
      $enqueue['args'] = [];

    return Yii::$app->redis->executeCommand(
      'lpush', 
      $this->namespace . ':queue:' . $this->queue,
      json_encode($enqueue));
  }

}

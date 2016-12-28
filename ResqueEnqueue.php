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
    foreach($args as $k => $v){
      if ($k < 1)
        continue;
      $enqueue['args'][] = $v;
    }
    if(!isset($enqueue['args']))
      $enqueue['args'] = [];

    return Yii::$app->redis->executeCommand(
      'lpush', [
        $this->namespace . 'queue:' . $this->queue,
        json_encode($enqueue)
        ]
      );
  }

  public function listWorkers($queues=[])
  {
    if(empty($queues))
      return $this->_listWorkers($this->queue);
    else {
      $workers = [];
      foreach($queues as $q){
        $workers = $workers + $this->_listWorkers($q);
      }
      return $workers;
    }
  }

  public function _listWorkers($queue)
  {
    //returns {host, pid, status, started_at || running details}
    $workers = [];
    $wrks = Yii::$app->redis->executeCommand('keys', ['resque:worker:*:'.$queue.'*']);
    foreach($wrks as $w){
      $arr = preg_split("/:/", $w);
      if(!isset($arr[5])){
        $workers[$arr[3]] = [ "host" => $arr[2], "pid" => $arr[3], "queue" => $arr[4], "status" => "running"];
        $details = json_decode(Yii::$app->redis->executeCommand('get', [$w]), true);
        $workers[$arr[3]]['started_at'] = date('Y-m-d h:i:s', strtotime($details["run_at"]));
      }else if (!isset($workers[$arr[3]])){
        $workers[$arr[3]] = [ "host" => $arr[2], "pid" => $arr[3], "queue" => $arr[4], "status" => "waiting" ];
        $details = Yii::$app->redis->executeCommand('get', [$w]);
        $workers[$arr[3]]['started_at'] = date('Y-m-d h:i:s', strtotime($details));
      }
    }

    return $workers;
  }


}

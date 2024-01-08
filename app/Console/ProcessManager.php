<?php namespace App\Console;

class ProcessManager {

    protected $redis;

    protected $process_limit = 5;

    protected $timeout = 60;

    protected $timeover;

    protected $group;

    public $key;

    public function __construct($group, $timeout = 60, $limit = 1)
    {
        /* comment by pinakin 19-11-2019
		$this->redis = new \Redis();
        $this->redis->connect('127.0.0.1', 6379);
        $this->redis->get('testing.conncetion');
		*/
        $this->group = $group;

        $this->timeout = $timeout;

        $this->timeover = time() + $this->timeout;

        $this->process_limit = $limit;

		/* comment by pinakin 19-11-2019
        $this->cleanKilledProcess();

        $this->register();
		*/
    }

    function __destruct()
    {
		/* comment by pinakin 19-11-2019
        $this->unregister();
		*/
    }

    public function canProcess() {
        if ($this->reachedLimit())
            return false;

        return $this->timeover > time();
    }

    public function lock($id)
    {
        $key = 'processing.' . $this->group . '.' . $id;

        $isSet = $this->redis->setnx($key, $this->key);

        if (!$isSet)
            return false;

        $this->redis->setTimeout($key, $this->timeout);

        return true;
    }

    public function unlock($id)
    {
        $key = 'processing.' . $this->group . '.' . $id;

        $this->redis->del($key);
    }

    private function unlockKeys($process_key = null)
    {
        if (!$process_key)
            $process_key = $this->key;

        $keys = $this->redis->keys('processing.' . $this->group . '.*');

        foreach($keys as $key) {
            $process = $this->redis->get($key);

            if ($process != $process_key)
                continue;

            $this->redis->del($key);
        }
    }

    private function reachedLimit()
    {
        $processes = $this->redis->keys('process.' . $this->group . '.*');

        return ($processes && count($processes) > $this->process_limit) ? true : false;
    }

    private function cleanKilledProcess()
    {
        $keys = $this->redis->keys('process.' . $this->group . '.*');

        foreach ($keys as $key) {
            $process = $this->redis->get($key);

            $process = json_decode($process);

            if (!$process) {
                $this->redis->del($key);
                continue;
            }

            // process is running?
            if (file_exists('/proc/'.$process->pid))
                continue;

            $this->unregister($process->key);
        }
    }

    private function register()
    {
        $this->key = md5( $this->group . time() . str_shuffle('QWERTYUIOOPASDFGHJKLZXCVBNMQWERTYUIOPASD') );

        $key = 'process.' . $this->group . '.' . $this->key;

        $this->redis->set($key, json_encode([
            'pid'      => posix_getpid(),
            'key'      => $this->key,
            'timeover' => $this->timeover
        ]));
    }

    public function unregister($process_key = null)
    {
        if (!$process_key)
            $process_key = $this->key;

        $this->redis->del('process.' . $this->group . '.' . $process_key);

        $this->unlockKeys($process_key);
    }


}
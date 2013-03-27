<?php

namespace Bioteawebapi\RedQueue;
use Credis_Client, Credis_Cluster;

class RedQueue
{
    /**
     * @private var Credis_Client|Credis_Cluster
     */
    private $redis;

    // --------------------------------------------------------------

    public function __construct($redisClient, array $tasks = array())
    {
        //Since Credis doesn't OOP correctly, we have to futz instead of use type hinting
        if ( ! ($redisClient instanceof Credis_Client) && ! ($redisClient instanceof Credis_Cluster)) {
            throw new \InvalidArgumentException("\$redisClient must be instace of Credis_Client or Credis_Cluster");
        }

        $this->redis = $redisClient;

        foraech($tasks as $task) {
            $this->addAvailableTask($task);
        }
    }

    // --------------------------------------------------------------

    /**
     * Add available task
     *
     * @param RedTask $task
     */
    public function addAvailableTask(RedTask $task)
    {
        $this->tasks[$task->getName()] = $task;
    }

    // --------------------------------------------------------------

    /**
     * Enqueue a task for processing later
     *
     * @param string  $task          The taskname
     * @param mixed   $data          Any json-encodable data or a scalar
     * @param string  $queue         The queue to place the task on
     * @param boolean $highPriority  If true, the task will be added to the front of the queue
     */
    public function enqueue($task, $data, $queue = 'default', $highPriority = false)
    {
        //Serialize JSON
        $rdata = array(
            'data' => $data,
            'task' => $task
        );

        //Add to queue
        ($highPriority) ? $this->redis->lpush($queue, $rdata) : $this->redis->rpush($queue, $rdata);
    }

    // --------------------------------------------------------------

    /**
     * Start a worker
     *
     * @param string|array $queues
     */
    public function startWorker($queues = array())
    {
        // --
    }

    // --------------------------------------------------------------

    public function perform($task, $data)
    {
        // --
    }
}

/* EOF: RedQueue.php */
<?php
class Test_redis extends CI_Controller
{

    function __construct()
    {
        parent::__construct();

        $this->load->library('unit_test');

        $this->load->driver('cache', array('adapter' => 'redis'));
    }

    function index()
    {
        $this->unit->run($this->cache->redis->is_supported(), 'is_true');

        $this->unit->run($this->cache->redis->save('foo', 'bar'), 'is_true');

        $this->unit->run($this->cache->redis->get('foo'), 'bar');

        $this->unit->run($this->cache->redis->delete('foo'), 'is_true');

        $this->unit->run($this->cache->redis->save('foo', 'bar', 1800), 'is_true');

        $this->unit->run(
            $this->cache->redis->get_metadata('foo'),
            array(
                'data' => 'bar',
                'expire' => time() + 1800
            )
        );

        $this->unit->run($this->cache->redis->clean(), 'is_true');

        $this->unit->run($this->cache->redis->get('foo'), 'is_false');

        $this->unit->run($this->cache->redis->cache_info(), 'is_array');

        echo $this->unit->report();
    }

}
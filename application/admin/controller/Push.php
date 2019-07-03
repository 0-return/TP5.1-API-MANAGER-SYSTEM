<?php
/**
 * Created by PhpStorm.
 * User: EVOL
 * Date: 2019/6/10
 * Time: 22:27
 */
namespace app\admin\controller;
use app\admin\common\controller\Init;
use think\Db;
use think\facade\Cookie;
use think\facade\Request;

class Push extends Init
{
    public function initialize()
    {
        parent::_init();
        $this->table = $this->config['prefix']."admin_push";
    }

    public function index()
    {
        $map = $this->_search();
        if (method_exists($this, '_filter'))
        {
            $this->_filter($map);
        }
        $map[] = [['status','>','-1']];
        $where['where'] = $map;
        $this->_list('',$where);
        return view();
    }

    public function _filter(&$map)
    {
        $get = Request::get();
        if (!empty($get['begintime']) && !empty($get['endtime']))
        {
            $map = [['add_time','between',[strtotime($get['begintime']),strtotime($get['endtime'])]]];
        }
        $this->checkSearch($map);
    }


    public function _after_show(&$list)
    {
        $list['thumb'] = unserialize($list['thumb']);
        $list['web_url'] = $this->config['assist']['web_url'];
    }
}
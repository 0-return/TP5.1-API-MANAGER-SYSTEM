<?php
/**
 * Created by PhpStorm.
 * User: EVOL
 * Date: 2019/5/10
 * Time: 20:21
 */
namespace app\admin\controller;
use app\admin\common\controller\Init;
use think\facade\Cookie;
use think\facade\Request;

class Video extends Init
{
    private $fields = array(
        'title' => '标题',
        'key' => '类型',
        'id' => 'id',
    );

    public function initialize()
    {
        parent::_init();
        $this->table = $this->config['prefix']."source_video";
    }

    public function index()
    {
        $map = $this->_search();
        if (method_exists($this, '_filter')) {
            $this->_filter($map);
        }

        $map[] = ['status','>','-1'];
        $where['where'] = $map;
        $this->_list('',$where,'','','');
        $this->assign('fields',$this->fields);
        return view();
    }
}
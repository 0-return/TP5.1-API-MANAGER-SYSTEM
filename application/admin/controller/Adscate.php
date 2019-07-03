<?php
/**
 * Created by PhpStorm.
 * User: EVOL
 * Date: 2019/6/10
 * Time: 22:33
 */

namespace app\admin\controller;
use app\admin\common\controller\Init;
use think\Db;
use think\facade\Request;

class Adscate extends Init
{
    public function initialize()
    {
        parent::_init();
        $this->table = $this->config['prefix']."adscate";
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

    public function _before_add(&$post)
    {
        if (Request::isPost())
        {
            $post['start_time'] = strtotime($post['start_time']);
            $post['end_time'] = strtotime($post['end_time'])+86399;
            $post['add_time'] = time();
            $post['flag'] = randNum('',6);
            $post['status'] = '0';
        }else{
            $options = cateTreeOption($this->table,$field = array('id' => 'id','pid' => 'pid','status' => 'status','title' => 'title'));
            $this->assign('options',$options);
        }
    }
    public function _before_show(&$post)
    {
        if (Request::isPost())
        {

        }else{
            $get = Request::get();
            $options = cateTreeOption($this->table,$field = array('id' => 'id','pid' => 'pid','status' => 'status','title' => 'title'),0,0,$get['id']);
            $this->assign('options',$options);
        }
    }

    public function _before_update(&$post)
    {
        $post['start_time'] = strtotime($post['start_time']);
        $post['end_time'] = strtotime($post['end_time'])+86399;
        $post['edit_time'] = time();
        $post = data2empty($post);
    }

    public function _after_edit(&$list)
    {
        $options = cateTreeOption($this->table,$field = array('id' => 'id','pid' => 'pid','status' => 'status','title' => 'title'));
        $this->assign('options',$options);
    }
}
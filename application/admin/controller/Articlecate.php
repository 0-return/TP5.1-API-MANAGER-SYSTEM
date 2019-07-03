<?php
/**
 * Created by PhpStorm.
 * User: EVOL
 * Date: 2019/5/10
 * Time: 20:21
 */
namespace app\admin\controller;
use app\admin\common\controller\Init;
use think\facade\Request;

class Articlecate extends Init{

    public function initialize()
    {
        parent::_init();
        $this->table = $this->config['prefix']."articlecate";
    }

    public function index()
    {
        $map = $this->_search();
        if (method_exists($this, '_filter')) {
            $this->_filter($map);
        }
        $map['status'] = (1);
        $where['where'] = $map;
        $this->_list('',$where,'','','',1000);
        return view();
    }

    public function _after_list(&$list)
    {
        foreach ($list as $key => $value)
        {
            if ($value['thumb'])
            {
                $list[$key]['thumb'] = unserialize($value['thumb']);
                $list[$key]['web_url'] = $this->config['assist']['web_url'];
            }
        }

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
            if (isset($post['images']) && !empty($post['images']))
            {
                $post['thumb'] = serialize($post['images']); unset($post['images']);
            }
            $post['add_time'] = time();
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

    public function _after_show(&$list)
    {
        $list['thumb'] = unserialize($list['thumb']);
        $list['web_url'] = $this->config['assist']['web_url'];
    }

    public function _before_update(&$post)
    {
        if (isset($post['images']) && !empty($post['images']))
        {
            $post['thumb'] = serialize($post['images']); unset($post['images']);
        }
        $post['edit_time'] = time();
        $post = data2empty($post);
    }



}
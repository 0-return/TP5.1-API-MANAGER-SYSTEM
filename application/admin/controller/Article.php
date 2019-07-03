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

class Article extends Init{

    private $fields = array(
        'title' => '标题',
        'keywords' => '关键字',
        'describe' => '描述',
        'content' => '内容',
        'source' => '来源',
    );

    public function initialize()
    {
        parent::_init();
        $this->table = $this->config['prefix']."article";
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
            $post['content'] = $post['editorValue'];unset($post['editorValue']);
            $post['author'] = isset($post['author']) && !empty($post['author'])?$post['author']:json_decode(Cookie::get(Request::module().'info'),1)['username'];
            $post['add_time'] = time();
        }else{
            $options = cateTreeOption($this->config['prefix'].'articlecate',$field = array('id' => 'id','pid' => 'pid','status' => 'status','title' => 'title'));
            $this->assign('options',$options);
        }
    }

    public function _before_show(&$post)
    {
        if (Request::isPost())
        {

        }else{
            $get = Request::get();
            $options = cateTreeOption($this->config['prefix'].'articlecate',$field = array('id' => 'id','pid' => 'pid','status' => 'status','title' => 'title'),0,0,$get['aid']);
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
        $post['content'] = $post['editorValue'];unset($post['editorValue']);
        $post['edit_time'] = time();
    }


}
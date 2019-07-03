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

class Ads extends Init
{
    private $fields = array(
        'title' => '标题',
        'position' => '广告位置',
        'link' => '链接特征',
        'describe' => '描述',
    );

    private $advertorials = array(
        '立即下载',
        '点击购买',
    );

    //获取广告商户
    private $merchant = array(
        '1' => '杨哥',
        '3' => '李哥'
    );
    public function initialize()
    {
        parent::_init();
        $this->table = $this->config['prefix']."ads";
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
            $position = Db::table($this->config['prefix'].'adscate')->where('id','=',$post['pid'])->value('position');
            $post['position'] = $position;
            $post['add_time'] = time();
            $post['source'] = json_encode($post['images']);unset($post['images']);
        }else{

            $this->assign('merchant',$this->merchant);
            $this->assign('advertorials',$this->advertorials);
        }
    }

    public function getDataById()
    {
        $post = Request::post();

        $res = Db::table($this->config['prefix'].'adscate')->where('uid','=',$post['id'])->select();
        if ($res)
        {
            self::returnMsg(10000,'获取成功',$res);
        }else{
            self::returnMsg(10001,'没有数据');
        }
    }

    public function _before_show(&$post)
    {
        if (Request::isPost())
        {

        }else{
            $this->assign('merchant',$this->merchant);
            $this->assign('advertorials',$this->advertorials);
        }
    }

    public function _after_show(&$list)
    {
        $list['merchant'] = $this->merchant[$list['uid']];
        $list['source'] = json_decode($list['source'],1);
        $list['web_url'] = $this->config['assist']['web_url'];
    }

    public function _before_update(&$post)
    {
        if (isset($post['images']) && !empty($post['images']))
        {
            $post['source'] = serialize($post['images']); unset($post['images']);
        }
        $position = Db::table($this->config['prefix'].'adscate')->where('id','=',$post['pid'])->value('position');
        $post['position'] = $position;
        $post['edit_time'] = time();
    }


}
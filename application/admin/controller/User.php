<?php
/**
 * Created by PhpStorm.
 * User: EVOL
 * Date: 2019/5/10
 * Time: 20:21
 */
namespace app\admin\controller;
use app\admin\common\controller\Init;
use think\Db;
use think\facade\Request;

class User extends Init{

    private $fields = array(
        'username' => '名称',
        'nickname' => '昵称',
    );
    public function initialize()
    {
        parent::_init();
        $this->table = $this->config['prefix']."admin_user";
    }

    public function index()
    {
        $map = $this->_search();
        if (method_exists($this, '_filter')) {
            $this->_filter($map);
        }
        $map[] = ['status','>','-1'];
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
            $post['username'] = mt_rand(1000,9999).mt_rand(10,99);
            parent::checkUnique('username',$post['username']);
            if (factoryMd5($post['password']) == factoryMd5($post['checkpass'])) $post['password'] = factoryMd5($post['password']);
            unset($post['checkpass']);
            $post['add_time'] = time();

        }else{
            $res = Db::table($this->config['prefix'].'admin_role')->select();
            $this->assign('list',$res);

        }
    }

    public function _before_show(&$post)
    {
        if (Request::isPost())
        {

        }else{
            $res = Db::table($this->config['prefix'].'admin_role')->select();
            $this->assign('list',$res);
        }
    }

    public function _before_update(&$post)
    {
        if (Request::isPost())
        {
            if (isset($post['password']) && !empty($post['password']) &&  factoryMd5($post['password']) == factoryMd5($post['checkpass'])) $post['password'] = factoryMd5($post['password']);
            unset($post['checkpass']);
            $post['edit_time'] = time();
        }
    }

    public function _before_anything()
    {
        $get = Request::get();
        $where = [['id','=',$get['id']]];
        $res = Db::table($this->table)->where($where)->find();
        $res['login_time'] = date('Y-m-d H:i:s',$res['login_time']);
        $res['pretime'] = date('Y-m-d H:i:s',$res['pretime']);
        self::returnMsg(10000,'获取成功',$res);
    }



}

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
use think\facade\Cookie;
use think\facade\Request;

class Member extends Init{

    private $fields = array(
        'username' => '会员名称',
        'nickname' => '昵称',
        'pid' => '父级编号',
        'email' => '邮箱',

    );

    public function initialize()
    {
        parent::_init();
        $this->table = $this->config['prefix']."member";
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
            $post['password'] = factoryMd5($post['password'],$post['username']);
            $post['confirmpassword'] = factoryMd5($post['confirmpassword'],$post['username']);
            if ($post['password'] !== $post['confirmpassword'])
            {
                self::returnMsg('10001','两次密码不一致，请重新输入！');
            }
            unset($post['confirmpassword']);
            $post['vip'] = isset($post['vip'])?$post['vip']:'0';
            $post['wallet'] = isset($post['wallet'])?$post['wallet']:'0';
            $post['status'] = isset($post['wallet'])?$post['wallet']:'0';
            $post['level'] = isset($post['level'])?$post['level']:'0';
            $post['add_time'] = time();
        }
    }

    public function _before_update(&$post)
    {
        if (isset($post['confirmpassword']))
        {
            $post['password'] = factoryMd5($post['confirmpassword'],$post['username']);

            if (isset($post['confirmpassword']))
            {
                $where = [['id','=',$post['id']],['username','=',$post['username']],['password','=',factoryMd5($post['confirmpassword'])]];
                $res = Db::table($this->table)->where($where)->find();
                if ($res)
                {
                    self::returnMsg('10001','新密码和原密码一致，无需修改！');
                }else{
                    $post['password'] = $post['confirmpassword'];
                }
            }
            unset($post['confirmpassword']);
        }
        $post['edit_time'] = time();
    }

    public function _after_show(&$list)
    {
        $list['web_url'] = $this->config['assist']['web_url'];
    }

    public function _before_forbid(&$field)
    {
        $field = 'user_status';
    }

    public function _before_anything()
    {
        $get = Request::get();
        $where = [['id','=',$get['id']]];
        $res = Db::table($this->table)->where($where)->find();
        $res['web_url'] = strpos($res['face'],'http') !== false?'':$this->config['assist']['web_url'];
        $this->assign('vo',$res);
        return view('show');
    }

    public function _calByAjax()
    {
        $obj = Db::table($this->table);
        self::calByAjax($obj,'id');
    }
}
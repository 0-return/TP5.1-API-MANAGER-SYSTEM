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

class Shield extends Init{



    public function initialize()
    {

        parent::_init();
        $this->table = $this->config['prefix']."admin_shield";
    }

    public function index()
    {
        $res = Db::table($this->table)->select();
        $this->assign('list',$res);
        return view();
    }

    public function _before_update(&$post)
    {
        $res = Db::table($this->table)->find();
        if (!$res)
        {
            $data['terms'] = $post['terms'];
            $data['ips'] = $post['ips'];
            $res = Db::table($this->table)->insert($data);
            $res?self::returnMsg(10000,$this->message['success']):self::returnMsg(10001,$this->message['error']);
            exit(0);
        }
    }

}

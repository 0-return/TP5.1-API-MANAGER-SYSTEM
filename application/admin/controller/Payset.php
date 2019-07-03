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

class Payset extends Init{



    public function initialize()
    {

        parent::_init();
        $this->table = $this->config['prefix']."admin_config_pay";
    }

    public function index()
    {
        $res = Db::table($this->table)->select();
        $this->assign('list',$res);
        return view();
    }

}

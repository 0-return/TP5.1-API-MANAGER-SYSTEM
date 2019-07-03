<?php

/**
 * Create by .
 * Cser Administrator
 * Time 15:07
 */
namespace app\admin\behaviors;
use think\Controller;
use app\admin\model\Auth;
use app\admin\model\Role;
use think\Db;
use think\facade\Cookie;
use think\facade\Request;

class Rbac extends Controller
{

    public function run()
    {
        $user = json_decode(Cookie::get(Request::module().'_info'),1);
        $where['id'] = $user['roleid'];
        $rules = Db::table('db_admin_role')->where($where)->value('rules');unset($where);
        $ids_arr = explode(',',$rules);
        $ac = strtolower('/'.Request::controller().'/'.Request::action());
        $where['mca'] = $ac;
        $where['is_check'] = '1';
        $auth = Db::table('db_admin_auth')->where($where)->value('id');

        if ($user['roleid'] != '1')
        {
            if (!in_array($auth,$ids_arr))
            {
                $this->error('你没有权限，请联系管理员');
            }
        }

    }

}
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

class Role extends Init{

    public function initialize()
    {
        parent::_init();
        $this->table = $this->config['prefix']."admin_role";
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

    public function _after_list(&$list)
    {
        foreach ($list as $key => $value)
        {
            $where['roleid'] = $value['id'];
            $res = Db::table($this->config['prefix'].'admin_user')->where($where)->select();
            $str = '';
            if ($res){
                foreach ($res as $ky => $val)
                {
                    $str .= $val['nickname'].',';
                }
                $str = trim($str,',');
            }
            $list[$key]['str'] = $str;
        }
    }
    /**
     * @auth YW
     * @date 2018.03.06
     * @purpose 获取控制器列表
     * @return void
     */
    public function _before_add(&$post)
    {
        if (Request::isPost())
        {
            $post['rules'] = trim(implode(',',$post['rules']),',');
            $post['add_time'] = time();
            $post['status'] = '1';
        }else{
            $auth = self::getAuth();
            $this->assign('auth',$auth);
        }

    }

    /**
     * @auth YW
     * @date 2018.03.06
     * @purpose 获取控制器列表
     * @return void
     */
    public function _after_show(&$post)
    {
        if (Request::isPost())
        {
            $post['rules'] = trim(implode(',',$post['rules']),',');
            $post['edit_time'] = time();
        }else{
            //获取用户操作列表
            $role = Request::get();
            $r = Db::table($this->config['prefix'].'admin_role')->where('id',$role['id'])->field('rules')->find();
            $rules = explode(',',$r['rules']);
            $auth = self::getAuth('',$rules);
            $this->assign('auth',$auth);
        }
    }

    public function _before_update(&$post)
    {
        if (Request::isPost())
        {
            $post['rules'] = trim(implode(',',$post['rules']),',');
            $post['edit_time'] = time();
        }
    }

    /**
     * @auth YW
     * @date 2018.03.06
     * @purpose 获取控制器列表
     * @return void
     */
    public function getAuth($pid = 0,$rules = array())
    {
        $res = DB::table($this->config['prefix'].'admin_auth')->where('pid','in',$pid)->select();
        if (!$res) return false;
        foreach ($res as $key => $value)
        {
            if (in_array($value['id'],$rules)) $res[$key]['check'] = 'checked';
            $res[$key]['child'] = $t = DB::table($this->config['prefix'].'admin_auth')->where('pid','=',$value['id'])->select();

            foreach ($res[$key]['child'] as $ky => $vl)
            {
                if (in_array($vl['id'],$rules)) $res[$key]['child'][$ky]['check'] = 'checked';
                $res[$key]['child'][$ky]['child'] = $t = DB::table($this->config['prefix'].'admin_auth')->where('pid','=',$vl['id'])->select();

                foreach ($res[$key]['child'][$ky]['child'] as $k => $v)
                {
                    if (in_array($v['id'],$rules)) $res[$key]['child'][$ky]['child'][$k]['check'] = 'checked';
                }
            }
        }
        return $res;
    }


}

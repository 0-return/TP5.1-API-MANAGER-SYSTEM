<?php
namespace app\admin\controller;
use app\admin\common\controller\Init;
use think\facade\Cache;
use think\facade\Cookie;
use think\facade\Request;
use think\Db;


class Iframe extends Init
{
    public function initialize()
    {
        parent::_init();
        $this->table = $this->config['prefix'].'admin_auth';
    }

    public function welcome()
    {

        return view();
    }


    public function index(){

        $map = '';
        if (method_exists($this, '_filter')) {
            $this->_filter($map);
        }
        $user = json_decode(Cookie::get(Request::module().'_info'),1);
        $this->config['user'] = $user;
        $this->assign('vo',$this->config);
        return $this->fetch();
    }

    /**
     * @auth YW
     * @date 2017.12.2
     * @purpose
     * @return void
     */
    protected function _filter(&$map)
    {

        $quickmenu = $this->takMenu();
        $menu = $this->getMenu();

        $left_menu = self::editMenu($menu);
        $menu_first = self::findMenu($left_menu,1);
        $menu_child = self::findMenu($left_menu,2);
        $this->assign('vo',$this->config);
        $this->assign('m_first',$menu_first);
        $this->assign('m_child',$menu_child);
        $this->assign('quickmenu',$quickmenu);
        $this->checkSearch($map);
    }

    /**
     * @auth YW
     * @date 2018.10.26
     * @purpose 快捷菜单
     * @return void
     */
    public function takMenu()
    {
        $user = json_decode(Cookie::get(Request::module().'_info'),1);
        $where['uid'] = $user['id'];
        $ids = Db::table($this->config['prefix'].'admin_menu')->where($where)->value('ids');unset($where);

        $ids_arr = explode(',',$ids);
        $data = '';
        foreach ($ids_arr as $key => $value)
        {

            $where = [['id','=',$value],['is_menu','=','1'],['pid','neq',0]];
            $res = Db::table($this->config['prefix'].'admin_auth')->where($where)->order('sort asc')->find();
            if ($res)
            {
                $data[] = $res;
            }
        }
        return $data;
    }



    private function getMenu(){

        if (Cache::has('backstage'))
        {
            $data = json_decode(Cache::get('backstage'),1);

        }else{
            $user = json_decode(Cookie::get(Request::module().'_info'),1);

            $ids = Db::table($this->config['prefix'].'admin_role')->where('id' , $user['roleid'])->value('rules');
            $ids_arr = explode(',',$ids);
            $data = '';
            foreach ($ids_arr as $key => $value)
            {
                $where['id'] = $value;
                $where['is_menu'] = '1';
                $res = Db::table($this->config['prefix'].'admin_auth')->where($where)->find();
                if ($res)
                {
                    $data[] = $res;
                }
            }
            Cache::set('backstage',json_encode($data));
        }
        return $data;
    }

    /**
     * note:菜单组装(无限级)
     * auth:Duncan
     * date:2018/01/08
     */
    static public function editMenu($data, $str = '|— ', $pid=0, $level=0){
        $arr = array();
        foreach ($data as $v){

            if($v['pid'] == $pid){
                $v['level'] = $level + 1;
                $v['str'] = str_repeat($str,$level);
                $v['ltitle'] = $v['str'].$v['title'];
                $arr[] = $v;
                $arr = array_merge($arr,self::editMenu($data,$str,$v['id'], $level+1));
            }
        }
        return $arr;
    }

    /**
     * note:查找目录层级
     * auth:YW
     * date:2018/01/08
     */
    static public function findMenu($data,$level=0){
        $arr = array();

        foreach ($data as $key => $val){
            if($val['level'] == $level){
                array_push($arr,$val);
            }
        }
        return $arr;
    }

    /**
     * note:刷新
     * auth:YW
     * date:2019/03/25
     */
    public function refresh()
    {
        self::refreshMenu();
        echo json_encode(array('code'=> 10000,'msg' => '刷新成功'));
    }
    /**
     * note:刷新菜单
     * auth:YW
     * date:2019/03/25
     */
    private function refreshMenu()
    {
        $user = json_decode(Cookie::get($this->request->module().'_info'),1);
        $ids = Db::table($this->config['prefix'].'admin_role')->where('id',$user['roleid'])->value('rules');
        $ids_arr = explode(',',$ids);
        $data = '';
        foreach ($ids_arr as $key => $value)
        {
            $where = [['id','=',$value],['is_menu','=','1']];
            $res = Db::table($this->config['prefix'].'admin_auth')->where($where)->find();
            if ($res)
            {
                $data[] = $res;
            }
        }
        Cache::set('backstage',json_encode($data));
    }
}
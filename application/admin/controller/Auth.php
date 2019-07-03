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
use think\facade\Cookie;

class Auth extends Init{

    public function initialize()
    {
        parent::_init();
        $this->table = $this->config['prefix']."admin_auth";
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


    /**
     * @auth YW
     * @date 2018.03.06
     * @purpose 权限管理排序
     * @return void
     */
    public function _after_list(&$list)
    {
        $list = self::getTree($list);
    }


    /**
     * note:目录组装array(无限级)
     * auth:YW
     * date:2018/01/08
     */
    static public function getTree($data,$pid = 0,$level = 0,$str='|— '){
        $temp = array();
        foreach ($data as $v){
            if($v['pid'] == $pid){
                $v['level'] = $level + 1;
                $v['icon'] = html_entity_decode($v['icon']);
                $v['str'] = str_repeat($str,$level);
                $v['show'] = $v['str'].$v['title'];
                $temp[] = $v;
                $temp = array_merge($temp,self::getTree($data,$v['id'], $level+1,$str));
            }
        }
        return $temp;
    }

    public function _before_add(&$post)
    {
        if (Request::post())
        {
            $post['add_time'] = time();
            $post['status'] = '1';
        }else{
            $options = self::getTreeByOption();
            $this->assign('options',$options);
        }
    }

    public function _after_add(&$id)
    {
        if (self::autoAddAuthToRole($id))
        {
            self::returnMsgAndToken('10000',$this->message['success']);
        }else{
            self::returnMsgAndToken('10001',$this->message['error']);
        }
    }

    public function _before_show(&$post)
    {
        if (Request::post())
        {
            $post['edit_time'] = time();
        }else{
            $options = self::getTreeByOption('',0,1,Request::get()['id']);
            $this->assign('options',$options);
        }
    }
    /**
     * note:目录组装option(无限级)
     * auth:YW
     * date:2018/01/08
     */
    public function getTreeByOption($option = '',$pid = 0,$level = 1,$current = '')
    {

        $res = Db::table($this->table)->where('pid',$pid)->select();
        if ($res)
        {
            $line = str_repeat('|—',$level);
            foreach ($res as $key => $value){
                if ($current == $value['id'])
                {
                    if ($value['id'] == $pid)
                    {
                        $option .= "<option selected value=\"{$value['id']}\">$line{$value['title']}</option>";
                    }else{
                        $option .= "<option selected value=\"{$value['id']}\">$line{$value['title']}</option>";
                    }
                }else{
                    if ($value['id'] == $pid)
                    {
                        $option .= "<option value=\"{$value['id']}\">$line{$value['title']}</option>";
                    }else{
                        $option .= "<option value=\"{$value['id']}\">$line{$value['title']}</option>";
                    }
                }

                $option = self::getTreeByOption($option,$value['id'],$level+1,$current);
            }
        }
        return $option;
    }
    /**
     * note:将新添加的控制器加入权限列表
     * auth:YW
     * date:2018/01/08
     */
    public function autoAddAuthToRole($roleId)
    {
        $user = json_decode(Cookie::get($this->request->module().'_info'),1);
        $rules = Db::table($this->config['prefix'].'admin_role')->where('id',$user['roleid'])->value('rules');
        $data['rules'] = $rules .= ','.$roleId;
        $res = Db::table($this->config['prefix'].'admin_role')->where('id',$user['roleid'])->update($data);
        return $res?true:false;
    }
    /**
     * note:删除控制器时释放权限
     * auth:YW
     * date:2018/01/08
     */
    public function autoDelRoleAuth($roleId)
    {

    }
}

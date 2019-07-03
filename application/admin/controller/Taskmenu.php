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

/**
 * Created by PhpStorm.
 * User: EVOL
 * Date: 2018/10/27
 * Time: 17:11
 */

class Taskmenu extends Init
{
    function initialize()
    {
        parent::_init();
        $this->table = $this->config['prefix'] . 'admin_menu';
    }
    /**
     * @auth YW
     * @date 2018.10.31
     * @purpose 常用设置
     * @return void
     */
    public function index()
    {

        $user = json_decode(\think\facade\Cookie::get($this->request->module() . '_info'), 1);

        $res = Db::table($this->table)->where('uid',$user['id'])->field('ids')->find();
        $ids['ids'] = explode(',', $res['ids']);

        $res = Db::table($this->config['prefix'] . 'admin_auth')->where([['is_menu','=','1'],['pid','>','-1']])->select();
        foreach ($res as $key => $value) {
            if (in_array($value['id'], $ids['ids'])) {
                $res[$key]['mark'] = '1';
            } else {
                $res[$key]['mark'] = '0';
            }
        }
        $this->assign('vo',$user);
        $this->assign('list', $res);
        return view();
    }
    /**
     * @auth YW
     * @date 2018.10.31
     * @purpose 更新
     * @return void
     */
    public function _before_update(&$post)
    {
        if (Request::isPost()) {
            $res = Db::table($this->table)->where('uid',$post['id'])->find();
            $post['ids'] = implode(',', $post['ids']);
            if (!$res)
            {
                $data['uid'] = $post['id'];
                $data['title'] = '快捷菜单';
                $data['ids'] = $post['ids'];
                $data['status'] = '1';
                $res = Db::table($this->table)->insert($data);
                $res?self::returnMsg(10000,$this->message['success']):self::returnMsg(10001,$this->message['error']);
                exit(0);
            }
        }
    }
}

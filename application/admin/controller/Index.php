<?php
namespace app\admin\controller;
use Admin\Model\UserModel;
use app\admin\common\controller\Init;
use app\admin\model\User;
use think\captcha\Captcha;
use think\facade\Request;
use think\facade\Cookie;
use think\Db;


class Index extends Init
{

    public function __construct()
    {
        parent::_init();
        $this->table = $this->config['prefix'].'admin_user';
    }

    /**
     * @auth YW
     * @date 2017.12.4
     * @purpose 后台登录
     * @return void
     */
    public function index()
    {
        return view();
    }


    /**
     * @auth YW
     * @date 2017.12.4
     * @purpose 登录验证
     * @return void
     */
    public function login()
    {
        if (Request::isPost()){
            //$code = $this->checkCode();
            $code = true;
            $login = $this->checkLogin();

            if ($login && $code){

                return $this->success('登录成功，正在跳转...',url('iframe/index'));
            }else{
                exit($this->error('账号密码错误，请稍后'));
            }
        }else{
            exit($this->error('请输入登录信息，请稍后'));
        }
    }

    /**
     * @auth YW
     * @date 2017.12.2
     * @purpose 登录验证
     * @return bool
     */
    private function checkLogin()
    {
        $post = Request::Post();
        $username = $post['username'];
        $password = $post['password'];

        if (!empty($username) && !empty($password)){
            $where = [['username','=',$username],['password','=',factoryMd5($password,$username)],['status','>','-1']];

            $res = Db::table($this->table)->where($where)->field('id,username,roleid')->find(); unset($where);
            if ($res){
                $token = makeToken();
                $data = array('token' => $token,'login_time' => time());
                $where['id'] = $res['id'];
                $upd = User::table('db_admin_user')->where($where)->update($data);

                $num = User::where($where)->inc('login_num',1);
                if ($upd && $num)
                {
                    $data = array(
                        'id' => $res['id'],
                        'username' => $res['username'],
                        'roleid' => $res['roleid'],
                        'token' => $token,
                    );
                    putUser(Request::module(),$data,$this->config);
                    $post['check'] == 'true'?Cookie::set(Request::module().'_info',json_encode($data)):Cookie::delete(Request::module().'_info');
                    return true;
                }else{
                    return false;
                }
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
    /**
     * @auth YW
     * @date 2017.12.2
     * @purpose 验证码
     * @return bool
     */
    private function checkCode($code = '',$id = '')
    {
        if (empty($code)) $code = input('post.code/s');
        $captcha = new Captcha();
        return $captcha->check($code, $id);
    }

    /**
     * @auth YW
     * @date 2018.03.05
     * @purpose 注销登录
     * @return bool
     */
    function logout(){

        $data = array(
            'preip' => getIP(),
            'pretime' => time(),
        );
        $where['id'] = getUser(Request::module())['id'];
        $res = User::where($where)->update($data);

        if ($res)
        {
            Cookie::delete('info',Request::module(),'_');
            Cookie::delete('data'.Request::module(),'_');
            return $this->success('退出成功',url('index/index'));
        }
    }

    /**
     * @auth YW
     * @date 2017.12.2
     * @purpose 验证码
     * @return bool
     */
    public function Verfiy()
    {
        $captcha = new Captcha();
        return $captcha->entry();
    }




}

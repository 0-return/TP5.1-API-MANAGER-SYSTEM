<?php
namespace app\index\common\controller;
use think\Controller;
use app\common\controller\Common;
use app\common\controller\Send;
use think\facade\Request;
use think\facade\Cookie;
use think\facade\Hook;
use think\Db;

class Init extends Controller
{
    Use Send;
    protected $config;
    protected $message;
    protected $table;
    protected $user;
    private $page_validate = array(
        '/index/index',
        '/index/login',
        '/index/logout',
        '/index/verfiy',
        );

    /**
     * @auth YW
     * @date 2017.12.2
     * @purpose 初始化
     * @return void
     */
    public function _init()
    {

        $this->config['system'] = Db::table('db_admin_config_system')->find();
        $this->config['assist'] = Db::table('db_admin_config_assist')->find();
        $this->config['mca'] = strtolower('/'.Request::controller().'/'.Request::action());
        $this->config['prefix'] = Config('database.prefix');
        if (!in_array($this->config['mca'],$this->page_validate))
        {
            $this->user = json_decode(Cookie(Request::module().'_info'),1);
            //行为加载器检查
            $this->message = Hook::listen('app_msg')[0];
            //Hook::listen('app_login',$this->config);
            Hook::listen('app_rbac');
            //控制器/方法
            $crumbs = $this->crumbs($this->config['mca']);
            $this->assign('crumbs',$crumbs);
        }
    }

    /**
     * @auth YW
     * @date 2017.12.2
     * @purpose 首页
     * @return void
     */
    public function index() {
        $model = $this->getModel();
        $map = $this->_condition($model);

        if (method_exists($this, '_filter')) {
            $this->_filter($map);
        }
        if (!empty($model)) {
            $this->_list($model, $map);
        }
        return view();

    }

    /**
     * @auth YW
     * @date 2017.12.4
     * @purpose 任何操作[Ajax]
     * @return void
     */
    public function Anything()
    {

        if (method_exists($this, '_before_anything')) {
            $res = $this->_before_anything();
        }
        if (method_exists($this, '_after_anything')) {
            $this->_after_anything($res);
        }
        return isset($res['tpl'])? view($res['tpl']):false;
    }
    /**
     * @auth YW
     * @date 2017.12.4
     * @purpose 添加[Ajax]
     * @return void
     */
    public function addByAjax()
    {

        if (method_exists($this, "_before_add")) {
            $this->_before_add($_POST);
        }

        if ($this->request->isPost()) {
            $model = !empty($model) ? $model : $this->getModel();
            $res = $model->insert($_POST);
            $id = $model->getLastInsID();
            if ($res) {                 //保存成功
                if (method_exists($this, "_after_add")) {
                    $this->_after_add($id);
                }else{
                    self::returnMsgAndToken('10000',$this->message['success']);
                }
            } else {
                self::returnMsgAndToken('10001',$this->message['error']);
            }
        }else{
            return view('add');
        }

    }
    /**
     * @auth YW
     * @date 2017.12.4
     * @purpose 删[Ajax]
     * @return void
     */
    public function deleteByAjax()
    {

        if(method_exists($this, "_before_delete")){
            $this->_before_delete($_REQUEST);
        }

        $model = !empty($model)?$model:$this->getModel();
        if (!empty($model)) {
            $pk = $model->getPk($this->table);
            $id = $_REQUEST [$pk];
            if (isset($id)) {
                if (is_array($id)){
                    $condition[] = ['id','in',trim(implode($id,','),',')];
                }else{
                    $condition[] = ['id','eq',$id];
                }
                $list = $model->where($condition)->update(['status'=> -1]);

                if ($list !== false) {
                    if(method_exists($this, "_after_delete")){
                        $this->_after_delete($id);
                    }else{
                        self::returnMsgAndToken('10000',$this->message['success']);
                    }
                } else {
                    self::returnMsgAndToken('10001',$this->message['error']);
                }
            } else {
                self::returnMsgAndToken('10001',$this->message['fail']);

            }
        }
    }
    /**
     * @auth YW
     * @date 2017.12.4
     * @purpose 根据id显示
     * @return void
     */
    public function showById() {
        if(method_exists($this, "_before_show")){
            $this->_before_show($_REQUEST);
        }

        $model = !empty($model)?$model:$this->getModel();
        $id = $_REQUEST [$model->getPk($this->table)];
        $res = $model->getById($id);
        if(method_exists($this, "_after_show")){
            $this->_after_show($res);
        }
        $this->assign('vo', $res);
        return view('edit');
    }
    /**
     * @auth YW
     * @date 2017.12.4
     * @purpose 改[Ajax]
     * @return void
     */
    public function updateByAjax()
    {

        if(method_exists($this, "_before_update")){
            $this->_before_update($_POST);
        }
        $model = $this->getModel();
        $pk = $model->getPk($this->table);
        $map[$pk] = $_REQUEST[$pk];
        $model = $this->getModel();
        $list = $model->where($map)->update($_POST);
        if (false !== $list) {
            if(method_exists($this, "_after_update")){
                $this->_after_update($_REQUEST[$pk]);
            }else{
                self::returnMsgAndToken('10000',$this->message['success']);
            }
        } else {
            self::returnMsgAndToken('10001',$this->message['error']);

        }
    }
    /**
     * @auth YW[可指定字段进行修改]
     * @date 2018.12.21
     * @purpose 数字计算
     * @type [setInc,setDec]
     * @return void
     */
    public function calByAjax($obj = '',$pk = '',$param = "")
    {
        if(method_exists($this, "_before_cal")){
            $this->_before_cal($_POST);
        }

        $model = empty($obj)?$this->getModel():$obj;
        if (!empty($model)) {
            if ($pk == '') $pk = $model->getPk($this->table);
            $id = $_REQUEST [$pk];
            $number = empty($param['number'])?$_REQUEST['number']:$param['number'];
            $type = empty($param['active'])?$_REQUEST['active']:$param['active'];
            $field = empty($param['field'])?$_REQUEST['field']:$param['field'];
            if (isset($id)) {
                if (is_array($id))
                {
                    $condition[$pk] = array($id);
                }else{
                    $condition[$pk] = explode(',', $id);
                }
                $list = $model->where($condition)->$type($field, $number);
                if ($list !== false) {
                    if(method_exists($this, "_after_cal")){
                        $data['str'] = ($str = $type == 'setInc'?'+':'-').$number.' '.$field;
                        $this->_after_cal($data);
                    }else{
                        self::returnMsg('10000',$this->message['success']);

                    }
                } else {
                    self::returnMsg('10001',$this->message['error']);
                }
            } else {
                self::returnMsg('10000',$this->message['fail']);
            }
        }
    }
    /**
     * @auth YW[可指定字段进行修改]
     * @date 2018.11.20
     * @purpose 状态修改
     * @return void
     */
    public function forbid($field = "status"){

        if(method_exists($this, "_before_forbid")){
            $this->_before_forbid($field);
        }
        $model = $this->getModel();
        if (!empty($model)) {
            $pk = $model->getPk($this->table);
            $id = $_REQUEST [$pk];
            $status = $_REQUEST[$field];
            if (isset($id)) {
                $condition = array($pk => array('in', $id));
                $list = $model->where($condition)->setField($field, $status);
                if ($list !== false) {
                    if(method_exists($this, "_after_forbid")){
                        $this->_after_forbid($id);
                    }else{
                        self::returnMsgAndToken('10000',$this->message['success']);
                    }

                } else {
                    self::returnMsgAndToken('10001',$this->message['error']);
                }
            } else {
                self::returnMsgAndToken('10001',$this->message['fail']);
            }
        }
    }
    /**
     * 公共查询数据方法
     * @param string $modelStr 模型名称（表名称）
     * @param $_where_order_field （条件）
     * @param bool $isReturnResult  是否返回结果
     * @param string $count （总数）
     * @return array
     */
    public function _list($model, $_where, $isreturn = false, $sortBy = '', $asc = false,$limit = '10'){
        $order = isset($_REQUEST['_order'])?$_REQUEST['_order']:!empty($sortBy)?$sortBy:'id';
        $sort = isset($_REQUEST ['_sort'])?$_REQUEST ['_sort']:$asc ? 'asc' : 'desc';
        $obj = $model = !empty($model)?$model:$this->getModel();

        //变量赋值
        if (isset($_where['where']))
        {
            $obj = $model->where($_where['where']);
        }
        if (isset($_where['field']))
        {
            $obj = $model->field($_where['field']);
        }
        if (isset($_where['union']))
        {
            $obj = $model->union([$_where['union']],true);
        }

        if (isset($_where['join']))
        {
            $obj = $model->join($_where['join']);

        }
        if (isset($_where['having']))
        {
            $obj = $model->having($_where['having']);
        }
        if (isset($_where['alias']))
        {
            $obj = $model->alias($_where['alias']);
        }
        if (isset($_where['group']))
        {
            $obj = $model->group($_where['group']);
        }
        $obj = $model->order($order.' '.$sort);
        //查询数据集合
        if ($limit == false || $limit == '0' || $limit == '')
        {
            $list = $obj->paginate();
        }else{
            $page = $this->request->param('page');
            $list = $obj->paginate($limit,false,['type' => 'Bootstrap','var_page' => 'page','page' => $page,'path'=>'javascript:ajaxpage([PAGE])']);
        }
        //echo $obj->getLastSql();
        $page = $list->render();
        $count = $list->total();
        $source = $list->items();
        if(method_exists($this,"_after_list")){
            $this->_after_list($source);
        }
        if (!$isreturn)
        {
            $this->assign('count',$count);      //获取总记录数
            $this->assign('list', $source);
            $this->assign('page', $page);
        }else{
            return $list;
        }
    }

    /**
     * @auth YW
     * @date 2017.12.6
     * @purpose 查询
     * @return void
     */
    protected function _search() {
        $map = $this->_condition($this->getModel());
        return $map;
    }

    /**
     * @auth YW
     * @date 2017.3.6
     * @purpose 获取数据库模型
     * @return void
     */
    public function getModel(){
        $model = Db::table($this->table);
        return $model;
    }
    /**
     * @auth YW
     * @date 2017.3.8
     * @purpose 拼装where条件
     * @return void
     */
    private function _condition($model) {
        $map = array();

        foreach ($model->getTableFields() as $key => $val) {
            if (isset($_REQUEST[$val]) && $_REQUEST [$val] != '') {
                $map [$val] = $_REQUEST [$val];
            }
        }
        return $map;
    }

    /**
     * @auth YW
     * @date 2017.3.8
     * @purpose 检查唯一性
     * @return void
     */
    public function checkUnique($field,$param){
        if(empty($field) or empty($param)){
            self::returnMsgAndToken('10001',$this->message['check_serch']);
            exit(0);
        }else{
            $model = $this->getModel();
            $pk = $model->getPk($this->table);
            $map[$field] = $param;
            $res = $model->field($pk)->where($map)->find();
            if(!empty($res)){
                self::returnMsgAndToken('10001',$this->message['check_unique']);

                exit(0);
            }else{
                return true;
            }
        }
    }

    /**
     * @auth YW
     * @date 2017.12.6
     * @purpose 拼装搜寻条件
     * @return void
     */
    public function checkSearch(&$map,$notlike = false){
        $get = Request::get();
        if(isset($get['sfields']) && !empty($get['sfields'])) {
            $get['sfields'] = trim($get['sfields'],',');
            $get['sfields'] = str_replace(',','|',$get['sfields']);
            $map[] = [$get['sfields'],'like','%'.$get['reunite'].'%'];
        }else{
            if (!empty($post['reunite']))
            {
                $module = $this->getModel();
                $pk = $module->getPk($this->table);
                $map[] = [$pk,'like',"%{$post['reunite']}%"];
            }

        }
    }

    /**
     * @auth YW
     * @date 2018.11.8
     * @purpose 面包屑导航
     * @return void
     */
    private function crumbs($ca = '',$pid = '',$str = '')
    {

        if (!empty($ca))
        {
            $where['mca'] = $ca;
        }elseif(!empty($pid)){
            $where['id'] = $pid;
        }else{
            return ;
        }
        $res = Db::table($this->config['prefix'].'admin_auth')->where($where)->field('pid,title,mca')->find();

        if ($res)
        {
            $str .= $this->crumbs('',$res['pid'],$str);
            $str .= ' > '.$res['title'];
        }
        return $str;
    }

    /**
     * @auth YW
     * @date 2018.11.28
     * @purpose 日志
     * @return void
     * $obj 数据模型，$request，$table
     */
    public function log($content = '')
    {
        $obj = new Common();
        $user = getUser($this->request->module());
        $data['uid'] = $user['id'];
        $data['username'] = $user['username'];
        $data['explain'] = 'sys';
        $obj->wLog($this->obj,$this->request,$data,$this->config,$content);
    }


}

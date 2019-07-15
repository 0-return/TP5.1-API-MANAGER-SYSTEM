

/**
 * 初始化界面
 */
var openurl,getHistoryByUser,getHistoryByMember;

layui.use(['element', 'layer', 'form'], function(){
	var element = layui.element;
	var layer = layui.layer;
	var form = layui.form;
	form.render();
	element.render();

	/******************************************************************初始化******************************************************************/
	
	var localData = [];										//本地缓存数据
	var timestamp = Date.parse(new Date());;				//时间戳

	if(!window.localStorage) {								//检查缓存是否可用
	    console.log('当前浏览器不支持localStorage!')
	}else{
		console.log('重要数据请保存到账户下，避免数据丢失!')
	}
	/**
	 * 设置界面高度
	 */
	var wh = $(window).height();
	//左侧高度
	$('.layui-left').height(wh - 60);
	$('.layui-left-url').height(wh-60-100);
	//输出窗口高度
	$('.response-window').height(wh - 400);
	$('.format-html').height(wh - 405);
	$('.format-json').height(wh - 405);
	$('.format-xml').height(wh - 405);
	$('.format-text').height(wh - 405);
	/******************************************************************初始化******************************************************************/


	//添加tab
	$('.site-demo-active').on('click', function(){
	    var othis = $(this), type = othis.data('type');
	    active[type] ? active[type].call(this, othis) : '';
	    
	});
	//新增一个Tab项
	var active = {
		tabAdd:function(){
		
		  element.tabAdd('request-box', {
		    title: 'Untitled Request',
		    content: '2',
		    id:tabId = Math.random()*10000|0,
		  });
		//添加选项卡时自动打开选项卡
		element.tabChange('request-box', tabId);
		}
	}


	//初始化默认父选项卡
	var pid = '0';
	$(window).load(function(){
		
		pid = $('.param-list-select li').index();
		pl = $('.param-list-select li').parent().parent().find('.param-list-'+pid+' li');
		if (pl.length > 0) 
		{
			
			n = pl.size();
			for (var i = 0; i < n; i++) {
				pl.eq(i).attr('data-id',i);
				obj = $('.param-list-'+pid+' li').parent().parent().find('.layui-tab-item').eq(i).find('table');
				
				//自动添加参数列表
				listParamBox(obj);
			}
			//将父id绑定到提交按钮上
			$('#sub').attr('data-pid',pid);
			//将子id绑定到提交按钮上
			$('#sub').attr('data-id',$(pl).parent().find('.layui-this').attr('data-id'));
		}
	})
	//监听父选项卡
	$(".param-list-select li").click(function(){
		pid = $(this).index();
		
	   //初始话下级选项卡
		pl = $(this).parent().parent().find('.param-list-'+pid+' li');
		if (pl.length > 0) 
		{
			
			//开始轮询绑定id
			n = pl.size();
			for (var i = 0; i < n; i++) {
				pl.eq(i).attr('data-id',i);
				obj = $('.param-list-'+pid+' li').parent().parent().find('.layui-tab-item').eq(i).find('table');
				$('.param-list-'+pid+' li').on('click',function(){
					$('#sub').attr('data-id',$(this).attr('data-id'));
				})

				//自动添加参数列表
				listParamBox(obj);
			}

			//将父id绑定到提交按钮上
			$('#sub').attr('data-pid',pid);
			
			//将子id绑定到提交按钮上
			$('#sub').attr('data-id',$(pl).parent().find('.layui-this').attr('data-id'));

			
		}
	})

	/**
	 * 添加参数列表
	 */
	var listParamBox =  function(obj)
	{
		
		//监听输入框值的变化
		$(obj).on("input propertychange",function(){
			
			//监听最后一组元素的值
			key = $($(obj).find("tr")[$(obj).find("tr").length-1]).find("input[name='key']").val();
			value = $($(obj).find("tr")[$(obj).find("tr").length-1]).find("input[name='value']").val();
			description = $($(obj).find("tr")[$(obj).find("tr").length-1]).find("input[name='description']").val();
			if (key.length > 0 || value.length > 0 || description.length > 0) 
		    {
		    	//检查删除按钮状态
		    	//console.log($(obj).children('tr').size());
				$(obj).children('tbody').size() > 0?$(obj).find('.param-list-delbut').show():$(obj).find('.param-list-delbut').hide();
		    	//创建html
		    	//console.log(str);
		    	createParamBox(obj);
		    	//绑定事件
		    	$(obj).find('tr td button').off('click').on('click',function(){
		    		delParamBox(this);
		    	})
		    }
		})
	}

	/**
	 * 参数列表模块
	 */
	var createParamBox = function(obj){
		str = '<tr>'
		    +'	<td>'
			+'    <div class="layui-form">'
			+'	    <input type="checkbox" name="id" title="" lay-skin="primary" checked />'
			+'	  </div>'
			+'  </td>'
		    +'  <td>'
		    +'  	<input type="text" name="key" required  lay-verify="required" placeholder="Key" autocomplete="off" class="layui-input">'
		    +'  </td>'
		    +'  <td>'
		    +'  <input type="text" name="value" required  lay-verify="required" placeholder="Value" autocomplete="off" class="layui-input">'
		    +'  </td>'
		    +'  <td colspan="3">'
		    +'  <input type="text" name="description" required  lay-verify="required" placeholder="Description" autocomplete="off" class="layui-input">'
			+'  <td>'
			+'  <button style="display: none;" type="button" class="layui-btn layui-btn-sm param-list-delbut">'
			+'    <i class="layui-icon">&#xe640;</i>'
			+'  </button>'
			+'  </td>'
		    +'</tr>';
		obj.append(str);

		layui.use('form', function(){
			var form = layui.form;
			form.render();
		})
	}
	/**
	 * 删除参数列表
	 */
	var delParamBox = function(obj) {
		num = $(obj).parents('tbody').children('tr').size();
		console.log(num);
		if (num - 1 < 2) 
		{
			$(obj).parents('tr').siblings().find('.param-list-delbut').hide();
		}
		$(obj).parents('tr').remove();
		
	}

	/**
	 * 开始提交参数
	 */
	$('#sub').click(function(){
		json = {};
		input = [];
		//获取提交按钮上的参数
		pid = $(this).attr('data-pid');
		id = $(this).attr('data-id');
		obj = $(".param-list-"+pid+" .layui-tab-content").find(".layui-tab-item").eq(id);
		//console.log(list);return;
		//提交数据的配置
		input['url_id'] = timestamp+Math.ceil(Math.random()*1000);
		input['subtype'] = $("select[name='subtype']").val();
		input['url'] = $("input[name='url']").val();
		input['timestamp'] = timestamp;
		input['posontion'] = [pid,id];
		
		//处理提交的参数
		
		key = get_param(obj,'key');
		value = get_param(obj,'value');
		description = get_param(obj,'description');
		input['jsonStr'] = merge2Arr(key,value,description);
		
		json.subtype = input['subtype'];
		json.url = input['url'];
		json.url_id = input['url_id'];
		json.param = input['jsonStr'];
		json.position = input['posontion'];
		console.log(json);
		try{
			//提交服务器
			request(json)
			//写入缓存
			setStorage(json);
		}catch(e)
		{
			alert(e);
		}
	})

	/**
	 * 获取表单数据
	 * @param  name 名称
	 * @return arr
	 */
	var get_param = function(obj , name)
	{
	  arr = [];
	  $(obj).find("input[name='"+name+"']").each(function(){
	        
	        arr.push($(this).val());
	    })
	    return arr;
	}

	/**
	 * 将所有提交数据提交到服务器
	 * @param  
	 * @return 
	 */
	var request = function (data) {

		$.ajax({
			type:'POST',
			url:'/index/index/begin',
			data:data,
			dataType:'JSON',
			success:function(data){
				
				$('.format-html').empty().html(data);
				
			},error:function(XMLHttpRequest, textStatus, errorThrown){
				$('.format-html').html('没有返回数据');
			}
		})
	}

	

	/**
	 * 获取游客的历史测试记录()
	 * @param  
	 * @return 
	 */
	getHistoryByUser = function()
	{

		res = getLocalData();
		html = '<div class="layui-colla-item" ><h2 class="layui-colla-title">今天</h2>';
		for (let i in res) {
			
			json = res[i];
			colors = '#63BA79';
			switch(json.subtype)
			{
				case 'GET':
				colors = "#63BA79";
				break;

				case 'POST':
				colors = "#FF5722";
				break;
			}
			html += '<div class="layui-colla-content  layui-show request-url-detail" onclick="openurl(\''+json.url_id+'\');"><a href="javascript:;" title="'+json.url+'"><span class="request-type" style="color:'+colors+'">'+json.subtype+'</span>'+json.url+'</a></div>';
		}
		html += '</div>';

		$('#user-history').empty().append(html);
	}


	/**
	 * 获取会员的历史测试记录
	 * @param  
	 * @return 
	 */
	getHistoryByMember = function()
	{
	  $.ajax({
	    type:'POST',
	    url:url,
	    data:param,
	    dataType:'JSON',
	    success:function(data){
	      console.log(data);
	    },error:function(er){
	      
	    }
	  })
	}
	/**
	 * 将数据加载到输入框
	 * @param  
	 * @return 
	 */
	openurl = function (id)
	{
		res = getLocalData(id);
		//获取参数
		param = $.isEmptyObject(data = JSON.parse(res.param));
		console.log(res);
		//获取坐标
		position = res.position;

		$('select[name=subtype]').find("option[value = '"+res.subtype+"']").attr('selected','true').siblings().removeAttr('selected');
		form.render();
		$('input[name=url]').val(res.url);

		
	}
	/**
	 * 将获取到的单条数据格式化到表单中
	 * @param  
	 * @return 
	 */
	var formatData2Form = function(id)
	{
	  
	}
	/**
	 * 将表单数据格式成键值对
	 * @param  
	 * @return 
	 */
	var Bulk2Edit =  function()
	{

	}
	/**
	 * 将键值对数据格式成表单数据
	 * @param  
	 * @return 
	 */
	var Edit2Bulk =  function()
	{
	  
	}
	/**
	 * 保存数据到数据表中（针对注册用户）
	 * @param  
	 * @return 
	 */
	var Saveas = function()
	{

	}

	

	/******************************************************************缓存操作公共方法******************************************************************/
	/**
	 * 获取本地历史记录保持在数组中准备使用
	 * @param key 为空则获取全部
	 * @return 
	 */
	var getLocalData = function(key = '')
	{
		newArr = [];
		day = timeStamp2String(timestamp);
		data = get_storage('local_data_'+day);
		//console.log(data);
		if (!data) {return false;}  
		res = data.split("|");

		for (var i = 0; i < res.length; i++) 
		{
			json = JSON.parse(res[i]);
			newArr[json.url_id] = json;
		}
		return key == ''?newArr:newArr[key];
	}
	//添加方法
	function add_storage(key,value) {
		try{
			localStorage.setItem("api_" + key , value);
			return true;
		}catch(e)
		{
			console.log('localStorage出错：'+e); 
		}
		
	}

	/**
	 * 将测试信息写入本地缓存
	 * @param data 即将缓存的数据
	 * @param name 保存是的名称
	 */
	function setStorage(data,name = '')
	{
		day = timeStamp2String(timestamp);
		storageName = name == ''?'local_data_'+day:name;
		obj = {};
		jsonStr = get_storage(storageName);
		
		if (!jsonStr) 
		{
			str = JSON.stringify(data);
			add_storage(storageName,str);

		}else{
			str = JSON.stringify(data)+'|';
			str += jsonStr;
			add_storage(storageName,str);
		}

	}

	//删除方法
	function remove_storage(key) {
		try{
			localStorage.removeItem("api_" + key);
			return true;
		}catch(e)
		{
			console.log('localStorage出错：'+e); 
		}
	    
	}

	//修改方法
	function update_storage(key,value){
	    try{
	    	localStorage.removeItem("api_" + key)
	        localStorage.setItem("api_" + key ,value);
	        return true;
	    }catch(e)
	    {
	    	console.log('localStorage出错：'+e); 
	    }
	}
	//获取
	function get_storage(key) {
		try{
	    	return localStorage.getItem("api_" + key);
	    }catch(e)
	    {
	    	console.log('localStorage出错：'+e); 
	    }
		
	}
	/******************************************************************缓存操作公共方法******************************************************************/

	/******************************************************************工具******************************************************************/
	/**
	 * 将中文转成Unicode
	 * @param str 
	 */
	ToUnicode = function(str){
	  try{
	      return escape(str).replace(/%/g,"\\").toLowerCase();
	  }catch(e){
	    return str;
	  }
	  
	}
	/**
	 * 将Unicode转成中文
	 * @param str 
	 */
	UnUnicode = function(str){
	  try{
	      return unescape(str.replace(/\\/g, "%"));
	  }catch(e){
	    return str;
	  }
	  
	}
	/**
	 * 获取url参数列表
	 * @param str 
	 */
	function parseQueryString (url) {
	  var reg_url = /^[^\?]+\?([\w\W]+)$/,
	  reg_para = /([^&=]+)=([\w\W]*?)(&|$|#)/g,
	  arr_url = reg_url.exec(url),
	  ret = {};
	  if (arr_url && arr_url[1]) {
	    var str_para = arr_url[1], result;
	    while ((result = reg_para.exec(str_para)) != null) {
	      ret[result[1]] = result[2];
	    }
	  }
	  return ret;
	}
	/**
	 * 根据url参数名称获取url参数
	 * @param str 
	 */
	function GetQueryString(name) { 

	  var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)"); 
	  var r = window.location.search.substr(1).match(reg); 
	  if (r!=null) return unescape(r[2]); return null; 
	}
	/**
	 * 时间格式化
	 * @param str 
	 */
	function timeStamp2String(time,type = 'year,month,date', dflag = '-', hflag = '：')
	{

	    var datetime = new Date();
	    datetime.setTime(time);
	    datArr = [];
	    datArr['year'] = datetime.getFullYear();
	    datArr['month'] = datetime.getMonth() + 1 < 10 ? "0" + (datetime.getMonth() + 1) : datetime.getMonth() + 1;
	    datArr['date'] = datetime.getDate() < 10 ? "0" + datetime.getDate() : datetime.getDate();
	    datArr['hour'] = datetime.getHours()< 10 ? "0" + datetime.getHours() : datetime.getHours();
	    datArr['minute'] = datetime.getMinutes()< 10 ? "0" + datetime.getMinutes() : datetime.getMinutes();
	    datArr['second'] = datetime.getSeconds()< 10 ? "0" + datetime.getSeconds() : datetime.getSeconds();
	    arr = type.split(',');
	    dstr = '';

	    for (var i = 0; i < arr.length; i++) {
	      dstr += datArr[arr[i]]+dflag;
	    }

	    if (arr.length == 3) 
	    {
	      dstr = dstr.substr(0,dstr.length-1);
	    }

	    if (arr.length > 3 && arr.length < 6) 
	    {
	      dstr = dstr.substr(0,dstr.length-1);
	    }

	    return dstr;
	}
	/**
	 * 时间格式化
	 * @param str 
	 * eg :{"q":{"key":"1","description":"q"},"a":{"key":"2","description":"w"},"z":{"key":"3","description":"e"}}
	 */

	function merge2Arr(key,value,description = [])
	{
	  

	  jsonStr = '{';
	  for (var i = 0; i < key.length; i++) {
	    //拼接描述
	    str = '';
	    str += '"description":'+'"'+description[i]+'"';

	    var reg=/:$/gi;
	    str = str.replace(reg, '');

	    //拼接json
	    if (key[i] != '') 
	    {
	      jsonStr += '"'+key[i]+'":{"value":"'+value[i]+'",'+str+'},';
	    }
	  }
	  var reg=/,$/gi;
	  jsonStr = jsonStr.replace(reg, '');
	  jsonStr += '}';
	  return jsonStr;
	}

	/******************************************************************工具******************************************************************/

});













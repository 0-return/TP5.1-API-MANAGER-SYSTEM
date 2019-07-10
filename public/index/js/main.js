

var timestamp = Date.parse(new Date());;

if(!window.localStorage) {
    console.log('当前浏览器不支持localStorage!')
}else{
	console.log('重要数据请保存到账户下，避免数据丢失!')
}

/**
 * 设置高度
 */
$(function(){
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
})


/**
 * 初始化界面
 */
layui.use(['element', 'layer', 'form'], function(){
	var element = layui.element;
	var layer = layui.layer;
	var form = layui.form;

	form.render();
	element.render();

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
});

/**
 * 添加参数列表前的html处理
 */
$(function(){
	obj = $('.param-list tbody');

	$(obj).on("input propertychange",function(){
		
		//监听最后一组元素的值
		key = $($(obj).find("tr")[$(obj).find("tr").length-1]).find("input[name='key']").val();
		value = $($(obj).find("tr")[$(obj).find("tr").length-1]).find("input[name='value']").val();
		description = $($(obj).find("tr")[$(obj).find("tr").length-1]).find("input[name='description']").val();
		if (key.length > 0 || value.length > 0 || description.length > 0) 
	    {
	    	//检查删除按钮状态
	    	console.log($(obj).children('tr').size());
			$(obj).children('tr').size() > 0?$(obj).find('.param-list-delbut').show():$(obj).find('.param-list-delbut').hide();
	    	//创建html
	    	//console.log(str);
	    	createParamBox(obj);
	    	//绑定事件
	    	$(obj).find('tr  td  button').off('click').on('click',function(){
	    		delParamBox(this);
	    	})
	    }
	})

	
})


/**
 * 添加参数列表
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

$('#sub').click(function(){
  str = '';
  ///提交数据的配置
  requestType = $("select[name='requestType']").val();
  url = $("input[name='url']").val();

  //提交的参数
  key = get_param('key');
  value = get_param('value');
  description = get_param('description');
  
  param = '{';
  for (var i = 0; i < key.length; i++) {
    
    if (key[i] != '') 
    {
      param += '"'+key[i]+'":"'+value[i]+'",'
    }
  }
  var reg=/,$/gi;
  param = param.replace(reg, '');
  param += '}';

  str += "requestType="+requestType+"&url="+url+"&param="+param;
  //返回数据的配置
  request_post(str);

   //写入缓存
   setStorage(str);
   
})

/**
 * 将所有提交数据提交到服务器
 * @param  
 * @return 
 */
var request_post = function (param) {
  $.ajax({
    type:'POST',
    url:'/index/index/begin',
    data:param,
    dataType:'JSON',
    success:function(data){
    	//console.log(data);
    	$('.format-html').empty().html(data);
    	
    },error:function(XMLHttpRequest, textStatus, errorThrown){
    	$('.format-html').html('没有返回数据');
    }
  })
}


/**
 * 获取表单数据
 * @param  name 名称
 * @return arr
 */
var get_param = function(name)
{
  arr = [];
  $("input[name='"+name+"']").each(function(){
        
        arr.push($(this).val());
    })
    return arr;
}


/**
 * 获取游客的历史测试记录
 * @param  
 * @return 
 */
var getHistoryByUser = function()
{
	
	day = timeStamp2String(timestamp);
	data = localStorage.getItem('local_data_'+day);
	if (!data) {return false;}  
	res = data.split(",");
	param = [];
	html = '<div class="layui-colla-item" ><h2 class="layui-colla-title">今天</h2>';
	for (var i = 0; i < res.length; i++) {
		param = parseQueryString("s?"+res[i]);
		switch(param['requestType'])
		{
			case 'GET':
			colors = "#63BA79";
			break;

			case 'POST':
			colors = "#FF5722";
			break;
		}
		html += '<div class="layui-colla-content layui-show request-url-detail" onclick="openurl('+param['param']+');"><a href="javascript:;" title="'+param['url']+'"><span class="request-type" style="color:'+colors+'">'+param['requestType']+'</span>'+param['url']+'</a></div>';
	}
	html += '</div>';

	$('#user-history').empty().append(html);
}


/**
 * 获取会员的历史测试记录
 * @param  
 * @return 
 */
var getHistoryByMember = function()
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

var openurl = function (param)
{
	console.log(param);
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

/**
 * 将测试信息写入本地缓存
 * @param data 即将缓存的数据
 */
function setStorage(str)
{

	str += "&time="+timestamp;
	day = timeStamp2String(timestamp);
	var dt = [];
	res = localStorage.getItem('local_data_'+day);
	if (!res) 
	{
		localStorage.setItem('local_data_'+day,str);
	}else{
		dt.push(res);
		dt.unshift(str);
		localStorage.setItem('local_data_'+day,dt);
		//console.log(localStorage.getItem('local_data_'+day));
	}
}

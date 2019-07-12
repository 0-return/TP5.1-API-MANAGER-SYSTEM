

var localData = [];										//本地缓存数据
var timestamp = Date.parse(new Date());;				//时间戳

if(!window.localStorage) {								//检查缓存是否可用
    console.log('当前浏览器不支持localStorage!')
}else{
	console.log('重要数据请保存到账户下，避免数据丢失!')
}


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

	//默认父选项卡
	$(window).load(function(){
		id = '0';
		//id = $('.param-list-select li').index();
		pl = $('.param-list-select li').parent().parent().find('.param-list-'+id+' li');
		
		if (pl.length != 0) 
		{
			
			n = pl.size();
			for (var i = 0; i < n; i++) {
				pl.eq(i).attr('data-id_'+id,i);
				obj = $('.param-list-'+id+' li').parent().parent().find('.layui-tab-item').eq(i).find('table');
				listParamBox(obj);
			}

			//将参数绑定到提交按钮上
			$('#sub').attr('data-pid',id);
		}
	})
	//监听父选项卡
	element.on('tab(param-list-select)', function(){
	   id = $(this).index();
	   //初始话下级选项卡
		pl = $(this).parent().parent().find('.param-list-'+id+' li');
		//console.log(pl);
		if (pl.length != 0) 
		{
			n = pl.size();
			for (var i = 0; i < n; i++) {
				pl.eq(i).attr('data-id_'+id,i);

				obj = $('.param-list-'+id+' li').parent().parent().find('.layui-tab-item').eq(i).find('table');
				listParamBox(obj);
			}
			//将参数绑定到提交按钮上
			$('#sub').attr('data-pid',id);
		}
	});
	

	//监听子选项卡
	element.on('tab(param-list-1)', function(){
	    id = this.getAttribute('data-id_1');
	    $('#sub').attr('data-id',id);
	});

});

/**
 * 设置界面高度
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
	//提交数据的配置
	input['url_id'] = timestamp+Math.ceil(Math.random()*1000);
	input['requestType'] = $("select[name='requestType']").val();
	input['url'] = $("input[name='url']").val();
	input['timestamp'] = timestamp;
	//处理提交的参数
	key = get_param('key');
	value = get_param('value');
	description = get_param('description');

	jsonStr = '{';
	for (var i = 0; i < key.length; i++) {

		if (key[i] != '') 
		{
		  jsonStr += '"'+key[i]+'":"'+value[i]+'",';
		}
	}
	var reg=/,$/gi;
	jsonStr = jsonStr.replace(reg, '');
	jsonStr += '}';
	input['jsonStr'] = jsonStr;

	json.requestType = input['requestType'];
	json.url = input['url'];
	json.url_id = input['url_id'];
	json.param = input['jsonStr'];

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
 * 获取本地历史记录保持在数组中准备使用
 * @param key 为空则获取全部
 * @return 
 */
var getLocalData = function(key = '')
{
	newArr = [];
	day = timeStamp2String(timestamp);
	data = localStorage.getItem('local_data_'+day);
	if (!data) {return false;}  
	res = data.split("|");

	for (var i = 0; i < res.length; i++) 
	{
		json = JSON.parse(res[i]);
		newArr[json.url_id] = json;
	}
	return key == ''?newArr:newArr[key];
}

/**
 * 获取游客的历史测试记录()
 * @param  
 * @return 
 */
var getHistoryByUser = function()
{

	res = getLocalData();
	html = '<div class="layui-colla-item" ><h2 class="layui-colla-title">今天</h2>';
	for (let i in res) {
		
		json = res[i];
		colors = '#63BA79';
		switch(json.requestType)
		{
			case 'GET':
			colors = "#63BA79";
			break;

			case 'POST':
			colors = "#FF5722";
			break;
		}
		html += '<div class="layui-colla-content  layui-show request-url-detail" onclick="openurl(\''+json.url_id+'\');"><a href="javascript:;" title="'+json.url+'"><span class="request-type" style="color:'+colors+'">'+json.requestType+'</span>'+json.url+'</a></div>';
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
/**
 * 将数据加载到输入框
 * @param  
 * @return 
 */
var openurl = function (id)
{
	res = getLocalData(id);
	param = $.isEmptyObject(data = JSON.parse(res.param));
	console.log(res);
	
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
function setStorage(data)
{
	
	obj = {};
	jsonStr = localStorage.getItem('local_data_'+day);
	
	if (!jsonStr) 
	{
		str = JSON.stringify(data);
		localStorage.setItem('local_data_'+day,str);

	}else{
		
		str = JSON.stringify(data)+'|';
		str += jsonStr;
		localStorage.setItem('local_data_'+day,str);
	}

}

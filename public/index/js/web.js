
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

	renderLine();
	$('.format-html').val('');		//默认数据
	$('.format-json').html('');
	$('.format-xml').html('');
})


/**
 * 代码左侧收起功能
 * 
 */
function renderLine(){
    var line_num = $('#json-target').height()/20;
    var line_num_html = "";
    for (var i = 1; i < line_num+1; i++) {
        line_num_html += "<div>"+i+"<div>";
    }
    $('.format-json').html(line_num_html);
}


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
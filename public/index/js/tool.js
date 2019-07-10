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



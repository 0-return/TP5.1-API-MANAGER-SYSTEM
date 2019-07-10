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




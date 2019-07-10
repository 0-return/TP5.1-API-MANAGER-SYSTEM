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

function get_storage(key) {
	try{
    	return localStorage.getItem("api_" + key);
    }catch(e)
    {
    	console.log('localStorage出错：'+e); 
    }
	
}
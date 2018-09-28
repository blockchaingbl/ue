/*拷贝*/
function copyinput(event,copyid){
    document.getElementById(copyid).select();
    document.execCommand("copy",false,null);
    layer.msg("已复制到贴粘板");
};

function nav_tab(e){

}
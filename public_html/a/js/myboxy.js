/*
* myboxy.js
*/
$.fn.myBoxy = function (popup, options) {
    myoptions = jQuery.extend ({
        title: "Thông báo",
        message: "",
        type: "alert",
        refresh: false,
        modal: true,
		afterHide:function(){},
        callback: function(){}
    }, options);
	
    var content ='<div class="popup" style="width:560px;margin:0 auto">'+
    '<div class="title-popup">'+
    '<span>'+myoptions.title+'</span>';
	
    if(myoptions.refresh==true)
    {	
    	content		+= '<a title="Đóng lại" class="btn-close close" onclick="Boxy.get(this).hide();location.reload();"><img src="'+Settings.imgurl+'/boxy/close_boxy.png" width="18" height="17" alt="Đóng lại" /></a>';
    }
    else
    {
    	content		+= '<a title="Đóng lại" class="btn-close close" onclick="Boxy.get(this).hide();"><img src="'+Settings.imgurl+'/boxy/close_boxy.png" width="18" height="17" alt="Đóng lại"/></a>';
    }
	
    content		+='</div>'+'<div class="content-popup">';

    
    if(myoptions.type=='alert')
    {
        content += '<div class="rev-form"><div style="margin:0 10px"><div class="alert warning-alert mt10"><span class="ico"></span>'+myoptions.message+'</div></div><div class="clear"></div>';
    }
    if(myoptions.type=='success')
    {
        content += '<div class="rev-form"><div style="margin:0 10px"><div class="alert success-alert mt10"><span class="ico"></span>'+myoptions.message+'</div></div><div class="clear"></div>';
    }
	else if(myoptions.type=='info')
    {
        content += '<div class="rev-form"><div style="margin:0 10px"><div class="alert info-alert mt10"><span class="ico"></span>'+myoptions.message+'</div></div><div class="clear"></div>';
    }
    else if(myoptions.type=='confirm')
    {
        content += '<div class="rev-form"><div style="margin:0 10px"><div class="alert question-alert mt10"><span class="ico"></span>'+myoptions.message+'</div></div><div class="clear"></div>';
    }
	 else if(myoptions.type=='error')
    {
        content += '<div class="bar-notice notice-error"><span class="ico"></span>'+myoptions.message+'</div><div class="clear"></div>';
    }
	else if(myoptions.type=='message' || myoptions.type=='loading')
    {
        content += '<div>'+myoptions.message+'</div>';
    }				
    content += '</div>';
	

    if(myoptions.type=='alert' || myoptions.type=='success' || myoptions.type=='message' || myoptions.type=='info')
    {
    	content +=	'<div class="footer-popup">'+'<div class="btn-default">';
	    if(myoptions.refresh==true)
	    {	
	    	content		+= '<input class="btn btn-s01 close" type="button" onclick="Boxy.get(this).hide();location.reload();" value="Đóng lại" /> ';
	    }
	    else
	    {
	    	content		+= '<input class="btn btn-s02 close" type="button" onclick="Boxy.get(this).hide();" value="Đóng lại" />';
	    }
	    content += '</div>';
    }
    else if(myoptions.type=='confirm')
    {
        content +=  '<div class="footer-popup">' +
		'<div class="btn-default btn-double"><input id="accept" class="btn btn-s01" name="" type="button" value="Đồng ý" /> '+
		'<input id="reject" class="btn btn-s02 close" name="" onclick="Boxy.get(this).hide();" type="button" value="Hủy bỏ" /></div>'+
		'</div>';
    }
    else if(myoptions.type=='loading')
    {
    	content +=  '<div class="footer-popup">';
    }
    content += '</div>';
    new popup(content, myoptions);

    if(myoptions.type=='confirm')
    {
        $("#accept").click(myoptions.callback);
    }
        
    return false;
};
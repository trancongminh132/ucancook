var maxNumID = 6;
(function($) {
    $.fn.extend({
        photoUpload: function(options) {    		
            var opt = $.extend({}, $.uploadSetUp.defaults, options);
            if (opt.file_types.match('jpg') && !opt.file_types.match('jpeg')) {
                opt.file_types += ',jpeg';
            }
            $this = $(this);
            new $.uploadSetUp(opt);
        },
        photoDel: function(id)
        {
           
        }
    });

    $.uploadSetUp = function(opt) 
	{
        var elm_input;        
        opt.maxNumID = parseInt(opt.maxNumID);       
                
        /*Add global variable*/       
        maxNumID = opt.maxNumID;
                
        $('body').append($('<div style="display: none;"></div>').append($('<iframe width="0" height="0" src="about:blank" id="'+ opt.prefix +'myFrame" name="'+ opt.prefix +'myFrame"></iframe>')));
        elm_input = createElmInput(opt);
        $this.append(elm_input);
        $("#"+ opt.prefix +"myFrame").after($('<form target="'+ opt.prefix +'myFrame" enctype="multipart/form-data" action="' + opt.ajaxFile + '" method="POST" name="'+ opt.prefix +'myUploadForm" id="'+ opt.prefix +'myUploadForm" style="display:none"></form>'));
						
		//$("input.file").attr('readonly','readonly');
		
        //Init load file
        initFile(opt);
        init(opt);
    };

    $.uploadSetUp.defaults = {
        // image types allowed
        file_types: "jpg,gif,png",
        // php script
        ajaxFile: "",
        maxNumID: 40,
        prefix: "",
		seckey: "",
		time: 0,
		width: 0,
		height: 0,
		max_size: 0,
        callback: null
    };

    function createElmInput(opt)
    {
		var input = '<input type="hidden" id="width" name="width" value="'+ opt.width +'" /><input type="hidden" id="height" name="height" value="'+ opt.height +'" /><input type="hidden" id="max_size" name="max_size" value="'+ opt.max_size +'" /><input type="hidden" id="time" name="time" value="'+ opt.time +'" /><input type="hidden" id="signkey" name="signkey" value="'+ opt.seckey +'" /><input type="file" name="files" rel="1" id="'+ opt.prefix  +'Filedata_1" class="file" />';
		return input;
    };
	
    //Init load file
    function initFile(opt)
    {
        //Bind
        $('#'+ opt.prefix +'Filedata_1').bind('change', function(e)
        {			
			if (checkFileType(opt, this.value))
			{
				var oldElement = this;
				var newElement = $(oldElement).clone();
				$(oldElement).attr('id', opt.prefix +'Filedata');
				$(oldElement).attr('name', 'files');
				$(oldElement).attr('class', 'clone');
				$(oldElement).before(newElement);
				var newSignElement = $('#signkey').clone();
				var newTimeElement = $('#time').clone();
				var newWidthElement = $('#width').clone();
				var newHeightElement = $('#height').clone();
				var newMaxSizeElement = $('#max_size').clone();
				$('#'+ opt.prefix +'myUploadForm').empty();
				$(oldElement).appendTo('#'+ opt.prefix +'myUploadForm');
				$(newSignElement).appendTo('#'+ opt.prefix +'myUploadForm');
				$(newTimeElement).appendTo('#'+ opt.prefix +'myUploadForm');
				$(newWidthElement).appendTo('#'+ opt.prefix +'myUploadForm');
				$(newHeightElement).appendTo('#'+ opt.prefix +'myUploadForm');
				$(newMaxSizeElement).appendTo('#'+ opt.prefix +'myUploadForm');
				$('#'+ opt.prefix +'spanLoading').html('<img width="16" height="11" src="'+ Settings.imgurl +'/loading_small.gif" />');
				$('#'+ opt.prefix +'myUploadForm').submit();				
			}            
        });
    }
			
    //check if file extension is allowed
    function checkFileType(opt, file_) {
        var ext_ = file_.toLowerCase().substr(file_.toLowerCase().lastIndexOf('.') + 1);
        if (!opt.file_types.match(ext_)) {
            alert('File ảnh không hợp lệ');
            return false;
        } 
        else return true;
    };
    
    function init(opt) {
        // execute event.submit when form is submitted
        $('#'+ opt.prefix +'myUploadForm').submit(function(){
            var bool = event.submit(this);
            initFile(opt);
            return bool;
        });        
                
        // function to handle form submission using iframe
        var event = {
            // setup iframe
            frame: function(_form) {
                $("#"+ opt.prefix +"myFrame")
                .empty()
                .one('load',  function() {
                    event.loaded(this, _form)
                });
            },
            // call event.submit after submit
            submit: function(_form) {
                event.frame(_form);
            },
            // display results from submit after loades into iframe
            loaded: function(id, _form) {            	
                var d = frametype(id);
                var data = d.body.innerHTML.replace(/^\s+|\s+$/g, '');                
                try
                {
                    try
                    {
                        var resp = eval('(' + data + ')');
						
                        if (opt.callback != null)
                        {
                            opt.callback(resp);
                        }
                    //callbackUpload(resp);
                    }
                    catch (ex){
                        alert(ex);
                    }
                    if(typeof rs == 'undefined')
                    {
                        var rs = null;
                    }
                }
                catch(ex)
                {
                    alert("Có lỗi xảy ra trong quá trình đăng ảnh. \nVui lòng thử lại lần nữa.");
                }
            },
            onerror: function(){
                try
                {
                }
                catch(ex)
                {
                    alert(ex);
                }
            }
        };		
        
        // check type of iframe
        function frametype(fid) {
            return (fid.contentDocument) ? fid.contentDocument: (fid.contentWindow) ? fid.contentWindow.document: window.frames[fid].document;
        };       
    };	
})(jQuery);

var ImageUpload = {
	boxy: undefined,
	textareaid: '',
	upload_api: ''
};

ImageUpload.showPopup = function(id)
{			
	var html = '<div class="popup" style="width:400px;margin:0 auto">' +
		'<div class="title-popup">Chèn ảnh<a class="btn-close close"><img src="'+ Settings.imgurl +'/boxy/close_boxy.png" width="18" height="17" alt="Đóng lại" /></a></div>' +
		'<div class="content-popup"><div class="rev-form"><div class="insert-pic"><div class="tab01"><div class="tab">' +
		'<a tab-id="1" class="mtab active">Upload mới</a>' +
		'<a tab-id="2" class="mtab">Chèn URL</a>' +
		'</div>' +
		'<div class="tab-content" id="up1">' +
		'<div class="row"><span class="frm-title">Chọn hình ảnh</span>' +
		'<div id="tinyMceUploadImage"></div>' +
		'</div></div>' +
		'<div id="up2" style="display:none" class="tab-content"><div class="row"><span class="frm-title">Nhập liên kết</span>' +
		'<input type="text" id="tinyMceUrlImage" class="field">' +
		'</div></div><div class="clear"></div>' +
		'</div></div>' +
		'<div class="footer-popup">' +
		'<div class="btn-default btn-double"><input class="btn-accept btn btn-style-08" style="display:none" type="button" value="Đồng ý" /> <input class="btn-cancel close btn btn-style-09" name="" type="button" value="Hủy bỏ" /></div>' +
		'</div>' +
		'</div>';

	ImageUpload.boxy = new Boxy(html, {
		modal: true,
		afterShow: function()
		{
			ImageUpload.textareaid = id;
			$(".mtab").live('click', function()
			{		
				$('.mtab').removeClass('active');
				$(this).addClass('active');
				var tabid = $(this).attr('tab-id');						
				if(tabid == 2)
				{
					$('.boxy-wrapper input.btn-accept').show();
					$('#up1').hide();
					$('#up2').show();
				}
				else
				{
					$('.boxy-wrapper input.btn-accept').hide();
					$('#up2').hide();
					$('#up1').show();
				}
			});
			
			$('#tinyMceUploadImage').photoUpload(
			{
				prefix: 'quick_',
				maxNumID: 1,			
				ajaxFile: Settings.uploadurl,
				file_types: "jpg,png",
				callback: ImageUpload.callback,				
			});
			
			$(".boxy-wrapper .btn-accept").click(function(){
				var photoSrc = $('.boxy-wrapper #tinyMceUrlImage').val().trim();
				if (photoSrc)
				{					
					if(ImageUpload.textareaid != '')
						$('textarea#'+ImageUpload.textareaid).tinymce().execCommand('mceInsertContent',false, '<img style="display:block;margin-left: auto; margin-right: auto" src="'+ photoSrc  +'" />');
					else
						$('textarea.box_html').tinymce().execCommand('mceInsertContent',false, '<img style="display:block;margin-left: auto; margin-right: auto; max-width:700px" src="'+ photoSrc  +'"/>');
				}
				Boxy.get($(".popup")).hide();
			});
		}
	});		
};

ImageUpload.callback = function(resp)
{
	switch(resp.error_code){
		case 0:		
			var photoSrc = resp.data.photo_src;	
			if(ImageUpload.textareaid != '')
				$('textarea#'+ImageUpload.textareaid).tinymce().execCommand('mceInsertRawHTML',false, '<img src="'+ photoSrc  +'" />');
			else
				$('textarea.box_html').tinymce().execCommand('mceInsertRawHTML',false, '<img style="display:block;margin-left: auto; margin-right: auto; max-width:700px" src="'+ photoSrc  +'" />');
			
			Boxy.get($(".popup")).hide();
			
			break;
	}
};
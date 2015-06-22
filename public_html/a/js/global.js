/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

var global = {
    loadingShow: function() {
        var html = global.contentLoading('Đang xử lý');
        new Boxy(html, {
            modal: true,
            unloadOnHide: true
        });
    },
    loadingHide: function() {
        var boxy = Boxy.get($("#popup"));
        boxy.hide();
    },
    contentLoading: function(title) {
        var content =
                '<div id="popup" style="width:400px;margin:0 auto">' +
                '<div class="title">' +
                '<div class="l_title"></div>' +
                '<div class="r_title"></div>' +
                '<h2>' + title + '</h2>' +
                '</div>' +
                '<div class="content">' +
                '<p class="alert"><img src="' + imgurl + '/zoomloader.gif" alt="Đang thực hiện" style="margin-bottom:5px;margin-left:10px;" /></p>' +
                '</div>';
        return content;
    },
    formatCurrency: function(num) {
        num = num.toString().replace(/\$|\,/g, '');
        if (isNaN(num))
            num = "0";
        sign = (num == (num = Math.abs(num)));
        num = Math.floor(num * 100 + 0.50000000001);
        cents = num % 100;
        num = Math.floor(num / 100).toString();
        if (cents < 10)
            cents = "0" + cents;
        for (var i = 0; i < Math.floor((num.length - (1 + i)) / 3); i++)
            num = num.substring(0, num.length - (4 * i + 3)) + ',' +
                    num.substring(num.length - (4 * i + 3));
        return (((sign) ? '' : '-') + num);
    }
}



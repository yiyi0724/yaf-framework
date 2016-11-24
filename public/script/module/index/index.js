$(function(){
    var appendNode = function(data, index) {
        var _html = [];
        _html.push('<tr>');
        _html.push('<th>'+(index ? index : '#')+'</th>');
            
        $.each(data, function(k, val) {
            _html.push('<th>'+val+'</th>');
        });
        
        _html.push('</tr>');
        
        return _html.join("");
    };
    
    var getData = function(path){
        var $panel = $("#"+path);
        var $tbody = $panel.find("tbody");
        
        $.getJSON("/admin/index/"+path+"/",function(json){
            if(json.status && json.data){
                var data = json.data, fAppend = $tbody.eq(0), cAppend = $tbody.eq(1);
                var flen = ("undefined" === typeof data['f']) ? 0 : data['f'].length,
                    clen = ("undefined" === typeof data['c']) ? 0 : data['c'].length;
                
                if(flen === 0 && clen === 0) {
                    return false;
                }
                
                var fData = data['f'], cData = data['c'], len = clen > flen ? clen : flen;
                
                for(var i=0;i<len;i++){
                    "undefined" !== typeof fData[i] && fAppend.append(appendNode(fData[i], i + 1));
                    "undefined" !== typeof cData[i] && cAppend.append(appendNode(cData[i], i + 1));
                }
            }
        });
    };
    
    getData('today');
    getData('week');
    getData('month');
});
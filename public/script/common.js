(function($){
    var delBtn = $('.apidel-btn'), disableBtn = $('.disableBtn'), modal = $('#modalbox'), modalBg = $('#modalBg'),
        showURI = $('.showURIUpload'),  mateForm = $('.materialForm'), inputShowURI = $('.inputShowURI'), startUpload = $('.btn-startUpload'),
        modalContent = $('#modalbox .content'), showURIPreview = $('.showURIPreview'),
        btnNo = $('#modalbox .btn-no'), btnYes = $('#modalbox .btn-yes'), planMult = $('.planmult'), pinput = $('.statSearch .pinput'),
        changeRealTime = $('.changeRealtime'),
        inputEntry = 0, cacheRealVal = 0, interTime = 0;

    var fromName = {site:'站点', resource:'资源', plan:'方案'};
    var planType = {show:'展示量',click:'点击量',reg:'注册量'};

    var openModal = function() {
        modal.show();
        modalBg.show();
    },
    closeModal = function() {
        modal.hide();
        modalBg.hide();
    },
    runing = function() {
        btnYes.text('正在处理...');
        btnYes.attr('disabled', true);
        btnNo.attr('disabled', true);
    },
    unruning = function() {
        btnYes.text('确 认');
        btnYes.attr('disabled', false);
        btnNo.attr('disabled', false);
    },
    tip = function(callback, time, referer) {
        time = time > 1000 ? time : 2500;
        inputEntry = 0;

        'function' === typeof callback && !function() {
            var tipBox = callback.call();
            var T = setTimeout(function(){
                tipBox.remove();
                clearTimeout(T);

                if(referer === true) {
                    window.location.reload();
                } else if('function' === typeof referer) {
                    referer.call();
                }
            }, time);
        }();

        unruning();
    };

    window.showURICallback = function(data) {
        startUpload.val('开始上传');
        startUpload.attr('disabled', false);
        btnNo.attr('disabled', false);

        if(!data.status || !data.data) {
            tip(function(){
                modalContent.after('<span class="error-msg msg">'+(data.msg?data.msg:'数据处理失败!')+'</span>');
                return modalContent.next();
            }, 3000);
            return false;
        }
        inputShowURI.val(data.data);
        tip(function(){
            modalContent.after('<span class="success-msg msg">'+(data.msg?data.msg:'数据处理成功!')+'</span>');
            return modalContent.next();
        }, 3000, closeModal);
        return false;
    };

    showURI.click(function() {
        openModal();
    });

    delBtn.click(function() {
        var that = $(this), html = [], key = that.attr('data-flow');
        btnYes.attr('data-flow', key);
        btnYes.attr('data-uri', that.attr('data-uri'));

        var keyName = key.split(':'), keyCn = key;

        if(keyName.length === 2) {
            var kn = keyName[0].toLowerCase();
            keyCn = (('undefined' !== typeof fromName[kn]) ? fromName[kn]+':'+keyName[1] : key);
        }

        html.push('<span>确认要删除当前记录 <b>'+keyCn+'</b> 吗? 操作不可逆.</span>');
        inputEntry = 0;
        modalContent.html(html.join(''));
        openModal();
    });

    disableBtn.click(function() {
        var that = $(this), html = [], disable = parseInt(that.attr('data-flow'));
        btnYes.attr('data-flow', disable === 0 ? 1 : 0);
        btnYes.attr('data-uri', that.attr('data-uri'));
        html.push('<span>确认要'+(disable === 0 ? '禁用' : '启用')+'这个API Key吗?</span>');
        modalContent.html(html.join(''));
        openModal();
    });

    btnNo.click(function(){
        closeModal();
    });

    btnYes.click(function(){
        runing();
        var that = $(this), uri = that.attr('data-uri'),
            flow = that.attr('data-flow'),
            comfirm = parseInt(that.attr('data-comfirm'));

        if(comfirm === 1) {
            var input = modalContent.find('.sure-input'), inputFlow = input.val();

            if(inputEntry < flow.length || inputFlow !== flow) {
                tip(function(){
                    input.after('<span class="error msg">输入有误, 请重试或取消操作.</span>');
                    return input.next();
                }, 3);
                return false;
            }
        }

        uri += (uri.indexOf('?') === -1 ? '?' : '&') + 'flow='+flow;

        $.get(uri,function(data) {
            if(data.status && data.status === true) {
                tip(function(){
                    var box = (comfirm === 1) ? input : modalContent;
                    box.after('<span class="success msg">'+(data.msg?data.msg:'数据处理成功!')+'</span>');
                    return box.next();
                }, 3000, true);

                return false;
            }

            tip(function(){
                input.after('<span class="error msg">通信错误.</span>');
                return input.next();
            }, 3);
        });
    });

    mateForm.submit(function() {
        startUpload.val('正在上传...');
        startUpload.attr('disabled', true);
        btnNo.attr('disabled', true);
        return true;
    });

    showURIPreview.click(function(){
        if('undefined' === typeof ADURL) {
            alert('广告主域未配置');
            return false;
        }

        window.open(ADURL+inputShowURI.val(), '_blank', 'menubar=no, z-look=yes, scrollbars=no, toolbar=no, status=yes, resizable=no');
    });

    modalContent.on('keypress',function(){
        inputEntry++;
    });

    pinput.click(function(){
        planMult.show();
        modalBg.show();
    });

    planMult.on('click', function() {
        pinput.html($('.planmultCheckbox:checked').size() > 0 ? '已选择' : '未选择');
    });

    if($(".datepicker").size() > 0) {
        $(".datepicker").datepicker({
            'format':'yyyy-mm-dd',
            'monthNames':['一月','二月','三月','四月','五月','六月','七月','八月','九月','十月','十一月','十二月'],
            'dayNamesMin':['日','一','二','三','四','五','六']
        });
     }

    $('.datepicker,.selectPlanState').change(function(){
        if(interTime > 0) {
            return false;
        }

        interTime++;

        var that = $(this), params = {}, uri = [], planDlCommon = $('.planDlCommon');

        planDlCommon.html('<span>数据加载中...</span>');
        pinput.html('未选择');

        params.start = $('.dateStart').val();
        params.end = $('.dateEnd').val();
        params.type = $('.selectPlanType').val();
        params.state = $('.selectPlanState').val();

        $.each(params, function(k, v) {
            uri.push(k+'='+encodeURIComponent(v));
        });

        $.get('/admin/ads/plans?'+uri.join('&'), function(json){
             if((!json.errmsg || json.errmsg.length === 0) && json.data) {
                var html = [];

                 html.push('<dl class="planDlCommon">');

                 $.each(json.data, function(code, name) {
                     html.push('<dd>');
                     html.push('<input type="checkbox" class="planmultCheckbox" name="planmult[]" value="'+code+'" />');
                     html.push('<span>'+name+'</span>');
                     html.push('</dd>');
                     return false;
                 });

                 html.push('</dl>');

                planDlCommon.remove();
                planMult.append(html.join(''));

                var T = setInterval(function(){
                    interTime--;

                    if(interTime === 0) {
                        clearInterval(T);
                    }
                }, 1000);

                return false;
            }

            return false;
        });
    });

    $('.syncStart2End').change(function(){
       $('.syncS2ETarget').val($(this).val());
       return false;
    });

    changeRealTime.click(function(){
        var that = $(this), input = that.children('.changeRealtimeInput'), crt = that.children('.changeRealtimeText');
        input.show();
        input.focus();
        cacheRealVal = parseInt(input.val());
        that.children('.changeRealtimeText').html('<em>Loading...</em>');
    });

    $('.changeRealtimeInput').blur(function(){
        var that = $(this), index = that.attr('data-index'), type = that.attr('data-type'),
            statDataTd = $('#statDataIndex_'+index).children('td'), digital = parseInt(that.val()), crt = that.next('.changeRealtimeText');
        that.hide();

        if(cacheRealVal === digital) {
            crt.html(cacheRealVal);
            return false;
        }

        var params = {}, uri = [];
        params.v = parseInt(digital);
        params.e = encodeURIComponent(type);

        $.each({t:'plantime', c:'plancode', d:'plandomain', a:'showaddr', w:'planwidth', h:'planheight'},function(key, name){
            params[key] = decodeURIComponent(statDataTd.children('#stat_'+name).val());
        });

        $.each(params, function(key, val){
            uri.push(key+'='+encodeURIComponent(val));
        });

        $.get('/admin/ads/input?'+uri.join('&'), function(json){
            if(json.status && json.status === true) {
                crt.html(digital);
                return false;
            }

            crt.html(cacheRealVal);
            return false;
        });
    });

})(jQuery);

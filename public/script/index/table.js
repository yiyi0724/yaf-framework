/**
 * Created by eny on 16-10-12.
 */
$(function(){
    //查询
    (function(){
        var $query = $("#query");
        var $mobile = $query.find("input[name='mobile']");
        var $status = $query.find("select[name='status']");
        var $type = $query.find("select[name='type']");
        var $cuserLevel = $query.find("select[name='cuserLevel']");
        var $search = $query.find("#search");
        $search.click(function(){
            var param = {
                'mobile':$.trim($mobile.val()),
                'status':$status.val(),
                'type':$type.val(),
                'cuserLevel':$cuserLevel.val()
            }
            var url = "/admin/channelapply/";
            for(var i in param){
                if(param[i]!=''){
                    url += "&"+i+"="+param[i];
                }
            }
            location.href=url.replace(/\/&/,'/?');
        });
    })();

    //编辑
    (function(){
        var $modal_edit = $('#sample-modal-edit');
        var $table = $('.main-content table');
        var parameter = {};
        var	$tip = $modal_edit.find('.help-block'),
            $uid = $modal_edit.find("input[name='uid']"),
            $name = $modal_edit.find("input[name='name']"),
            $channel_key = $modal_edit.find("input[name='channel_key']"),
            $ratio = $modal_edit.find("input[name='ratio']"),
            $status = $modal_edit.find("select[name='status']"),
            $disabled = $modal_edit.find("input[name='disabled']"),
            $auth = $modal_edit.find("input[name='auth']"),
            $remark = $modal_edit.find("textarea[name='remark']");
        $typeName = $modal_edit.find("input[name='typeName']");
        $leve = $modal_edit.find("input[name='leve']");
        $contactName = $modal_edit.find("input[name='contact_name']");
        $contactPhone = $modal_edit.find("input[name='contact_phone']");
        $qq = $modal_edit.find("input[name='qq']");
        $addtime = $modal_edit.find("input[name='addtime']");
        $auditing = $modal_edit.find("input[name='auditing']");
        $table.find('.btn-default').click(function(){
            var index = $(this).data('index');
            parameter = _data[index];
            $uid.val(parameter['uid']);
            $name.val(parameter['name']);
            $channel_key.val(parameter['channel_key']);
            $ratio.val(parameter['ratio']);
            $remark.val(parameter['remark']);
            $status.prop('select',parameter['status']);
            $typeName.val(parameter['typeName']);
            $leve.val(parameter['leve']);
            $contactName.val(parameter['contact_name']);
            $contactPhone.val(parameter['contact_phone']);
            $qq.val(parameter['qq']);
            $addtime.val(parameter['addtime']);
            $auditing.val(parameter['auditing']);
            $disabled.prop('checked',parameter['disabled']);
            $auth.prop('checked',parameter['auth']);
            $('.select').find('option').each(function(){
                if($(this).val() === parameter['status']){
                    $(this).attr('selected',true);
                }
            });
            if(parameter['disabled']){
                $disabled.parents('.checkbox').addClass('checked');
            }else{
                $disabled.parents('.checkbox').removeClass('checked');
            }
            if(parameter['auth']){
                $auth.parents('.checkbox').addClass('checked');
            }else{
                $auth.parents('.checkbox').removeClass('checked');
            }
        });
        $modal_edit.find('.modal-footer .btn-info').click(function(){
            var param = {
                'id':parameter['id'],
                'channel_key':$.trim($channel_key.val()),
                'ratio':$.trim($ratio.val()),
                'remark':$.trim($remark.val()),
                'disabled':$disabled.prop('checked')?1:0,
                'auth':$auth.prop('checked')?1:0,
                'status':$.trim($status.val())
            };
            $modal_edit.find('.form-group').removeClass('has-error');
            if(param['name']==''){
                $name.focus().parents('.form-group').addClass('has-error');
            }else if(param['channel_key']==''){
                $channel_key.focus().parents('.form-group').addClass('has-error');
            }else{
                $.post('/admin/channelapply/audit/',param,function(json){
                    if(json.status){
                        $modal_edit.modal('hide');
                        location.reload();
                    }else{
                        $tip.text(json.msg).parents('.form-group').addClass('has-error');
                    }
                },'json');
            }
        });
    })();

    //设置分成比
    (function(){
        var $modal_edit = $('#sample-modal-ratio');
        var $table = $('.main-content table');
        var parameter = {};
        var	$tip = $modal_edit.find('.help-block'),
            $name = $modal_edit.find("input[name='name']"),
            $ratio = $modal_edit.find("input[name='ratio']"),
            $gameratio = $modal_edit.find("input[name='gameratio']"),
            $gameid = $modal_edit.find("select[name='gameid']");
        var getRatio = function(){
            $.getJSON('/admin/channelapply/getratio/',{
                applyid:parameter['id'],
                gameid:$gameid.val()
            },function(json){
                if(json.status){
                    $gameratio.val(json.data.ratio);
                }else{
                    $gameratio.val('');
                }
            });
        };
        $gameid.change(getRatio);
        $table.find('.btn-default').click(function(){
            var index = $(this).data('index');
            parameter = _data[index];
            $name.val(parameter['name']);
            $ratio.val(parameter['ratio']);
            getRatio();
        });
        $modal_edit.find('.modal-footer .btn-info').click(function(){
            var param = {
                'applyid':parameter['id'],
                'gameid':$.trim($gameid.val()),
                'gameratio':$.trim($gameratio.val())
            };
            $modal_edit.find('.form-group').removeClass('has-error');
            if(param['gameid']==''){
                $gameid.focus().parents('.form-group').addClass('has-error');
            }else if(param['gameratio']==''){
                $gameratio.focus().parents('.form-group').addClass('has-error');
            }else{
                $.post('/admin/channelapply/setratio/',param,function(json){
                    if(json.status){
                        $modal_edit.modal('hide');
                        location.reload();
                    }else{
                        $tip.text(json.msg).parents('.form-group').addClass('has-error');
                    }
                },'json');
            }
        });
    })();
});

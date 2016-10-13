// $("#main-menu li:has(.active)").addClass("opened");

(function(){
	var $modal_message = $('#sample-modal-message');
	var $tip = $modal_message.find('.help-block'),
		$target_id = $modal_message.find("input[name='target_id']"),
		$content = $modal_message.find("textarea[name='content']");
		$modal_message.find('.modal-footer .btn-info').click(function(){
			var param = {
				'target_id':$.trim($target_id.val()),
				'content':$.trim($content.val())
			};
			$.post('/admin/message/send/',param,function(json){
				if(json.status){
					$modal_message.modal('hide');
					location.reload();
				}else{
					$tip.text(json.msg).parents('.form-group').addClass('has-error');
				}
			},'json');
		});
        
        var $modal_message = $('#sample-modal-announcement'), $addAnnouBtn = $('.addAnnouBtn');
	var $tip = $modal_message.find('.help-block'),
		$title = $modal_message.find("input[name='announ_title']"),
                $announcement_id = $modal_message.find("input[name='announcement_id']"),
		$content = $modal_message.find("textarea[name='announ_content']"),
                $announ_release = $modal_message.find("input[name='announ_release']"),
                $announ_release_to = $modal_message.find("input[name='announ_release_to']");
        
        $addAnnouBtn.click(function(){
            $announcement_id.val(0);
            $title.val('');
            $content.val('');
            $announ_release.attr("checked",false);
            $announ_release_to.attr("checked",false);
            
            $announ_release.first().prop("checked",true);
            $announ_release_to.first().prop("checked",true);
                    
            $tip.text('').parents('.form-group').removeClass('has-error');
        });
                
	$modal_message.find('.modal-footer .btn-info').click(function(){
		var param = {
			'title':$.trim($title.val()),
			'content':$content.val(),
                        'release':$modal_message.find("input[name='announ_release']:checked").val(),
                        'release_to':$modal_message.find("input[name='announ_release_to']:checked").val()
		};
                
                var anouID = $announcement_id.val(), url = '/admin/announcement/add';
                
                if(anouID > 0) {
                    param['id'] = anouID;
                    url = '/admin/announcement/edit';
                }
                
		$.post(url,param,function(json){
			if(json.status){
				$modal_message.modal('hide');
				location.reload();
			}else{
				$tip.text(json.msg).parents('.form-group').addClass('has-error');
			}
		},'json');
	});

	// 展开栏目
	$('#main-menu li').each(function() {
		if(window.location.pathname == $(this).find('a').attr('href')) {
			$(this).addClass('active');
			var closest = $(this).closest('.root-level');
			if(closest.hasClass('has-sub')) {
				closest.addClass('opened');
			}
		}
	})
})();
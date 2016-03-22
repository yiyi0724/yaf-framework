$(function() {

	var $anchors = $('#main-menu li a');
	var url = window.location.href;

	url = '/admin/member';

	$anchors.each(function() {
		if($(this).attr('href') == url) {

			$li = $(this).parent('li');
			while(true) {
				if($li.hasClass('.root-level')) {
					$li.addClass('active opened');
					break;
				}

				$li.addClass('active');

				$li.parent('ul').addClass('visible');
				$li = $li.parent('ul').find('li');

			}


/*			var $li = $(this).closest('.root-level');
			$li.addClass('active opened');
			if($li.hasClass('has-sub')) {
				var $ul = $li.children('ul');
				$ul.addClass('visible');
			}*/

			//visible

			return false;
		}
	})
})
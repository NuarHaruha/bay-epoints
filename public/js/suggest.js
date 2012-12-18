/**
 * require jquery, jquery suggest
 *
 * @package Isra'Life
 *
 * @author  Nuarharuha <nhnoah+bay-isra@gmail.com>
 * @license MIT License <http://mdag.mit-license.org/>
 * @since   1.0.0
 */
jQuery(document).ready(function($) {
	$('<img>', {
		src: plugin_uri + 'img/small-loader.gif',
		class: 'dn isloading'
	}).appendTo('#mc-deposit h3.hndle');
	var mc = {
		type: {
			name: 1,
			code: 2
		},
		url: ajaxurl,
		params: {
			action: "mc-get-uservar",
			json: 1
		},
		data: false,
		start: function() {
			$('img.isloading').toggleClass('dn');
		},
		end: function() {
			this.start();
		},
		setup: function(data, type) {
			this.start();
			$('#dataresult').val(data.id);
			switch (type) {
			case 1:
				$('#user_code').val(data.code);
				break;
			case 2:
				$('#user_name').val(data.name);
				break;
			}
			//console.log('Request : ' + type + ', user id: ' + data.id + ', user code: ' + data.code + ', name: ' + data.name);
			this.end();
		},
		getUserInfo: function(key, val) {
			switch (key) {
			case 1:
				this.params.name = val;
				break;
			case 2:
				this.params.code = val;
                
				break;
			}
            
            this.params.json = key;
			var request_type = key;
			$.getJSON(this.url, this.params, function(data) {
				mc.setup(data, request_type);
			});
		},
	};
	$("input#user_name").suggest(ajaxurl + "?action=mc-suggest-name", {
		delay: 500,
		minchars: 2,
		onSelect: function() {
			mc.getUserInfo(mc.type.name, this.value);
		}
	});
	$("input#user_code").suggest(ajaxurl + "?action=mc-suggest-code", {
		delay: 500,
		minchars: 2,
		onSelect: function() {
			mc.getUserInfo(mc.type.code, this.value);
		}
	});
});
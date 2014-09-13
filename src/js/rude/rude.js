//http://stackoverflow.com/questions/881515/javascript-namespace-declaration

var rude =
{
	animate: function(selector)
	{
		$(selector).slideToggle();
	},

	redirect: function(url)
	{
		window.open(url, '_self', false);
	},

	form:
	{
		submit: function(selector)
		{
			$(selector).submit();
		}
	},

	cookie:
	{
		get: function(key)
		{
			var matches = document.cookie.match(new RegExp("(?:^|; )" + key.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"));

			return matches ? decodeURIComponent(matches[1]) : undefined;
		},

		set: function (key, value, days, path, domain, secure)
		{
			if (!key || /^(?:expires|max\-age|path|domain|secure)$/i.test(key))
			{
				return false;
			}

			var expires = '';

			if (days)
			{
				var date = new Date();

				date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));

				expires = '; expires=' + date.toUTCString();
			}

			document.cookie = encodeURIComponent(key) + '=' + encodeURIComponent(value) + expires + (domain ? '; domain=' + domain : '') + (path ? '; path=' + path : '') + (secure ? '; secure' : '');

			return true;
		},

//		remove: function(key)
//		{
//			document.cookie = key + '=;expires=Thu, 01 Jan 1970 00:00:01 GMT;';
//		},

		is_exists: function(key, value)
		{
			if (value)
			{
				return rude.cookie.is_equals(key, value);
			}

			return (rude.cookie.get(key) == true);
		},

		is_equals: function(key, value)
		{
			return (rude.cookie.get(key) == value);
		},

		toggle: function(key)
		{
			if (rude.cookie.is_exists(key))
			{
				rude.cookie.set(key, 0, 180, '/');
			}
			else
			{
				rude.cookie.set(key, 1, 180, '/');
			}
		}
	},

	semantic:
	{
		init:
		{
			menu: function()
			{
				window.menu = {};

				// ready event
				window.menu.ready = function() {

					// selector cache
					var $menuItem = $('.menu a.item, .menu .link.item'),
					// alias
						handler = {
							activate: function() {
								$(this)
									.addClass('active')
									.closest('.ui.menu')
									.find('.item')
									.not($(this))
									.removeClass('active');
							}
						};

					$menuItem.on('click', handler.activate);
				};


				// attach ready event
				$(document).ready(window.menu.ready);
			},

			radio: function()
			{
				$(function() {
					$('.ui.checkbox').checkbox();
				});
			},

			dropdown: function()
			{
				$(function() {
					$('.ui.dropdown').dropdown();
				});
			}
		}
	},

	copy: function(text)
	{
		// CTRL+C workaround
		var textarea = document.createElement("textarea");
		textarea.textContent = text;
		var body = document.getElementsByTagName('body')[0];
		body.appendChild(textarea);
		textarea.select();
		document.execCommand('copy');
		body.removeChild(textarea);
	}
};
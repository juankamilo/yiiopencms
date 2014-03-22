/**
 * @namespace Holds the cms functionality.
 */
var Cms = Cms || {};

(function() {
	/**
	 * Rot13 class.
	 * @author Christoffer Niska <christoffer.niska@nordsoftware.com>
	 * @class Cms.Rot13
	 */
	Cms.Rot13 = {
		map: null,
		length: 0,
		init: function() {
			if (this.map)
				return;

			var i, map = [],
				chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz',
				length = chars.length;

			for (i = 0; i < length; ++i) {
				map.push(chars.charAt((i + 13) % length));
			}

			this.map = map;
			this.length = length;
		},
		decode: function(text) {
			this.init();

			var i, c, idx, result = '';

			for (i = 0; i < text.length; ++i) {
				c = text.charAt(i);
				idx = (this.map.indexOf(c) - 13) % this.length;

				if (idx < 13 && idx >= 0 ) {
					idx += this.length / 2;
				}

				result += this.map[idx] ? this.map[idx] : c;
			}

			return result;
		}
	};

})();
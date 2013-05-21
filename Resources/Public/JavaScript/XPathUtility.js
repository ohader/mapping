/**
 * @see http://getfirebug.com/developer/api/firebug1.5/symbols/src/content_firebug_lib.js.html
 */
XPathUtility = {
	getElementXPath: function(element) {
		if (element && element.id)
			return '//*[@id="' + element.id + '"]';
		else
			return XPathUtility.getElementTreeXPath(element);
	},

	getElementTreeXPath: function(element) {
		var paths = [];

		for (; element && element.nodeType == 1; element = element.parentNode) {
			var index = 0;
			for (var sibling = element.previousSibling; sibling; sibling = sibling.previousSibling)
			{
				if (sibling.localName == element.localName)
					++index;
			}

			var tagName = element.localName.toLowerCase();
			// Always add XPath element index
			var pathIndex = "[" + (index+1) + "]";
			paths.splice(0, 0, tagName + pathIndex);
		}

		return paths.length ? "/" + paths.join("/") : null;
	}
};
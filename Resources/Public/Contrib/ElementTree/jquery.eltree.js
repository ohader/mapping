/*
 *  Project: Rocking Element Tree (a.k.a. eltree)
 *  Description: Adds a firebug-like functionality on page, that gives you element tree and properties by clicking on it
 *  Author: Rochester Oliveira
 *  License: GNU General Public License ( http://en.wikipedia.org/wiki/GNU_General_Public_License )
 */

// awesome structure from http://jqueryboilerplate.com/
;(function ( $, window, document, undefined ) {
    // Create the defaults once
    var pluginName = 'eltree',
        defaults = {
			debug: "<div id='eltreeDebug'><span id='eltreeCSSSel'></span><span id='eltreeJSSel'></span><span id='eltreeXPathSel'></span><a href='#' class='btn primary' id='eltreeStop'>Stop debugging</a></div>"
        };

    // The actual plugin constructor
    function Plugin( element, options ) {
        this.element = element;

        this.options = $.extend( {}, defaults, options) ;
        
        this._defaults = defaults;
        this._name = pluginName;
		
		this.obj = $(this.element);
        
        this.init();
    }

    Plugin.prototype.init = function () {
		var obj = this.obj,
			$this = "",
			debugDiv = "",
			debugCss = "",
			debugJS = "",
			debugXPath = '',
			debug = this.options.debug;
			
		//add our debugger box and cache
		debugDiv = $(debug).appendTo($("body"));
		debugCss = debugDiv.children("#eltreeCSSSel");
		debugJS = debugDiv.children("#eltreeJSSel");
		debugXPath = debugDiv.children("#eltreeXPathSel");
		
		//prepare remove functions
		$("#eltreeStop").click(function(){
			window.location.reload(); // Instead of unbinding everything, just reload page. Now, IE, you can take a deep breath
		});
		//now we'll add those logger functions
		obj.addClass("debugging").click(function(event){
			event.preventDefault(); // so links wont be opened while debugging
			logThis( event.target, debugCss, debugJS, debugXPath ); //and let's add this to our logger spans
		});
    };
	function logThis(elem, css, js, xpath ) {
		var sel = selectors(elem);
		//if you want to do something else with results (like sending to a feedback plugin) add stuff here
		css.text( sel[0] );
		js.text( sel[1] );
		xpath.text(XPathUtility.getElementXPath(elem));
	}
	function selectors(elem) {
		var css = "",
			continueCss = 1,
			js = "",
			parent = "",
			ret = [];
			
		while (elem !== window.document && elem.parentNode) {
			parent = elem.parentNode;
			console.log(elem);
			console.log(parent);

			//js selector
			x=0;
			while(  ( $(parent.childNodes[x])[0] !== elem ) && (x < parent.childNodes.length) ) {
				x++;
			}
			//now we have our childNode!
			js = x + "," + js;
			
			//CSS selector
			if (continueCss) {
				if(elem.id) {
					css = elem.nodeName + '#' + elem.id + " " + css;
					continueCss = 0;
				} else if(elem.className) {
					css = elem.nodeName + '.' + elem.className + " " + css;
				} else {
					css = elem.nodeName + " " + css;
				}
			}
			//let's go up one level
			elem = elem.parentNode;
		}
		//let's make our js selector useful
		js = (js.slice(0, -1)).split(",");
		for(x=0; x< js.length; x++) {
			js[x] = "childNodes[" + js[x] + "]";
		}
		js = "window. document. " + js.join(". ");
		
		ret[0] = css.toLowerCase(); //css
		ret[1] = js; //js
		return ret;
	}
    // A really lightweight plugin wrapper around the constructor, preventing against multiple instantiations
    $.fn[pluginName] = function ( options ) {
        return this.each(function () {
            if (!$.data(this, 'plugin_' + pluginName)) {
                $.data(this, 'plugin_' + pluginName, new Plugin( this, options ));
            }
        });
    }
})(jQuery, window, document);
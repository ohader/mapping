(function($) {
	OliverHader_Mapping_Module = function(settings, options) {
		var defaults = {
			selector: {
				templateComponent: '#mapping-workspace-template',
				elementsComponent: '#mapping-workspace-elements',
				frameComponent: '#mapping-iframe-template',
				xpathComponent: '#mapping-xpath-template',
				selected: '.mapping-selected',
				defined: '.mapping-defined',
				addElementButton: '.btn.mapping-element-add',
				loadStructureButton: '.btn.mapping-structure-load',
				updateStructureButton: '.btn.mapping-structure-update',
				structure: '.mapping-structure',
				element: '.mapping-element'
			},
			identifier: {
				frame: 'mapping-iframe-template'
			},
			cssClass: {
				hover: 'mapping-hover',
				selected: 'mapping-selected',
				defined: 'mapping-defined',
				element: 'mapping-element'
			}
		};

		this.xpath = '';
		this.structure = null;
		this.elements = {};
		this.settings = settings || [];
		this.options = $.extend({}, defaults, options);
		this.templateComponent = $(this.options.selector.templateComponent);
		this.elementsComponent = $(this.options.selector.elementsComponent);
		this.xpathComponent = $(this.options.selector.xpathComponent);
		this.frameComponent = null;

		this.addElementButton = $(this.options.selector.addElementButton);
		this.addElementButton.click($.proxy(this.addElementButtonClick, this));

		this.loadStructureButton = $(this.options.selector.loadStructureButton);
		this.loadStructureButton.click($.proxy(this.loadStructureButtonClick, this));

		this.updateStructureButton = $(this.options.selector.updateStructureButton);
		this.updateStructureButton.click($.proxy(this.updateStructureButtonClick, this));

		this.initialize();
	};

	OliverHader_Mapping_Module.prototype.initialize = function() {
		this.getData();
	};

	OliverHader_Mapping_Module.prototype.getData = function() {
		$.ajax({
			type: 'POST',
			url: this.settings.urls.Module.data,
			dataType: 'json',
			success: $.proxy(this.getDataCallback, this),
			context: this
		})
	};

	OliverHader_Mapping_Module.prototype.loadStructureButtonClick = function(event) {
		var element = $(event.target).parents(this.options.selector.structure, this.options.selector.structure);

		if (element && element.data('structure')) {
			this.updateStructureButton.hide();
			this.loadStructure(element.data('structure'));
			element.find(this.options.selector.updateStructureButton).show();
		}
	};

	OliverHader_Mapping_Module.prototype.updateStructureButtonClick = function(event) {
		var element = $(event.target).parents(this.options.selector.structure, this.options.selector.structure);

		if (element && element.data('structure')) {
			var data = {};
			data[this.settings.arguments.prefix] = {
				structure: this.structure.uid,
				elements: JSON.stringify(this.elements)
			};

			$.ajax({
				type: 'POST',
				url: this.settings.urls.Structure.update,
				dataType: 'json',
				data: data,
//				success: $.proxy(this.updateStructureCallback, this),
				context: this
			});
		}
	};

	OliverHader_Mapping_Module.prototype.loadStructure = function(structure) {
		var data = {};
		data[this.settings.arguments.prefix] = { structure: structure };

		$.ajax({
			type: 'POST',
			url: this.settings.urls.Structure.load,
			dataType: 'json',
			data: data,
			success: $.proxy(this.loadStructureCallback, this),
			context: this
		});
	};

	OliverHader_Mapping_Module.prototype.loadStructureCallback = function(structure) {
		console.log(structure);

		this.xpath = '';
		this.structure = structure;
		this.elements = structure.elements;

		this.loadTemplate(structure.uid);
		this.drawXPath();
		this.drawElements();
	};

	OliverHader_Mapping_Module.prototype.loadTemplate = function(structure) {
		var url = this.settings.urls.Structure.html +
			'&' + this.settings.arguments.prefix + '[structure]=' + structure;

		this.frameComponent = $('<iframe></iframe>');
		this.frameComponent.attr('id', this.options.identifier.frame);
		this.frameComponent.attr('width', '100%');
		this.frameComponent.attr('height', '100%');

		this.templateComponent.empty().append(this.frameComponent);

		this.frameComponent.load($.proxy(this.initializeTemplate, this));
		this.frameComponent.attr('src', url);
	};

	OliverHader_Mapping_Module.prototype.initializeTemplate = function(event) {
		this.frameComponent.contents().find('body').addClass('mapping-active').click($.proxy(this.handleMappingClick, this));
		this.visualizeElements();
	};

	OliverHader_Mapping_Module.prototype.handleMappingClick = function(event) {
		event.preventDefault();

		this.xpath = XPathUtility.getElementXPath(event.target);
		this.selectElement(event.target, this.xpath);
	};

	OliverHader_Mapping_Module.prototype.selectElement = function(element, xpath) {
		this.frameComponent.contents().find(this.options.selector.selected).removeClass(this.options.cssClass.selected);
		$(element).addClass(this.options.cssClass.selected);

		if (xpath) {
			this.xpath = xpath;
			this.drawXPath();
		}
	};

	OliverHader_Mapping_Module.prototype.addXPathButton = function(value, xpath) {
		var button;

		if (value !== '') {
			button = $('<button class="btn"></button>').text(value).attr('title', xpath).data('xpath', xpath);
			button.click($.proxy(this.handleXPathClick, this));
			this.xpathComponent.append(button);
		}
	};

	OliverHader_Mapping_Module.prototype.handleXPathClick = function(event) {
		var element = $(event.target);
		var xpath = element.data('xpath');
		this.selectElement(this.getElement(xpath));
	};

	OliverHader_Mapping_Module.prototype.addElementButtonClick = function(event) {
		if (!this.elements[this.xpath]) {
			this.elements[this.xpath] = {
				name: 'element_name',
				scope: 'inner'
			};
		}

		this.drawElements();
		this.visualizeElements();
	};

	OliverHader_Mapping_Module.prototype.removeElementButtonClick = function(event) {
		var element = $(event.target).parents(this.options.selector.element, this.options.selector.element);

		if (element && this.elements[element.data('xpath')]) {
			delete(this.elements[element.data('xpath')]);
		}

		this.drawElements();
		this.visualizeElements();
	};

	OliverHader_Mapping_Module.prototype.drawXPath = function() {
		var self = this;
		var xpathItems = this.xpath.split('/');

		this.xpathComponent.empty();

		$(xpathItems).each(function(index, value) {
			self.addXPathButton(value, xpathItems.slice(0, index+1).join('/'));
		});

		if (xpathItems.length > 1) {
			this.addElementButton.show();
		} else {
			this.addElementButton.hide();
		}
	};

	OliverHader_Mapping_Module.prototype.drawElements = function() {
		var self = this;
		this.elementsComponent.empty();

		$.each(this.elements, function(xpath, data) {
			var element = $('<div class="' + self.options.cssClass.element + ' well well-small well-dark"></div>');
			var removeButton = $('<button class="close pull-right"><i class="icon-remove-circle"></i></button>').click($.proxy(self.removeElementButtonClick, self));
			var nameField = $('<input type="text" value="' + data.name + '">').change($.proxy(self.elementNameChange, self));
			var scopeSelect = $('<select><option value="inner">inner</option><option value="outer">outer</option></select>').val(data.scope).change($.proxy(self.elementScopeChange, self));

			element.append(removeButton);
			element.append($('<div><strong>XPath:</strong> ' + xpath.split('/').join(' / ') + '</div>'));
			element.append($('<div></div>').append(nameField));
			element.append($('<div></div>').append(scopeSelect));
			element.data('xpath', xpath);

			self.elementsComponent.append(element);

			element.click(
				function() { self.selectElement(self.getElement(xpath), xpath); }
			);
			element.hover(
				function() { self.getElement(xpath).addClass(self.options.cssClass.hover); },
				function() { self.getElement(xpath).removeClass(self.options.cssClass.hover); }
			);
		});
	};

	OliverHader_Mapping_Module.prototype.visualizeElements = function(event) {
		var self = this;
		this.frameComponent.contents().find(this.options.selector.defined).removeClass(this.options.cssClass.defined);

		$.each(this.elements, function(xpath, data) {
			self.getElement(xpath).addClass(self.options.cssClass.defined);
		});
	};

	OliverHader_Mapping_Module.prototype.getElement = function(xpath) {
		return $.xpath(this.frameComponent.contents(), xpath);
	};

	OliverHader_Mapping_Module.prototype.elementNameChange = function(event) {
		var element = $(event.target).parents(this.options.selector.element, this.options.selector.element);

		if (element && element.data('xpath')) {
			var xpath = element.data('xpath');
			this.elements[xpath].name = $(event.target).val();
		}
	};

	OliverHader_Mapping_Module.prototype.elementScopeChange = function(event) {
		var element = $(event.target).parents(this.options.selector.element, this.options.selector.element);

		if (element && element.data('xpath')) {
			var xpath = element.data('xpath');
			this.elements[xpath].scope = $(event.target).val();
		}
	};

	OliverHader_Mapping_Module.prototype.getViewComponent = function() {
		return this.viewComponent;
	};

}(jQuery));
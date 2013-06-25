(function($) {
	OliverHader_Mapping_Assignment = function(baseId, data, options) {
		var defaults = {
			selector: {
				structureComponent: baseId + '-structure',
				contextComponent: baseId + '-context',
				assignmentComponent: baseId + '-assignment',
				dataComponent: baseId + '-data'
			},
			cssClass: {
				element: 'mapping-element'
			}
		};

		this.data = data || {};
		this.options = $.extend({}, defaults, options);
		this.structureComponent = $(this.options.selector.structureComponent);
		this.contextComponent = $(this.options.selector.contextComponent);
		this.assignmentComponent = $(this.options.selector.assignmentComponent);
		this.dataComponent = $(this.options.selector.dataComponent);

		if (this.dataComponent.val().trim()) {
			try {
				this.assignments = JSON.parse(this.dataComponent.val().trim());
				this.updateData();
			} catch (error) {
			}
		}

		this.structureComponent.change($.proxy(this.structureChange, this));
		this.contextComponent.change($.proxy(this.contextChange, this));

		this.initialize();
	};

	OliverHader_Mapping_Assignment.prototype.get = function() {
		this.assignments = this.sanitize();
		return this.assignments;
	};

	OliverHader_Mapping_Assignment.prototype.sanitize = function() {
		var self = this;
		var assignments = {
			structure: null,
			context: null,
			assignments: {}
		};

		if (typeof this.assignments !== 'object' || !this.assignments.structure) {
			return assignments;
		}

		assignments.structure = this.assignments.structure;
		if (!this.assignments.context) {
			return assignments;
		}

		assignments.context = this.assignments.context;
		if (!this.assignments.assignments) {
			return assignments;
		}

		assignments.assignments = this.assignments.assignments;

		$.each(assignments.assignments, function(elementName, nodeIdentifier) {
			if (!self.data.nodes || !self.data.nodes[nodeIdentifier] || !self.data.structures || !self.data.structures[assignments.structure] || !self.data.structures[assignments.structure]['elements'].indexOf(elementName) === -1) {
				delete(assignments.assignments[elementName]);
			}
		});

		return assignments;
	};

	OliverHader_Mapping_Assignment.prototype.initialize = function() {
		this.initializeStructures();
		this.initializeContexts();
		this.initializeAssignment();
	};

	OliverHader_Mapping_Assignment.prototype.initializeStructures = function() {
		var self = this;

		this.structureComponent.empty();
		this.structureComponent.append($('<option></option>').val('').html('none'));
		$.each(this.data.structures, function(index, structure) {
			self.structureComponent.append($('<option></option>').val(structure.identifier).html(structure.title));
		});

		if (this.assignments.structure) {
			this.structureComponent.val(this.assignments.structure);
		}
	};

	OliverHader_Mapping_Assignment.prototype.initializeContexts = function() {
		var self = this;

		this.contextComponent.empty();
		this.contextComponent.append($('<option></option>').val('').html('none'));
		if (this.assignments.structure && this.data.structures[this.assignments.structure]) {
			$.each(this.data.structures[this.assignments.structure]['contexts'], function(index, context) {
				self.contextComponent.append($('<option></option>').val(context.name).html(context.name));
			});
		}

		if (this.assignments.context) {
			this.contextComponent.val(this.assignments.context);
		}
	};

	OliverHader_Mapping_Assignment.prototype.initializeAssignment = function() {
		var self = this;

		this.assignmentComponent.empty();

		if (self.assignments.structure && self.assignments.context && self.data.structures[self.assignments.structure] && self.data.structures[self.assignments.structure]['contexts'][self.assignments.context]) {
			$.each(self.data.structures[self.assignments.structure]['contexts'][self.assignments.context]['elements'], function(index, elementName) {
				var wrapper = $('<div class="mapping-assignment-assignment"></div>');
				var selector = $('<select></select>').data('assignedElementName', elementName);
				selector.change($.proxy(self.assignmentChange, self));
				selector.append($('<option></option>').val('').html('none'));

				wrapper.append($('<div class="mapping-assignment-element"></div>').html(elementName));
				wrapper.append($('<div class="mapping-assignment-node"></div>').append(selector));

				$.each(self.data.nodes, function(index, node) {
					selector.append($('<option></option>').val(node.identifier).html(node.name))
				});

				if (self.assignments.assignments[elementName]) {
					selector.val(self.assignments.assignments[elementName]);
				}

				self.assignmentComponent.append(wrapper);
			});
		}
	};

	OliverHader_Mapping_Assignment.prototype.updateData = function(event) {
		this.dataComponent.val(
			JSON.stringify(this.get())
		);
	};

	OliverHader_Mapping_Assignment.prototype.structureChange = function(event) {
		this.assignments.structure = this.structureComponent.val();

		this.initializeContexts();
		this.initializeAssignment();
		this.updateData();
	};

	OliverHader_Mapping_Assignment.prototype.contextChange = function(event) {
		this.assignments.context = this.contextComponent.val();

		this.initializeAssignment();
		this.updateData();
	};

	OliverHader_Mapping_Assignment.prototype.assignmentChange = function(event) {
		var $target = $(event.target);
		var elementName, nodeIdentifier;

		if (typeof this.assignments.assignments !== 'object') {
			this.assignments.assignments = {};
		}

		if ($target && $target.data('assignedElementName')) {
			elementName = $target.data('assignedElementName');
			nodeIdentifier = $target.val();

			if (nodeIdentifier !== '') {
				this.assignments.assignments[elementName] = nodeIdentifier;
			} else {
				delete(this.assignments.assignments[elementName]);
			}
		}

		this.updateData();
	};

}(jQuery));
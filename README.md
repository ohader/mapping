TYPO3 Template Mapping
======================

Abstract
--------

This TYPO3 CMS extension aims to use the benefits of a template mapping engine (e.g. like
TemplaVoila does) in regular available template engines like TEMPLATE (marker-based) or
FLUIDTEMPLATE (based on Fluid).

An additional backend module allows to define structures using an accordant DOM mapping
interface to determine XPath and scope (inner or outer).

This process eases modifications to the original template without having to know the
concrete implementation in TYPO3 or TypoScript.

TypoScript example
------------------

Example of new **MAPPING** content object in regular TEMPLATE object:

	page = PAGE
	page {
		10 = TEMPLATE
		10 {
			template = MAPPING
			template {
				structure = 1
				renderAs = marker
			}
			marks {
				variable_first = TEXT
				variable_first {
					value = First Section
				}
				variable_second = TEXT
				variable_second {
					value = Second Section
				}
			}
		}
	}

TypoScript reference
--------------------

This is the TypoScript reference for the **MAPPING** content object.

<table>
	<tr>
		<th>Property</th>
		<th>Type</th>
		<th>Comment</th>
	</tr>
	<tr>
		<td>structure</td>
		<td>string / stdWrap</td>
		<td>Defines entity by uid of table tx_mapping_domain_model_structure</td>
	</tr>
	<tr>
		<td>renderAs</td>
		<td>string / stdWrap</td>
		<td>Defines how variables are substituted. Can be <em>fluid</em> or <em>marker</em></td>
	</tr>
</table>

How does it look like?
----------------------

This is the backend module that is used to determine accordant DOM elements and create a
mapping structure. The user interface is still target to be changed further.

![Mapping module in the backend](/Documentation/screenshot.png "Mapping module in the backend")

Vocabulary
----------

* contexts: define rendering context of a <em>mapping structure</em>, e.g. <em>all</em>,
  <em>body</em> or any other valid and custom XPath
* heads: define HTML head sections, like scripts, stylesheets or a HTML template
* elements: define areas that can be overloaded by any content
* scopes: define scope on <em>elements</em> and <em>contexts</em> like <em>inner</em>,
  <em>outer</em> or <em>disable</em>
* assignments: are used to connect <em>mapping structures</em> with a particular usage
  during rendering process

Assignments
-----------

### Assumptions

* mapping structures are just used to define elements
* elements are overloaded with particular content in a separate content rendering process
* assignments of different rendering mechanisms have to be base in the domain of that particular
  mechanism (e.g. backend layouts, grid elements, dynamic content elements, fluid content, ...)

### Requirements

* domain of rendering process is known
* assignment of structure and its context is known
* assignment of structure elements to accordant disposed nodes is known (e.g. backend layout
  columns, dynamic content element field, ...)

### Drawbacks

Since different mechanisms can be considered as <em>definition</em> how content is represented,
gluing together <em>mapping structure</em> and the <em>definition</em> seems to be limiting.
On the other hand, defining a <em>mapping structure</em> on each actual usage on a page, any
content element or record seems to be an overhead in terms of usability.

### Result

Each mechanism has to define an <em>assignment</em> property to achieve the assignment on a
particular <em>mapping structure</em> and its <em>context</em>.

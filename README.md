TYPO3 Template Mapping
======================

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
					value = First Section
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

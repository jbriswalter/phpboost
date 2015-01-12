<script>
<!--
function FormFieldMultipleAutocompleter(){
	this.integer = {NBR_FIELDS};
	this.id_input = ${escapejs(HTML_ID)};
	this.max_input = {MAX_INPUT};
};

FormFieldMultipleAutocompleter.prototype.add_field = function () {
	if (this.integer <= this.max_input) {
		var id = this.id_input + '_' + this.integer;
		var input = new Element('div', {'id' : id, 'class' : 'form-autocompleter-container'});
		$('input_fields_' + this.id_input).insert(input);

		var div = new Element('input', {'type' : 'text', 'id' : 'field_' + id, 'name' : 'field_' + id, 'class' : 'text', 'size' : ${escapejs(SIZE)}, 'autocomplete' : 'off'});
		$(id).insert(div);

		var div = new Element('div', {'id' : 'field_' + id + '_completer', 'class' : 'form-autocompleter'});
		$(id).insert(div);

		this.load_autocompleter('field_' + id);
		
		var delete_input = new Element('a', {href : 'javascript:FormFieldMultipleAutocompleter.delete_field('+ this.integer +');', 'id' : 'delete_' + id, 'class' : 'fa fa-delete'});
		$(id).insert('&nbsp;');
		$(id).insert(delete_input);
	
		this.integer++;
	}
	if (this.integer == this.max_input) {
		jQuery('#add_' + this.id_input).hide();
	}
};

FormFieldMultipleAutocompleter.prototype.delete_field = function (id) {
	var id = this.id_input + '_' + id;
	$(id).remove();
	this.integer--;
	jQuery('#add_' + this.id_input).show();
};

FormFieldMultipleAutocompleter.prototype.load_autocompleter = function (id) {
	new Ajax.Autocompleter(id, id + '_completer', 
			${escapejs(FILE)}, {method: ${escapejs(METHOD)}, parameters: "token={TOKEN}", paramName: ${escapejs(NAME_PARAMETER)}});
};

var FormFieldMultipleAutocompleter = new FormFieldMultipleAutocompleter();
-->
</script>

<div id="input_fields_${escape(HTML_ID)}">
# START fieldelements #
	<div id="${escape(HTML_ID)}_{fieldelements.ID}" class="form-autocompleter-container">
		<input type="text" name="field_${escape(HTML_ID)}_{fieldelements.ID}" id="field_${escape(HTML_ID)}_{fieldelements.ID}" onfocus="javascript:FormFieldMultipleAutocompleter.load_autocompleter('field_${escape(HTML_ID)}_{fieldelements.ID}');" value="{fieldelements.VALUE}" size="{SIZE}" autocomplete="off"/>
		<div id="field_${escape(HTML_ID)}_{fieldelements.ID}_completer" class="form-autocompleter"></div>
		<a href="javascript:FormFieldMultipleAutocompleter.delete_field({fieldelements.ID});" id="delete_${escape(HTML_ID)}_{fieldelements.ID}" class="fa fa-delete" data-confirmation="delete-element"></a>
	</div>
# END fieldelements #
</div>
<a href="javascript:FormFieldMultipleAutocompleter.add_field();" class="fa fa-plus" id="add_${escape(HTML_ID)}"></a>
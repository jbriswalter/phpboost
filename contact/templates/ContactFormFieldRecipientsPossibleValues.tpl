<script type="text/javascript">
<!--
var ContactFormFieldRecipientsPossibleValues = Class.create({
	integer : {NBR_FIELDS},
	id_input : ${escapejs(ID)},
	max_input : {MAX_INPUT},
	add_field : function () {
		if (this.integer <= this.max_input) {
			var id = this.id_input + '_' + this.integer;
			
			var input = new Element('input', {'type' : 'checkbox', 'id' : 'field_is_default_' + id, 'name' : 'field_is_default_' + id, 'value' : '1', 'class' : 'per_default' });
			$('input_fields_' + this.id_input).insert(input);
			$('input_fields_' + this.id_input).insert(' ');
			
			var input = new Element('input', {'type' : 'text', 'id' : 'field_name_' + id, 'name' : 'field_name_' + id, 'class' : 'text', 'size' : '20' });
			$('input_fields_' + this.id_input).insert(input);
			$('input_fields_' + this.id_input).insert(' ');
			
			var input = new Element('input', {'type' : 'text', 'id' : 'field_email_' + id, 'name' : 'field_email_' + id, 'class' : 'text', 'size' : '30' });
			$('input_fields_' + this.id_input).insert(input);
			$('input_fields_' + this.id_input).insert(' ');
			
			var delete_input = new Element('a', {href : 'javascript:ContactFormFieldRecipientsPossibleValues.delete_field('+ this.integer +');', 'id' : 'delete_' + id, 'title' : "${LangLoader::get_message('delete', 'main')}", 'class' : 'icon-delete'});
			$('input_fields_' + this.id_input).insert(delete_input);
			
			var div = new Element('div', {'id' : 'spacer_' + id, 'class' : 'height_spacer'});
			$('input_fields_' + this.id_input).insert(div);
			
			this.integer++;
		}
		if (this.integer == this.max_input) {
			$('add_' + this.id_input).hide();
		}
	},
	delete_field : function (id) {
		var id = this.id_input + '_' + id;
		$('field_is_default_' + id).remove();
		$('field_name_' + id).remove();
		$('field_email_' + id).remove();
		$('delete_' + id).remove();
		$('spacer_' + id).remove();
		this.integer--;
		$('add_' + this.id_input).show();
	},
});

var ContactFormFieldRecipientsPossibleValues = new ContactFormFieldRecipientsPossibleValues();
-->
</script>

<div id="input_fields_${escape(ID)}">
<div class="text_strong"><span class="is_default_title">{@admin.field.possible_values.is_default}</span><span class="name_title">{@admin.field.name}</span><span>{@admin.field.possible_values.email}</span></div>
<div class="height_spacer"></div>
# START fieldelements #
	<input type="checkbox" name="field_is_default_${escape(ID)}_{fieldelements.ID}" id="field_is_default_${escape(ID)}_{fieldelements.ID}" value="1"# IF fieldelements.IS_DEFAULT # checked="checked"# ENDIF # class="per_default" />
	<input type="text" name="field_name_${escape(ID)}_{fieldelements.ID}" id="field_name_${escape(ID)}_{fieldelements.ID}" value="{fieldelements.NAME}" size="20" class="text" />
	<input type="text" name="field_email_${escape(ID)}_{fieldelements.ID}" id="field_email_${escape(ID)}_{fieldelements.ID}" value="{fieldelements.EMAIL}" size="30" class="text" />
	# IF fieldelements.C_DELETABLE #<a href="javascript:ContactFormFieldRecipientsPossibleValues.delete_field({fieldelements.ID});" id="delete_${escape(ID)}_{fieldelements.ID}" title="${LangLoader::get_message('delete', 'main')}" class="icon-delete"></a># ENDIF #
	<div id="spacer_${escape(ID)}_{fieldelements.ID}" class="height_spacer"></div>
# END fieldelements #
</div>
<div class="height_spacer"></div>
<a href="javascript:ContactFormFieldRecipientsPossibleValues.add_field();" class="icon-plus" id="add_${escape(ID)}"></a>

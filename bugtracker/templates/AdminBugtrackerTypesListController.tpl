<script>
<!--
var BugtrackerFormFieldTypes = Class.create({
	integer : {NEXT_ID},
	max_input : {MAX_INPUT},
	add_type : function () {
		if (this.integer <= this.max_input) {
			var id = this.integer;
			
			var tr = Builder.node('tr', {'id' : 'tr_' + id}, []);
			
			var td = Builder.node('td', {'id' : 'td1_' + id}, [
				Builder.node('input', {type : 'radio', name : 'default_type', value : id}),
				' ',
			]);
			tr.insert(td);
			
			var td = Builder.node('td', {'id' : 'td2_' + id}, [
				Builder.node('input', {type : 'text', id : 'type_' + id, name : 'type_' + id, size : 40, maxlength : 100, placeholder : ${escapejs(LangLoader::get_message('name', 'main'))}}),
				' ',
			]);
			tr.insert(td);
			
			var td = Builder.node('td', {'id' : 'td3_' + id}, [
				Builder.node('a', {id : 'delete_' + id, href : 'javascript:BugtrackerFormFieldTypes.delete_type(' + id + ');', title : ${escapejs(LangLoader::get_message('delete', 'common'))}, className: 'fa fa-delete'}),
				' ',
			]);
			tr.insert(td);
			
			$('types_list').insert(tr);
			
			this.integer++;
		}
		if (this.integer == this.max_input) {
			jQuery('#add_type').hide();
		}
		if (this.integer) {
			jQuery('#no_type').hide();
		}
	},
	delete_type : function (id) {
		$('tr_' + id).remove();
		this.integer--;
		if (this.integer == 1)
			$('no_type').style.display = "";
		
		jQuery('#add_type').show();
	},
});

var BugtrackerFormFieldTypes = new BugtrackerFormFieldTypes();
-->
</script>

<table>
	<thead>
		<tr>
			<th class="small-column">
				{@labels.default}
			</th>
			<th>
				${LangLoader::get_message('name', 'main')}
			</th>
			<th class="small-column">
				${LangLoader::get_message('delete', 'common')}
			</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th>
				<a href="javascript:BugtrackerFormFieldTypes.add_type();" class="fa fa-plus" title="{@titles.add_type}" id="add_type"></a>
			</th>
			<th colspan="2" class="right">
				# IF C_DISPLAY_DEFAULT_DELETE_BUTTON #<a href="{LINK_DELETE_DEFAULT}" title="${LangLoader::get_message('delete', 'common')}" data-confirmation="{@actions.confirm.del_default_value}"><i class="fa fa-delete"></i> {@labels.del_default_value}</a># ENDIF #
			</th>
		</tr>
	</tfoot>
	<tbody id="types_list">
		<tr id="no_type"# IF C_TYPES # style="display:none;"# ENDIF #>
			<td colspan="3">
				${LangLoader::get_message('no_item_now', 'common')}
			</td>
		</tr>
		# START types #
		<tr>
			<td>
				<input type="radio" name="default_type" value="{types.ID}"# IF types.C_IS_DEFAULT # checked="checked"# ENDIF # />
			</td>
			<td>
				<input type="text" maxlength="100" size="40" name="type{types.ID}" value="{types.NAME}" />
			</td>
			<td>
				<a href="{types.LINK_DELETE}" title="${LangLoader::get_message('delete', 'common')}" class="fa fa-delete" data-confirmation="delete-element"></a>
			</td>
		</tr>
		# END types #
	</tbody>
</table>

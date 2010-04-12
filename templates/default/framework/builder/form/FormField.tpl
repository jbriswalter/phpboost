		<dl id="{E_ID}_field" # IF C_HIDDEN # style="display:none;" # ENDIF #>
			<dt>
				<label for="{E_ID}">
					# IF C_REQUIRED # * # ENDIF #
					{LABEL}
				</label>
				<br />
				<span class="text_small">{DESCRIPTION}</span>
			</dt>
			<dd>
			# START fieldelements #
				{fieldelements.ELEMENT}
			# END fieldelements #
			# IF C_HAS_CONSTRAINTS #
				&nbsp;
				<span style="display:none" id="onblurContainerResponse{E_ID}"></span>
				<div style="font-weight:bold;display:none" id="onblurMesssageResponse{E_ID}"></div>
			# ENDIF #
			</dd>
		</dl>
		<script type="text/javascript">
		<!--
		var field = new FormField("{E_ID}");
		HTMLForms.get("{E_FORM_ID}").addField(field);
		
		field.doValidate = function() {
			var result = "";
			# START constraint #
				result = {constraint.CONSTRAINT};
				if (result != "") {
					return result;
				}
			# END constraint #
			return result;
		}

		# IF C_DISABLED #
		field.disable();
		# ENDIF #
		
		{JS_SPECIALIZATION_CODE}
		
		Event.observe("{E_ID}", 'blur', function() {
			HTMLForms.get("{E_FORM_ID}").getField("{E_ID}").enableValidationMessage();
			HTMLForms.get("{E_FORM_ID}").getField("{E_ID}").liveValidate();
			# START related_field #
			HTMLForms.get("{E_FORM_ID}").getField("{related_field.E_ID}").liveValidate();
			# END related_field #
		});

		# START event_handler #
		Event.observe("{E_ID}", '{event_handler.E_EVENT}', function() {
			{event_handler.HANDLER}
		});
		# END event_hander #
		-->
		</script>
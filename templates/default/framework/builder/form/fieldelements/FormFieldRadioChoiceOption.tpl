<div class="form-field-radio">
	<input id="${escape(ID)}" type="radio" name="${escape(NAME)}" value="${escape(VALUE)}" # IF C_CHECKED # checked="checked" # ENDIF # # IF C_DISABLE # disabled="disabled" # ENDIF #>
	<label for="${escape(ID)}"><span class="sr-only">{LABEL}</span></label>
</div>
<span class="form-field-radio-span"> {LABEL}</span>
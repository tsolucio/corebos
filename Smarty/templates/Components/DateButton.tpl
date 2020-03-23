{assign var='BUTTONICON' value=[
	'title'=>'LBL_SELECT'|@getTranslatedString,
	'id'=>'jscal_trigger_'|cat:$fldname,
	'size'=>'large',
	'library' => 'utility',
	'icon' => 'date_input'
]}
{extends file='Components/ButtonIcon.tpl'}

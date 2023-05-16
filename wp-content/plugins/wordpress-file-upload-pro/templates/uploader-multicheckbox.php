<?php

/**
 * Defines a custom upload template that implements multiselect checkboxes
 */
class WFU_UploaderTemplate_multicheckbox extends WFU_Original_Template {

function wfu_userdata_template($data) {

	$data0 = $data;
	$data0["params"]["uploadertemplate"] = "";
	$output = wfu_read_template_output("userdata", $data0);
	
?><style>
<?php echo $output["css"]; ?>
</style><script type="text/javascript">
<?php echo $output["js"]; ?> 
GlobalData.WFU[$ID].userdata._init = GlobalData.WFU[$ID].userdata.init;
GlobalData.WFU[$ID].userdata.init = function() {
this._init();
this._getValue = this.getValue;
this.getValue = function(props) {
	var field = document.getElementById("userdata_$ID_field_" + props.key);
	if (props.type == "radiobutton" && props.format.indexOf("multicheckbox") > -1) {
		var value = "";
		var items = document.getElementsByName(field.name);
		for (var i = 0; i < items.length; i++)
			if (items[i].checked) value += (value == "" ? "" : ",") + items[i].value;
		return value;
	}
	else return this._getValue(props);
}

this._setValue = this.setValue;
this.setValue = function(props, value) {
	var field = document.getElementById("userdata_$ID_field_" + props.key);
	if (props.type == "radiobutton" && props.format.indexOf("multicheckbox") > -1) {
		var value_arr = value.split(",");
		var items = document.getElementsByName(field.name);
		for (var i = 0; i < items.length; i++)
			items[i].checked = (value_arr.indexOf(items[i].value) > -1);
	}
	else this._setValue(props, value);
}
}
</script><?php

	$templates_html = "";
	foreach ( $output as $key => $item ) if ( substr($key, 0, 4) == "line" ) $templates_html .= ( $templates_html == "" ? "" : "\r\n" ).$item;
	$new_templates_html = "";
	$error_found = false;
	foreach ( $data["props"] as $p ) {
		$matches = array();
		preg_match("/<userdata_".$p["key"]."_template>(.*?)<\/userdata_".$p["key"]."_template>/s", $templates_html, $matches);
		if ( isset($matches[1]) ) {
			$template = "<userdata_".$p["key"]."_template>".$matches[1]."</userdata_".$p["key"]."_template>";
			if ( $p["type"] == "radiobutton" && strpos($p["format"], "multicheckbox") !== false )
				$template = str_replace('input type="radio"', 'input type="checkbox"', $template);
			$new_templates_html .= ( $new_templates_html == "" ? "" : "\r\n" ).$template;
		}
		else {
			$error_found = true;
			break;
		}
	}
	if ( $error_found ) echo $new_templates_html;
	else echo $new_templates_html;
}

}
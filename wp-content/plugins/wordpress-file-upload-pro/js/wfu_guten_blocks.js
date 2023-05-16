var el=wp.element.createElement,Fragment=wp.element.Fragment,registerBlockType=wp.blocks.registerBlockType,PlainText=wp.editor.PlainText;wfu_registerShortcodeBlock("wfu-guten-blocks/wfu-uploader","WFU Uploader Shortcode","upload","wordpress_file_upload",'[wordpress_file_upload uploadid="'+(Math.floor(Math.random()*9E3)+1E3)+'"]',"WFU Uploader Shortcode","wfu-uploader-shortcode");
wfu_registerShortcodeBlock("wfu-guten-blocks/wfu-browser","WFU File Viewer Shortcode","images-alt2","wordpress_file_upload_browser",'[wordpress_file_upload_browser browserid="'+(Math.floor(Math.random()*9E3)+1E3)+'"]',"WFU File Viewer Shortcode","wfu-browser-shortcode");
function wfu_registerShortcodeBlock(type,title,icon,tag,default_content,block_label,classname){registerBlockType(type,{title:title,icon:icon,category:"widgets",attributes:{content:{type:"string",source:"html","default":default_content}},transforms:{from:[{type:"shortcode",tag:tag,attributes:{content:{type:"string",shortcode:function(attributes){var cont="";for(var attr in attributes.named)if(attributes.named.hasOwnProperty(attr))cont+=" "+attr+'="'+attributes.named[attr]+'"';return"[wordpress_file_upload"+
cont+"]"}}}},{type:"block",blocks:["core/html","core/paragraph","core/heading","core/preformatted","core/verse"],isMatch:function(attributes){return attributes.content&&attributes.content.substr(0,tag.length+1)=="["+tag&&attributes.content.substr(-1)=="]"},transform:function(attributes){return wp.blocks.createBlock(type,{content:attributes.content})}},{type:"block",blocks:["core/shortcode"],isMatch:function(attributes){return attributes.text&&attributes.text.substr(0,tag.length+1)=="["+tag&&attributes.text.substr(-1)==
"]"},transform:function(attributes){return wp.blocks.createBlock(type,{content:attributes.text})}}],to:[{type:"block",blocks:["core/html"],transform:function(attributes){return wp.blocks.createBlock("core/html",{content:attributes.content})}},{type:"block",blocks:["core/paragraph"],transform:function(attributes){return wp.blocks.createBlock("core/paragraph",{content:attributes.content})}},{type:"block",blocks:["core/shortcode"],transform:function(attributes){return wp.blocks.createBlock("core/shortcode",
{text:attributes.content})}}]},edit:function(props){var content="";if(props.attributes.content)content=props.attributes.content;if(content.substr(0,tag.length+1)=="["+tag)content=content.substr(tag.length+1);if(content.substr(-1)=="]")content=content.substr(0,content.length-1);if(content.length>0&&content.substr(0,1)==" ")content=content.substr(1);function onChangeContent(newContent){if(newContent.length>0&&newContent.substr(0,1)!=" ")newContent=" "+newContent;newContent="["+tag+newContent+"]";props.setAttributes({content:newContent})}
WFUData.editors.push({id:props.clientId,window:null,updater:onChangeContent});return el(Fragment,null,el("div",{className:classname},el("label",null,block_label),AdminParams.wfu_can_edit=="true"?el("span",{className:"dashicons dashicons-edit "+classname+"-btn"+(props.isSelected?" active":""),title:"Edit shortcode",onClick:new Function("wfu_invoke_shortcode_guteditor('"+props.clientId+"', '"+tag+"');")}):null,el(PlainText,{key:"editable",className:classname+"-text",onChange:onChangeContent,value:content})))},
save:function(props){return props.attributes.content}})}WFUData={editors:[]};function wfu_GetHttpRequestObject(){var xhr=null;try{xhr=new XMLHttpRequest}catch(e$0){try{xhr=new ActiveXObject("Msxml2.XMLHTTP")}catch(e2){try{xhr=new ActiveXObject("Microsoft.XMLHTTP")}catch(e){}}}if(xhr==null&&window.createRequest)try{xmlhttp=window.createRequest()}catch(e$1){}return xhr}
function wfu_plugin_encode_string(str){var i=0;var newstr="";var num;var hex="";for(i=0;i<str.length;i++){num=str.charCodeAt(i);if(num>=2048)num=((num&16773120|917504)<<4)+((num&4032|8192)<<2)+(num&63|128);else if(num>=128)num=((num&65472|12288)<<2)+(num&63|128);hex=num.toString(16);if(hex.length==1||hex.length==3||hex.length==5)hex="0"+hex;newstr+=hex}return newstr}
function wfu_plugin_decode_string(str){var i=0;var newstr="";var num,val;while(i<str.length){num=parseInt(str.substr(i,2),16);if(num<128)val=num;else if(num<224)val=((num&31)<<6)+(parseInt(str.substr(i+=2,2),16)&63);else val=((num&15)<<12)+((parseInt(str.substr(i+=2,2),16)&63)<<6)+(parseInt(str.substr(i+=2,2),16)&63);newstr+=String.fromCharCode(val);i+=2}return newstr}
function wfu_get_block_editor_data(id){var data=null;for(var i=0;i<WFUData.editors.length;i++)if(WFUData.editors[i].id==id){data=WFUData.editors[i];break}return data}function wfu_set_block_editor(id,window){for(var i=0;i<WFUData.editors.length;i++)if(WFUData.editors[i].id==id){WFUData.editors[i].window=window;break}}
function wfu_save_from_editor(id){var data=wfu_get_block_editor_data(id);if(!data)return;var editor_window=data.window;updater=data.updater;if(editor_window!=null&&updater!=null){var shortcode=editor_window.ShortcodeString;editor_window.close();wfu_set_block_editor(id,null);updater(shortcode)}}
function wfu_invoke_shortcode_guteditor(id,tag){var xhr=wfu_GetHttpRequestObject();if(xhr==null)return;var block=wp.data.select("core/editor").getBlock(id);if(!block)return;var shortcode=block.attributes.content;if(shortcode.substr(0,tag.length+1)=="["+tag)shortcode=shortcode.substr(tag.length+1);if(shortcode.substr(-1)=="]")shortcode=shortcode.substr(0,shortcode.length-1);shortcode=shortcode.trim();var url=AdminParams.wfu_ajax_url;params=new Array(4);params[0]=new Array(2);params[0][0]="action";
params[0][1]="wfu_ajax_action_gutedit_shortcode";params[1]=new Array(2);params[1][0]="shortcode";params[1][1]=wfu_plugin_encode_string(shortcode);params[2]=new Array(2);params[2][0]="post_id";params[2][1]=wp.data.select("core/editor").getCurrentPostId();params[3]=new Array(2);params[3][0]="shortcode_tag";params[3][1]=tag;var parameters="";for(var i=0;i<params.length;i++)parameters+=(i>0?"&":"")+params[i][0]+"="+encodeURI(params[i][1]);xhr.open("POST",url,true);xhr.setRequestHeader("Content-type",
"application/x-www-form-urlencoded");xhr.onreadystatechange=function(){if(xhr.readyState==4)if(xhr.status==200){var start_text="wfu_gutedit_shortcode:";var pos=xhr.responseText.indexOf(start_text);if(pos==-1)pos=xhr.responseText.length;var messages=xhr.responseText.substr(0,pos);var response=xhr.responseText.substr(pos+start_text.length,xhr.responseText.length-pos-start_text.length);pos=response.indexOf(":");var txt_header=response.substr(0,pos);txt_value=response.substr(pos+1,response.length-pos-
1);if(txt_header=="success"){var data=wfu_get_block_editor_data(id);if(!data){alert("Error opening the shortcode editor. Please reload the page editor.");return}editor_window=data.window;if(editor_window!=null){if(!editor_window.wfu_changes_saved())if(!confirm("Another editor with unsaved changes might be open. If you press Ok the editor will re-open and any unsaved changes will be lost."))return;editor_window.wfu_force_save_changes();editor_window.close();wfu_set_block_editor(id,null)}editor_window=
window.open(wfu_plugin_decode_string(txt_value),"_blank");if(editor_window){wfu_set_block_editor(id,editor_window);editor_window.fromGutenberg=true;editor_window.blockId=id;editor_window.plugin_window=window}else alert("Please enable popup windows.")}else if(txt_header=="check_page_obsolete")alert(txt_value)}};xhr.send(parameters)};
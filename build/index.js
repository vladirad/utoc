!function(){"use strict";var e=window.wp.element,t=window.wp.blocks,o=window.wp.blockEditor;(0,t.registerBlockType)("btoc/preview-block",{edit:()=>{const[t,c]=(0,e.useState)(""),r=(0,o.useBlockProps)();return(0,e.useEffect)((()=>{get_utoc_html().then((e=>{c(e),jQuery("select[name='utoc_position']").val("disabled"),jQuery(".utoc-remove-on-ajax").remove()}))}),[]),(0,e.createElement)("div",r,(0,e.createElement)("div",{dangerouslySetInnerHTML:{__html:t},className:"utoc-block-html"}))},save:()=>(jQuery("select[name='utoc_position']").val("disabled"),jQuery(".utoc-remove-on-ajax").remove(),"[btoc]")})}();
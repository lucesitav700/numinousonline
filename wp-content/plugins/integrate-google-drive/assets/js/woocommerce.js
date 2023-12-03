(()=>{"use strict";var e,o,t,d,i,n,l,a,c,s;e=jQuery,o=window.ModuleBuilderModal,t=igd.settings,d=t.wooCommerceDownload,i=void 0===d||d,n=t.wooCommerceUpload,l=void 0!==n&&n,a=t.dokanUpload,c=void 0!==a&&a,s={init:function(){i&&(s.addSelectFilesButton(),e("#variable_product_options").on("woocommerce_variations_added",s.addSelectFilesButton),e("#woocommerce-product-data").on("woocommerce_variations_loaded",s.addSelectFilesButton),e(document).on("click",".igd-wc-button",s.selectFiles)),(l||c)&&(e("#igd-wc-select-parent-folder").on("click",s.selectParentFolder),e(document).on("click",".upload-folder-name-field .variable",s.handlePlaceholder),e("input#_uploadable").on("change",s.handleUploadable),e("input#_uploadable").trigger("change"),e("input#_igd_upload").on("change",s.handleGoogleDriveSettings),e("input#_igd_upload").trigger("change"))},handleGoogleDriveSettings:function(){var o=e("input#_igd_upload:checked").length;e(".show_if_igd_upload").hide(),e(".hide_if_igd_upload").hide(),o&&(e(".hide_if_igd_upload").hide(),e(".show_if_igd_upload").show())},handleUploadable:function(){var o=e(this).is(":checked");e(".show_if_uploadable").hide(),e(".hide_if_uploadable").hide(),o&&(e(".hide_if_uploadable").hide(),e(".show_if_uploadable").show())},handlePlaceholder:function(){var o=e(this).text(),t=e(this).closest(".upload-folder-name-field").find("input");t.val(t.val()+" "+o)},addSelectFilesButton:function(){e(".downloadable_files").each((function(){e(this).find("tfoot th:last-child").append('<div class="igd-woocommerce"><button type="button" class="button button-secondary igd-wc-button"><img src="'.concat(igd.pluginUrl,'/assets/images/drive.png" width="20" /><span>').concat(wp.i18n.__("Add File","integrate-google-drive"),"</span></button></div>"))}))},selectFiles:function(){var t=e(this).parents(".downloadable_files");Swal.fire({html:'<div id="igd-select-files" class="igd-module-builder-modal-wrap"></div>',showConfirmButton:!1,customClass:{container:"igd-module-builder-modal-container"},didOpen:function(d){ReactDOM.render(React.createElement(o,{initData:{},onUpdate:function(o){var d=o.folders,i=void 0===d?[]:d,n=t.find("tbody"),l=t.find(".button.insert").data("row");i.map((function(o){var t=o.id,d=o.name,i=(o.type,o.accountId),a="https://drive.google.com/open?action=igd-wc-download&id=".concat(t,"&account_id=").concat(i,"&name=").concat(d,"}"),c=e(l);c.find(".file_name .input_text").val(d),c.find(".file_url .input_text").val(a),n.append(c)})),Swal.close()},onClose:function(){return Swal.close()},isSelectFiles:!0}),document.getElementById("igd-select-files"))},willClose:function(e){ReactDOM.unmountComponentAtNode(document.getElementById("igd-select-files"))}})},selectParentFolder:function(){Swal.fire({html:'<div id="igd-select-files" class="igd-module-builder-modal-wrap"></div>',showConfirmButton:!1,customClass:{container:"igd-module-builder-modal-container"},didOpen:function(t){var d=document.getElementById("igd-select-files"),i=e("#igd_upload_parent_folder").val();ReactDOM.render(React.createElement(o,{initData:{folders:i?[JSON.parse(i)]:[]},onUpdate:function(o){var t=o.folders,d=(void 0===t?[]:t)[0];e("#igd_upload_parent_folder").val(JSON.stringify(d)),e(".parent-folder-account").text(igd.accounts[d.accountId]?igd.accounts[d.accountId].email:""),e(".parent-folder-name").text(d.name),Swal.close()},onClose:function(){return Swal.close()},isSelectFiles:!0,selectionType:"parent"}),d)},willClose:function(e){var o=document.getElementById("igd-select-files");ReactDOM.unmountComponentAtNode(o)}})}},e(document).ready(s.init)})();
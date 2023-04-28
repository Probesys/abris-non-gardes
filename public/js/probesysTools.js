
function loadAjaxModalContent(id_modal, content_url)
{
   $('#' + id_modal + ' .modal-content').load(content_url, function() {
      $('#' + id_modal).modal({show: true});
   });
}

function htmlEntities(str) {
   return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}


$(document).ready(function() {

   initSelect2();
   initializeDatepicker();
   //initializeTinyMce();
   initializeDateRangePicker();
   defineTextareaAttributes();


   $('.modal').on('shown.bs.modal', function(e) {
      initializeModalFunctions(e.target.id);

   });


   $('.modal').on('hide.bs.modal', function() {

   });

   $('body').on('click', function(e) {
      $('[data-toggle="popover"]').each(function() {
         //the 'is' for buttons that trigger popups
         //the 'has' for icons within a button that triggers a popup
         if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.popover').has(e.target).length === 0) {
            $(this).popover('hide');
         }
      });
   });


   ShowHideFormElementsFromCheckboxOrSelect();
   $(document).on("click", '.hiddenPilotField', function()
   {
      ShowHideFormElementsFromCheckboxOrSelect();
   });
   $(document).on("change", '.hiddenPilotField', function()
   {
      ShowHideFormElementsFromCheckboxOrSelect();
   });


   //Gestion de la photo de l'animal
   if ($("div#dropzone-animal-picture").length > 0)
   {
      var url_dropzone_animal_picture = $("div#dropzone-animal-picture").attr("data-dropzone-action");
      var id_animal = $("div#dropzone-animal-picture").attr("data-id-animal");
      var scheme = $("div#dropzone-animal-picture").attr("data-scheme");
      var myDropzone = new Dropzone("div#dropzone-animal-picture", {url: url_dropzone_animal_picture, createImageThumbnails: false, acceptedFiles: 'image/*', autoQueue: false});
      if ($("div#dropzone-animal-picture img").length > 0)
      {
         myDropzone.disable();
      }
      myDropzone.on('sending', function(file, xhr, formData) {
         formData.append('entity', 'animalPhoto');
         formData.append('id_entity', id_animal);
      });
      myDropzone.on('success', function(file, data) {
         var domResult = '<span id="delete-animal-picture" data-id-picture="' + data.id + '"class="fa fa-times-circle fa-3x text-success pull-right"></span>';
         var url = data.url.replace('http', scheme);
         domResult += '<img src="' + url + '" style="height:100%;max-width:100%">';
         $("div#dropzone-animal-picture").html(domResult);
         myDropzone.disable();
      });

      myDropzone.on("addedfile", function(origFile) {
         var MAX_WIDTH = 400;
         var MAX_HEIGHT = 300;
         var reader = new FileReader();
         // Convert file to img
         reader.addEventListener("load", function(event) {
            var origImg = new Image();
            origImg.src = event.target.result;
            origImg.addEventListener("load", function(event) {
               var width = event.target.width;
               var height = event.target.height;
               // Don't resize if it's small enough
               if (width <= MAX_WIDTH && height <= MAX_HEIGHT) {
                  myDropzone.enqueueFile(origFile);
                  return;
               }
               // Calc new dims otherwise
               if (width > height) {
                  if (width > MAX_WIDTH) {
                     height *= MAX_WIDTH / width;
                     width = MAX_WIDTH;
                  }
               } else {
                  if (height > MAX_HEIGHT) {
                     width *= MAX_HEIGHT / height;
                     height = MAX_HEIGHT;
                  }
               }
               // Resize
               var canvas = document.createElement('canvas');
               canvas.width = width;
               canvas.height = height;
               var ctx = canvas.getContext("2d");
               ctx.drawImage(origImg, 0, 0, width, height);
               var resizedFile = base64ToFile(canvas.toDataURL(), origFile);
               // Replace original with resized
               var origFileIndex = myDropzone.files.indexOf(origFile);
               myDropzone.files[origFileIndex] = resizedFile;
               // Enqueue added file manually making it available for
               // further processing by dropzone
               myDropzone.enqueueFile(resizedFile);
            });
         });

         reader.readAsDataURL(origFile);
      });

      function base64ToFile(dataURI, origFile) {
         var byteString, mimestring;
         if (dataURI.split(',')[0].indexOf('base64') !== -1) {
            byteString = atob(dataURI.split(',')[1]);
         } else {
            byteString = decodeURI(dataURI.split(',')[1]);
         }
         mimestring = dataURI.split(',')[0].split(':')[1].split(';')[0];
         var content = new Array();
         for (var i = 0; i < byteString.length; i++) {
            content[i] = byteString.charCodeAt(i);
         }
         var newFile = new File(
                 [new Uint8Array(content)], origFile.name, {type: mimestring}
         );
         // Copy props set by the dropzone in the original file
         var origProps = [
            "upload", "status", "previewElement", "previewTemplate", "accepted"
         ];
         $.each(origProps, function(i, p) {
            newFile[p] = origFile[p];
         });

         return newFile;
      }

      $(document).on('click', '#delete-animal-picture', function()
      {
         if (confirm(Translator.trans('Generics.messages.confirmationDeleteMessageQuestion')))
         {
            var urlDeletePicture = Routing.generate('unlink_animal_photo', {id: id_animal});
            $.ajax({
               type: "GET",
               url: urlDeletePicture,
               success: function(data) {
                  obj_response = $.parseJSON(data);

                  if (obj_response.result)
                  {
                     $("div#dropzone-animal-picture").html("");
                     myDropzone.enable();
                  }

               },
               error: function() {
                  //message d'alerte
               }
            });
         }
      });

   }


   //Gestion des l'ordre des items dans listing value
   $(document).on('change', 'input[data-id-listing-value]', function(e)
   {
      var urlSetOrderListingValue = Routing.generate('set_order_listing_value');
      var idListingValueToUpdate = $(this).attr("data-id-listing-value");
      var orderValue = $(this).val();

      $.ajax({
         type: "POST",
         url: urlSetOrderListingValue,
         data: {'id_value': idListingValueToUpdate, 'orderValue': orderValue},
         success: function(data) {
            obj_response = $.parseJSON(data);

            if (obj_response.result)
            {
               $("div#dropzone-animal-picture").html("");
               myDropzone.enable();
            }

         },
         error: function() {
            //message d'alerte
         }
      });
   });

   // ajout confirmation
   $(document).on('click', '.confirmBeforeLoadDocument', function(e) {
      if (confirm(Translator.trans('Generics.messages.confirmBeforeLoadDocumentMessageAlert'))) {
         return true;
      }
      return false;
   });

   $(document).on('click', '.ajax_content', function(e)
   {
      e.preventDefault();
      content_url = $(this).attr('data-url-content');
      modalId = $(this).data('modal-id');
      if (!modalId || !$('#' + modalId).length > 0)
      {
         modalId = 'defaultAjaxModal';
      }

      loadAjaxModalContent(modalId, content_url);
      initializeModalFunctions(modalId);
   });




   function defineTextareaAttributes() {
      $("textarea").each(function() {
         $(this).attr('rows', '6');
      });
   }

   $(function() {
      $('body').on('submit', 'form.ajax-form', function(e) {
         e.preventDefault();

         // return;
         var form = $(this);
         var url = $(this).attr('action');
         var type = $(this).attr('method').toUpperCase();
         var target = $(e.target);
         var successAction = $(this).data('success_action');
         var modal = false;
         if (target.closest('.modal').length) {
            var modal = target.closest('.modal');
         }
         tinyMCE.triggerSave();
         enableDisabledFormInput($(this));

         var formData = new FormData($(this)[0]);
         $.ajax({
            url: url,
            type: type,
            modal: modal,
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function(e)
            {
               if (!modal) {
                  $('#tabContentConsultation').hide();
                  $('#loading').show();
               }
            },
            success: function(data) {
               data = $.parseJSON(data);
               if (typeof data.status !== "undefined") {
                  if (data.status === "success") {
                     if (activeTabHash)
                     {
                        window.location.hash = activeTabHash;
                     }
                     if (typeof data.message !== "undefined") {
                        var message = data.message;
                     } else {
                        var message = Translator.trans('Generics.flash.editSuccess');
                     }

                     if (modal) {
                        modal.modal('toggle');
                        flashMessage(data.status, message);
                        // traitement du retour
                        switch (successAction) {
                           case 'reload':
                              window.location.reload();
                              break;
                           case 'updateDiv':
                              break;
                           default:
                              return;
                        }

                     } else {
                        flashMessage(data.status, message, true);
                     }
                     eval(callBackAfterSuccesAjaxFormSubmission);
                     // traitement du retour
                     switch (successAction) {
                        case 'reload':
                           window.location.reload();
                           break;
                        case 'updateDiv':
                           break;
                     }

                  } else if (data.status === "warning") {
                     window.location.hash = activeTabHash;
                     populateAndOpenDefaultModal(data.status, '<div class="alert alert-warning" role="alert">' + JSON.stringify(data.message) + '</div>');
                     eval(callBackAfterSuccesAjaxFormSubmission);
                  } else {
                     // affichage message erreur dans la modal default
                     populateAndOpenDefaultModal(data.status, '<div class="alert alert-danger" role="alert">' + JSON.stringify(data.message) + '</div>');

                     form.find('.modal-body').first().prepend('<div class="alert alert-' + data.status + ' alert-dismissible fade in" role="alert">'
                             + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>'
                             + data.message + '</div>');
                     hideFlashMessage();
                     eval(callBackAfterSuccesAjaxFormSubmission);
                  }
               }
            },
            error: function() {
               flashMessage('danger', Translator.trans('Generics.flash.genericFailure'), false);
            }

         });
      });
   });

   var strDomModalConfirm = '<div id="dataConfirmModal" class="modal" role="dialog" aria-labelledby="dataConfirmLabel" aria-hidden="true">'
           + '<div class="modal-dialog"><div class="modal-content"><div class="modal-header">'
           + '<h5 id="dataConfirmLabel">Confirmation</h5>'
           + '<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>'
           + '</div>'
           + '<div class="modal-body"></div><div class="modal-footer">'
           + '<button class="btn btn-secondary" data-dismiss="modal" aria-hidden="true">Non</button><a data-dismiss="modal" class="btn btn-danger text-white" id="dataConfirmOK">Oui</a>'
           + '</div></div></div></div>';

   $(document).on('click', 'a:not(.ajax-request)[data-confirm]', function(ev) {
      var href = $(this).attr('href');
      if (!$('#dataConfirmModal').length) {
         $('body').append(strDomModalConfirm);
      }
      $('#dataConfirmModal').find('.modal-body').text($(this).attr('data-confirm'));
      $('#dataConfirmOK').attr('href', href);
      $('#dataConfirmModal').modal({show: true});
      $('#dataConfirmOK').on('click', function() {
         window.location = $(this).attr("href");
      });
      return false;
   });

   $('body').on('click', 'a.ajax-request,a.javascript-call', function(e) {
      e.preventDefault();
      var url = $(this).attr('href');
      var invoker = $(this);
      var data_confirm = $(this).data('confirm');
      var data_action = $(this).data('action');
      if (url.indexOf('?') != -1) {
         url = url + "&ajaxCall=true";
      } else {
         url = url + "?ajaxCall=true";
      }

      if (typeof data_confirm != "undefined") {
         if (!$('#dataConfirmModal').length) {
            $('body').append('<div id="dataConfirmModal" class="modal" role="dialog" aria-labelledby="dataConfirmLabel" aria-hidden="true">'
                    + '<div class="modal-dialog"><div class="modal-content"><div class="modal-header">'
                    + '<h5 id="dataConfirmLabel">Confirmation</h5>'
                    + '<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>'
                    + '</div>'
                    + '<div class="modal-body"></div><div class="modal-footer">'
                    + '<button class="btn btn-secondary" data-dismiss="modal" aria-hidden="true">Non</button><a data-dismiss="modal" class="btn btn-danger text-white" id="dataConfirmOK">Oui</a>'
                    + '</div></div></div></div>');
         }

         $('#dataConfirmModal').find('.modal-body').text($(this).data('confirm'));
         $('#dataConfirmModal').modal({show: true});
         $('#dataConfirmOK').on('click', function() {
            if ($(this).hasClass('ajax-request'))
            {
               ajaxCall(data_action, url);
            } else
            {

               if (data_action === 'delete') {
                  ajaxCall(data_action, url);
                  invoker.parents('tr').remove();
               }

               //Todo Call JS function
            }

         });
      } else {
         ajaxCall();
      }

      function ajaxCall(data_action, url) {
         $.ajax({
            url: url,

            success: function(data) {
               if (data_action === 'delete') {
                  invoker.parents('tr').remove();
               }
               var alertbox = $('#alert-box');
               if (!alertbox.lenght) {
                  $('#content-wrapper').prepend('<div id="alert-box"></div>');
               }
               $('#alert-box').html('<div class="alert alert-success mt-15 alert-dismissible fade in" role="alert">'
                       + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>'
                       + Translator.trans('Generics.flash.' + data_action + 'Success') + '</div>');
               $('.alert-success').fadeTo(3000, 500).slideUp(500, function() {
               });

            },
            error: function(data) {
               var alertbox = $('#alert-box');
               if (!alertbox.lenght) {
//                 debugger;
                  $('#content-wrapper').prepend('<div id="alert-box"></div>');
               }
               $('#alert-box').prepend('<div class="alert alert-danger mt-15 alert-dismissible fade in" role="alert">'
                       + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>'
                       + Translator.trans('Generics.flash.' + data_action + 'Failure') + '</div>');
               $('.alert-danger').fadeTo(3000, 500).slideUp(500, function() {
               });
            }
         });
      }
   });

});

function populateAndOpenDefaultModal(title, body) {
   $('#defaultAjaxModal').find('.modal-content').first().html(
           '<div class="modal-header">'
           + '<h5 class="modal-title">' + title + '</h5>'
           + '<button type="button" class="close" data-dismiss="modal" aria-label="Close">'
           + '<span aria-hidden="true">&times;</span>'
           + '</button>'
           + '</div>'
           + '<div class="modal-body">' + body + '</div>'
           + '</div>'
           );
   $('#defaultAjaxModal').modal('show');
}

function initCboxHelpMessage() {

   $('input:checkbox').each(function() {
      if ($(this).data('id-help-message')) {
         var $objHelp = $('<a data-id-help-message=' + $(this).data('id-help-message') + ' href="javascript:void(0)"><span  class="helpblock fa fa-question-circle fa"></span></a>');
//      $(this).prev();
         $(this).parent().append($objHelp);
//      id_help_msg = $(this).data('id-help-message');
         $objHelp.on('click', function(evt) {
            var url_help_message = Routing.generate('get_help_message_for_listing_value', {id: $(this).data('id-help-message')});
            loadAjaxModalContent('defaultAjaxModal', url_help_message);
         });
      }
   });
}

//Gestion des bulles d'aide dans les options des select2
function format(data, container) {

   var element = data.element;
   var $option = $('<span ></span>');
   $option.text(data.text);
   if ($(element).hasClass('with-help-message'))
   {

      var $objHelp = $('<a href="javascript:void(0)"><span  class="helpblock fa fa-question-circle fa"></span></a>');

      $objHelp.on('mouseup', function(evt) {
         evt.stopPropagation();
      });

      $objHelp.on('click', function(evt) {
         var url_help_message = Routing.generate('get_help_message_for_listing_value', {id: $(element).data("id-help-message")});
         loadAjaxModalContent('defaultAjaxModal', url_help_message);
      });


      $option.append($objHelp);

//      return $option;
   }
   if ($(element).data("risk-level")) {
      var $objHelp = $('<a href="javascript:void(0)"><span  class="helpblock fa fa-question-circle fa"></span></a>');
      $objHelp.on('mouseup', function(evt) {
         evt.stopPropagation();
      });
      $option.append($objHelp);
//     console.log($(element).prev());
      $(container).addClass("level" + $(element).data("risk-level"))
   }

   return $option;
}


function initSelect2()
{
   $('select').each(function(i, select) {
      if (!$(select).hasClass('noSelect2') && !$(this).parents('.modal').length) {
         strPlaceHolder = $(select).attr("data-placeholder");
         if ($(select).attr("data-placeholder"))
         {
            strPlaceHolder = $(select).attr("data-placeholder");
         }
         var select2 = $(select).select2({
            placeholder: strPlaceHolder,
            templateResult: format,
//            templateSelection: format,
            escapeMarkup: function(markup) {
               return markup;
            }
         });
         $("span.select2-results").each(function() {
            console.log($(this));
         });
      }
   });
}



var curFlashMessageStatus = 'success';
/**
 * Show a div to display information .Autohide is default
 * @param {type} status
 * @param {type} message
 * @param {type} autoHide
 * @returns {undefined}
 */
function flashMessage(status, message, autoHide = true)
{

   if ($('#flash-message-container').length > 0)
   {
      $('#flash-message-container').removeClass("alert-" + curFlashMessageStatus);
      $('#flash-message-container').addClass("alert-" + status);
      $('#flashmessagecontent').text(message);
      $('#flash-message-container').show();
   } else {

      $(".custom-search-form").prepend('<div id="flash-message-container" class="ml-auto mb-0 mt-0 alert alert-' + status + ' alert-dismissible " role="alert">'
              + '<span id="flashmessagecontent">' + message + '</span>'
              + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>'
              + '</div>');

      $('#flash-message-container').show();
   }
   curFlashMessageStatus = status;
   if (autoHide)
   {
      setTimeout('$("#flash-message-container").fadeOut(1500)', 1000);
}
}

function hideFlashMessage()
{
   $('#flash-message-container').hide();
}
function initializeModalFunctions(modalId) {
   initializeModalSelect2(modalId);

}

function initializeModalSelect2(modalId) {

   $('.modal select2').css('width', '100%');
   $('.modal select').select2({
      language: "fr",
      dropdownParent: $('#' + modalId)
   });
}

function initializeDatepicker() {
   if ($.fn.datepicker && $('.datepicker').length > 0) {
      $('.datepicker').datepicker({
         language: 'fr',
         format: 'dd/mm/yyyy',
         todayHighlight: true,
         autoclose: true
      });

      $(".monthPicker").datepicker({
         format: "mm/yyyy",
         startView: "months",
         minViewMode: "months",
         autoclose: true
      });
   }
}

function initializeDateRangePicker() {
   if ($('input.dateRangePicker').length > 0) {
      $('input.dateRangePicker').daterangepicker({
         opens: 'left'
      });
   }
}


//TinyMCE
function initializeTinyMce()
{
   tinymce.init({
      selector: '.tinymce',
      content_css: Routing.generate('homepage').replace('/index.php', '') + "vendor/bootstrap/css/bootstrap.min.css",
      // fontsize_formats: "8pt 10pt 12pt 14pt 18pt 24pt 36pt",
      menubar: false,
      height: "360",
      plugins: [
         'advlist autolink lists link image charmap print preview anchor textcolor',
         'searchreplace visualblocks code fullscreen',
         'insertdatetime media table paste code help wordcount hr contextmenu visualblocks'
      ],
      relative_urls: false,
      visualblocks_default_state: false,
      toolbar: 'table undo redo |  formatselect | fontsizeselect bold italic forecolor backcolor  | visualblocks | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | hr | image | removeformat | code fullscreen',
      images_upload_url: Routing.generate('homepage') + '_uploader/gallery/upload?src=tinymce',
      contextmenu: "link image inserttable | cell row column deletetable",
      init_instance_callback: function(editor) {
         editor.on('blur', function(e) {
            tinyMCE.triggerSave();
            var form = $("#" + editor.id).closest('form');
            if ($(form).attr("id") == 'form-generate-ordTherpy' || $(form).attr("id") == 'form-generate-ordonnance' || $(form).attr("id") == 'form-generate-report')
            {
               var form_action = $(form).attr('action') + '?saveOnly=true';
               $.ajax({
                  type: "POST",
                  url: form_action,
                  data: $(form).serialize(),
                  success: function(data) {
                     try {
                        obj_response = $.parseJSON(data);
                        flashMessage(obj_response.status, Translator.trans('Generics.flash.editSuccess'), true);
                     } catch (e) {
                        console.warn(e);
                     }


                  },
                  error: function() {
                     //message d'alerte
                  }
               });
            }

         });
      },
      //forced_root_block: '<div>'
   });
}

$(document).on('click', 'span[data-auto-hide="true"] input', function()
{

   var target_toggle = $(this).parent().parent().attr("data-target");

   if ($(this).is(':checked'))
   {
      $(this).parent().parent().prev().hide();
      $(target_toggle).hide();
      $(target_toggle + ' :input').prop('checked', false);
      $(target_toggle + ' :input').val('').trigger('change');

   } else
   {
      $(this).parent().parent().prev().show();
      $(target_toggle).show();
   }

});



/**
 * vérifie si une valeur est sélectionnée dans un select2
 * @param {type} objSelect2
 * @param {type} val
 * @returns {Boolean}
 */
function checkSelect2Selection(objSelect2, val)
{
   var tabSelectedVal = $(objSelect2).select2('data');
   if (tabSelectedVal)
   {
      for (var i = 0; i < tabSelectedVal.length; i++)
      {
         if (tabSelectedVal[i].id === val.toString()) {
            return true;
            break;
         }
      }
   }

   return false;
}



function ShowHideFormElementsFromCheckboxOrSelect() {

   //debugger;
   $('.hiddenPilotField').each(function(index) {
      var hiddedClass = $(this).data('hiddedclass');
      var showedClass = $(this).data('showedclass');
      var valueforyes = $(this).data('valueforyes');
      if ($(this).is("select")) {
         if ($(this).val() > 1 || (valueforyes && $(this).val() == valueforyes)) {
            $('.' + hiddedClass).parent().show();
            $('.' + showedClass).parent().hide();
         } else {
            $('.' + hiddedClass).parent().hide();
            $('.' + showedClass).parent().show();
            $('.' + hiddedClass + ' :input').prop('checked', false);
            $('.' + hiddedClass + ' select').val("").trigger('change');
         }
      } else {
         if ($(this).is(":checked")) {
            $('.' + hiddedClass).parent().show();
            $('.' + showedClass).parent().hide();
         } else {
            $('.' + hiddedClass).parent().hide();
            $('.' + hiddedClass).prop('checked', false);
            $('.' + hiddedClass).children('input').prop('checked', false);
            $('.' + hiddedClass).val("").trigger('change');
            $('.' + showedClass).parent().show();
         }
      }
   });

}

function checkAll() {
   var boxes = document.getElementsByTagName('input');
   for (var index = 0; index < boxes.length; index++) {
      box = boxes[index];
      if (box.type == 'checkbox' && box.className == 'sf_admin_batch_checkbox') {
         box.checked = document.getElementById('sf_admin_list_batch_checkbox').checked
      }
   }
   return true;
}





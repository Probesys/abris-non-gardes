$(document).ready(function() {
   Translator.locale = 'fr';
   /* surcharge des form-help pour interpretation du html */
   $("small[id$='_help']").each(function(i, obj) {
      $('#' + obj.id).html(obj.innerText);
   });

   // remplit le champ image avec le nom du fichier suite à sa slection
   if( $('.custom-file-input').length ) {
    document.querySelector('.custom-file-input').addEventListener('change', function(e) {
       console.log($(this).attr('id'));
       var fileName = document.getElementById($(this).attr('id')).files[0].name;
       var nextSibling = e.target.nextElementSibling;
       nextSibling.innerText = fileName;
    });
   }

   /* city autocomplete */
   $('.city-select2-autocomplete').select2({
      width: '100%',
      ajax: {
         url: Routing.generate('autocomplete_city'),
         dataType: 'json',
         delay: 250,
         data: function(params) {
            return {
               q: params.term // search term
            };
         },
         processResults: function(data) {
            return {
               results: data.items
            };
         }
      },
      placeholder: {
         id: "",
         placeholder: Translator.trans('Entities.City.actions.selectOneCity')
      },
      allowClear: true,
      language: "fr",
      dropdownAutoWidth: false,
      minimumInputLength: 2
   });

   $(document).on('change', '#dysfonctionnement_natureDys', function(e)
   {
      var ajax_url = Routing.generate('ApiListElementsDys', {'natureDysId': $(this).val()});
      $('#dysfonctionnement_elementDys').parent().remove();
      $('#dysfonctionnement_detailDys').parent().remove();

      $.ajax({
         type: "GET",
         url: ajax_url,
         success: function(data) {

            $dom = '<div class="form-group" id="div_dysfonctionnement_elementDys">';
            $dom += '<label for="dysfonctionnement_elementDys" class="">' + Translator.trans('Entities.Dysfonctionnement.fields.elementDys') + '</label>';
            $dom += '<select id="dysfonctionnement_elementDys" class="form-control select2" name="dysfonctionnement[elementDys]" required>';
            $dom += '<option value=""></option>';
            if (data.length > 0)
            {
               $.each(data, function(i, obj) {
                  $dom += '<option value="' + obj.id + '">' + obj.name + '</option>';
               });
            }
            $('#dysfonctionnement_natureDys').parent().after($dom)
            $('#dysfonctionnement_elementDys').select2();
            $dom += '</select>';
            $dom += '</div>';

         },
         error: function() {
            $('#dysfonctionnement_elementDys').parent().remove();
         }
      });
      e.stopImmediatePropagation();
   });

   $(document).on('change', '#dysfonctionnement_elementDys', function(e)
   {
      /*var ajax_url = Routing.generate('ApiListDetailsDys', {'elementDysId': $(this).val()});
       $.ajax({
       type: "GET",
       url: ajax_url,
       success: function(data) {
       $('#dysfonctionnement_detailDys').parent().remove();
       $dom = '<div class="form-group">';
       $dom += '<label for="dysfonctionnement_detailDys" class="">' + Translator.trans('Entities.Dysfonctionnement.fields.detailDys') + '</label>';
       $dom += '<select id="dysfonctionnement_detailDys" class="form-control select2" name="dysfonctionnement[detailDys]" >';
       $dom += '<option value=""></option>';
       if (data.length > 0)
       {

       $.each(data, function(i, obj) {
       $dom += '<option value="' + obj.id + '">' + obj.name + '</option>';
       });
       }
       $('#dysfonctionnement_elementDys').parent().after($dom)
       $('#dysfonctionnement_detailDys').select2();
       $dom += '</select>';
       $dom += '</div>';

       },
       error: function() {
       $('#dysfonctionnement_detailDys').parent().remove();
       }
       });
       e.stopImmediatePropagation();*/
   });


   // bootstrap WYSIHTML5 - text editor
   $('.summernote').summernote({
      lang: 'fr-FR',
      height: 300
   });


   $(function() {
      $(document).on('click', '[data-toggle="lightbox"]', function(event) {
         event.preventDefault();
         $(this).ekkoLightbox({
            alwaysShowClose: true
         });
      });

//    $('.filter-container').filterizr({gutterPixels: 3});
      $('.btn[data-filter]').on('click', function() {
         $('.btn[data-filter]').removeClass('active');
         $(this).addClass('active');
      });
   });


//Validation du formulaire abris. Ouverture de l'onglet qui contient la première erreur
   $('form[name="abris_form"]').validate({
      ignore: [],
      success: function(label, element) {
         label.parent().removeClass('error');
         label.remove();
      },
   });

   $('form[name="abris_form"]').on("invalid-form.validate", function(event, validator) {
      tabErrors = validator.errorList;
      if (tabErrors.length > 0) {
         var targetTabWithError = $(tabErrors[0].element).parents('.tab-pane').attr('id');

         $('#tabs-form-abris a[aria-controls="' + targetTabWithError + '"]').tab('show');
         $([document.documentElement, document.body]).animate({
            scrollTop: $(tabErrors[0].element).offset().top
         }, 500);
      }
   });
});






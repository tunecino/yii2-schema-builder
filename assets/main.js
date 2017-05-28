(function(){

   $('body').on('beforeSubmit', "form[data-type='ajax']", function (event) {
        var form = $(this);
        if (form.find('.has-error').length) return false;

        var timeout = 1400;
        var closeModal = false;
        var pjaxContainers = [];

        form.find('input[name="reload-pjax"]').each(function(){
             var container = '#' + this.value;
             if ($(container).length) {
                  pjaxContainers.push(container);
                  if (!closeModal && this.getAttribute('data-close-modal') === 'true') closeModal = true;
             }
        });

        if (closeModal) {
             var modal = form.children("[data-toggle='modal']:first");
             var modalId = modal ? modal.attr("data-target") : null;
        }

        var reloadPjax = function(pjaxContainers, timeout) {
             $.each(pjaxContainers , function(index, container) { 
                  if (pjaxContainers.length > index + 1) {
                       $(container).one('pjax:end', function (xhr, options) {
                            $.pjax.reload({container: pjaxContainers[index+1], 'timeout': timeout}) ;
                            if (index+2 === pjaxContainers.length) enableModalBackdrop();
                       });
                  }
             });
             if (pjaxContainers.length) $.pjax.reload({container: pjaxContainers[0], 'timeout': timeout}) ;
        }
        
        $.ajax({
             url: form.attr('action'),
             type: 'post',
             data: form.serialize(),
             success: function (response) {
                  if (response) {
                       if (closeModal && modalId) {
                            $(modalId).one('hidden.bs.modal', function () { reloadPjax(pjaxContainers, timeout) });
                            $(modalId).modal('hide');
                       }
                       else reloadPjax(pjaxContainers, timeout);
                  }
                  else throw new Error('Whoops! Unexpected Error');
             },
             error: function (request, status, error) {
                throw new Error(request.responseText);
            }
        });
        return false;
   });


   // full screen modal. source: http://www.minimit.com/articles/solutions-tutorials/bootstrap-3-transparent-and-fullscreen-modals
   var enableModalBackdrop = function() {
        $(".modal-fullscreen").on('show.bs.modal', function () {
          setTimeout( function() {
            $(".modal-backdrop").addClass("modal-backdrop-fullscreen");
          }, 0);
        });
        $(".modal-fullscreen").on('hidden.bs.modal', function () {
          $(".modal-backdrop").addClass("modal-backdrop-fullscreen");
        });
   }

   enableModalBackdrop();
     
})();
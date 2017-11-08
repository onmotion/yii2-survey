;(function ($) {
    "use strict";

    /**
     *
     */
    function SurveyWidget(options = {}) {

        this.id = options.id || null;

        $(document).on('afterValidateAttribute', function (event, attribute, messages) {
            if (messages.length === 0) {
                let submitBtn = $(event.target).find('.btn-submit');
               // submitBtn.showUp();
              //  console.log(submitBtn.showUp());
            }
        });

        function createModal() {
            $('html').append(`
<div id="survey-modal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Modal Header</h4>
            </div>
            <div class="modal-body">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>`);
        }

        function removeModal() {
            $('#survey-modal').remove();
        }

        function confirmForm(form) {
            return new Promise((resolve, reject) => {
                let container = form.closest('[data-pjax-container]');
                form.submit();
                container.off('afterValidate').on('afterValidate', function (event, messages, errorAttributes) {
                    if (errorAttributes.length > 0) {
                        reject(messages);
                    } else {
                        resolve(true);
                    }
                });

                container.off('pjax:error').on('pjax:error', function (e) {
                    //  console.log(e);
                    reject(e);
                });
                setTimeout(() => {
                    resolve(true);
                }, 5000);
            });

        }

        let that = this;
        async function submitAllForms() {
            let forms = [];
            let $body = $('body');
            $body.toggleClass('survey-loading');
            let btn = $(this);
            btn.prop('disabled', true);
            let defaultText = btn.data('default-text') || '';
            btn.html('<i class="fa fa-spinner fa-pulse fa-fw" aria-hidden="true"></i>');
            let allFormsIsValid = true;
            console.log(allFormsIsValid);

            $(document).find('form.question-form').each(function (i, el) {
                forms.push($(el));
            });

            let promises = [];
            for (let item of forms) {
                promises.push(confirmForm(item));
            }

            await Promise.all(promises).then(resolve => {}, reject => {
                allFormsIsValid = false;
            });

            console.log(allFormsIsValid);
            if (allFormsIsValid) {
                let csrfParam = $('meta[name="csrf-param"]').attr("content");
                let csrfToken = $('meta[name="csrf-token"]').attr("content");
                let data = {};
                data[csrfParam] = csrfToken;
                data.id = that.id;
                $.ajax({
                    url: btn.data('action') || window.location.pathname,
                    type: 'post',
                    data: data,
                    success(response) {
                        btn.remove();
                        createModal();
                        let modal = $('#survey-modal');
                        modal.find('.modal-header').html(response.title);
                        modal.find('.modal-body').html(response.content);
                        modal.find('.modal-footer').html(response.footer);
                        modal.modal();
                    },
                    complete() {
                        $body.toggleClass('survey-loading');
                    },
                    error(err){
                        createModal();
                        let modal = $('#survey-modal');
                        modal.find('.modal-header').html(err.statusText);
                        modal.find('.modal-body').html(err.responseText);
                        modal.modal();
                    }
                });
            } else {
                btn.prop('disabled', false);
                btn.html(defaultText);
                $body.toggleClass('survey-loading');
            }
        }

        $(document).on('click', '#s-done', submitAllForms);

    }


    $.fn.showUp = function () {
        if (!this.hasClass('fadeIn')) {
            this.removeClass('hidden').addClass('fadeIn');
        }
        return this;
    };

    $.fn['surveyWidget'] = function (options = {}) {
        console.log('survey widget init');
        if (!$.data(this, 'plugin_SurveyWidget')) {
            return $.data(this, 'plugin_SurveyWidget',
                new SurveyWidget(options));
        }
    };


})(window.jQuery);



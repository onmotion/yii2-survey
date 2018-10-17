;(function ($) {
    "use strict";

    /**
     *
     */
    function SurveyWidget(options) {

        this.id = options.id || null;

        $(document).on('pjax:end', function (e, contents, options) {
            console.log(e);
            if (e.target.id === 'survey-questions-append') {
                var appendContainer = $('#survey-questions-append').find('.survey-question-pjax-container');
                appendContainer.appendTo('#survey-questions');
            }
        });

        $(document).on('pjax:timeout', function (event) {
            // Prevent default timeout redirection behavior
            console.log(event);
            event.preventDefault();
        });

        $(document).on('pjax:error', function (event, xhr, textStatus, error, options) {
            console.log(event);
            event.preventDefault();
        });

        var showProgress;
        showProgress = function showProgress(container) {
            try {
                container.prepend('<div class="preloader"><div class="cssload-spin-box"></div></div>');
            } catch (err) {
                console.log(e.message);
            }
            $('.preloader').fadeIn();
        };

        var hideProgress = function hideProgress(container) {
            container = container || null;
            try {
                if (container !== null) {
                    $(container).find('.preloader').fadeOut(500, function () {
                        $(this).remove();
                    });
                } else {
                    $('.preloader').fadeOut(500, function () {
                        $(this).remove();
                    });
                }
            } catch (err) {
                console.log(e.message);
            }
        };
        hideProgress();

        $(document).on('pjax:start', function (e) {
            if ((e.target.id).indexOf('survey-questions-pjax-') !== -1) {
                showProgress($('#' + e.target.id).find('.survey-block '));
            }
        });

        $(document).on('pjax:complete', function (e) {
            if ((e.target.id).indexOf('survey-questions-pjax-') !== -1 || (e.target.id === 'survey-questions-append')) {
                hideProgress($('#' + e.target.id));
            }
        });


        $(document).on('afterValidateAttribute', function (event, attribute, messages) {
            if (messages.length === 0) {
                var submitBtn = $(event.target).find('.btn-submit');
                // submitBtn.showUp();
                //  console.log(submitBtn.showUp());
            }
        });

        function createModal() {
            $('html').append("\
<div id='survey-modal' class='modal fade' role='dialog'>\
    <div class='modal-dialog'>\
        <div class='modal-content'>\
            <div class='modal-header'>\
                <button type='button' class='close' data-dismiss='modal'>&times;</button>\
                <h4 class='modal-title'>Modal Header</h4>\
            </div>\
            <div class='modal-body'>\
            </div>\
            <div class='modal-footer'>\
                <button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>\
            </div>\
        </div>\
    </div>\
</div>");
        }

        function removeModal() {
            $('#survey-modal').remove();
        }

        function confirmForm(form) {
            return new Promise(function(resolve, reject) {
                var container = form.closest('[data-pjax-container]');
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
                setTimeout(function() {
                    resolve(true);
                }, 5000);
            });

        }

        var that = this;

        function submitAllForms() {
            var forms = [];
            var $body = $('body');
            $body.toggleClass('survey-loading');
            var btn = $(this);
            btn.prop('disabled', true);
            var defaultText = btn.data('default-text') || '';
            btn.html('<i class="fa fa-spinner fa-pulse fa-fw" aria-hidden="true"></i>');
            var allFormsIsValid = true;
            console.log(allFormsIsValid);

            $(document).find('form.question-form').each(function (i, el) {
                forms.push($(el));
            });

            var promises = [];
            for (var _i = 0; _i < forms.length; _i++) {
                var item = forms[_i];
                promises.push(confirmForm(item));
            }

            Promise.all(promises).then(function (resolve) {
                var csrfParam = $('meta[name="csrf-param"]').attr("content");
                var csrfToken = $('meta[name="csrf-token"]').attr("content");
                var data = {};
                data[csrfParam] = csrfToken;
                data.id = that.id;
                $.ajax({
                    url: btn.data('action') || window.location.pathname,
                    type: 'post',
                    data: data,
                    success: function success(response) {
                        btn.remove();
                        createModal();
                        var modal = $('#survey-modal');
                        modal.find('.modal-header').html(response.title);
                        modal.find('.modal-body').html(response.content);
                        modal.find('.modal-footer').html(response.footer);
                        modal.modal();
                    },
                    complete: function complete() {
                        $body.toggleClass('survey-loading');
                    },
                    error: function error(err) {
                        createModal();
                        var modal = $('#survey-modal');
                        modal.find('.modal-header').html(err.statusText);
                        modal.find('.modal-body').html(err.responseText);
                        modal.modal();
                    }
                });
            }, function (reject) {
                btn.prop('disabled', false);
                btn.html(defaultText);
                $body.toggleClass('survey-loading');
            });
        }

        $(document).on('click', '#s-done', submitAllForms);

    }


    $.fn.showUp = function () {
        if (!this.hasClass('fadeIn')) {
            this.removeClass('hidden').addClass('fadeIn');
        }
        return this;
    };

    $.fn["surveyWidget"] = function() {
        var options =
            arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};

        console.log("survey widget init");
        if (!$.data(this, "plugin_SurveyWidget")) {
            return $.data(this, "plugin_SurveyWidget", new SurveyWidget(options));
        }
    };


})(window.jQuery);



;(function ($) {
    "use strict";

    /**
     *
     */
    function Survey() {

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


        $(document).on('click', '.survey-question-submit, .user-assign-submit', pseudoSubmit);

        function pseudoSubmit(e) {
            e.preventDefault();
            var $this = $(this);
            var data = $this.data();
            var action = data.action;
            var $form = $this.closest('form');
            console.log($form);
            if ($form && action) {
                $form.attr('action', action).submit();
            } else {
                console.log('Error');
            }
        }

        $(document).on('click', '.checkbox-updatable', function (e) {
            var container = $(this).closest('[data-pjax-container]');
            container.find('.update-question-btn').click();
        });

        $(document).on('click', '.submit-on-click', function (e) {
            var container = $(this).closest('[data-pjax-container]');
            container.find('button[type=submit]').click();
        });


        async function submitAllForms() {
            var forms = [];
            var $body = $('body');
            $body.toggleClass('survey-loading');
            var btn = $(this).find('button');
            btn.prop('disabled', true);
            var defaultText = btn.data('default-text') || '';
            btn.html('<i class="fa fa-spinner fa-pulse fa-fw" aria-hidden="true"></i>');
            var allFormsIsValid = true;

            $(document).find('form.form-inline').each(function (i, el) {
                forms.push($(el));
            });

            for (var item of forms) {
                try {
                    await confirmForm(item);
                } catch (err) {
                    allFormsIsValid = false;
                }
            }

            if (allFormsIsValid) {
                location.href = $('#save').data('action');
            } else {
                btn.prop('disabled', false);
                btn.html(defaultText);
                $body.toggleClass('survey-loading');
            }
        }

        $(document).on('click', '#save', submitAllForms);

        $(document).on('click', '.close-btn', function (e) {
            $(this).parent().toggleClass('opened');
            $('body').toggleClass('modal-open');
        });

        $(document).on('click', '.respondents-toggle', function (e) {
            $('#respondents-modal').toggleClass('opened');
            $('body').toggleClass('modal-open');
        });


        function confirmForm(form) {
            return new Promise((resolve, reject) => {
                var container = form.closest('[data-pjax-container]');
                form.submit();
                container.on('afterValidate', function (event, messages, errorAttributes) {
                    if (errorAttributes.length > 0) {
                        reject(messages);
                    }
                });
                container.on('pjax:end', function (e) {
                    console.log(e);
                    resolve(e);
                });
                container.on('pjax:error', function (e) {
                    console.log(e);
                    reject(e);
                });
            });
        }


        var showProgress;
        showProgress = function showProgress(container) {
            try {
                container.prepend('<div class="preloader"><div class="cssload-spin-box"></div></div>');
            } catch (err) {
                console.log(e.message);
            }
            $('.preloader').fadeIn();
        };

        var hideProgress;
        (hideProgress = function hideProgress(container = null) {
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
        })();

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
    }

    $.fn['survey'] = function () {
        console.log('survey init');
        if (!$.data(this, 'plugin_Survey')) {
            return $.data(this, 'plugin_Survey',
                new Survey());
        }
    }

})(window.jQuery);
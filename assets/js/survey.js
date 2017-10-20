$(document).ready(function (e) {

    $(document).on('pjax:end', function (e, contents, options) {
        if (e.target.id === 'survey-questions-append') {
            let appendContainer = $('#survey-questions-append [data-pjax-container]');
            appendContainer.appendTo('#survey-questions');
        }
    });

    $(document).on('pjax:timeout', function (event) {
        // Prevent default timeout redirection behavior
        console.log(event);
        event.preventDefault();
    });

    $(document).on('click', '.survey-question-submit', function (e) {
        e.preventDefault();
        let $this = $(this);
        let data = $this.data();
        let action = data.action;
        let $form = $this.closest('form');
        if ($form && action) {
            $form.attr('action', action).submit();
        } else {
            console.log('Error');
        }
    });

    $(document).on('click', '.checkbox-updatable', function (e) {
        let container = $(this).closest('[data-pjax-container]');
        container.find('.update-question-btn').click();
    });

    $(document).on('click', '.submit-on-click', function (e) {
        let container = $(this).closest('[data-pjax-container]');
        container.find('button[type=submit]').click();
    });



    async function submitAllForms() {
        var forms = [];
        $(this).prop('disabled', true);
        var allFormsIsValid = true;

        $(document).find('form.form-inline').each(function (i, el) {
            forms.push($(el));
        });

        for (let item of forms) {
            try {
                await confirmForm(item);
            } catch (err) {
                allFormsIsValid = false;
            }
        }

        $(this).prop('disabled', false);

        if (allFormsIsValid) {
            location.href = $('#done').data('action');
        }
    }

    $(document).on('click', '#done', submitAllForms);


    function confirmForm(form) {
        return new Promise((resolve, reject) => {
            console.log(form);
            let container = form.closest('[data-pjax-container]');
            form.submit();
            container.on('afterValidate', function (event, messages, errorAttributes) {
                if (errorAttributes.length > 0) {
                    reject(messages);
                }
            });
            container.on('pjax:end', function (e, contents, options) {
                console.log(e);
                resolve(e);
            });
            container.on('pjax:error', function (e, contents, options) {
                console.log(e);
                reject(e);
            });
        });
    }


    let showProgress;
    showProgress = function showProgress(container) {
        try {
            container.prepend('<div class="preloader"><div class="cssload-spin-box"></div></div>');
        } catch (err) {
            console.log(e.message);
        }
        $('.preloader').fadeIn();
    };

    let hideProgress;
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
        if ((e.target.id).indexOf('survey-questions-pjax-') !== -1) {
            hideProgress($('#' + e.target.id));
        }
    });


});

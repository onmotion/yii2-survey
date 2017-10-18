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
        console.log(e);
        if ((e.target.id).indexOf('survey-questions-pjax-') !== -1) {
            hideProgress($('#' + e.target.id));
        }
    });


});

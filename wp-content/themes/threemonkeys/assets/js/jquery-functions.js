import {
  Toaster,
  ToasterPosition,
  ToasterTimer,
  ToasterType,
} from 'bs-toaster';

const advancedToaster = new Toaster({
  position: ToasterPosition.TOP_END,
  type: ToasterType.DEFAULT,
  delay: 5000,
  timer: ToasterTimer.ELAPSED,
  animation: true,
  defaultIconMarkup: `<i class="fas fa-bolt me-2"></i>`,
});
const lang = ThemeSetup.lang || 'es';
const msgs = {
  favorites: {
    es: {
      success: 'Se agregó correctamente',
      error: 'Error',
      msgAdd: `Se agregó <strong>%s$1</strong> a tu lista`,
      msgDelete: `Se eliminó <strong>%s$1</strong> de tu lista`,
    },
    en: {
      success: 'Successfully added',
      error: 'Removed from your list',
      msgAdd: `Added <strong>%s$1</strong> successfully`,
      msgDelete: `Removed <strong>%s$1</strong> from your list`,
    },
  },
  reviews: {
    es: {
      success: 'Correcto',
      error: 'Error',
      msg: 'Se envió tu reseña correctamente. Una vez aprobada podrás verla en esta página',
      msgError: 'Es necesario escribir una reseña.',
    },
    en: {
      success: 'Success',
      error: 'Error',
      msg: 'Your review has been submitted successfully. Once approved you can see it on this page',
      msgError: 'You need to write a review.',
    },
  },
};

(function ($) {
  'use strict';

  $(document).on(
    'favorites-updated-single',
    function (event, favorites, post_id, site_id, status) {
      // Do stuff here as needed
      // console.log('EY', event, favorites, post_id, status);
      let item;
      let itemTitle = '';
      if (document.querySelector(`[data-socio-id="${post_id}"]`)) {
        item = document.querySelector(`[data-socio-id="${post_id}"]`);

        itemTitle = item.querySelector('.article-item__title').innerText;
      }

      let actionMsg = '';
      let actionTitle = '';
      let actionType = ToasterType.DEFAULT;

      if (status === 'active') {
        actionTitle = msgs.favorites[lang].success; //'Se agregó correctamente';
        actionType = ToasterType.SUCCESS;
        actionMsg = msgs.favorites[lang].msgAdd; //`Se agregó <strong>${itemTitle}</strong> a tu lista`;

        actionMsg = actionMsg.replace('%s$1', itemTitle);
      } else {
        actionTitle = msgs.favorites[lang].error;
        //('Se eliminó correctamente');
        actionMsg = msgs.favorites[lang].msgDelete; //`Se eliminó <strong>${itemTitle}</strong> de tu lista`;

        actionType = ToasterType.DANGER;

        actionMsg = actionMsg.replace('%s$1', itemTitle);
      }

      advancedToaster.create(actionTitle, actionMsg, {
        type: actionType,
        timer: ToasterTimer.COUNTDOWN,
        delay: 5000,
      });
    }
  );

  // function toggleDropdown(e) {
  //   const _d = $(e.target).closest('.dropdown'),
  //     _m = $('.dropdown-menu', _d);
  //   setTimeout(
  //     function () {
  //       const shouldOpen = e.type !== 'click' && _d.is(':hover');
  //       _m.toggleClass('show', shouldOpen);
  //       _d.toggleClass('show', shouldOpen);
  //       $('[data-toggle="dropdown"]', _d).attr('aria-expanded', shouldOpen);
  //     },
  //     e.type === 'mouseleave' ? 5 : 0
  //   );
  // }

  // $('body')
  //   .on('mouseenter mouseleave', '.dropdown', toggleDropdown)
  //   .on('click', ".dropdown-menu a:not('.dropdown-item')", toggleDropdown);

  // $('.dropdown a')
  //   .not('.dropdown-item')
  //   .on('click tap', function (event) {
  //     event.preventDefault();
  //     //if ($(window).width() > 991 && $(this).attr('href') != '#') {
  //     window.location.href = $(this).attr('href');
  //     //}
  //   });
})(jQuery);

/*
 * Let's begin with validation functions
 */
jQuery.extend(jQuery.fn, {
  /*
   * check if field value lenth more than 3 symbols ( for name and comment )
   */
  validate: function () {
    if (jQuery(this).val() === '') {
      console.log(jQuery(this).val());
      jQuery(this).addClass('error');
      return false;
    } else {
      jQuery(this).removeClass('error');
      return true;
    }
  },

  validateReview: function () {
    if (jQuery(this).val().length < 3) {
      jQuery(this).addClass('error');
      jQuery(this).addClass('is-invalid');

      return false;
    } else {
      jQuery(this).removeClass('error');
      jQuery(this).removeClass('is-invalid');

      return true;
    }
  },
  /*
   * check if email is correct
   * add to your CSS the styles of .error field, for example border-color:red;
   */

  validateEmail: function () {
    var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/,
      emailToValidate = jQuery(this).val();
    if (!emailReg.test(emailToValidate) || emailToValidate == '') {
      jQuery(this).addClass('error');
      return false;
    } else {
      jQuery(this).removeClass('error');
      return true;
    }
  },
});

jQuery(function ($) {
  /*
   * On comment form submit
   */
  $('#commentform').on('submit', function () {
    // define some vars
    var button = $('#submit'), // submit button
      respond = $('#respond'), // comment form container
      commentlist = $('.comment-list'), // comment list container
      cancelreplylink = $('#cancel-comment-reply-link');

    // if user is logged in, do not validate author and email fields
    if ($('#author').length) $('#author').validate();

    if ($('#email').length) $('#email').validateEmail();
    if ($('#rate-it').length) $('#rate-it').validate();

    // validate comment in any case
    $('#comment').validateReview();

    // if comment form isn't in process, submit it
    if (
      !button.hasClass('loadingform') &&
      !$('#author').hasClass('error') &&
      !$('#email').hasClass('error') &&
      !$('#comment').hasClass('error') &&
      !$('#rate-it').hasClass('error')
    ) {
      // ajax request
      $.ajax({
        type: 'POST',
        url: ThemeSetup.ajaxurl, // admin-ajax.php URL
        data: $(this).serialize() + '&action=ajaxcomments', // send form data + action parameter
        beforeSend: function (xhr) {
          // what to do just after the form has been submitted
          button.addClass('loadingform').val('Loading...');
          button.prop('disabled', true);
        },
        error: function (request, status, error) {
          if (status == 500) {
            //   alert('Error while adding comment');
            advancedToaster.create('Error', 'Error while adding comment', {
              type: ToasterType.DANGER,
              timer: ToasterTimer.COUNTDOWN,
              delay: 5000,
            });
          } else if (status == 'timeout') {
            //  alert("Error: Server doesn't respond.");
            advancedToaster.create('Error', "Server doesn't respond", {
              type: ToasterType.DANGER,
              timer: ToasterTimer.COUNTDOWN,
              delay: 5000,
            });
          } else {
            // process WordPress errors
            console.log(request.responseText);
            var wpErrorHtml = request.responseText.split('<p>'),
              wpErrorStr = wpErrorHtml[1].split('</p>');

            advancedToaster.create('Error', wpErrorStr[0], {
              type: ToasterType.DANGER,
              timer: ToasterTimer.COUNTDOWN,
              delay: 5000,
            });
          }
        },
        success: function (addedCommentHTML) {
          $('#comment').val('');
          button.prop('disabled', false);

          advancedToaster.create(
            msgs.reviews[lang].succes,
            msgs.reviews[lang].msg,
            {
              type: ToasterType.SUCCESS,
              timer: ToasterTimer.COUNTDOWN,
              delay: 5000,
            }
          );
        },
        complete: function () {
          // what to do after a comment has been added
          button
            .removeClass('loadingform')
            .val('Post review')
            .prop('disabled', false);
        },
      });
    } else {
      advancedToaster.create(
        msgs.reviews[lang].error,
        msgs.reviews[lang].msgError,
        {
          type: ToasterType.DANGER,
          timer: ToasterTimer.COUNTDOWN,
          delay: 5000,
        }
      );
    }
    return false;
  });
});

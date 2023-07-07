import * as bootstrap from 'bootstrap';
import { ls } from './localstorage';

const AgeValidator = (() => {
  const formValidator = document.getElementById('bushmills-verification-form');
  const formContainer = document.querySelector('.bushmills-verification__form');
  const errorContainer = document.querySelector(
    '.bushmills-verification__error'
  );
  const ageWindow = bootstrap.Offcanvas.getOrCreateInstance(
    '#bushmills-verification'
  );

  const inputs = formValidator.querySelectorAll('input');

  let isAdult = ls.get(`isAdult`) || false;

  const init = () => {
    //|| !ThemeSetup.canRefresh
    if (isBot() || isAdult) return;

    //   window.ageWindow = ageWindow;

    ageWindow.show();

    formValidator.addEventListener('submit', event => {
      event.preventDefault();
      event.stopPropagation();
      validateAge();
    });

    document
      .getElementById('bushmills-verification-return')
      .addEventListener('click', e => {
        e.preventDefault();
        showForm();
      });

    [...inputs].forEach(input => {
      input.addEventListener('input', e => {
        const target = e.target;
        const maxLength = target.maxLength ? target.maxLength : 4;
        const val = target.value;
        const next = document.getElementById(target.dataset.next);
        const min = target.min;
        const max = target.max;

        if (val.length === maxLength) {
          if (parseInt(val) >= min && parseInt(val) <= max) {
            target.classList.remove('invalid');

            if (target.dataset.next === 'submit') {
              formValidator.dispatchEvent(new Event('submit'));
            } else {
              next.focus();
            }
          } else {
            target.classList.add('invalid');
          }
        } else if (val.length > maxLength) {
          target.value = target.value.slice(0, -1);
        }
      });
    });
  };

  const validateInput = () => {};

  const isBot = () => {
    return /bot|googlebot|crawler|spider|robot|crawling|Chrome-Lighthouse/i.test(
      navigator.userAgent
    );
  };

  const showForm = () => {
    formContainer.classList.remove('d-none');
    errorContainer.classList.add('d-none');
  };

  const showError = () => {
    errorContainer.classList.remove('d-none');
    formContainer.classList.add('d-none');
  };

  const validateAge = () => {
    const day = formValidator.querySelector('#inputDay').value;
    const month = formValidator.querySelector('#inputMonth').value;
    const year = formValidator.querySelector('#inputYear').value;

    // console.log(day, month, year);

    const yourAge = calculateAge(new Date(year, month, day));

    if (yourAge >= 18) {
      //   console.log(`Eres mayor tienes ${yourAge} años`);
      ageWindow.hide();
      storeAge(true);
    } else {
      showError();
    }
  };

  const calculateAge = dob => {
    var diff_ms = Date.now() - dob.getTime();
    var age_dt = new Date(diff_ms);

    return Math.abs(age_dt.getUTCFullYear() - 1970);
  };

  const storeAge = validAge => {
    ls.set(`isAdult`, validAge, 864000); //Se recuerda por 10 días en el navegador
  };

  return {
    init: init,
  };
})();
export { AgeValidator };

const langs = document.querySelectorAll('[hreflang]');
const currentLang = ThemeSetup.lang;
const navLang = window.navigator.language;
let langsAvailables = {};

const validateLang = () => {
  const currentLangStr = currentLang;
  const regex = new RegExp(currentLangStr, 'g'); // correct way

  if (!regex.test(navLang)) {
    const [res] = navLang.split('-');

    if (res in langsAvailables) {
      //   console.log(langsAvailables[res]);
      createAlert(res, langsAvailables[res]);
    }
  }
};

const createAlert = (lang, url) => {
  const parentDiv = document.getElementById('masthead').parentNode;
  const header = document.getElementById('masthead');
  var wrapper = document.createElement('div');

  const msgs = {};
  msgs[
    'es'
  ] = `Puedes visitar esta <a href="${url}">página en español</a> o continuar en la versión en inglés`;
  msgs[
    'en'
  ] = `You can visit the <a href="${url}">english version</a> of this page or continue browsing the spanish version`;

  wrapper.innerHTML = `<div class="alert alert-msg alert-dismissible" role="alert"> ${msgs[lang]} <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>`;

  parentDiv.insertBefore(wrapper, header);
};

if (langs) {
  [...langs].forEach(lang => {
    langsAvailables[lang.getAttribute('hreflang')] = lang.getAttribute('href');
  });
  validateLang();
}

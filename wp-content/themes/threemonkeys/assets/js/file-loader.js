const fileLoader = (() => {
  const loadScript = (url, id = '') => {
    return new Promise((resolve, reject) => {
      if (checkId(id)) reject(new Error('Ya existe el script'));
      let script = document.createElement('script');
      script.type = 'text/javascript';
      script.src = url;
      if (id) script.id = id;
      script.addEventListener('load', () => resolve(script), false);
      script.addEventListener('error', () => reject(script), false);
      document.body.appendChild(script);
    });
  };

  const loadCss = (url, id = '') => {
    return new Promise((resolve, reject) => {
      if (checkId(id)) reject(new Error('Ya existe el css'));
      let css = document.createElement('link');
      css.type = 'text/css';
      css.rel = 'stylesheet';
      css.href = url;
      if (id) css.id = id;
      css.addEventListener('load', () => resolve(css), false);
      css.addEventListener('error', () => reject(css), false);
      document['head'].appendChild(css);
    });
  };

  const checkId = id => document.getElementById(id);

  return {
    css: loadCss,
    js: loadScript,
  };
})();

export { fileLoader };

/*
 * Sticky Header
 */
const body = document.body;
const nav = document.querySelector('#header');
const hero = document.querySelector('.hero-home');

const stickyHeaderHeight = document.querySelector(':root');

stickyHeaderHeight.style.setProperty(
  '--sticky-header',
  '' + nav.offsetHeight / 16 + 'rem'
);

const config = {
  root: null,
  rootMargin: '0px 0px 0px',
  threshold: 0,
};

// console.log(hero);
let isHeroVisible = false;
const observer = new IntersectionObserver(function (entries, observer) {
  entries.forEach(entry => {
    // console.log(entry.intersectionRatio);

    if (entry.intersectionRatio === 0) {
      nav.classList.add('isSticky');
      body.classList.add('sticky-active');
      if (typeof $bp === 'function' && $bp('hero-video').isReady) {
        // console.log('Video Hero Pause');
        $bp('hero-video').pause();
      }
    } else {
      nav.classList.remove('isSticky');
      body.classList.remove('sticky-active');

      if (typeof $bp === 'function' && $bp('hero-video').isReady) {
        // console.log('Video Hero Play');
        $bp('hero-video').play();
      }
    }
  });
}, config);

if (hero) {
  observer.observe(hero);

  hero.addEventListener(
    'mouseenter',
    function (event) {
      if (typeof $bp === 'function' && $bp('hero-video').isReady) {
        // console.log('Video Hero Play on Enter');
        $bp('hero-video').play();
      }
    },
    false
  );
  hero.addEventListener(
    'touchstart',
    function (event) {
      if (typeof $bp === 'function' && $bp('hero-video').isReady) {
        // console.log('Video Hero Play on Touch');
        $bp('hero-video').play();
      }
    },
    false
  );
}

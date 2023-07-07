import marquee from 'vanilla-marquee';
import Siema from 'siema';

const Ticker = (() => {
  let timer, ticker;
  const init = params => {
    const tickerContainer = document.querySelector('#ticker__container');
    // ticker = new Siema({
    //   selector: '.ticker__container',
    //   perPage: {
    //     300: 1,
    //     768: 2,
    //     1024: 3,
    //   },
    //   duration: 800,
    //   loop: true,
    //   transition: 'ease-in',
    // });

    // //let timer = setInterval(() => mySiema.next(), 1500);

    // initTimer();
    // tickerContainer.addEventListener('mouseover', resetTimer);
    // tickerContainer.addEventListener('mouseout', initTimer);
    // tickerContainer.addEventListener('touchstart', resetTimer);
    // tickerContainer.addEventListener('touchend', initTimer);
    const scroller = new marquee(tickerContainer, {
      // dulicate the content to create a continuous flow
      duplicated: true,

      // duration of animation
      duration: 5000,

      // space between tickers
      gap: 20,

      // pause on hover
      pauseOnHover: true,

      // re-calculate on window resize
      recalcResize: true,

      // animation speed
      speed: 25,

      // if is visibile from thestart
      startVisible: true,

      direction: 'left',
    });
  };

  const checkIsValidSize = () =>
    window.matchMedia('(min-width: 1024px)').matches;

  const resetTimer = () => {
    if (timer) {
      window.clearTimeout(timer);
    }
  };

  const initTimer = () => {
    let timeInMiliseconds = 1500;

    timer = window.setInterval(() => ticker.next(), timeInMiliseconds);
  };
  return {
    init: init,
  };
})();

export { Ticker };

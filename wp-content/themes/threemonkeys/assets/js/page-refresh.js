const PageRefresh = (() => {
  let timer;
  const canRefresh =
    typeof ThemeSetup.canRefresh === 'undefined' ? true : ThemeSetup.canRefresh;
  const timeToRefresh = ThemeSetup.timeToRefresh || 90;
  const counterID = 'refreshCounter_' + ThemeSetup.current.postID;
  let counter;

  const init = () => {
    if (top === self && canRefresh) {
      counter = window.sessionStorage.getItem(counterID) || 0;
      resetTimer();

      // console.log(counterID, counter);

      counter++;
      window.sessionStorage.setItem(counterID, counter);

      document.querySelector('body').addEventListener('mousemove', resetTimer);
      document.querySelector('body').addEventListener('keydown', resetTimer);
      window.addEventListener('scroll', resetTimer);
    } else {
    }
  };

  const resetTimer = () => {
    if (timer) {
      window.clearTimeout(timer);
    }

    let timeInMiliseconds = timeToRefresh * 1000;

    timer = window.setTimeout(doRefresh, timeInMiliseconds);
  };

  const doRefresh = () => {
    resetTimer();
    if (canRefresh) {
      if (counter < 10 && top === self) {
        location.href = window.location.href;
      }
    }
  };

  return {
    init: init,
  };
})();

PageRefresh.init();

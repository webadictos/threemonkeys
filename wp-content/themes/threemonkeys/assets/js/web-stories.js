const webStories = (() => {
  const player = document.body.querySelector('amp-story-player');
  const lightboxEl = document.querySelector('.stories-lightbox');

  const init = () => {
    if (player.isReady) {
      initializeCarousel();
    } else {
      player.addEventListener('ready', () => {
        initializeCarousel();
      });
    }

    player.addEventListener('amp-story-player-close', () => {
      player.pause();
      lightboxEl.classList.toggle('show');
    });
  };
  const initializeCarousel = () => {
    const stories = player.getStories();

    const webstories = document.querySelectorAll('.web-story-item');

    webstories.forEach((story, idx) => {
      const img = story.querySelector('img');

      img.src = img.dataset.storyPoster;
      img.classList.remove('story-loading');

      story.addEventListener('click', event => {
        const i = stories.find(
          t => t.href === event.currentTarget.dataset.storyUrl
        );
        player.show(i.href);
        player.play();
        lightboxEl.classList.toggle('show');
      });
    });
  };

  return {
    init: init,
  };
})();

webStories.init();

const player = document.body.querySelector('amp-story-player');
const lightboxEl = document.querySelector('.stories-lightbox');

if (player.isReady) {
  initializeCarousel();
} else {
  player.addEventListener('ready', () => {
    initializeCarousel();
  });
}

function initializeCarousel() {
  const stories = player.getStories();

  const webstories = document.querySelectorAll('.web-story-item');

  webstories.forEach((story, idx) => {
    story.addEventListener('click', event => {
      // player.show(stories[idx].href);
      // player.play();
      //lightboxEl.classList.toggle("show");

      const i = stories.find(
        t => t.href === event.currentTarget.dataset.storyUrl
      );
      player.show(i.href);
      player.play();
      lightboxEl.classList.toggle('show');
    });
  });
}

player.addEventListener('amp-story-player-close', () => {
  player.pause();
  lightboxEl.classList.toggle('show');
});

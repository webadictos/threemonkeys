import { fileLoader } from './file-loader';

const youtubePlayer = (() => {
  // const carouselVideos = document.getElementById('carouselVideos');
  // let canSlide = true;
  const videoPlayers = {};

  const init = () => {
    loadYouTubeAPI();
  };

  const loadYouTubeAPI = () => {
    fileLoader
      .js('https://www.youtube.com/iframe_api', 'youtube-api')
      .then(script => {
        console.log('Youtube API Loaded');
        initCarousel();
      });
  };

  const initCarousel = () => {
    const videoPlayers = document.querySelectorAll('.video-thumbnail');

    [...videoPlayers].forEach(player => {
      player.addEventListener('click', prepareVideo);
    });
  };

  const prepareVideo = e => {
    const currentPlayer = e.currentTarget;
    const currentYouTubeID = currentPlayer.dataset.youtubeId;
    const currentVideoPlayer = document
      .getElementById(currentYouTubeID)
      .closest('.video-player');

    pauseVideos();

    if (currentYouTubeID in videoPlayers) {
      //   console.log('Ya se reprodujo ese video');
      return false;
    }

    console.log('Playing video', currentYouTubeID);

    const player = new YT.Player(currentYouTubeID, {
      videoId: currentYouTubeID,
      playerVars: {
        height: '100%',
        width: '100%',
        enablejsapi: 1,
        autoplay: 1,
        playsinline: 1,
        modestbranding: 1,
        showinfo: 0,
        rel: 0,
        origin: 'https://instyle.mx',
        wmode: 'transparent',
        iv_load_policy: '3',
      },
      events: {
        onReady: playVideo,
      },
    });

    currentVideoPlayer.classList.toggle('video-play');

    player.addEventListener('onStateChange', function (state) {
      switch (state.data) {
        case YT.PlayerState.PLAYING:
          ThemeSetup.canRefresh = false;
          break;

        case YT.PlayerState.ENDED:
          ThemeSetup.canRefresh = true;
        case YT.PlayerState.PAUSED:
          ThemeSetup.canRefresh = false;
          break;
      }
    });

    videoPlayers[currentYouTubeID] = player;
  };

  const playVideo = e => {
    //   function onPlayerReady(event) {

    e.target.playVideo();
    //   }
  };

  const pauseVideos = () => {
    Object.keys(videoPlayers).forEach(player => {
      //   console.log(player);
      videoPlayers[player].pauseVideo();
    });
  };

  return {
    init: init,
  };
})();

youtubePlayer.init();

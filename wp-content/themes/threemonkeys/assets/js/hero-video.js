import { fileLoader } from './file-loader';

const videoContainer = 'hero-video';
let videoLandscapeID = '1192922';
let videoPortraitID = '1192923';
const heroVideo = document.getElementById('hero-video');
const isLandscape = window.matchMedia('(orientation: landscape)');
const videoConfig = {
  id: videoLandscapeID,
  width: 16,
  height: 9,
};
let isLoaded = false;

if (heroVideo) {
  videoLandscapeID = heroVideo.dataset.videoLandscape
    ? heroVideo.dataset.videoLandscape
    : videoLandscapeID;

  videoPortraitID = heroVideo.dataset.videoPortrait
    ? heroVideo.dataset.videoPortrait
    : videoPortraitID;

  fileLoader
    .js('//services.brid.tv/player/build/brid.min.js', 'brid-tv')
    .then(script => {
      let orientation = 'landscape';

      if (!isLandscape.matches) {
        orientation = 'portrait';
      }

      showVideo(orientation);

      isLandscape.addEventListener('change', event => {
        let orientation = 'landscape';

        if (!event.matches) {
          orientation = 'portrait';
        }

        showVideo(orientation);
      });
    });
}

const showVideo = (orientation = 'landscape') => {
  if (isLoaded) {
    $bp(videoContainer).destroy(true);
  }

  if (orientation === 'portrait') {
    videoConfig.id = videoPortraitID;
    videoConfig.height = 16;
    videoConfig.width = 9;
  } else {
    videoConfig.id = videoLandscapeID;
    videoConfig.height = 9;
    videoConfig.width = 16;
  }

  $bp(
    videoContainer,
    {
      id: '38187',
      width: videoConfig.width,
      height: videoConfig.height,
      video: videoConfig.id,
      video_source: 'fhd',
    },
    () => {
      isLoaded = true;
      $bp(videoContainer).muted(true);
    }
  );
};

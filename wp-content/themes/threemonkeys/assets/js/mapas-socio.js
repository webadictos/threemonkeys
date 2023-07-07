import { Loader } from '@googlemaps/js-api-loader';
import { MarkerClusterer } from '@googlemaps/markerclusterer';

let location = {};
let map = {};
let markers = [];
const themeUri = ThemeSetup.themeUri;
const iconLocation = `${themeUri}/assets/images/my-location.png`;
const iconMarker = `${themeUri}/assets/images/marker.png`;

const maps = document.querySelectorAll('.mapa-socio');
const mapApiKey = ThemeSetup.map.key || '';

const loader = new Loader({
  apiKey: mapApiKey,
  version: 'weekly',
  // libraries: ['places'],
});

const mapOptions = {
  center: {
    lat: 19.4129173, //location.coords.latitude ? location.coords.latitude : 19.4129173,
    lng: -99.2213188, //location.coords.longitude ? location.coords.longitude : -99.2213188,
  },
  zoom: 17,
};

[...maps].map(item => {
  //console.log(item.dataset);

  loader
    .load()
    .then(google => {
      if (item.dataset.lat) {
        mapOptions.center.lat = parseFloat(item.dataset.lat);
      }
      if (item.dataset.long) {
        mapOptions.center.lng = parseFloat(item.dataset.long);
      }

      //console.log(mapOptions);

      map = new google.maps.Map(item, mapOptions);

      if (item.dataset.lat) {
        const marker = new google.maps.Marker({
          position: mapOptions.center,
          map,
          title: item.dataset.title,
          icon: iconMarker,
        });

        marker.addListener('click', () => {
          const locationWindow = window.open(
            `https://www.google.com/maps/dir/Current+Location/${item.dataset.lat},${item.dataset.long}`
          );
        });
      }
    })
    .catch(e => {
      // do something
    });
});

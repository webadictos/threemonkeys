import { Loader } from '@googlemaps/js-api-loader';
import autoComplete from '@tarekraafat/autocomplete.js';
import { ls } from './localstorage';

//import { MarkerClusterer } from '@googlemaps/markerclusterer';
/**
 * // Opción 1 -> sessionStorage.getItem(name, content)
// Opción 2 -> sessionStorage.name

 */
let location = {};
let map = {};
let markers = {};
let mapLoaded = false;
const currentLang = ThemeSetup.lang || 'es';

console.log(currentLang);

let listOfSocios = ls.get(`listOfSocios_${currentLang}`) || [];
let firstLoad = false;
const resultsContainer = document.querySelector(
  '#buscador-results .buscador-results-container'
);
const formBuscador = document.getElementById('buscador-form');

let autoCompleteConfig = {
  selector: '#mapsearch',
  placeHolder: 'Buscar socios...',
  data: {
    src: [
      'Sauce - Thousand Island',
      'Wild Boar - Tenderloin',
      'Goat - Whole Cut',
    ],
  },
  resultItem: {
    highlight: {
      render: true,
    },
  },
};
const themeUri = ThemeSetup.themeUri;
const iconLocation =
  ThemeSetup.map.location || `${themeUri}/assets/images/my-location.png`;
const iconMarker =
  ThemeSetup.map.marker || `${themeUri}/assets/images/marker.png`;
const mapApiKey = ThemeSetup.map.key || '';
const mapCenter = ThemeSetup.map.center || {
  lat: 19.4326018,
  lng: -99.1332049,
};

navigator.geolocation.getCurrentPosition(
  res => {
    location = res;
    addMyLocationToMap();
  },
  err => console.log(err)
);

const initGoogle = () => {
  const loader = new Loader({
    apiKey: mapApiKey,
    version: 'weekly',
  });

  const mapOptions = {
    center: mapCenter,
    zoom: 14,
    styles: [
      {
        featureType: 'poi',
        elementType: 'labels',
        stylers: [
          {
            visibility: 'off',
          },
        ],
      },
      { featureType: 'transit', stylers: [{ visibility: 'off' }] },
    ],
    mapTypeControl: false,
    streetViewControl: false,
  };
  loader
    .load()
    .then(google => {
      map = new google.maps.Map(
        document.getElementById('buscador-map'),
        mapOptions
      );

      google.maps.event.addListener(map, 'idle', () => {
        mapLoaded = true;
        //    r

        if (!firstLoad) {
          resultsContainer.innerHTML = '';

          if (listOfSocios.length === 0) {
            console.log('No hay cache');
            getSocios();
          } else {
            console.log('Si hay cache');
            addSocioInToMap(listOfSocios);
          }
          firstLoad = true;

          autoCompleteConfig = {
            selector: '#mapsearch',
            placeHolder: 'Buscar socios...',
            data: {
              src: listOfSocios,
              keys: ['gg_socio_title'],
              cache: true,
              filter: list => {
                // Filter duplicates
                // incase of multiple data keys usage
                const filteredResults = Array.from(
                  new Set(list.map(value => value.match))
                ).map(gg_socio_title => {
                  return list.find(value => value.match === gg_socio_title);
                });

                return filteredResults;
              },
            },
            resultsList: {
              element: (list, data) => {
                const info = document.createElement('p');
                if (data.results.length) {
                  info.innerHTML = `Displaying <strong>${data.results.length}</strong> out of <strong>${data.matches.length}</strong> results`;
                } else {
                  info.innerHTML = `Found <strong>${data.matches.length}</strong> matching results for <strong>"${data.query}"</strong>`;
                }
                list.prepend(info);
              },
              noResults: true,
              maxResults: 15,
              tabSelect: true,
            },

            resultItem: {
              highlight: {
                render: true,
              },
            },
          };

          const autoCompleteJS = new autoComplete(autoCompleteConfig);
        }
      });

      google.maps.event.addListener(map, 'center_changed', () => {
        addSocioInToMap(listOfSocios);
      });

      google.maps.event.addListener(map, 'zoom_changed', () => {
        addSocioInToMap(listOfSocios);
      });
    })
    .catch(e => {
      // do something
    });
};

const addMyLocationToMap = () => {
  if (location) {
    var idVar = setInterval(() => {
      if (mapLoaded) {
        clearInterval(idVar);
        let myLocation = {
          lat: location.coords.latitude,
          lng: location.coords.longitude,
        };
        const MyLocationMarker = new google.maps.Marker({
          position: myLocation,
          animation: google.maps.Animation.BOUNCE,
          map,
          title: 'Estas aquí',
          icon: iconLocation,
        });

        MyLocationMarker.addListener('click', () => {
          MyLocationMarker.setAnimation(null);
        });

        // map.setCenter(myLocation);
      }
    }, 100);
  }
};

const API = `${window.location.protocol}//${window.location.hostname}/wp-json/wp/v2/gg_socio?context=view&per_page=100&orderby=title&order=asc&_fields=id,gg_socio_latlng,gg_category,gg_features,gg_type,gg_socio_image,gg_socio_map_card,gg_socio_card,gg_socio_title&lang=${currentLang}`;
let pagination = 1;

const fetchSocios = async () => {
  try {
    const response = await fetch(API + '&page=' + pagination);
    return response;
  } catch (error) {
    return new Error('Error al cargar los socios', error);
  }
};

const addSociosToMap = () => {};

const getSocios = async () => {
  try {
    const socios = await fetchSocios();
    const sociosData = await socios.json();
    const sociosTotalPages = socios.headers.get('X-WP-TotalPages');
    // addSocioInToMap(sociosData);

    listOfSocios = listOfSocios.concat(sociosData);

    console.log('Numero:', listOfSocios.length);
    console.log('Socios', listOfSocios);

    if (pagination < sociosTotalPages) {
      pagination++;
      getSocios();
    } else {
      optimizeSociosDataToLocal(listOfSocios);
    }

    // console.log('Pagina', pagination);

    //ls.set('listOfSocios', listOfSocios, 3600);
    // sessionStorage.listOfSocios = JSON.stringify(listOfSocios);

    addSocioInToMap(listOfSocios);
    // const markerCluster = new MarkerClusterer({ map, markers });
    // markerCluster.setMap(map);

    //   mymap.addLayer(markers);
  } catch (error) {
    console.log(error);
  }
};

const optimizeSociosDataToLocal = sociosData => {
  const sociosTmp = [];

  sociosData.forEach(socio => {
    const sociosDataTmp = (({
      id,
      gg_socio_title,
      gg_category,
      gg_location,
      gg_features,
      gg_payments,
      gg_prices,
      gg_socio_card,
      gg_socio_image,
      gg_socio_map_card,
      gg_type,
      gg_socio_latlng,
    }) => ({
      id,
      gg_socio_title,
      gg_category,
      gg_location,
      gg_features,
      gg_payments,
      gg_prices,
      gg_socio_card,
      gg_socio_image,
      gg_socio_map_card,
      gg_type,
      gg_socio_latlng,
    }))(socio);

    sociosTmp.push(sociosDataTmp);
  });

  ls.set(`listOfSocios_${currentLang}`, sociosTmp, 3600);
};

const addSocioInToMap = data => {
  //console.log('DATA', data);
  data.forEach(element => {
    // console.log('EL:', element);

    if (markerExists(element.id)) {
      addMarkerIfInBouds(markers[element.id], element);
    } else {
      const latitud = parseFloat(element.gg_socio_latlng.lat);
      const longitud = parseFloat(element.gg_socio_latlng.lng);

      const socioInfo = element.gg_socio_map_card;

      const infowindow = new google.maps.InfoWindow({
        content: socioInfo,
      });

      const marker = new google.maps.Marker({
        position: {
          lat: latitud, //location.coords.latitude,
          lng: longitud, //location.coords.longitude,
        },
        visible: false,
        map,
        title: element.gg_socio_title || '',
        icon: iconMarker,
        infowindow: infowindow,
      });

      marker.addListener('click', () => {
        hideAllInfoWindows();
        infowindow.open({
          anchor: marker,
          map,
          shouldFocus: false,
        });
        // map.setCenter(marker.getPosition());
        if (map.getZoom() < 14) map.setZoom(14);

        resultsContainer
          .querySelector(`[data-socio-id="${element.id}"]`)
          .scrollIntoView({
            behavior: 'smooth',
            block: 'nearest',
            inline: 'start',
          });
      });
      markers[element.id] = marker;

      addMarkerIfInBouds(marker, element);
    }

    //markers.push(marker);
  });
  getMarkersInfo();
};

const markerExists = id => {
  return markers[id] ? true : false;
};

const addMarkerIfInBouds = (marker, element) => {
  let socioItem = resultsContainer.querySelector(
    `[data-socio-id="${element.id}"]`
  );

  if (map.getBounds().contains(marker.getPosition())) {
    // console.log('Map', marker.getVisible());
    // console.log('Esta Cerca');
    // marker.setMap(map);

    if (!marker.getVisible()) {
      marker.setVisible(true);

      if (!socioItem || !socioItem.dataset.isVisible) {
        const fragment = document
          .createRange()
          .createContextualFragment(element.gg_socio_card);

        resultsContainer.appendChild(fragment);

        socioItem = resultsContainer.querySelector(
          `[data-socio-id="${element.id}"]`
        );

        socioItem.addEventListener('click', e => {
          hideAllInfoWindows();

          map.setCenter(marker.getPosition());
          map.setZoom(16);
          marker.infowindow.open({
            anchor: marker,
            map,
            shouldFocus: false,
          });
        });

        socioItem.dataset.isVisible = true;
      } else {
        console.log(socioItem);
      }
    }
  } else {
    // console.log('No esta cerca');
    // if (socioItem) {
    //   socioItem.parentElement.removeChild(socioItem);
    //   console.log('Eliminando item');
    // }
  }
};

const hideAllInfoWindows = () => {
  Object.entries(markers).forEach(([id, marker]) => {
    marker.infowindow.close(map, marker);
  });
};

const getMarkersInfo = () => {
  let visibles = 0;
  let hidden = 0;
  Object.entries(markers).forEach(([id, marker]) => {
    if (marker.getVisible()) {
      visibles++;
    } else {
      hidden++;
    }
  });
  console.log('Visibles:', visibles);
  console.log('Hidden:', hidden);
};

const showNearestToMe = e => {
  e.preventDefault();
  console.log('Showing Nearest');

  navigator.geolocation.getCurrentPosition(
    res => {
      location = res;
      let myLocation = {
        lat: location.coords.latitude,
        lng: location.coords.longitude,
      };
      map.setCenter(myLocation);
    },
    err => console.log(err)
  );
};

document
  .querySelector('[data-wa-near="1"]')
  .addEventListener('click', showNearestToMe);

initGoogle();

// The autoComplete.js Engine instance creator
formBuscador.addEventListener('submit', event => {
  event.preventDefault();
  // const entries = Object.fromEntries(new FormData(event.target).entries());
  const data = new FormData(event.target);

  const value = Object.fromEntries(data.entries());

  console.log(value);

  value.gg_category = data.getAll('gg_category');
  value.gg_type = data.getAll('gg_type');
  value.gg_prices = data.getAll('gg_prices');
  value.gg_features = data.getAll('gg_features');

  console.log({ value });
});

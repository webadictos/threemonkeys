import { Loader } from '@googlemaps/js-api-loader';
// import autoComplete from '@tarekraafat/autocomplete.js';
import { ls } from './localstorage';
import {
  getDistanceFromLatLonInKm,
  showSpinner,
  hideSpinner,
  mapAlert,
} from './map-utils';
// import {
//   Toaster,
//   ToasterPosition,
//   ToasterTimer,
//   ToasterType,
// } from 'bs-toaster';
// import { data } from 'jquery';
//import { MarkerClusterer } from '@googlemaps/markerclusterer';

const MapaGG = (() => {
  let location = {};
  let map = {};
  let markers = {};
  let mapLoaded = false;
  let canRefresh = true;
  let isFromSearch = false;
  let MyLocationMarker;
  const mapSelector = 'locations-map';
  const currentLang = ThemeSetup.lang || 'es';

  let listOfSocios = ls.get(`listOfSocios_${currentLang}`) || [];
  let totalSocios = ls.get(`totalSocios_${currentLang}`) || 0;

  let firstLoad = false;
  const resultsContainer = document.querySelector(
    '#buscador-results .buscador-results-container'
  );

  const formBuscador = document.getElementById('buscador-form');
  const themeUri = ThemeSetup.themeUri;
  const iconLocation =
    ThemeSetup.map.location || `${themeUri}/assets/images/my-location.png`;
  const iconMarker =
    ThemeSetup.map.marker || `${themeUri}/assets/images/marker.png`;
  const mapApiKey = ThemeSetup.map.key || '';
  let mapCenter = ThemeSetup.map.center || {
    lat: 19.4326018,
    lng: -99.1332049,
  };
  const API = `${window.location.protocol}//${window.location.hostname}/wp-json/wp/v2/gg_socio?context=view&per_page=100&orderby=title&order=asc&_fields=id,gg_socio_latlng,gg_category,gg_features,gg_type,gg_payments,gg_prices,gg_socio_image,gg_socio_map_card,gg_socio_card,gg_socio_title&lang=${currentLang}`;
  let pagination = 1;

  const advancedToaster = new Toaster({
    position: ToasterPosition.TOP_END,
    type: ToasterType.DEFAULT,
    delay: 5000,
    timer: ToasterTimer.COUNTDOWN,
    animation: true,
    defaultIconMarkup: `<i class="fas fa-bolt me-2"></i>`,
  });

  const init = () => {
    //Inicializa mapa del buscador en Home
    if (document.getElementById(mapSelector)) {
      initGoogle();
    }
    /**
     * Llamamos a los socios, la función debe detectar si hay cache o no
     */
    //getSocios();
    document.body.addEventListener('is.socios-loaded', e => {
      mapAlert(gg_translate.loaded.replace('%d', e.detail.total));

      //mapAlert(`Se cargaron <strong>${e.detail.total}</strong> socios`);

      initAutoComplete();
      hideSpinner();
    });

    document.body.addEventListener('is.before-loading-socios', e => {
      showSpinner();
    });

    formBuscador.addEventListener('submit', event => {
      event.preventDefault();
      setMapOnAll(null);

      isFromSearch = true;
      addSocioInToMap(listOfSocios);
    });

    if (document.querySelector('[data-wa-near="1"]')) {
      document
        .querySelectorAll('[data-wa-near="1"]')
        .forEach(item => item.addEventListener('click', showNearestToMe));
    }

    if (document.getElementById('reset-filters-btn')) {
      document
        .getElementById('reset-filters-btn')
        .addEventListener('click', event => {
          event.preventDefault();
          resetFilters(event);
        });
    }

    formBuscador.querySelectorAll('input[type=checkbox]').forEach(item => {
      item.addEventListener('change', function () {
        setMapOnAll(null);
        if (this.checked) {
          addFilterLabel(this);
        } else {
          removeFilterLabel(this);
        }
        if (canRefresh) {
          isFromSearch = true;
          addSocioInToMap(listOfSocios);
        }
      });
    });
  };

  const resetFilters = e => {
    const checkboxes = formBuscador.querySelectorAll('input[type=checkbox]');
    let checkedBoxes = [];

    checkboxes.forEach(item => {
      if (item.checked) {
        checkedBoxes.push(item);
        //item.click();
      }
    });

    canRefresh = false;
    if (document.querySelector('#mapsearch')) {
      document.querySelector('#mapsearch').value = '';
    }
    checkedBoxes.forEach((checkbox, idx, arr) => {
      if (idx === arr.length - 1) {
        // console.log('Last callback call at index ' + idx + ' with value ');
        canRefresh = true;
      }
      checkbox.click();
    });
    canRefresh = true;

    const filterBar = e.target.parentElement;
    map.setZoom(15);
    map.setCenter(mapCenter);

    filterBar.classList.remove('show');
  };

  const addFilterLabel = item => {
    if (document.querySelector(`label_${item.id}`)) return;

    const filterLabel = document.createElement('button');
    filterLabel.classList.add('btn');
    filterLabel.classList.add('btn-primary');
    filterLabel.classList.add('active-filter');
    filterLabel.id = `label_${item.id}`;

    filterLabel.innerHTML = item.dataset.name;

    const labelContainer = formBuscador.querySelector(
      '.buscador__form--active-filters'
    );

    if (!labelContainer.classList.contains('show')) {
      labelContainer.classList.add('show');
    }

    labelContainer.appendChild(filterLabel);

    filterLabel.addEventListener('click', () => {
      item.click();
    });
  };

  const removeFilterLabel = item => {
    if (document.querySelector(`#label_${item.id}`)) {
      const label = document.querySelector(`#label_${item.id}`);
      label.parentElement.removeChild(label);
    }
    // e.parentElement.removeChild(e);
  };

  const getFormData = () => {
    const data = new FormData(formBuscador); //new FormData(event.target);

    const value = Object.fromEntries(data.entries());

    value.gg_category = data.getAll('gg_category');
    value.gg_type = data.getAll('gg_type');
    value.gg_prices = data.getAll('gg_prices');
    value.gg_features = data.getAll('gg_features');
    value.gg_payments = data.getAll('gg_payments');

    return value;
  };

  const initGoogle = () => {
    const loader = new Loader({
      apiKey: mapApiKey,
      version: 'weekly',
    });

    const mapOptions = {
      center: mapCenter,
      zoom: 15,
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
          document.getElementById(mapSelector),
          mapOptions
        );

        google.maps.event.addListener(map, 'idle', () => {
          mapLoaded = true;
          //    r

          if (!firstLoad) {
            resultsContainer.innerHTML = '';

            if (
              listOfSocios.length === 0 ||
              listOfSocios.length < totalSocios
            ) {
              listOfSocios = [];
              dispatchBeforeSociosLoadedEvent();
              getSocios();
            } else {
              addSocioInToMap(listOfSocios);
              dispatchSociosLoadedEvent(listOfSocios.length);
            }
            firstLoad = true;
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

  const initAutoComplete = () => {
    const autoCompleteConfig = {
      selector: '#mapsearch',
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
        noResults: true,
        maxResults: 15,
        tabSelect: true,
      },

      resultItem: {
        highlight: {
          render: true,
        },
      },
      events: {
        input: {
          selection: event => {
            const feedback = event.detail;
            const socio = event.detail.selection.value;
            const selection = event.detail.selection.value.gg_socio_title;
            autoCompleteJS.input.value = selection;

            if (markerExists(socio.id)) {
              const marker = markers[socio.id];

              hideAllInfoWindows();

              map.setCenter(marker.getPosition());
              map.setZoom(16);
              marker.infowindow.open({
                anchor: marker,
                map,
                shouldFocus: false,
              });

              resultsContainer
                .querySelector(`[data-socio-id="${socio.id}"]`)
                .scrollIntoView({
                  behavior: 'smooth',
                  block: 'nearest',
                  inline: 'start',
                });
            }
          },
        },
      },
    };

    const autoCompleteJS = new autoComplete(autoCompleteConfig);
  };

  const filterData = data => {
    const filters = getFormData();

    let filteredData = data;

    // console.log(filters);

    if (filters.mapsearch !== '') {
      filteredData = filteredData.filter(item => {
        return item.gg_socio_title
          .toLowerCase()
          .includes(filters.mapsearch.toLowerCase());
      });
    }
    if (filters.gg_category.length > 0) {
      filteredData = filteredData.filter(item => {
        return filters.gg_category.some(i =>
          item.gg_category.includes(parseFloat(i))
        );
      });
    }

    if (filters.gg_features.length > 0) {
      filteredData = filteredData.filter(item => {
        return filters.gg_features.some(i =>
          item.gg_features.includes(parseFloat(i))
        );
      });
    }

    if (filters.gg_prices.length > 0) {
      filteredData = filteredData.filter(item => {
        return filters.gg_prices.some(i =>
          item.gg_prices.includes(parseFloat(i))
        );
      });
    }
    if (filters.gg_payments.length > 0) {
      filteredData = filteredData.filter(item => {
        return filters.gg_payments.some(i =>
          item.gg_payments.includes(parseFloat(i))
        );
      });
    }
    if (filters.gg_type.length > 0) {
      filteredData = filteredData.filter(item => {
        return filters.gg_type.some(i => item.gg_type.includes(parseFloat(i)));
      });
    }

    return filteredData;
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

          if (typeof MyLocationMarker === 'object') {
            MyLocationMarker.setMap(null);
            MyLocationMarker = null;
          }

          MyLocationMarker = new google.maps.Marker({
            position: myLocation,
            animation: google.maps.Animation.BOUNCE,
            map,
            title: 'Estas aquí',
            icon: iconLocation,
          });

          MyLocationMarker.addListener('click', () => {
            MyLocationMarker.setAnimation(null);
          });
          mapCenter = myLocation;
          map.setCenter(myLocation);

          setTimeout(function () {
            MyLocationMarker.setAnimation(null);
          }, 1000);

          document.getElementById(mapSelector).scrollIntoView({
            behavior: 'smooth',
            block: 'nearest',
            inline: 'start',
          });
        }
      }, 100);
    }
  };

  const fetchSocios = async () => {
    try {
      const response = await fetch(API + '&page=' + pagination);
      return response;
    } catch (error) {
      return new Error('Error al cargar los socios', error);
    }
  };

  const getSocios = async () => {
    try {
      const socios = await fetchSocios();
      const sociosData = await socios.json();
      const sociosTotalPages = socios.headers.get('X-WP-TotalPages');
      const sociosTotal = socios.headers.get('X-WP-Total');

      listOfSocios = listOfSocios.concat(sociosData);

      ls.set(`totalSocios_${currentLang}`, sociosTotal, 3600);

      // console.log('Total:', sociosTotal);
      // console.log('Numero:', listOfSocios.length);
      // console.log('Socios', listOfSocios);

      if (pagination < sociosTotalPages) {
        pagination++;
        getSocios();
      } else {
        dispatchSociosLoadedEvent(sociosTotal);
      }

      processSocios(listOfSocios);

      addSocioInToMap(listOfSocios);
    } catch (error) {
      console.log(error);
    }
  };

  const dispatchSociosLoadedEvent = sociosTotal => {
    const sociosLoaded = new CustomEvent('is.socios-loaded', {
      detail: {
        total: sociosTotal,
      },
    });
    document.querySelector('body').dispatchEvent(sociosLoaded);
  };

  const dispatchBeforeSociosLoadedEvent = () => {
    const beforeSociosLoaded = new CustomEvent('is.before-loading-socios', {
      detail: {
        loading: true,
      },
    });
    document.querySelector('body').dispatchEvent(beforeSociosLoaded);
  };

  const processSocios = sociosData => {
    const sociosTmp = [];

    sociosData.forEach(socio => {
      const distanceFromCenter = getDistanceFromLatLonInKm(
        mapCenter.lat,
        mapCenter.lng,
        socio.gg_socio_latlng.lat,
        socio.gg_socio_latlng.lng
      );

      socio.distance = distanceFromCenter;
    });

    sociosData.sort((a, b) => a.distance - b.distance);

    listOfSocios = sociosData;
    try {
      ls.set(`listOfSocios_${currentLang}`, sociosData, 3600);
    } catch (error) {
      console.error('No se pudo guardar la cache local', error);
    }
  };

  const addSocioInToMap = data => {
    if (!canRefresh) return;
    const filteredData = filterData(data);

    filteredData.forEach(element => {
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
    if (isFromSearch) {
      showTotalResults(filteredData);
      isFromSearch = false;
    }
    //now fit the map to the newly inclusive bounds
    // getMarkersInfo(filteredData);
  };

  const showTotalResults = results => {
    if (results.length > 0) {
      // advancedToaster.create(
      //   'Encontrados',
      //   `Se encontraron ${results.length} socios`
      // );
      mapAlert(gg_translate.search_results.replace('%d', results.length));

      // mapAlert(`Se encontraron <strong>${results.length}</strong> socios`);
    } else {
      // advancedToaster.create(
      //   'Sin resultados',
      //   `No encontraron socios con tus parámetros de búsqueda`
      // );
      mapAlert(gg_translate.not_found);
    }

    document.getElementById(mapSelector).scrollIntoView({
      behavior: 'smooth',
      block: 'nearest',
      inline: 'start',
    });
  };

  const markerExists = id => {
    return markers[id] ? true : false;
  };

  const addMarkerIfInBouds = (marker, element) => {
    let socioItem = resultsContainer.querySelector(
      `[data-socio-id="${element.id}"]`
    );
    if (map.getBounds().contains(marker.getPosition())) {
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
          // console.log(socioItem);
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

  // Sets the map on all markers in the array.
  const setMapOnAll = mapa => {
    // console.log('ocultando', markers);
    Object.entries(markers).forEach(([id, marker]) => {
      marker.setVisible(false);
      marker.infowindow.close(map, marker);
      resultsContainer.innerHTML = '';
    });
  };

  const getMarkersInfo = data => {
    let visibles = 0;
    let hidden = 0;
    let maxItems = 10;
    let numItems = 0;
    const items = data.length;

    Object.entries(markers).forEach(([id, marker]) => {
      if (marker.getVisible()) {
        visibles++;
        if (numItems <= maxItems) {
          numItems++;
        }
      } else {
        hidden++;
      }
    });

    // var bounds = new google.maps.LatLngBounds();

    // //Center map and adjust Zoom based on the position of all markers.
    // map.setCenter(latlngbounds.getCenter());

    //console.log('Visibles:', visibles);
    //console.log('Hidden:', hidden);
  };

  const showNearestToMe = e => {
    e.preventDefault();

    advancedToaster.create(gg_translate.location, gg_translate.get_location, {
      delay: 2000,
    });

    navigator.geolocation.getCurrentPosition(
      res => {
        location = res;
        ls.set(`lastLocation`, location, 3600);
        addMyLocationToMap();
      },
      error => {
        advancedToaster.create('Error', error.message);
        console.log(error);
      }
    );
  };

  return {
    init: init,
  };
})();

export { MapaGG };

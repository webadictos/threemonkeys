import { Loader } from '@googlemaps/js-api-loader';
// import autoComplete from '@tarekraafat/autocomplete.js';
import { ls } from './localstorage';
import {
  getDistanceFromLatLonInKm,
  showSpinner,
  hideSpinner,
  mapAlert,
  getCurrentLocation,
  getLocationByIp,
} from './map-utils';

const MapaGG = (() => {
  let location = {};
  let map = {};
  let markers = {};
  let mapLoaded = false;
  let canRefresh = true;
  let isFromSearch = false;
  let MyLocationMarker;
  const mapSelector = 'locations-map';
  const currentLang = 'es';

  let listOfSocios = ls.get(`listOfSocios`) || [];
  let totalSocios = ls.get(`totalSocios`) || 0;

  let firstLoad = false;
  const resultsContainer = document.querySelector('#locations-items-list');
  const formBuscador = document.getElementById('form-locations');
  const actionSelector = document.querySelector(
    '.container-locations__title--action'
  );
  const themeUri = ThemeSetup.themeUri;
  const iconLocation =
    ThemeSetup.map.location || `${themeUri}/assets/images/my-location.png`;
  const iconMarker =
    ThemeSetup.map.marker || `${themeUri}/assets/images/marker.png`;
  const iconMarkerActive =
    ThemeSetup.map.markerActive || `${themeUri}/assets/images/marker.png`;
  const mapApiKey = ThemeSetup.map.key || '';
  let mapCenter = ThemeSetup.map.center || {
    lat: 19.4326018,
    lng: -99.1332049,
  };
  const initialZoom = parseInt(ThemeSetup.map.zoom) || 13;

  const API = `${window.location.protocol}//${window.location.hostname}/wp-json/bushmills/v1/get_locations`;
  let pagination = 1;

  const init = () => {
    //Inicializa mapa del buscador en Home
    if (document.getElementById(mapSelector)) {
      initGoogle();
      // showSpinner('Obteniendo ubicación...');
      // getCurrentLocation()
      //   .then(position => {
      //     //console.log('My real location', position);

      //     setLocation({
      //       lat: position.coords.latitude,
      //       lng: position.coords.longitude,
      //     });
      //   })
      //   .catch(err => {
      //     console.error(err.message);
      //     getLocationByIp()
      //       .then(position => {
      //         //  console.log('Aproximate location', position);
      //         setLocation(position);
      //       })
      //       .catch(e =>
      //         console.log('Error al obtener la ubicación aproximada')
      //       );
      //   });
    }

    document.body.addEventListener('is.socios-loaded', e => {
      hideSpinner();
      showTotalResults(parseInt(e.detail.total));
    });

    document.body.addEventListener('is.before-loading-socios', e => {
      showTotalResults(0);
      addMyLocationToMap();
      showSpinner('Obteniendo lugares cercanos...');
    });

    formBuscador.querySelectorAll('input[type=radio]').forEach(item => {
      item.addEventListener('change', function () {
        setMapOnAll(null);
        // if (this.checked) {
        //   addFilterLabel(this);
        // } else {
        //   removeFilterLabel(this);
        // }
        // if (canRefresh) {
        //   isFromSearch = true;
        //   addSocioInToMap(listOfSocios);
        // }
        getLocations();
      });
    });
  };

  const initGoogle = () => {
    const loader = new Loader({
      apiKey: mapApiKey,
      version: 'weekly',
    });

    const mapOptions = {
      center: mapCenter,
      zoom: initialZoom,
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

            // if (
            //   listOfSocios.length === 0 ||
            //   listOfSocios.length < totalSocios
            // ) {
            //   listOfSocios = [];
            //   dispatchBeforeSociosLoadedEvent();
            //   getSocios();
            // } else {
            //   addSocioInToMap(listOfSocios);
            //   dispatchSociosLoadedEvent(listOfSocios.length);
            // }
            firstLoad = true;
          }
        });

        console.log('Map loaded');
        showSpinner('Obteniendo ubicación...');
        getCurrentLocation()
          .then(position => {
            //console.log('My real location', position);

            setLocation({
              lat: position.coords.latitude,
              lng: position.coords.longitude,
            });
          })
          .catch(err => {
            console.error(err.message);
            getLocationByIp()
              .then(position => {
                //  console.log('Aproximate location', position);
                setLocation(position);
              })
              .catch(e =>
                console.log('Error al obtener la ubicación aproximada')
              );
          });

        // google.maps.event.addListener(map, 'center_changed', () => {
        //   addSocioInToMap(listOfSocios);
        // });

        // google.maps.event.addListener(map, 'zoom_changed', () => {
        //   addSocioInToMap(listOfSocios);
        // });
      })
      .catch(e => {
        // do something
      });
  };

  const setLocation = position => {
    location = position;
    ls.set(`lastLocation`, location, 3600);

    hideSpinner();
    addMyLocationToMap();
    getLocations();
  };

  const filterData = data => {
    // const filters = getFormData();

    let filteredData = data;

    return filteredData;
  };

  const addMyLocationToMap = () => {
    if (location) {
      var idVar = setInterval(() => {
        if (mapLoaded) {
          clearInterval(idVar);

          if (typeof MyLocationMarker === 'object') {
            MyLocationMarker.setMap(null);
            MyLocationMarker = null;
          }

          MyLocationMarker = new google.maps.Marker({
            position: location,
            animation: google.maps.Animation.BOUNCE,
            map,
            title: 'Estas aquí',
            icon: {
              url: iconLocation,
              scaledSize: new google.maps.Size(40, 40),
            },

            // icon: iconLocation,
          });

          MyLocationMarker.addListener('click', () => {
            MyLocationMarker.setAnimation(null);
          });
          mapCenter = location;
          map.setCenter(location);

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
    const formData = getFormData();

    if (formData.type == 'consumo') {
      actionSelector.innerHTML = 'consumir';
    } else {
      actionSelector.innerHTML = 'comprar';
    }

    try {
      const response = await fetch(
        API + `?lat=${location.lat}&lng=${location.lng}&type=${formData.type}`
      );
      return response;
    } catch (error) {
      return new Error('Error al cargar los lugares', error);
    }
  };

  const getLocations = async () => {
    dispatchBeforeSociosLoadedEvent();

    try {
      const socios = await fetchSocios();
      const sociosData = await socios.json();
      const sociosTotalPages = socios.headers.get('X-WP-TotalPages')
        ? socios.headers.get('X-WP-TotalPages')
        : 0;
      const sociosTotal = socios.headers.get('X-WP-Total')
        ? socios.headers.get('X-WP-Total')
        : 0;

      //console.log('TOTAL', sociosTotal);

      if (sociosData.data && sociosData.data.status === 404) {
        dispatchSociosLoadedEvent(sociosTotal);
        // console.log(sociosTotal);

        return false;
      }

      listOfSocios = sociosData; //listOfSocios.concat(sociosData);

      ls.set(`totalSocios`, sociosTotal, 3600);

      // console.log('Total:', sociosTotal);
      // console.log('Numero:', listOfSocios.length);
      // console.log('Socios', listOfSocios);

      if (pagination < sociosTotalPages) {
        pagination++;
        getLocations();
      } else {
        dispatchSociosLoadedEvent(sociosTotal);
      }

      //  processSocios(listOfSocios);

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

  const getFormData = () => {
    const data = new FormData(formBuscador); //new FormData(event.target);

    const value = Object.fromEntries(data.entries());

    // value.gg_category = data.getAll('gg_category');
    // value.gg_type = data.getAll('gg_type');
    // value.gg_prices = data.getAll('gg_prices');
    // value.gg_features = data.getAll('gg_features');
    // value.gg_payments = data.getAll('gg_payments');

    return value;
  };

  //   const processSocios = sociosData => {
  //     const sociosTmp = [];

  //     sociosData.forEach(socio => {
  //       const distanceFromCenter = getDistanceFromLatLonInKm(
  //         mapCenter.lat,
  //         mapCenter.lng,
  //         socio.gg_socio_latlng.lat,
  //         socio.gg_socio_latlng.lng
  //       );

  //       socio.distance = distanceFromCenter;
  //     });

  //     sociosData.sort((a, b) => a.distance - b.distance);

  //     listOfSocios = sociosData;
  //     try {
  //       ls.set(`listOfSocios_${currentLang}`, sociosData, 3600);
  //     } catch (error) {
  //       console.error('No se pudo guardar la cache local', error);
  //     }
  //   };

  const getItemTemplate = (item, isInfo = false) => {
    const template = `
        ${isInfo ? '' : `<li id="item-${item.id}" data-item-id="${item.id}">`}

        <h4 class="location-item-title">${item.title} ${
      item.retail !== ''
        ? `<span>${item.retail} ${
            item.distance ? ` - ${item.distance} km` : ''
          }</span>`
        : ''
    }</h4>
        ${
          item.address !== ''
            ? `<p class="location-item-address">${item.address}</p>`
            : ''
        }
        

        <a class="btn btn-primary btn-product" href="https://www.google.com/maps/dir/Current+Location/${
          item.lat
        },${item.lng}">Cómo llegar</a>
    ${isInfo ? '' : `</li>`}
    `;

    return template;
  };

  const addSocioInToMap = data => {
    if (!canRefresh) return;
    const filteredData = filterData(data);

    filteredData.forEach(element => {
      if (markerExists(element.id)) {
        addMarkerIfInBouds(markers[element.id], element);
      } else {
        //  console.log('no existe');
        const latitud = parseFloat(element.lat);
        const longitud = parseFloat(element.lng);

        const socioInfo = getItemTemplate(element, true);

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
          title: element.title || '',
          icon: {
            url: iconMarker,
            scaledSize: new google.maps.Size(40, 40),
          },
          //   icon: iconMarker,
          infowindow: infowindow,
        });

        infowindow.addListener('closeclick', () => {
          changeMarkerIcon(marker, false);
        });

        marker.addListener('click', () => {
          hideAllInfoWindows();

          changeMarkerIcon(marker, true);

          infowindow.open({
            anchor: marker,
            map,
            shouldFocus: false,
          });
          // map.setCenter(marker.getPosition());
          if (map.getZoom() < 14) map.setZoom(14);

          resultsContainer
            .querySelector(`[data-item-id="${element.id}"]`)
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
    // if (isFromSearch) {
    //   isFromSearch = false;
    // }
    //now fit the map to the newly inclusive bounds
    // getMarkersInfo(filteredData);
  };

  const showTotalResults = results => {
    if (results > 0) {
      // advancedToaster.create(
      //   'Encontrados',
      //   `Se encontraron ${results.length} socios`
      // );
      //   mapAlert(gg_translate.search_results.replace('%d', results.length));

      document.querySelector(
        '.container-locations__counter--number'
      ).innerHTML = results;

      // mapAlert(`Se encontraron <strong>${results.length}</strong> socios`);
    } else {
      // advancedToaster.create(
      //   'Sin resultados',
      //   `No encontraron socios con tus parámetros de búsqueda`
      // );
      document.querySelector(
        '.container-locations__counter--number'
      ).innerHTML = 0;
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
      `[data-item-id="${element.id}"]`
    );
    // if (map.getBounds().contains(marker.getPosition())) {
    if (!marker.getVisible()) {
      marker.setVisible(true);

      if (!socioItem || !socioItem.dataset.isVisible) {
        const fragment = document
          .createRange()
          .createContextualFragment(getItemTemplate(element));

        resultsContainer.appendChild(fragment);

        socioItem = resultsContainer.querySelector(
          `[data-item-id="${element.id}"]`
        );

        socioItem.addEventListener('click', e => {
          hideAllInfoWindows();
          changeMarkerIcon(marker, true);

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
    // } else {
    //   console.log('No esta cerca');
    //   // if (socioItem) {
    //   //   socioItem.parentElement.removeChild(socioItem);
    //   //   console.log('Eliminando item');
    //   // }
    // }
  };

  const changeMarkerIcon = (marker, activo) => {
    activo
      ? marker.setIcon({
          url: iconMarkerActive,
          scaledSize: new google.maps.Size(40, 40),
        })
      : marker.setIcon({
          url: iconMarker,
          scaledSize: new google.maps.Size(40, 40),
        });
  };

  const hideAllInfoWindows = () => {
    Object.entries(markers).forEach(([id, marker]) => {
      marker.infowindow.close(map, marker);
      changeMarkerIcon(marker, false);
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

  return {
    init: init,
  };
})();

export { MapaGG };

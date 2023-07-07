import { Toast } from 'bootstrap';
import { map } from 'jquery';

const deg2rad = deg => {
  return deg * (Math.PI / 180);
};

const getDistanceFromLatLonInKm = (lat1, lon1, lat2, lon2) => {
  var R = 6371; // Radius of the earth in km
  var dLat = deg2rad(lat2 - lat1); // deg2rad below
  var dLon = deg2rad(lon2 - lon1);
  var a =
    Math.sin(dLat / 2) * Math.sin(dLat / 2) +
    Math.cos(deg2rad(lat1)) *
      Math.cos(deg2rad(lat2)) *
      Math.sin(dLon / 2) *
      Math.sin(dLon / 2);
  var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
  var d = R * c; // Distance in km
  return d;
};

const showSpinner = msg => {
  const spinner = document.getElementById('loading-spinner');
  const spinnerTxt = spinner.querySelector('.loading-spinner__text');

  if (spinner) {
    spinner.classList.add('show');

    if (msg !== '') spinnerTxt.innerHTML = msg;
    else spinnerTxt.innerHTML = '';
  }
};

const hideSpinner = () => {
  const spinner = document.getElementById('loading-spinner');

  const mapAreas = document.querySelectorAll('.map-area');

  // console.log(mapAreas);

  if (mapAreas) {
    mapAreas.forEach(area => area.classList.add('loaded'));
  }

  if (spinner) {
    spinner.classList.remove('show');
  }
};

const mapAlert = msg => {
  const alertContainer = document.getElementById('map-alert');
  const alertBody = alertContainer.querySelector('.toast-body');

  if (alertContainer && alertBody) {
    if (msg !== '') {
      alertBody.innerHTML = msg;
      const toast = new Toast(alertContainer);
      toast.show();
    }
  }
};

const getCurrentLocation = options => {
  return new Promise(function (resolve, reject) {
    navigator.geolocation.getCurrentPosition(resolve, reject, options);
  });
};

const getLocationByIp = async () => {
  const request = await fetch('https://ipinfo.io/json?token=19aaebc43dab51');
  const jsonResponse = await request.json();
  let locationByIP = {};

  if (jsonResponse.loc) {
    let loc = jsonResponse.loc.split(',');
    locationByIP = {
      lat: parseFloat(loc[0]),
      lng: parseFloat(loc[1]),
    };
  }

  return locationByIP;
};

export {
  getDistanceFromLatLonInKm,
  showSpinner,
  hideSpinner,
  mapAlert,
  getCurrentLocation,
  getLocationByIp,
};

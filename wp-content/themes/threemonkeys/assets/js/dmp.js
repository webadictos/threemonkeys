import { ls } from './localstorage';

const DMP = (() => {
  let currentUser = ls.get('userProfile') || {};
  const expiryInDays = 520;
  let previousTimestamp, lastTimestamp;

  const init = () => {
    // console.log('Before:', currentUser);
    recordUser();
  };

  const recordUser = async () => {
    setUserId();
    setSessions();
    setVisitsIds(ThemeSetup.current.postID);
    setSections();
    //await setLocation();
    setUserProfile();

    // console.log('After:', currentUser);

    // console.log(ls.get('userProfile'));

    //console.log('After Tracked:', currentUser);
  };

  const setUserId = () => {
    if (!currentUser.clientId) {
      if (typeof ga === 'function') {
        ga(function (tracker) {
          currentUser.clientId = tracker.get('clientId');
        });
      }
    }
    lastTimestamp = Date.now();
    previousTimestamp = currentUser.lastTimestamp || lastTimestamp;

    currentUser.lastTimestamp = lastTimestamp;
    currentUser.previousTimestamp = previousTimestamp;
  };

  const setUserProfile = () => {
    let expire = expiryInDays * 86400;
    ls.set('userProfile', currentUser, expire);
  };

  const setLocation = async () => {
    //https://extreme-ip-lookup.com/json/
    try {
      const fetchLocation = await fetch('https://extreme-ip-lookup.com/json/');

      const locationData = await fetchLocation.json();

      console.log(locationData);

      currentUser.location = locationData;
    } catch (e) {
      console.log('Location fetch error', e);
    }
  };

  const setSessions = () => {
    if (typeof currentUser.sessions === 'undefined') currentUser.sessions = 0;

    currentUser.sessions++;
  };

  const setVisitsIds = postID => {
    if (typeof currentUser.articlesIds === 'undefined')
      currentUser.articlesIds = [];

    if (currentUser.articlesIds.includes(postID) === false)
      currentUser.articlesIds.push(postID);
  };

  const setSections = () => {
    if (typeof currentUser.sections === 'undefined') currentUser.sections = [];
    if (Array.isArray(ThemeSetup.current.canal)) {
      ThemeSetup.current.canal.forEach(canal => {
        if (currentUser.sections.includes(canal) === false)
          currentUser.sections.push(canal);
      });
    }
  };

  return {
    init: init,
  };
})();

export { DMP };

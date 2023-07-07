const FavsEvents = (() => {
  const init = () => {
    console.log('FAvortes');
    document.addEventListener(
      'favorites-updated-single',
      (event, favorites, post_id, site_id, status) => {
        console.log(event, favorites, post_id, site_id, status);
      }
    );
  };

  const showAlert = () => {};

  return {
    init: init,
  };
})();
export { FavsEvents };

document.querySelectorAll('a[href="#search"]').forEach(item => {
  item.addEventListener('click', event => {
    event.preventDefault();
    document.querySelector('#search').classList.add('open');
    document.querySelector('#searchoverlay').value = '';
    document.querySelector('#searchoverlay').focus();
  });
});

if (document.querySelector('.search-overlay')) {
  document
    .querySelector('.search-overlay button.close')
    .addEventListener('click', event => {
      if (event.target == this || event.target.className == 'close') {
        document.querySelector('#search').classList.remove('open');
      }
    });

  document.querySelector('.search-overlay').addEventListener('keyup', event => {
    if (event.target == this || event.keyCode == 27) {
      document.querySelector('#search').classList.remove('open');
    }
  });
}

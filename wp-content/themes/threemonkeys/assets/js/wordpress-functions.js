const is_single = () => document.body.classList.contains('single');
const is_archive = () => document.body.classList.contains('archive');
const is_home = () => document.body.classList.contains('home');
const is_singular = () =>
  document.body.classList.contains('single') ||
  (document.body.classList.contains('page') && !is_home());

const is_category = () => document.body.classList.contains('category');
const is_tag = () => document.body.classList.contains('tag');
const is_blog = () => document.body.classList.contains('blog');

const is_category_id = id => document.body.classList.contains('category-' + id);
const is_tag_id = id => document.body.classList.contains('tag-' + id);
const post_type = type => document.body.classList.contains('single-' + type);

const getPostConfig = pID => {
  //console.log(document.getElementById('post-' + pID));
  if (document.getElementById(`post-${pID}`))
    return JSON.parse(document.getElementById(`post-${pID}`).dataset.meta);
  return false;
};

const uniqid = () => Math.random().toString(36).substr(2, 6);

const nextAll = element => {
  const nextElements = [];
  let nextElement = element;

  while (nextElement.nextElementSibling) {
    nextElements.push(nextElement.nextElementSibling);
    nextElement = nextElement.nextElementSibling;
  }

  return nextElements;
};

const validNodes = ['P', 'UL', 'DIV', 'OL', 'FIGURE', 'IFRAME', 'BLOCKQUOTE'];

const filterTags = childs =>
  [...childs].filter(({ nodeName }) => validNodes.includes(nodeName));

export {
  is_single,
  is_archive,
  is_home,
  is_blog,
  is_singular,
  is_category,
  is_tag,
  is_category_id,
  is_tag_id,
  post_type,
  getPostConfig,
  uniqid,
  nextAll,
  filterTags,
};

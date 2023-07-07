const ls = (() => {
  /**
   * @param {string} keyName - A key to identify the value.
   * @param {any} keyValue - A value associated with the key.
   * @param {number} ttl- Time to live in seconds.
   */
  const set = (keyName, keyValue, ttl = 86400) => {
    const data = {
      value: keyValue, // store the value within this object
      ttl: Date.now() + ttl * 1000, // store the TTL (time to live)
    };

    // store data in LocalStorage
    localStorage.setItem(keyName, JSON.stringify(data));
  };
  /**
   * @param {string} keyName - A key to identify the data.
   * @returns {any|null} returns the value associated with the key if its exists and is not expired. Returns `null` otherwise
   */
  const get = keyName => {
    const data = localStorage.getItem(keyName);
    if (!data) {
      // if no value exists associated with the key, return null
      return null;
    }

    const item = JSON.parse(data);

    // If TTL has expired, remove the item from localStorage and return null
    if (Date.now() > item.ttl) {
      localStorage.removeItem(keyName);
      return null;
    }

    // return data if not expired
    return item.value;
  };
  return {
    set: set,
    get: get,
  };
})();

export { ls };

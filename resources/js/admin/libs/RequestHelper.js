/**
 * Convert a URI query string into an object
 *
 * @param {string} uri
 * @returns {Object}
 */
function decodeUriQuery(uri) {
  const params = {};
  // Remove '?' from the start of the query string if present
  const queryString = uri[0] === '?' ? uri.substring(1) : uri;
  // Split the query string into key-value pairs
  const pairs = queryString.split('&');
  // Iterate over each key-value pair
  for (let pair of pairs) {
    // Split the pair into key and value
    let [key, value] = pair.split('=');
    value = decodeURIComponent(value);
    // Check if key represents a nested object (e.g., order[by])
    if (/\[.*\]/.test(key)) {
      // Extract the main key and subkey for nested objects
      const [mainKey, subKey] = key.split(/\[|\]/).filter(Boolean);
      // Initialize nested object if necessary
      params[mainKey] = params[mainKey] || {};
      // Assign the value to the nested object, converting to number if possible
      params[mainKey][subKey] = isNaN(value) ? value : Number(value);
    } else {
      // For non-nested properties, assign value directly, converting to number if possible
      params[key] = isNaN(value) ? value : Number(value);
    }
  }
  return params;
}

function encodeUriQuery(obj, prefix = '') {
  let queryString = [];
  // Iterate over each property in the object
  for (let key in obj) {
    if (obj.hasOwnProperty(key)) {
      // Get the value of the property
      const value = obj[key];
      // Encode the key, using a prefix for nested objects
      const encodedKey = encodeURIComponent(prefix ? `${prefix}[${key}]` : key);
      // Check if the value is a nested object
      if (typeof value === 'object' && value !== null) {
        // Recursively encode the nested object
        queryString.push(encodeUriQuery(value, encodedKey));
      } else {
        // Encode the value and add the key-value pair to the query string parts
        const encodedValue = encodeURIComponent(value);
        !!encodedValue && queryString.push(`${encodedKey}=${encodedValue}`);
      }
    }
  }

  return queryString.join('&');
}

export {
  decodeUriQuery,
  encodeUriQuery
}
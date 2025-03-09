/**
 * Steam API Plugin - Location Module
 * Handles user location information from Steam API
 */

/**
 * Gets country code from Steam location data
 * 
 * @param {string} countryCode - Country code from Steam API
 * @param {string} stateCode - State code from Steam API
 * @param {string} cityId - City ID from Steam API
 * @returns {string} Country code or empty string
 */
export const getLocation = (countryCode, stateCode, cityId) => {
  // Return country code in lowercase for consistency
  if (countryCode) {
    return countryCode.toLowerCase();
  }
  
  return '';
};

/**
 * Gets full location string from Steam location data
 * Currently only returns country, but could be expanded to include state and city
 * if a geographic database were incorporated
 * 
 * @param {string} countryCode - Country code from Steam API
 * @param {string} stateCode - State code from Steam API
 * @param {string} cityId - City ID from Steam API
 * @returns {string} Formatted location string
 */
export const getFullLocation = (countryCode, stateCode, cityId) => {
  // A more comprehensive implementation would use a country/region database
  // to convert codes to human-readable names
  
  // For now, just return the country code
  if (countryCode) {
    // ISO country codes are two letters
    if (countryCode.length === 2) {
      try {
        // Try to get localized country name (if browser supports it)
        const regionNames = new Intl.DisplayNames(
          [navigator.language || 'en-US'], 
          {type: 'region'}
        );
        return regionNames.of(countryCode.toUpperCase());
      } catch (error) {
        // Fallback to just the country code
        return countryCode.toUpperCase();
      }
    }
    return countryCode.toUpperCase();
  }
  
  return '';
};

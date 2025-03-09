/**
 * Steam API Plugin - Flag Emoji Module
 * Converts country codes to flag emojis
 */

/**
 * Converts a country code to a flag emoji
 * 
 * @param {string} countryCode - ISO 3166-1 alpha-2 country code
 * @returns {string} Flag emoji or empty string if invalid
 */
export const getFlagEmoji = (countryCode) => {
  // Validate input
  if (!countryCode || typeof countryCode !== 'string' || countryCode.length !== 2) {
    return '';
  }
  
  try {
    // Convert country code to flag emoji
    // Each letter in the country code is converted to a regional indicator symbol
    // which when combined, creates the flag emoji
    const codePoints = countryCode
      .toUpperCase()
      .split('')
      .map(char => {
        // Verify the character is a letter
        if (!/[A-Z]/.test(char)) {
          throw new Error('Invalid country code character');
        }
        // Convert to regional indicator symbol
        return 127397 + char.charCodeAt(0);
      });
    
    return String.fromCodePoint(...codePoints);
  } catch (error) {
    console.error('Error creating flag emoji:', error);
    return '';
  }
};

/**
 * Checks if flag emojis are supported in the current browser
 * 
 * @returns {boolean} True if flag emojis are likely supported
 */
export const areFlagEmojisSupported = () => {
  // A basic check - not foolproof but catches older browsers
  const canvas = document.createElement('canvas');
  const ctx = canvas.getContext('2d');
  
  if (!ctx) return false;
  
  const flagEmoji = getFlagEmoji('us'); // US flag as test
  ctx.fillText(flagEmoji, -10, -10);
  
  // If emoji is supported, canvas will not be empty
  return canvas.toDataURL() !== document.createElement('canvas').toDataURL();
};

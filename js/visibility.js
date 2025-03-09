/**
 * Steam API Plugin - Visibility Module
 * Handles profile visibility status
 */

/**
 * Visibility states from Steam API
 * @see https://developer.valvesoftware.com/wiki/Steam_Web_API#Player_Resources
 */
const VISIBILITY_STATES = {
  PRIVATE: 1,
  FRIENDS_ONLY: 2,
  FRIENDS_OF_FRIENDS: 3,
  USERS_ONLY: 4,
  PUBLIC: 5
};

/**
 * Get readable visibility status from Steam visibility state
 * 
 * @param {number} visibilityState - Visibility state from Steam API
 * @returns {string} Human-readable visibility status
 */
export const getVisibility = (visibilityState) => {
  // Ensure visibilityState is a number
  const state = Number(visibilityState);
  
  switch (state) {
    case VISIBILITY_STATES.PRIVATE:
      return 'Private';
    case VISIBILITY_STATES.FRIENDS_ONLY:
      return 'Friends Only';
    case VISIBILITY_STATES.FRIENDS_OF_FRIENDS:
      return 'Friends of Friends';
    case VISIBILITY_STATES.USERS_ONLY:
      return 'Users Only';
    case VISIBILITY_STATES.PUBLIC:
      return 'Public';
    default:
      return 'Unknown';
  }
};

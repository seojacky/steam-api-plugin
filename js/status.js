/**
 * Steam API Plugin - Status Module
 * Converts numeric Steam status to readable text
 */

/**
 * Status codes from Steam API
 * @see https://developer.valvesoftware.com/wiki/Steam_Web_API#Player_Resources
 */
const STEAM_STATUS = {
  OFFLINE: 0,
  ONLINE: 1,
  BUSY: 2,
  AWAY: 3,
  SNOOZE: 4,
  LOOKING_TO_TRADE: 5,
  LOOKING_TO_PLAY: 6
};

/**
 * Get readable status from numeric Steam persona state
 * 
 * @param {number} personastate - Numeric status from Steam API
 * @returns {string} Human-readable status with emoji
 */
export const getStatus = (personastate) => {
  // Ensure personastate is a number
  const state = Number(personastate);
  
  switch (state) {
    case STEAM_STATUS.OFFLINE:
      return '🔴 Offline';
    case STEAM_STATUS.ONLINE:
      return '🟢 Online';
    case STEAM_STATUS.BUSY:
      return '🔴 Busy';
    case STEAM_STATUS.AWAY:
      return '🟡 Away';
    case STEAM_STATUS.SNOOZE:
      return '😴 Snooze';
    case STEAM_STATUS.LOOKING_TO_TRADE:
      return '🔄 Looking to trade';
    case STEAM_STATUS.LOOKING_TO_PLAY:
      return '🎮 Looking to play';
    default:
      return 'Unknown';
  }
};

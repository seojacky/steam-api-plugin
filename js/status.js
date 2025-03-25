// js/status.js
import i18n from './i18n.js';

// Get personal status
export const getStatus = (personastate) => {
  switch (personastate) {
    case 0:
      return i18n.offline;
    case 1:
      return i18n.online;
    case 2:
      return i18n.busy;
    case 3:
      return i18n.away;
    case 4:
      return i18n.snooze;
    case 5:
      return i18n.lookingToTrade;
    case 6:
      return i18n.lookingToPlay;
    default:
      return i18n.unknown;
  }
};
// Get personal status
export const getStatus = (personastate) => {
  switch (personastate) {
    case 0:
      return '🔴 Offline';
    case 1:
      return '🟢 Online';
    case 2:
      return 'Busy';
    case 3:
      return 'Away';
    case 4:
      return 'Snooze';
    case 5:
      return 'Looking to trade';
    case 6:
      return 'Looking to play';
    default:
      return 'Unknown';
  }
};

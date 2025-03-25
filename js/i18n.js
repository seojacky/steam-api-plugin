// js/i18n.js
// Get translations from WordPress, fallback to English if not available
const i18n = typeof steamApiData !== 'undefined' && steamApiData.i18n ? steamApiData.i18n : {
    offline: '🔴 Offline',
    online: '🟢 Online',
    busy: 'Busy',
    away: 'Away',
    snooze: 'Snooze',
    lookingToTrade: 'Looking to trade',
    lookingToPlay: 'Looking to play',
    unknown: 'Unknown',
    private: 'Private',
    public: 'Public',
    level: 'Level',
    copyButton: 'Copy',
    copyButtonDone: 'Done',
    steamID2: 'SteamID2',
    steamID3: 'SteamID3',
    steamID64: 'SteamID64',
    realName: 'Real Name',
    hidden: 'Hidden',
    profileURL: 'Profile URL',
    accountCreated: 'Account created',
    visibility: 'Visibility',
    status: 'Status',
    location: 'Location',
    avatar: 'Avatar',
    errorFetchingData: 'Error fetching player data.',
    playerNotFound: 'Player data not found.',
    find: 'Find',
    enterDetails: 'Enter SteamID / SteamCommunityID / Profile Name / Profile URL',
    findSteamId: 'Find and get your Steam ID, Steam ID 64, customURL and community ID'
};

export default i18n;
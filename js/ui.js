/**
 * Steam API Plugin - UI Functions
 * Handles rendering player information
 */

import { getLocation } from './location.js';
import { getVisibility } from './visibility.js';
import { getStatus } from './status.js';
import { getSteamId2, getSteamId3 } from './steamId2.js';
import { getFlagEmoji } from './flagEmoji.js';

/**
 * Formats date based on user's locale
 * 
 * @param {number} timestamp - Unix timestamp
 * @returns {string} Formatted date string
 */
const formatDate = (timestamp) => {
  if (!timestamp) return 'N/A';
  
  try {
    const date = new Date(timestamp * 1000);
    return new Intl.DateTimeFormat(navigator.language || 'en-US', {
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    }).format(date);
  } catch (error) {
    console.error('Date formatting error:', error);
    return new Date(timestamp * 1000).toLocaleDateString();
  }
};

/**
 * Safely gets a property from an object with a default fallback
 * 
 * @param {Object} obj - Object to get property from
 * @param {string} key - Key to access
 * @param {*} defaultValue - Default value if property doesn't exist
 * @returns {*} Property value or default
 */
const safeGet = (obj, key, defaultValue = '') => {
  return obj && obj[key] !== undefined ? obj[key] : defaultValue;
};

/**
 * Displays player information in the UI
 * 
 * @param {Object} data - Player data from Steam API
 */
export const displayPlayerInfo = (data) => {
  if (!data || typeof data !== 'object') {
    console.error('Invalid player data received');
    document.getElementById('results').innerHTML = 
      '<div class="steam-api-error">Invalid player data received.</div>';
    return;
  }

  const userInfo = document.getElementById('user-info');
  if (!userInfo) {
    console.error('User info container not found');
    return;
  }

  try {
    // Extract Steam IDs
    const steamId64 = safeGet(data, 'steamid');
    let steamId2 = '';
    let steamId3 = '';
    
    try {
      steamId2 = getSteamId2(steamId64);
      steamId3 = getSteamId3(steamId2);
    } catch (error) {
      console.error('Error converting Steam ID:', error);
      steamId2 = 'Error converting ID';
      steamId3 = 'Error converting ID';
    }
    
    // Get location data
    const location = getLocation(
      safeGet(data, 'loccountrycode'),
      safeGet(data, 'locstatecode'),
      safeGet(data, 'loccityid')
    );
    
    // Get flag emoji
    const flagIcon = getFlagEmoji(safeGet(data, 'loccountrycode'));
    
    // Get visibility and status
    const visibility = getVisibility(safeGet(data, 'communityvisibilitystate', 0));
    const status = getStatus(safeGet(data, 'personastate', 0));
    
    // Format creation date
    const creationDate = formatDate(safeGet(data, 'timecreated'));

    // Build the HTML
    userInfo.innerHTML = `
    <div class="card-body">
      <div class="text-center">
        <img
          width="75"
          height="75"
          class="user-avatar"
          loading="lazy"
          alt="Avatar ${safeGet(data, 'nickname', 'User')}"
          src="${safeGet(data, 'avatar', '')}"
          onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\\'http://www.w3.org/2000/svg\\' width=\\'75\\' height=\\'75\\' viewBox=\\'0 0 24 24\\'%3E%3Cpath fill=\\'%23cccccc\\' d=\\'M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z\\'/%3E%3C/svg%3E';"
        />
        <div class="lvl-wrap">
          <span>Level</span> 
          ${safeGet(data, 'playerlevel') 
            ? `<div class="player-level"><span>${data.playerlevel}</span></div>` 
            : 'N/A'}
        </div>
        <h3>${safeGet(data, 'nickname', 'Unknown User')}</h3>
      </div>
      <hr />
      <dl class="row">
        <dt>SteamID2</dt>
        <dd>
          <span class="steamId2">${steamId2}</span>
          <button class="button-copy">Copy</button>
        </dd>
        
        <dt>SteamID3</dt>
        <dd>
          <span class="steamId3">${steamId3}</span>
          <button class="button-copy">Copy</button>
        </dd>
        
        <dt>SteamID64</dt>
        <dd>
          <span class="steamId64">${steamId64}</span>
          <button class="button-copy">Copy</button>
        </dd>
        
        <dt>Real Name</dt>
        <dd>${safeGet(data, 'realname', 'Hidden')}</dd>
        
        <dt>Profile URL</dt>
        <dd>
          <a href="${safeGet(data, 'profileurl')}" target="_blank" rel="noopener noreferrer">
            ${safeGet(data, 'profileurl')}
          </a>
        </dd>
        
        <dt>Account created</dt>
        <dd>${creationDate}</dd>
        
        <dt>Visibility</dt>
        <dd>${visibility}</dd>
        
        <dt>Status</dt>
        <dd>${status}</dd>
        
        <dt>Location</dt>
        <dd>
          <span class="profile-location">${location || 'N/A'}</span>
          ${location ? `<span class="profile-flag">${flagIcon}</span>` : ''}
        </dd>
      </dl>
    </div>
    `;
  } catch (error) {
    console.error('Error displaying player info:', error);
    userInfo.innerHTML = '<div class="steam-api-error">Error displaying player information.</div>';
  }
};

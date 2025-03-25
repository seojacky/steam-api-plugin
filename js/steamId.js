/**
 * Steam API Plugin - SteamID Conversion
 * Handles conversion between different Steam ID formats
 */

/**
 * Constants for Steam ID conversion
 */
const STEAM_ID_CONST = {
  ACCOUNT_ID_MASK: BigInt('0xFFFFFFFF'),
  ACCOUNT_INSTANCE_MASK: BigInt('0x000FFFFF'),
  ACCOUNT_TYPE_MASK: BigInt('0x000F0000'),
  ACCOUNT_UNIVERSE_MASK: BigInt('0xFF000000'),
  ACCOUNT_TYPE_INDIVIDUAL: BigInt(1),
  ACCOUNT_UNIVERSE_PUBLIC: BigInt(1),
  ACCOUNT_INSTANCE_DESKTOP: BigInt(1),
  STEAMID64_BASE: BigInt('76561197960265728') // Steam ID 64 base: 0x0110000100000000
};

/**
 * Converts SteamID64 to SteamID2 format
 * 
 * @param {string} steamid64 - SteamID64 format
 * @returns {string} SteamID in STEAM_0:X:XXXXXX format
 * @throws {Error} If input is invalid
 */
export const getSteamId2 = (steamid64) => {
  if (!steamid64 || !/^\d+$/.test(steamid64)) {
    throw new Error('Invalid SteamID64 format');
  }
  
  try {
    // Parse as BigInt for precise integer math
    const steam64BigInt = BigInt(steamid64);
    
    // Subtract the base constant and get Y value (account type)
    const accountId = steam64BigInt - STEAM_ID_CONST.STEAMID64_BASE;
    const Y = accountId % BigInt(2);
    
    // Calculate Z value
    const Z = (accountId - Y) / BigInt(2);
    
    // Format as STEAM_0:Y:Z
    return `STEAM_0:${Y}:${Z}`;
  } catch (error) {
    console.error('Error converting SteamID64 to SteamID2:', error);
    throw new Error('Error converting SteamID');
  }
};

/**
 * Converts SteamID2 to SteamID3 format
 * 
 * @param {string} steamId2 - SteamID in STEAM_0:X:XXXXXX format
 * @returns {string} SteamID in [U:1:XXXXXX] format
 * @throws {Error} If input is invalid
 */
export const getSteamId3 = (steamId2) => {
  if (!steamId2 || !/^STEAM_0:[0-1]:\d+$/.test(steamId2)) {
    throw new Error('Invalid SteamID2 format');
  }
  
  try {
    // Split into components
    const parts = steamId2.split(':');
    if (parts.length !== 3) {
      throw new Error('Invalid SteamID format');
    }
    
    // Extract Y and Z values
    const universe = '1'; // Steam public universe is 1
    const Y = parseInt(parts[1], 10);
    const Z = parseInt(parts[2], 10);
    
    // Calculate account number
    const accountNumber = Z * 2 + Y;
    
    // Format as [U:1:XXXXX]
    return `[U:${universe}:${accountNumber}]`;
  } catch (error) {
    console.error('Error converting SteamID2 to SteamID3:', error);
    throw new Error('Error converting SteamID');
  }
};

/**
 * Converts SteamID3 to SteamID64 format
 * 
 * @param {string} steamId3 - SteamID in [U:1:XXXXXX] format
 * @returns {string} SteamID64 format
 * @throws {Error} If input is invalid
 */
export const getSteamId64FromSteamId3 = (steamId3) => {
  if (!steamId3 || !/^\[U:1:\d+\]$/.test(steamId3)) {
    throw new Error('Invalid SteamID3 format');
  }
  
  try {
    // Extract the account number
    const match = steamId3.match(/^\[U:1:(\d+)\]$/);
    if (!match || !match[1]) {
      throw new Error('Cannot extract account number from SteamID3');
    }
    
    const accountNumber = BigInt(match[1]);
    const steamId64 = STEAM_ID_CONST.STEAMID64_BASE + accountNumber;
    
    return steamId64.toString();
  } catch (error) {
    console.error('Error converting SteamID3 to SteamID64:', error);
    throw new Error('Error converting SteamID');
  }
};

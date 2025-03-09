export const getSteamId2 = (steamid) => {
  const steam64BigInt = BigInt(steamid);
  const Y = steam64BigInt % 2n;
  const Z = (steam64BigInt - 76561197960265728n - Y) / 2n;
  return `STEAM_0:${Y}:${Z}`;
};

export const getSteamId3 = (getSteamId2) => {
  const parts = getSteamId2.split(':');
  if (parts.length !== 3 || parts[0] !== 'STEAM_0') {
    throw new Error('Invalid SteamID format');
  }

  const universe = parts[1];
  const accountType = parts[2];
  const accountNumber = parseInt(accountType, 10) * 2 + parseInt(universe, 10);

  return `[U:${universe}:${accountNumber}]`;
};

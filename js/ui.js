// js/ui.js
import { getLocation } from './location.js';
import { getVisibility } from './visibility.js';
import { getStatus } from './status.js';
import { getSteamId2, getSteamId3 } from './steamId2.js';
import { getFlagEmoji } from './flagEmoji.js';
import i18n from './i18n.js';

export const displayPlayerInfo = (data) => {
  const userInfo = document.getElementById('user-info');
  const location = getLocation(
    data.loccountrycode,
    data.locstatecode,
    data.loccityid
  );
  const flagIcon = getFlagEmoji(data.loccountrycode);
  const steamId2 = getSteamId2(data.steamid);
  const steamId3 = getSteamId3(steamId2);
  const visibility = getVisibility(data.communityvisibilitystate);
  const status = getStatus(data.personastate);
  
  // Форматирование времени последнего входа
  const lastLogoff = data.last_logoff 
      ? new Date(data.last_logoff * 1000).toLocaleString() 
      : i18n.unknown;
  
  // Convert minutes to hours and minutes for playtime display
  const formatPlaytime = (minutes) => {
    if (minutes === 'private') return i18n.privateProfile;
    const hours = Math.floor(minutes / 60);
    const remainingMinutes = minutes % 60;
    return `${hours} ${i18n.hours} ${remainingMinutes} ${i18n.minutes}`;
  };
  
  // Format ban status display
  const formatBanStatus = () => {
    if (data.vacBanned) {
      return `<span class="ban-status banned">${i18n.vacBanned}: ${data.vacBanCount} ${i18n.bans} (${data.daysSinceLastBan} ${i18n.daysSince})</span>`;
    } else {
      return `<span class="ban-status clean">${i18n.noVacBans}</span>`;
    }
  };
  
  // Format trade ban status
  const formatTradeBan = () => {
    if (!data.economyBan || data.economyBan === 'none') {
      return `<span class="trade-status allowed">${i18n.noTradeBans}</span>`;
    } else {
      return `<span class="trade-status banned">${i18n.tradeBanned}: ${data.economyBan}</span>`;
    }
  };
  
  // Format recently played games list
  const formatRecentGames = () => {
    if (!data.recentGames || data.recentGames.length === 0) {
      return `<p>${i18n.noRecentGames}</p>`;
    }
    
    let html = '<div class="recent-games">';
    data.recentGames.forEach(game => {
      const playtimeRecent = Math.floor(game.playtime_2weeks / 60 * 10) / 10; // Convert to hours with 1 decimal
      const playtimeTotal = Math.floor(game.playtime_forever / 60 * 10) / 10; // Convert to hours with 1 decimal
      
      html += `
        <div class="game-item">
          <img src="https://media.steampowered.com/steamcommunity/public/images/apps/${game.appid}/${game.img_icon_url}.jpg" 
               alt="${game.name}" class="game-icon" loading="lazy">
          <div class="game-info">
            <strong>${game.name}</strong>
            <span>${i18n.recentPlaytime}: ${playtimeRecent} ${i18n.hours}</span>
            <span>${i18n.totalPlaytime}: ${playtimeTotal} ${i18n.hours}</span>
          </div>
        </div>
      `;
    });
    html += '</div>';
    return html;
  };
	
	
	// Format top played games list
const formatTopGames = () => {
  if (!data.topGames || data.topGames.length === 0) {
    return `<p>${i18n.noTopGames || 'Нет популярных игр'}</p>`;
  }
  
  let html = '<div class="recent-games">'; // Используем тот же класс для стилизации
  data.topGames.forEach(game => {
    const playtimeTotal = Math.floor(game.playtime_forever / 60 * 10) / 10; // Convert to hours with 1 decimal
    
    // Проверяем наличие иконки игры
    let imageUrl;
    if (game.img_icon_url) {
      imageUrl = `https://media.steampowered.com/steamcommunity/public/images/apps/${game.appid}/${game.img_icon_url}.jpg`;
    } else {
      // Используем заглушку, если иконка отсутствует
      imageUrl = 'https://steamcdn-a.akamaihd.net/steamcommunity/public/images/apps/753/placeholder.jpg';
    }
    
    html += `
      <div class="game-item">
        <img src="${imageUrl}" 
             alt="${game.name}" class="game-icon" loading="lazy">
        <div class="game-info">
          <strong>${game.name}</strong>
          <span>${i18n.totalPlaytime}: ${playtimeTotal} ${i18n.hours}</span>
        </div>
      </div>
    `;
  });
  html += '</div>';
  return html;
};
	
	
  
  // Format wishlist items
  const formatWishlist = () => {
    if (data.wishlistCount === 'private') {
      return `<p>${i18n.privateWishlist}</p>`;
    }
    
    if (!data.wishlist || data.wishlist.length === 0) {
      return `<p>${i18n.emptyWishlist}</p>`;
    }
    
    let html = '<div class="wishlist-items">';
    data.wishlist.forEach(item => {
      html += `
        <div class="wishlist-item">
          <a href="https://store.steampowered.com/app/${item.appid}" target="_blank" rel="noopener noreferrer">
            ${item.name}
          </a>
        </div>
      `;
    });
    html += '</div>';
    return html;
  };
  
  // Build the enhanced profile HTML
  userInfo.innerHTML = `
    <div class="card-body">
      <div class="text-center">
        <img
          width="75"
          height="75"
          class="user-avatar"
          loading="lazy"
          alt="${i18n.avatar} ${data.nickname}"
          src="${data.avatar}"
        />
        <div class="lvl-wrap"><span>${i18n.level}</span> ${
          data.playerlevel
            ? `<div class="player-level" style="--text-length: ${data.playerlevel.toString().length}"><span>${data.playerlevel}</span></div>`
            : 'N/A'
        } </div>
        <!--Nickname -->
        <h3>${data.nickname}</h3>
      </div>
      
      <!-- Ban status section -->
      <div class="status-section">
        <div class="ban-info">
          ${formatBanStatus()}
        </div>
        <div class="trade-info">
          ${formatTradeBan()}
        </div>
      </div>
      
      <hr />
      
      <!-- Basic Steam ID information -->
      <dl class="row">
        <!--SteamID2 -->
        <dt>${i18n.steamID2}</dt>
        <dd>
          <!--SteamID2 Value -->
          <span class="steamId2">${steamId2}</span>
          <button class="button-copy">${i18n.copyButton}</button>
        </dd>
        <!--SteamID3 -->
        <dt>${i18n.steamID3}</dt>
        <dd>
          <!--SteamID3 Value -->
          <span class="steamId3">${steamId3}</span>
          <button class="button-copy">${i18n.copyButton}</button>
        </dd>
        <!--SteamID64 -->
        <dt>${i18n.steamID64}</dt>
        <dd>
          <!--SteamID64 Value -->
          <span class="steamId64">${data.steamid}</span>
          <button class="button-copy">${i18n.copyButton}</button>
        </dd>
        <!--Real Name -->
        <dt>${i18n.realName}</dt>
        <dd>${data.realname ? data.realname : i18n.hidden}</dd>
        <!--Profile URL -->
        <dt>${i18n.profileURL}</dt>
        <dd>${data.profileurl}</dd>
        <!--Account Created -->
        <dt>${i18n.accountCreated}</dt>
        <dd>${new Date(data.timecreated * 1000).toLocaleDateString()}</dd>
        <!--Visibility -->
        <dt>${i18n.visibility}</dt>
        <dd>${visibility}</dd>
        <!--Status -->
        <dt>${i18n.status}</dt>
        <dd>${status}</dd>
        <!--Location -->
        <dt>${i18n.location}</dt>
        <dd><span class="profile-location">${location ? location : 'N/A'}${
          location ? `</span> <span class="profile-flag">${flagIcon}</span>` : ''
        }</dd>
        <!--Last Login -->
        <dt>${i18n.lastLogin}</dt>
        <dd>${lastLogoff}</dd>
      </dl>
      
      <hr />
      
      <!-- Extended information section -->
      <h4 class="section-title">${i18n.extendedInfo}</h4>
      <dl class="row">
        <!--Games Count -->
        <dt>${i18n.gamesCount}</dt>
        <dd>${data.gamesCount === 'private' ? i18n.privateProfile : data.gamesCount}</dd>
        
        <!--Total Playtime -->
        <dt>${i18n.totalPlaytime}</dt>
        <dd>${formatPlaytime(data.totalPlaytime)}</dd>
        
        <!--Friends Count -->
        <dt>${i18n.friendsCount}</dt>
        <dd>${data.friendsCount === 'private' ? i18n.privateProfile : data.friendsCount}</dd>
        
        <!--Inventory Status -->
        <dt>${i18n.inventoryStatus}</dt>
        <dd>${data.inventoryAccessible ? i18n.accessible : i18n.notAccessible}</dd>
        
        <!--Wishlist Count -->
        <dt>${i18n.wishlistCount}</dt>
        <dd>${data.wishlistCount === 'private' ? i18n.privateProfile : data.wishlistCount}</dd>
        
<!--Achievement Percentage -->
<dt>${i18n.achievementProgress}</dt>
<dd>${data.achievementPercentage === 'private' ? i18n.privateProfile : (data.achievementPercentage === 'N/A' || data.achievementPercentage === undefined) ? i18n.noData : `${data.achievementPercentage}% (${data.achievementGameName}: ${data.achievementCompletedCount}/${data.achievementTotalCount})`}</dd>

      </dl>
      
      <!-- Recently played games section -->
      <h4 class="section-title">${i18n.recentlyPlayed} (${data.recentGamesCount || 0})</h4>
      ${formatRecentGames()}

      <!-- Top played games section -->
      <h4 class="section-title">${i18n.topPlayed} (${data.topGamesCount || 0})</h4>
      ${formatTopGames()}
      
      <!-- Wishlist section -->
      <h4 class="section-title">${i18n.wishlist}</h4>
      ${formatWishlist()}
    </div>
  `;
};
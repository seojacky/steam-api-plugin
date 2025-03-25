// js/ui.js
import { getLocation } from './location.js';
import { getVisibility } from './visibility.js';
import { getStatus } from './status.js';
import { getSteamId2, getSteamId3 } from './steamId2.js';
import { getFlagEmoji } from './flagEmoji.js';
import i18n from './i18n.js';

export const displayPlayerInfo = (data) => {
	console.log('Player data received:', data);
  	console.log('Last logoff data:', data.last_logoff);
  	console.log('VAC ban data:', data.vac_banned);
  	console.log('Economy ban data:', data.economy_ban);
	
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
  
  // Форматирование статуса VAC-бана
  const vacBanStatus = data.vac_banned 
      ? `${i18n.yes} (${data.vac_ban_count} ${i18n.bans}, ${data.days_since_last_ban} ${i18n.daysSinceLastBan})` 
      : i18n.no;
  
  // Форматирование статуса торговых ограничений
  let economyBanStatus = i18n.no;
  if (data.economy_ban && data.economy_ban !== 'none') {
      economyBanStatus = data.economy_ban === 'banned' 
          ? i18n.permanentBan 
          : data.economy_ban;
  }
  
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
        <hr />
        <dl class="row">
          <!--SteamID2 -->
          <dt>${i18n.steamID2}</dt>
          <dd>
            <!--SteamID2 Value -->
            <span class="steamId2">${steamId2}</span>
			<button class="button-copy">${i18n.copyButton}</button>
          </dd>
          <!---->
          <!--SteamID3 -->
          <dt>${i18n.steamID3}</dt>
          <dd>
            <!--SteamID3 Value -->
            <span class="steamId3">${steamId3}</span>
			<button class="button-copy">${i18n.copyButton}</button>
          </dd>
          <!---->
          <!--SteamID64 -->
          <dt>${i18n.steamID64}</dt>
          <dd>
            <!--SteamID64 Value -->
            <span class="steamId64">${data.steamid}</span>
			<button class="button-copy">${i18n.copyButton}</button>
          </dd>
          <!---->

          <!--Real Name -->
          <dt>${i18n.realName}</dt>
          <dd>${data.realname ? data.realname : i18n.hidden}</dd>
          <!----><!---->
          <dt>${i18n.profileURL}</dt>
          <dd>${data.profileurl}</dd>
          <dt>${i18n.accountCreated}</dt>
          <dd>${new Date(data.timecreated * 1000).toLocaleDateString()}</dd>
          <!---->
          <dt>${i18n.visibility}</dt>
          <dd>${visibility}</dd>
          <!----><!----><!----><!---->
          <dt>${i18n.status}</dt>
          <dd>${status}</dd>
          <!----><!----><!----><!---->
          <dt>${i18n.location}</dt>
          <dd><span class="profile-location">${location ? location : 'N/A'}${
    location ? `</span> <span class="profile-flag">${flagIcon}</span>` : ''
  }</dd>
          
          <!-- Last Login -->
          <dt>${i18n.lastLogin}</dt>
          <dd>${lastLogoff}</dd>
          
          <!-- VAC Ban Status -->
          <dt>${i18n.vacBanStatus}</dt>
          <dd class="${data.vac_banned ? 'vac-banned' : ''}">${vacBanStatus}</dd>
          
          <!-- Trade Ban Status -->
          <dt>${i18n.tradeBanStatus}</dt>
          <dd class="${economyBanStatus !== i18n.no ? 'trade-banned' : ''}">${economyBanStatus}</dd>
        </dl>
  </div>
		`;
};
import { getLocation } from './location.js';
import { getVisibility } from './visibility.js';
import { getStatus } from './status.js';
import { getSteamId2, getSteamId3 } from './steamId2.js';
import { getFlagEmoji } from './flagEmoji.js';

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
  userInfo.innerHTML = `
	<div class="card-body">



        <div class="text-center">
          <img
            width="75"
            height="75"
            class="user-avatar"
            loading="lazy"
						alt="Avatar ${data.nickname}"
            src="${data.avatar}"
          />
					<div class="lvl-wrap"><span>Level</span> ${
            data.playerlevel
              ? `<div class="player-level"><span>${data.playerlevel}</span></div>`
              : 'N/A'
          } </div>
          <!--Nickname -->
          <h3>${data.nickname}</h3>
        </div>
        <hr />
        <dl class="row">
          <!--SteamID2 -->
          <dt>SteamID2</dt>
          <dd>
            <!--SteamID2 Value -->
            <span class="steamId2">${steamId2}</span>
						<button class="button-copy"> Copy </button>
          </dd>
          <!---->
          <!--SteamID3 -->
          <dt>SteamID3</dt>
          <dd>
            <!--SteamID3 Value -->
            <span class="steamId3">${steamId3}</span>
						<button class="button-copy"> Copy </button>
          </dd>
          <!---->
          <!--SteamID64 -->
          <dt>SteamID64</dt>
          <dd>
            <!--SteamID64 Value -->
            <span class="steamId64">${data.steamid}</span>
						<button class="button-copy"> Copy </button>
          </dd>
          <!---->

          <!--Real Name -->
          <dt>Real Name</dt>
          <dd>${data.realname ? data.realname : 'Hidden'}</dd>
          <!----><!---->
          <dt>Profile URL</dt>
          <dd>${data.profileurl}</dd>
          <dt>Account created</dt>
          <dd>${new Date(data.timecreated * 1000).toLocaleDateString(
            'ru-RU'
          )}</dd>
          <!---->
          <dt>Visibility</dt>
          <dd>${visibility}</dd>
          <!----><!----><!----><!---->
          <dt>Status</dt>
          <dd>${status}</dd>
					<!----><!----><!----><!---->
          <dt>Location</dt>
          <dd><span class="profile-location">${location ? location : 'N/A'}${
    location ? `</span> <span class="profile-flag">${flagIcon}</span>` : ''
  }</dd>
        </dl>
  </div>
		`;
};

// js/enhanced-steam-api-public.js
import { displayEnhancedPlayerInfo } from './enhanced-ui.js';
import { sendEnhancedAjaxRequest } from './enhanced-api.js';
import { copyText } from './copy-button.js';
import i18n from './i18n.js';

document.addEventListener('DOMContentLoaded', () => {
  const getStatsButton = document.getElementById('get-stats-button');
  const steamInput = document.getElementById('steamInput');
  const results = document.getElementById('results');
  const userInfo = document.getElementById('user-info');
  const loadingIndicator = document.getElementById('loading-indicator');

  getStatsButton.addEventListener('click', async () => {
    const steamId = steamInput.value;
    if (!steamId.trim()) {
      results.innerHTML = i18n.pleaseEnterSteamID;
      return;
    }
    
    results.innerHTML = '';
    userInfo.innerHTML = '';
    
    // Show loading indicator
    loadingIndicator.classList.remove('hidden');

    try {
      const data = await sendEnhancedAjaxRequest(steamId);
      // Hide loading indicator
      loadingIndicator.classList.add('hidden');

      if (data.error) {
        results.innerHTML = data.error;
        return;
      }

      if (data) {
        displayEnhancedPlayerInfo(data);
        addCopyButtonListener();
      } else {
        results.innerHTML = i18n.playerNotFound;
      }
    } catch (error) {
      // Hide loading indicator
      loadingIndicator.classList.add('hidden');
      results.innerHTML = i18n.errorFetchingData;
    }
  });

  // Allow Enter key to trigger search
  steamInput.addEventListener('keypress', function(event) {
    if (event.key === 'Enter') {
      event.preventDefault();
      getStatsButton.click();
    }
  });

  // Copy text
  const addCopyButtonListener = () => {
    const container = document.querySelector('.row');

    container.addEventListener('click', (event) => {
      if (event.target.classList.contains('button-copy')) {
        const textToCopy = event.target.previousElementSibling.innerText;
        const buttonElement = event.target;
        copyText(textToCopy, buttonElement);
      }
    });
  };
});
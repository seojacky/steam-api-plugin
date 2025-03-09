import { displayPlayerInfo } from './ui.js';
import { sendAjaxRequest } from './api.js';
import { copyText } from './copy-button.js';

document.addEventListener('DOMContentLoaded', () => {
  const getStatsButton = document.getElementById('get-stats-button');
  const steamInput = document.getElementById('steamInput');
  const results = document.getElementById('results');
  const userInfo = document.getElementById('user-info');

  getStatsButton.addEventListener('click', async () => {
    const steamId = steamInput.value;
	results.innerHTML = '';
    userInfo.innerHTML = '';

    try {
      const data = await sendAjaxRequest(steamId);
//       console.log(data);

      if (data) {
        displayPlayerInfo(data);
        addCopyButtonListener();
      } else {
        results.innerHTML = 'Данные о статистике игрока не найдены.';
      }
    } catch (error) {
//       console.error(error);
      results.innerHTML = error.message;
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

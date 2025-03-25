// js/api.js
import i18n from './i18n.js';

// Функция для отправки AJAX-запроса с использованием async/await
export const sendAjaxRequest = async (steamId) => {
  try {
    const response = await fetch(steamApiData.ajax_url, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: 'action=get_player_stats&steamId=' + steamId,
    });

    if (!response.ok) {
      throw new Error(i18n.errorFetchingData);
    }

    const data = await response.json();
    return data;
  } catch (error) {
    console.error('Error: ' + error);
    throw new Error(i18n.errorFetchingData);
  }
};

// Функцию sendEnhancedAjaxRequest переименовываем и больше не используем отдельно,
// так как основная функция sendAjaxRequest теперь возвращает полные данные
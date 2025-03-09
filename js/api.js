// Функция для отправки AJAX-запроса с использованием async/await
export const sendAjaxRequest = async (steamId) => {
  try {
    const response = await fetch(ajax_object.ajax_url, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: 'action=get_player_stats&steamId=' + steamId,
    });

    if (!response.ok) {
      throw new Error('Ошибка при выполнении запроса.');
    }

    const data = await response.json();
    return data;
  } catch (error) {
    console.error('Ops!! Error: ' + error);
    throw new Error('Произошла ошибка при обработке данных.');
  }
};

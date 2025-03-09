/**
 * Steam API Plugin - AJAX request handler
 * Handles sending and receiving data from WordPress backend
 */

/**
 * Sends AJAX request to get player information
 * 
 * @param {string} steamId - Steam ID or profile identifier
 * @returns {Promise<Object>} - Player information object
 */
export const sendAjaxRequest = async (steamId) => {
  if (!steamId || steamId.trim() === '') {
    throw new Error('Please enter a valid Steam ID or profile URL');
  }

  try {
    // Create form data for the request
    const formData = new FormData();
    formData.append('action', 'get_player_stats');
    formData.append('steamId', steamId);
    formData.append('nonce', ajax_object.nonce);

    // Send the request
    const response = await fetch(ajax_object.ajax_url, {
      method: 'POST',
      body: formData,
      credentials: 'same-origin'
    });

    if (!response.ok) {
      throw new Error(`Request failed with status ${response.status}: ${response.statusText}`);
    }

    const data = await response.json();
    
    // Handle error responses from the server
    if (data.error) {
      throw new Error(data.error);
    }
    
    return data;
  } catch (error) {
    console.error('Steam API Error:', error);
    throw new Error(error.message || 'An error occurred while processing your request.');
  }
};

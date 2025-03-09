/**
 * Steam API Plugin - Main JavaScript file
 * Handles user interactions and display logic
 */

import { displayPlayerInfo } from './ui.js';
import { sendAjaxRequest } from './api.js';
import { copyText } from './copy-button.js';

document.addEventListener('DOMContentLoaded', () => {
  // Get DOM elements
  const getStatsButton = document.getElementById('get-stats-button');
  const steamInput = document.getElementById('steamInput');
  const results = document.getElementById('results');
  const userInfo = document.getElementById('user-info');
  
  // Add event listeners
  if (getStatsButton && steamInput) {
    // Handle button click
    getStatsButton.addEventListener('click', handleGetStats);
    
    // Handle Enter key press in input field
    steamInput.addEventListener('keydown', (e) => {
      if (e.key === 'Enter') {
        e.preventDefault();
        handleGetStats();
      }
    });
  }
  
  /**
   * Handle the get stats button click or Enter key press
   */
  async function handleGetStats() {
    if (!steamInput.value.trim()) {
      showError('Please enter a Steam ID, profile URL, or username.');
      return;
    }
    
    // Clear previous results
    clearResults();
    
    // Show loading indicator
    showLoading();
    
    try {
      // Send request to get player data
      const data = await sendAjaxRequest(steamInput.value);
      
      // Clear loading indicator
      hideLoading();
      
      // Display the player info
      if (data) {
        displayPlayerInfo(data);
        addCopyButtonListener();
      } else {
        showError('No player data found.');
      }
    } catch (error) {
      // Handle errors
      hideLoading();
      showError(error.message || 'An error occurred while fetching player data.');
    }
  }
  
  /**
   * Show error message
   * @param {string} message - Error message to display
   */
  function showError(message) {
    if (results) {
      results.innerHTML = `<div class="steam-api-error">${message}</div>`;
    }
  }
  
  /**
   * Clear previous results
   */
  function clearResults() {
    if (results) results.innerHTML = '';
    if (userInfo) userInfo.innerHTML = '';
  }
  
  /**
   * Show loading indicator
   */
  function showLoading() {
    if (results) {
      results.innerHTML = '<div class="steam-api-loading">Loading player data...</div>';
    }
  }
  
  /**
   * Hide loading indicator
   */
  function hideLoading() {
    if (results) {
      const loading = results.querySelector('.steam-api-loading');
      if (loading) {
        loading.remove();
      }
    }
  }
  
  /**
   * Add event listeners to copy buttons
   */
  function addCopyButtonListener() {
    const container = document.querySelector('.row');
    
    if (container) {
      container.addEventListener('click', (event) => {
        if (event.target.classList.contains('button-copy')) {
          const textToCopy = event.target.previousElementSibling.innerText;
          copyText(textToCopy, event.target);
        }
      });
    }
  }
});

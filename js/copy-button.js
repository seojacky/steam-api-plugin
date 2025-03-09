/**
 * Modern clipboard functionality for copying text
 * Uses the Clipboard API with fallback to older methods
 */

/**
 * Copies text to clipboard
 * 
 * @param {string} text - Text to copy to clipboard
 * @param {HTMLElement} buttonElement - Button element that was clicked
 * @returns {Promise<void>}
 */
export const copyText = async (text, buttonElement) => {
  try {
    // Save original button text
    const originalText = buttonElement.textContent;
    
    // Try to use modern Clipboard API
    if (navigator.clipboard && window.isSecureContext) {
      await navigator.clipboard.writeText(text);
      buttonElement.textContent = 'Copied!';
      
      // Reset button text after 2 seconds
      setTimeout(() => {
        buttonElement.textContent = originalText;
      }, 2000);
      
      return;
    }
    
    // Fallback for older browsers or non-secure contexts
    const textArea = document.createElement('textarea');
    textArea.value = text;
    
    // Make the textarea out of viewport
    textArea.style.position = 'fixed';
    textArea.style.left = '-999999px';
    textArea.style.top = '-999999px';
    document.body.appendChild(textArea);
    
    textArea.focus();
    textArea.select();
    
    // Execute copy command
    const successful = document.execCommand('copy');
    document.body.removeChild(textArea);
    
    if (successful) {
      buttonElement.textContent = 'Copied!';
      
      // Reset button text after 2 seconds
      setTimeout(() => {
        buttonElement.textContent = originalText;
      }, 2000);
    } else {
      buttonElement.textContent = 'Failed';
      setTimeout(() => {
        buttonElement.textContent = originalText;
      }, 2000);
    }
  } catch (err) {
    console.error('Failed to copy text: ', err);
    buttonElement.textContent = 'Error';
    
    // Reset button after 2 seconds
    setTimeout(() => {
      buttonElement.textContent = 'Copy';
    }, 2000);
  }
};

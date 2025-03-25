// js/visibility.js
import i18n from './i18n.js';

// Check account's visibility
export const getVisibility = (visibilityState) => {
  return visibilityState === 1 ? i18n.private : i18n.public;
};
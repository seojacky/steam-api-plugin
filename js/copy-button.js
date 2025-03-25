// js/copy-button.js
import i18n from './i18n.js';

export const copyText = (text, buttonElement) => {
  // Создаем временный элемент input для копирования текста
  const input = document.createElement('input');
  input.style.position = 'absolute';
  input.style.left = '-9999px';
  input.value = text;
  document.body.appendChild(input);

  // Выделяем текст в input
  input.select();

  // Копируем выделенный текст в буфер обмена
  document.execCommand('copy');

  // Удаляем временный элемент input
  document.body.removeChild(input);

  buttonElement.textContent = i18n.copyButtonDone;
};
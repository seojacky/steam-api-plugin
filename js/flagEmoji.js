export const getFlagEmoji = (countryCode) => {
  if (countryCode) {
    const codePoints = countryCode
      .toUpperCase()
      .split('')
      .map((char) => 127397 + char.charCodeAt());
    return String.fromCodePoint(...codePoints);
  } else {
    return '';
  }
};

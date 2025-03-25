// Get user location
export const getLocation = (countryCode, stateCode, cityId) => {
  if (countryCode) {
    const flagImage = countryCode.toLowerCase();
    // console.log(flagImage);
    return flagImage;
  } else {
    return '';
  }
};

const makeCRCTable = (crcTable = []) => {
  for(let i = 0; i < 256; i++){
    let c = i;
    for(let j = 0; j < 8; j++){
      c = ((c&1) ? (0xEDB88320 ^ (c >>> 1)) : (c >>> 1));
    }
    crcTable[i] = c;
  }
  return crcTable;
}

const crc32 = str => {
  const crcTable = window.crcTable || (window.crcTable = makeCRCTable());
  let crc = 0 ^ (-1);

  for (let i = 0; i < str.length; i++) {
    crc = (crc >>> 8) ^ crcTable[(crc ^ str.charCodeAt(i)) & 0xFF];
  }

  return (crc ^ (-1)) >>> 0;
};

/**
 * Get contrast for rgb array
 * @param rgb
 * @returns {number}
 */
const lightness = rgb => (Math.max(...rgb) + Math.min(...rgb)) / 510;
/**
 * Convert some string to color
 * @param str
 * @returns {number[]}
 */
const stringToColor = str => {
  if (str.length < 6) {
    str = str.padEnd(6, '0')
  }
  return crc32(str).toString(16).substring(0, 6).match(/.{1,2}/g).map(item => parseInt(item, 16))
}

export const userAvatarColors = user => {
  let text = user.first_name.charAt(0);
  if (user.last_name.length) {
    text += user.last_name.charAt(0)
  }
  const bg = stringToColor(user.email)
  return {
    bg: bg,
    color: lightness(bg) > 0.7 ? '#000000' : '#ffffff',
    text: text
  }
}
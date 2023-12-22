<template>
  <div class="avatar-wrap">
    <div v-if="null === data.image" class="avatar-image" :style="`background-color: rgb(${data.bg.join(',')})`">
      <span :style="`color: ${data.color}`">{{ data.text }}</span>
    </div>
    <div class="avatar-image" v-else>
      <img :src="data.image" alt=""/>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({user: Object})
/**
 * Generates a CRC lookup table for computing CRC checksums.
 *
 * @param {number[]} [crcTable=[]] - The initial CRC lookup table.
 * @returns {number[]} The updated CRC lookup table.
 */
const makeCRCTable = (crcTable = []) => {
  for (let i = 0; i < 256; i++) {
    let temp = i;
    for (let j = 0; j < 8; j++) {
      // Polynomial used in CRC calculation.
      temp = temp & 1 ? 0xedb88320 ^ (temp >>> 1) : temp >>> 1;
    }
    crcTable[i] = temp;
  }
  return crcTable;
}

/**
 * Calculates the CRC32 checksum for a given string.
 *
 * @param {string} str - The input string for which CRC32 checksum needs to be calculated.
 * @returns {number} The CRC32 checksum of the input string.
 */
const crc32 = str => {
  // Cache CRC table
  const crcTable = window.crcTable || (window.crcTable = makeCRCTable());
  let crc = 0 ^ -1;

  for (let i = 0; i < str.length; i++) {
    crc = (crc >>> 8) ^ crcTable[(crc ^ str.charCodeAt(i)) & 0xff];
  }

  return (crc ^ -1) >>> 0;
}

/**
 * Calculates the lightness of an RGB color.
 *
 * @param {number[]} rgb - The RGB color represented as an array of three numbers [r, g, b], where each value is in the range of 0-255.
 * @returns {number} The lightness of the color, calculated as the average of the maximum and minimum RGB values, normalized to the range of 0-1.
 */
const lightness = rgb => (Math.max(...rgb) + Math.min(...rgb)) / 510

const stringToColor = str => {
  if (str.length < 6) {
    str = str.padEnd(6, "0");
  }
  return crc32(str)
    .toString(16)
    .substring(0, 6)
    .match(/.{1,2}/g)
    .map((item) => parseInt(item, 16));
}

/**
 * Returns an object containing options for generating an avatar image.
 *
 * @returns {object} - The avatar options.
 */
const avatarOptions = () => {
  let text = props.user.first_name.charAt(0);
  if (props.user.last_name.length) {
    text += props.user.last_name.charAt(0);
  }
  const bg = stringToColor(props.user.email);
  return {
    bg: bg,
    color: lightness(bg) > 0.7 ? "#000000" : "#ffffff",
    text: text,
    image: null
  };
}


const data = avatarOptions()

if (typeof props.user.img_url === 'string') {
  data.image = props.user.img_url;
} else if (typeof props.user.img_url === 'object' && Object.keys(props.user.img_url).length) {
  data.image = props.user.img_url.small || props.user.img_url.large || props.user.img_url.original;
}

</script>
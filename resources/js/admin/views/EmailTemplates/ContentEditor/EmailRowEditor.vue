<template>
  <div class="email-row-edit-wrap">
    <div class="email-row-edit-preview">
      <ul class="text-elements-list" :style="{'text-align': textAlign}">
        <template v-for="(el, i) in elements">
          <li :class="{active: activeEl === i}" :data-tag="el.tag">
            <span :style="stylesToString(el)">{{ el.text }}</span>
          </li>
        </template>
      </ul>
    </div>

    <div class="email-row-elements-controls">
      <label class="caption">
        <span>Tag</span>
        <select name="tag" class="form-select" v-model="element.tag" @change="changeElementTag">
          <option value="a">a</option>
          <option value="br">br</option>
          <option value="div">div</option>
          <option value="p">p</option>
          <option value="span">span</option>
        </select>
      </label>

      <template v-if="element.tag !== 'br'">
        <label class="caption">
          <span>Text</span>
          <textarea
            autocomplete="off"
            class="form-text"
            name="text"
            v-model="element.text"
          ></textarea>
        </label>

        <SliderCheckbox
          name="isButton"
          text="Is a Button"
          v-if="element.tag === 'a'"
          :checked="element.attributes.hasOwnProperty('class')"
          @change="toggleFormValue('isButton')"
        />

        <label class="caption" v-if="element.tag === 'a'">
          <span>URL</span>
          <input autocomplete="off" class="form-input" name="text" :value="element?.attributes?.href">
        </label>

        <div class="buttons-row">
          <EditRowButton
            align="left"
            icon="text-align-left-icon"
            :value="item.style['text-align']"
            @click="item.style['text-align'] = 'left'"
          />

          <EditRowButton
            align="center"
            icon="text-align-center-icon"
            :value="item.style['text-align']"
            @click="item.style['text-align'] = 'center'"
          />

          <EditRowButton
            align="right"
            icon="text-align-right-icon"
            :value="item.style['text-align']"
            @click="item.style['text-align'] = 'right'"
          />

          <EditRowButton
            icon="text-bold-icon"
            :value="element?.attributes?.style && element.attributes.style.hasOwnProperty('font-weight')"
            @click="toggleFormValue('font-weight')"
          />

          <EditRowButton
            icon="text-italic-icon"
            :value="element?.attributes?.style && element.attributes.style.hasOwnProperty('font-style')"
            @click="toggleFormValue('font-style')"
          />

          <EditRowButton
            icon="text-underline-icon"
            :value="element?.attributes?.style && element.attributes.style.hasOwnProperty('text-decoration')"
            @click="toggleFormValue('text-decoration')"
          />
        </div>

        <label class="caption">
          <span>Background Color</span>
          <ColorPicker :value="bgColor" @change="updateFormValue('background-color', $event)"/>
        </label>

        <label class="caption">
          <span>Text Color</span>
          <ColorPicker :value="color" @change="updateFormValue('color', $event)"/>
        </label>

        <label class="caption">
          <span>Font Size</span>
          <input
            autocomplete="off"
            class="form-input"
            type="number"
            min="1"
            step="1"
            :value="fontSize"
            @input="updateFormValue('font-size', $event.target.value.trim() + 'px')"
          >
        </label>

        <label class="caption">
          <span>Line Height</span>
          <input
            autocomplete="off"
            class="form-input"
            type="number"
            min="1"
            step="1"
            :value="lineHeight"
            @input="updateFormValue('line-height', $event.target.value.trim() + 'px')"
          >
        </label>
      </template>
    </div>
  </div>
</template>

<script setup lang="ts">
import {computed, reactive, ref, watch} from 'vue';
import {ColorPicker, SliderCheckbox} from '../../../components/Form/';
import EditRowButton from './EditRowButton.vue';

const props = defineProps({
  item: {
    type: Object,
    default: {}
  }
})

const emit = defineEmits(['changeContent'])

/*
 * Computed
 */
const bgColor = computed((): string => getStyleValue('background-color', '#ffffff'));
const color = computed((): string => getStyleValue('color', '#00145e'));
const fontSize = computed((): string => getStyleValue('font-size', 12));
const lineHeight = computed((): string => getStyleValue('line-height', 18));
const textAlign = computed((): string => !props.item?.style || !props.item.style.hasOwnProperty('text-align')
  ? ''
  : props.item.style['text-align'])

/*
 * Methods
 */
/**
 * Check the element contain style attribute and create it if not
 * @param {object} el
 * @return {object} el
 */
const checkElementHasAttributes = (el = null) => {
  if (!el.hasOwnProperty('attributes')) {
    el.attributes = {}
  }
  if (!el.attributes.hasOwnProperty('style')) {
    el.attributes.style = {}
  }

  return el;
}
/**
 * Processes an array of elements to transform style strings into objects and handle specific tags.
 * @param {Array<Object>} elements
 * @return {Array<Object>}
 */
const fillElements = (elements: Array<Object>) => {
  return elements.reduce((result: Array<Object>, el: object) => {
    // Deconstruct the element for easier access to properties.
    const {tag, attributes, text} = el;

    // Convert style string to an object if it exists.
    if (attributes?.style) {
      el.attributes.style = attributes.style.split(';').reduce((styles, row) => {
        const [key, value] = row.split(':');
        if (key && value) styles[key.trim()] = value.trim();
        return styles;
      }, {});
    }

    // Handle <br> tags and their text, if any.
    if (tag === 'br') {
      result.push({tag: 'br'});
      text && result.push({text, tag: 'span'});
    } else if (tag && tag[0] !== '/') {
      // Push non-closing tags directly to the result.
      result.push(el);
    } else if (text) {
      // Wrap text nodes in 'span' if they follow closing tags.
      result.push({text, tag: 'span'});
    }
    return result;
  }, []);
}
/**
 * Parse a string of HTML attributes and converts it into an object.
 * @param {string} str
 * @return {object}
 */
const getAttributes = (str: string): object => {
  const attributes = {};
  // 1. An attribute name, which consists of one or more word characters (\w+).
  // 2. An attribute value, enclosed in either single or double quotes, allowing for any characters except quotes inside ([^"']+).
  const attributeRegex = /(\w+)=["']([^"']+)["']/g;
  let match;
  // Add the attribute name and value as a key-value pair to the attribute object.
  while ((match = attributeRegex.exec(str)) !== null) {
    const [_, key, value] = match;
    attributes[key] = value;
  }
  return attributes;
}
/**
 * Get the element style attribute value by key or replace it by the default value
 * @param {string} key
 * @param {any} defaultVal
 * @return {any}
 */
const getStyleValue = (key: string, defaultVal: any) => {
  return element?.attributes?.style && element.attributes.style.hasOwnProperty(key)
    ? (
      /^\d/.test(element.attributes.style[key])
        ? parseInt(element.attributes.style[key])
        : element.attributes.style[key]
    )
    : defaultVal;
}
/**
 * Parses an HTML string, extracting tags, their attributes, and text content,
 * and organizes them into a structured array.
 * @param {string} html
 * @return {Array<Object>}
 */
const parseHtmlContent = (html: string) => {
  const elements = [];
  const tagRegex = /<([^>]+)>([^<]*)/g;
  let lastIdx = 0;
  // Regex to extract tags and text
  html.replace(tagRegex, (match, tagWithAttributes, text, offset) => {
    // Handle raw text between tags
    if (lastIdx < offset) {
      elements.push({text: html.slice(lastIdx, offset)});
    }
    lastIdx = offset + match.length;

    // Extract tagName and the rest as attributeString
    const spaceIndex = tagWithAttributes.indexOf(' ');
    const tagName = tagWithAttributes.slice(0, spaceIndex >= 0 ? spaceIndex : tagWithAttributes.length);
    const attributeString = spaceIndex >= 0 ? tagWithAttributes.slice(spaceIndex + 1) : '';
    const attributes = getAttributes(attributeString);

    const tagResult = {tag: tagName.toLowerCase()};
    if (Object.keys(attributes).length > 0) {
      tagResult.attributes = attributes;
    }
    if (text.trim()) {
      tagResult.text = text;
    }

    elements.push(tagResult);
    return match; // Not used, but needed for replace function
  });

  // Handle trailing text after the last tag
  if (lastIdx < html.length) {
    elements.push({text: html.slice(lastIdx)});
  }
  return fillElements(elements);
}
const serialize = elements => elements.map(el => {
  // Directly return <br> for line breaks.
  if (el.tag === 'br') {
    return '<br>';
  }

  el = checkElementHasAttributes(el);
  if (!el.attributes.style.hasOwnProperty('color')) {
    el.attributes.style.color = getStyleValue('color', '#00145e');
  }
  if (!el.attributes.style.hasOwnProperty('background-color')) {
    el.attributes.style['background-color'] = getStyleValue('background-color', '#ffffff');
  }
  if (!el.attributes.style.hasOwnProperty('font-size')) {
    el.attributes.style['font-size'] = getStyleValue('font-size', 12) + 'px';
  }
  if (!el.attributes.style.hasOwnProperty('line-height')) {
    el.attributes.style['line-height'] = getStyleValue('line-height', 18) + 'px';
  }

  const attributes = Object.entries(el.attributes || {}).map(([type, value]) => {
    if (type === 'style') {
      // Special handling for styled to concatenate rules.
      return `style="${Object.entries(value).map(([key, val]) => `${key}:${val}`).join(';')}"`;
    }
    // General attribute formatting.
    return `${type}="${value}"`;
  }).join(' ');
  // Construct element string.
  return `<${el.tag} ${attributes}>${el.text || ''}</${el.tag}>`;
}).join('');
/**
 * Check the Element tag was changed.
 */
const changeElementTag = () => {
  if ('a' === element.tag) {
    element = checkElementHasAttributes(element);
    element.attributes.href = '';
  } else {
    delete element.attributes.href;
  }

  // Use Array.map for iteration and Array.join to concatenate.
  props.item.text = serialize(elements)

  emit('changeContent', props.item)
}
/**
 * Convert an element style object to CSS string
 * @param {object} el
 * @return {string}
 */
const stylesToString = (el: object): string => el?.attributes?.style
  ? Object.entries(el.attributes.style).map(([rule, value]) => `${rule}:${value};`).join('')
  : '';
/**
 * Toggles form values for an element's attributes, specifically for style attributes
 * like font-style, font-weight, text-decoration, and class attributes for buttons.
 * @param {string} key
 */
const toggleFormValue = (key: string) => {
  // Ensures the element has attributes defined before proceeding.
  element = checkElementHasAttributes(element);
  switch (key) {
    // For style-related keys, toggle between deleting the attribute and setting a specific value.
    case 'font-style':
    case 'font-weight':
    case 'text-decoration':
      // If the style attribute already exists, delete it.
      if (element.attributes.style.hasOwnProperty(key)) {
        delete element.attributes.style[key];
      } else {
        // If the attribute does not exist, set a default value based on the key.
        let value;
        if (key === 'font-style') {
          value = 'italic';
        } else if (key === 'font-weight') {
          value = '500';
        } else {// 'text-decoration'
          value = 'underline';
        }
        element.attributes.style[key] = value;
      }
      break;
    // For the 'isButton' key, toggle the class attribute to add or remove button styling.
    case 'isButton': {
      if (element.attributes.hasOwnProperty('class')) {
        delete element.attributes.class;
      } else {
        element.attributes.class = 'btn blue';
      }
      break;
    }
  }
}
/**
 * Set the style attribute value
 * @param {string} key
 * @param {any} value
 */
const updateFormValue = (key: string, value: any) => {
  element = checkElementHasAttributes(element);
  element.attributes.style[key] = value
}
/*
 * Variables
 */
let elements = reactive(parseHtmlContent(props.item.text))

let activeEl = ref(0)

let element = reactive(elements[activeEl.value])

/*
 * Watchers
 */
watch(props, val => emit('changeContent', val.item));
watch(element, () => {
  props.item.text = serialize(elements)
  emit('changeContent', props.item)
})
</script>
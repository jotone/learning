<template>
  <div class="overlay" @click="close" v-if="active">
    <div class="default-popup-wrap">
      <div class="close-popup" @click="close"><i class="icon close-icon"></i></div>

      <div class="popup-title-wrap">Email Row</div>

      <div class="popup-body-wrap">
        <form class="email-body-elements-wrap" @submit.prevent="submit">
          <label class="caption">
            <span></span>
          </label>
          <ul class="text-elements-list" :style="{'text-align': textAlign}">
            <template v-for="(el, i) in items.elements">
              <EditRowString :element="el" :isActive="i === activeItem" @click="showEdit(i)"/>
            </template>
          </ul>
          <div class="text-elements-controls">
            <div class="col-1-2">
              <label class="caption">
                <span>Tag</span>
                <select name="tag" class="form-select" v-model="element.tag">
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
                  <input autocomplete="off" class="form-input" name="text" v-model="element.text">
                </label>

                <label class="caption" v-if="element.tag === 'a'">
                  <span>URL</span>
                  <input autocomplete="off" class="form-input" name="text" v-model="element.attributes.href">
                </label>

                <label class="caption">
                  <span>Background Color</span>
                  <ColorPicker
                    :value="bgColor"
                    @change="updateFormValue('background-color', $event)"
                  />
                </label>

                <label class="caption">
                  <span>Text Color</span>
                  <ColorPicker
                    :value="color"
                    @change="updateFormValue('color', $event)"
                  />
                </label>
              </template>
            </div>

            <div class="col-1-2" v-if="element.tag !== 'br'">
              <SliderCheckbox
                name="isButton"
                text="Is a Button"
                v-if="element.tag === 'a'"
                :checked="element.attributes.hasOwnProperty('class')"
                @change="toggleFormValue('isButton')"
              />

              <div class="button-row">
                <EditRowButton
                  align="left"
                  icon="text-align-left-icon"
                  :value="items.style['text-align']"
                  @click="items.style['text-align'] = 'left'"
                />

                <EditRowButton
                  align="center"
                  icon="text-align-center-icon"
                  :value="items.style['text-align']"
                  @click="items.style['text-align'] = 'center'"
                />

                <EditRowButton
                  align="right"
                  icon="text-align-right-icon"
                  :value="items.style['text-align']"
                  @click="items.style['text-align'] = 'right'"
                />
              </div>
              <div class="button-row">
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
            </div>
          </div>

          <div class="button-row">
            <button class="btn blue" type="submit">Apply</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import {DefaultPopupMixin} from "../../../../mixins/default-popup-mixin.js";
import EditRowButton from "./EditRowButton.vue";
import EditRowString from "./EditRowString.vue";
import {ColorPicker, SliderCheckbox} from "../../../components/Form/";

export default {
  mixins: [DefaultPopupMixin],
  components: {ColorPicker, EditRowButton, EditRowString, SliderCheckbox},
  data() {
    return {
      activeItem: 0
    }
  },
  computed: {
    bgColor() {
      return this.getStyleValue('background-color', '#ffffff');
    },
    color() {
      return this.getStyleValue('color', '#00145e');
    },
    element() {
      return this.items.elements[this.activeItem];
    },
    fontSize() {
      return this.getStyleValue('font-size', 14);
    },
    lineHeight() {
      return this.getStyleValue('line-height', 24);
    },
    textAlign() {
      return !this.items?.style || !this.items.style.hasOwnProperty('text-align') ? null : this.items.style['text-align']
    }
  },
  methods: {
    /**
     * Check the element contain style attribute and create it if not
     */
    checkElementHasAttributes() {
      if (!this.element.hasOwnProperty('attributes')) {
        this.element.attributes = {}
      }
      if (!this.element.attributes.hasOwnProperty('style')) {
        this.element.attributes.style = {}
      }
    },
    /**
     * Processes an array of elements to transform style strings into objects and handle specific tags.
     * @param {Array<Object>} elements
     * @return {Array<Object>}
     */
    fillElements(elements) {
      return elements.reduce((result, el) => {
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
    },
    /**
     * Parse a string of HTML attributes and converts it into an object.
     * @param {string} str
     * @return {object}
     */
    getAttributes(str) {
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
    },
    /**
     * Get the element style attribute value by key or replace it by the default value
     * @param {string} key
     * @param {any} defaultVal
     * @return {any}
     */
    getStyleValue(key, defaultVal) {
      return this.element?.attributes?.style && this.element.attributes.style.hasOwnProperty(key)
        ? (/^\d/.test(this.element.attributes.style[key]) ? parseInt(this.element.attributes.style[key]) : this.element.attributes.style[key])
        : defaultVal;
    },
    /**
     * Open modal window
     * @param item
     * @return {Promise<unknown>}
     */
    open(item = null) {
      this.active = true;

      this.items = item
      this.items.elements = this.parseHtmlContent(this.items.text)

      return new Promise(resolve => {
        this.resolver = resolve
      })
    },
    /**
     * Parses an HTML string, extracting tags, their attributes, and text content,
     * and organizes them into a structured array.
     * @param {string} html
     * @return {Array<Object>}
     */
    parseHtmlContent(html) {
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
        const attributes = this.getAttributes(attributeString);

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
      return this.fillElements(elements);
    },
    /**
     * View editable item
     * @param {int} i
     */
    showEdit(i) {
      this.activeItem = i;
    },
    submit() {
      // Use Array.map for iteration and Array.join to concatenate.
      this.items.text = this.items.elements.map(element => {
        // Directly return <br> for line breaks.
        if (element.tag === 'br') {
          return '<br>';
        }

        const attributes = Object.entries(element.attributes || {}).map(([type, value]) => {
          if (type === 'style') {
            // Special handling for styled to concatenate rules.
            return `style="${Object.entries(value).map(([key, val]) => `${key}:${val}`).join(';')}"`;
          }
          // General attribute formatting.
          return `${type}="${value}"`;
        }).join(' ');
        // Construct element string.
        return `<${element.tag} ${attributes}>${element.text || ''}</${element.tag}>`;
      }).join('');
      // Clear the original elements.
      delete this.items.elements;

      this.handle()
    },
    /**
     * Toggles form values for an element's attributes, specifically for style attributes
     * like font-style, font-weight, text-decoration, and class attributes for buttons.
     * @param {string} key
     */
    toggleFormValue(key) {
      // Ensures the element has attributes defined before proceeding.
      this.checkElementHasAttributes();
      switch (key) {
        // For style-related keys, toggle between deleting the attribute and setting a specific value.
        case 'font-style':
        case 'font-weight':
        case 'text-decoration':
          // If the style attribute already exists, delete it.
          if (this.element.attributes.style.hasOwnProperty(key)) {
            delete this.element.attributes.style[key];
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
            this.element.attributes.style[key] = value;
          }
          break;
        // For the 'isButton' key, toggle the class attribute to add or remove button styling.
        case 'isButton': {
          if (this.element.attributes.hasOwnProperty('class')) {
            delete this.element.attributes.class;
          } else {
            this.element.attributes.class = 'btn blue';
          }
          break;
        }
      }
    },
    /**
     * Set the style attribute value
     * @param {string} key
     * @param {any} value
     */
    updateFormValue(key, value) {
      this.checkElementHasAttributes();
      this.element.attributes.style[key] = value
    }
  }
}
</script>
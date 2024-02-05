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
          <ul class="text-elements-list" :style="`text-align: ${form.textAlign}`">
            <li
              v-for="(el, i) in items.elements" @click="showEdit(i)"
              :class="{active: i === activeItem}"
              :data-tag="el.tag"
            >
              {{ el.text }}
            </li>
          </ul>
          <div class="text-elements-controls">
            <div class="col-1-2">
              <label class="caption">
                <span>Tag</span>
                <select name="tag" class="form-select" v-model="form.tag">
                  <option value="a">a</option>
                  <option value="br">br</option>
                  <option value="div">div</option>
                  <option value="p">p</option>
                  <option value="span">span</option>
                </select>
              </label>

              <template v-if="form.tag !== 'br'">
                <label class="caption">
                  <span>Text</span>
                  <input class="form-input" name="text" :value="form.text">
                </label>

                <label class="caption" v-if="form.tag === 'a'">
                  <span>URL</span>
                  <input class="form-input" name="text" :value="form.href">
                </label>

                <label class="caption">
                  <span>Background Color</span>

                  <ColorPicker
                    :value="form.bgColor"
                    @change="updateFormValue('bgColor', $event)"
                  />
                </label>

                <label class="caption">
                  <span>Text Color</span>

                  <ColorPicker
                    :value="form.color"
                    @change="updateFormValue('color', $event)"
                  />
                </label>
              </template>
            </div>

            <div class="col-1-2" v-if="form.tag !== 'br'">
              <SliderCheckbox
                name="isButton"
                text="Is a Button"
                v-if="form.tag === 'a'"
                :checked="form.isButton"
                @change="toggleFormValue('isButton')"
              />

              <div class="button-row">
                <button
                  name="textAlignLeft"
                  type="button"
                  class="btn"
                  :class="{active: form.textAlign === 'left'}"
                  @click="updateFormValue('textAlign', 'left')"
                >
                  <i class="icon text-align-left-icon"></i>
                </button>

                <button
                  name="textAlignCenter"
                  type="button"
                  class="btn"
                  :class="{active: form.textAlign === 'center'}"
                  @click="updateFormValue('textAlign', 'center')"
                >
                  <i class="icon text-align-center-icon"></i>
                </button>

                <button
                  name="textAlignRight"
                  type="button"
                  class="btn"
                  :class="{active: form.textAlign === 'right'}"
                  @click="updateFormValue('textAlign', 'right')"
                >
                  <i class="icon text-align-right-icon"></i>
                </button>
              </div>
              <div class="button-row">
                <button
                  name="textBold"
                  type="button"
                  class="btn"
                  :class="{active: form.bold}"
                  @click="toggleFormValue('bold')"
                >
                  <i class="icon text-bold-icon"></i>
                </button>

                <button
                  name="textItalic"
                  type="button"
                  class="btn"
                  :class="{active: form.italic}"
                  @click="toggleFormValue('italic')"
                >
                  <i class="icon text-italic-icon"></i>
                </button>

                <button
                  name="textUnderline"
                  type="button"
                  class="btn"
                  :class="{active: form.underline}"
                  @click="toggleFormValue('underline')"
                >
                  <i class="icon text-underline-icon"></i>
                </button>
              </div>

              <div class="button-row">
                <label class="caption">
                  <span>Font Size</span>
                  <input class="form-input" type="number" min="1" step="1" v-model="form.fontSize">
                </label>
              </div>

              <div class="button-row">
                <label class="caption">
                  <span>Line Height</span>
                  <input class="form-input" type="number" min="1" step="1" v-model="form.lineHeight">
                </label>
              </div>
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
import {ColorPicker, SliderCheckbox} from "../../../components/Form/";
import {DefaultPopupMixin} from "../../../../mixins/default-popup-mixin.js";

export default {
  mixins: [DefaultPopupMixin],
  components: {SliderCheckbox, ColorPicker},
  data() {
    return {
      form: {
        bgColor: '#ffffff',
        color: '#00145e',
        fontSize: 14,
        lineHeight: 24,
        textAlign: 'center',
        bold: false,
        italic: false,
        underline: false,
        tag: 'br',
        isButton: false,
        href: null
      },
      activeItem: 0
    }
  },
  methods: {
    /**
     * Improved attribute parsing
     * @param attributeString
     * @return {object}
     */
    getAttributes(attributeString) {
      const attributes = {};
      const attributeRegex = /(\w+)=["']([^"']+)["']/g;
      let match;
      while ((match = attributeRegex.exec(attributeString)) !== null) {
        const [_, key, value] = match;
        attributes[key] = value;
      }
      return attributes;
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

      if (this.items?.style) {
        this.form.textAlign = this.items.style['text-align'];
      }

      this.setFormSettings(this.items.elements[0]);

      return new Promise(resolve => {
        this.resolver = resolve
      })
    },
    parseHtmlContent(htmlString) {
      const elements = [];
      const tagRegex = /<([^>]+)>([^<]*)/g;
      let lastIdx = 0;

      htmlString.replace(tagRegex, (match, tagWithAttributes, text, offset) => {
        // Handle raw text between tags
        if (lastIdx < offset) {
          elements.push({text: htmlString.slice(lastIdx, offset)});
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
      if (lastIdx < htmlString.length) {
        elements.push({text: htmlString.slice(lastIdx)});
      }

      let result = [];

      for (let i = 0, n = elements.length; i < n; i++) {
        const el = elements[i];
        if ('tag' in el) {
          const tag = el.tag;

          if (tag[0] !== '/') {
            if (el?.attributes?.style) {
              let styles = {}
              for (let row of el.attributes.style.split(';')) {
                const temp = row.split(':');
                styles[temp[0]] = temp[1];
              }
              el.attributes.style = styles
            }

            if (el.tag === 'br') {
              result.push({tag: 'br'})

              el.hasOwnProperty('text') && result.push({text: el.text, tag: 'span'})
            } else {
              result.push(el)
            }
          } else if ('text' in el) {
            result.push({text: el.text, tag: 'span'})
          }
        }
      }

      return result;
    },
    setFormSettings(element) {
      this.form.tag = element.tag;
      this.form.text = element.text ?? '';

      if (element?.attributes?.href) {
        this.form.href = element.attributes.href
      }

      if (element?.attributes?.class) {
        this.form.isButton = true
      }

      if (element?.attributes?.style) {
        if ('background-color' in element.attributes.style) {
          this.form.bgColor = element.attributes.style['background-color'];
        }

        if ('color' in element.attributes.style) {
          this.form.color = element.attributes.style['color'];
        }

        if ('font-size' in element.attributes.style) {
          this.form.fontSize = parseInt(element.attributes.style['font-size']);
        }

        if ('font-style' in element.attributes.style) {
          this.form.italic = element.attributes.style.hasOwnProperty('font-style');
        }

        if ('font-weight' in element.attributes.style) {
          this.form.bold = element.attributes.style.hasOwnProperty('font-weight');
        }

        if ('line-height' in element.attributes.style) {
          this.form.lineHeight = parseInt(element.attributes.style['line-height']);
        }

        if ('text-decoration' in element.attributes.style) {
          this.form.underline = element.attributes.style['text-decoration'];
        }
      }
    },
    showEdit(i) {
      this.activeItem = i;
      this.setFormSettings(this.items.elements[i])
    },
    submit() {
      console.log(this.form)
    },
    toggleFormValue(key) {
      this.form[key] = !this.form[key]
    },
    updateFormValue(key, value) {
      this.form[key] = value;
    }
  }
}
</script>
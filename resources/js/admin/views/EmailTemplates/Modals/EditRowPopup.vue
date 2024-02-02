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
          <ul class="text-elements-list" :style="items.style">
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
                <select name="tag" class="form-select">
                  <option value="a">a</option>
                  <option value="br">br</option>
                  <option value="div">div</option>
                  <option value="p">p</option>
                  <option value="span">span</option>
                </select>
              </label>

              <label class="caption">
                <span>Text</span>
                <input class="form-input" name="text" placeholder="" required>
              </label>

              <!-- TODO color, background-color -->
            </div>

            <div class="col-1-2">
              <div class="button-row">
                <button name="textAlignLeft" type="button" class="btn">
                  <i class="icon text-align-left-icon"></i>
                </button>
                <button name="textAlignCenter" type="button" class="btn">
                  <i class="icon text-align-center-icon"></i>
                </button>
                <button name="textAlignRight" type="button" class="btn">
                  <i class="icon text-align-right-icon"></i>
                </button>
              </div>
              <div class="button-row">
                <button name="textBold" type="button" class="btn">
                  <i class="icon text-bold-icon"></i>
                </button>
                <button name="textItalic" type="button" class="btn">
                  <i class="icon text-italic-icon"></i>
                </button>
                <button name="textUnderline" type="button" class="btn">
                  <i class="icon text-underline-icon"></i>
                </button>
              </div>

              <!-- TODO Font-size, line-height -->
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import {DefaultPopupMixin} from "../../../../mixins/default-popup-mixin.js";

export default {
  mixins: [DefaultPopupMixin],
  data() {
    return {
      activeItem: 0
    }
  },
  methods: {
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
    showEdit(i) {
      console.log(this.items.elements[i])
    },
    parseHtmlContent(htmlString) {
      const elements = [];
      const tagRegex = /<([^>]+)>([^<]*)/g;
      let lastIdx = 0;

      // Improved attribute parsing
      function getAttributes(attributeString) {
        const attributes = {};
        const attributeRegex = /(\w+)=["']([^"']+)["']/g;
        let match;
        while ((match = attributeRegex.exec(attributeString)) !== null) {
          const [_, key, value] = match;
          attributes[key] = value;
        }
        return attributes;
      }

      htmlString.replace(tagRegex, (match, tagWithAttributes, text, offset) => {
        // Handle raw text between tags
        if (lastIdx < offset) {
          elements.push({ text: htmlString.slice(lastIdx, offset) });
        }
        lastIdx = offset + match.length;

        // Extract tagName and the rest as attributeString
        const spaceIndex = tagWithAttributes.indexOf(' ');
        const tagName = tagWithAttributes.slice(0, spaceIndex >= 0 ? spaceIndex : tagWithAttributes.length);
        const attributeString = spaceIndex >= 0 ? tagWithAttributes.slice(spaceIndex + 1) : '';
        const attributes = getAttributes(attributeString);

        const tagResult = { tag: tagName.toLowerCase() };
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
        elements.push({ text: htmlString.slice(lastIdx) });
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
              result.push({text: el.text})
            } else {
              result.push(el)
            }
          } else if ('text' in el) {
            result.push({text: el.text})
          }
        }
      }

      return result;
    },
    submit() {

    }
  }
}
</script>
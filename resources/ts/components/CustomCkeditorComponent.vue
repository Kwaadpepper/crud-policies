<template>
  <div>
    <div class="wordCounter">
      <span class="words"><u>{{ __("Mots") }}</u>&nbsp;:&nbsp;<b>{{ words }}</b></span>
      <span class="chars"><u>{{ __("Caractères") }}</u>&nbsp;:&nbsp;<b>{{ chars }}</b></span>
    </div>
    <div ref="ckeditor" />
    <input
      :id="internalId"
      class="form-control"
      type="hidden"
      :name="inTname"
      :value="internalText"
      :required="internalRequired"
    >
  </div>
</template>

<script lang="ts">
import { defineComponent } from "vue";
import { CKUploadAdaptater } from "../modules/CkUploadAdaptater";
import trans from "../modules/trans";

export default defineComponent({
  mixins: [trans],
  emits: ["input"],
  mounted() {
    const json = String(this.$attrs.json ?? "{}"),
          data = JSON.parse(json);
    this.inTname = data.name;
    this.internalText = data.value;
    this.internalId = data.id ? data.id : "";
    this.internalRequired = Boolean(data.required);
    this.makeSuperBuild();
  },
  data(): {
    inTname: string;
    internalText: string;
    internalId: string;
    internalRequired: boolean;
    words: number;
    chars: number;
  } {
    return {
      inTname: "",
      internalText: "",
      internalId: "",
      internalRequired: false,
      words: 0,
      chars: 0,
    };
  },
  methods: {
    makeSuperBuild() {
      const self = this,
            ckeditor = this.$refs.ckeditor as HTMLElement,
            loadScriptOnlyOnce = (url: string, callback: () => void = () => {}) => {
              if (window.__CRUD.loadonceCallbacks) {
                window.__CRUD.loadonceCallbacks.push(callback);
                return;
              }

              if (!window.__CRUD.loadonceCallbacks) {
                window.__CRUD.loadonceCallbacks = [];
              }

              window.__CRUD.loadonceCallbacks.push(callback);

              // adding the script element to the head as suggested before
              const head = document.getElementsByTagName("head")[0],
                    script = document.createElement("script");
              script.type = "text/javascript";
              script.src = url;

              // then bind the event to the callback function
              // there are several events for cross browser compatibility
              // @ts-ignore
              script.onreadystatechange = callback;
              script.onload = () => {
                setTimeout(() => {
                  window.__CRUD.loadonceCallbacks?.forEach((delayedCallback) => {
                    delayedCallback();
                  }, 200);
                });
              };

              // fire the loading
              head.appendChild(script);
            },
            // @ts-ignore
            initEditor = (editor) => {
              editor
                .create(ckeditor, {
                  extraPlugins: [
                    // @ts-ignore
                    (editor) => {
                      editor.plugins.get("FileRepository").createUploadAdapter = (
                        // @ts-ignore
                        loader
                      ) => {
                        // Configure the URL to the upload script in your back-end here!
                        return new CKUploadAdaptater(loader);
                      };
                    },
                  ],
                  wordCount: {
                    // @ts-ignore
                    onUpdate: (stats) => {
                      this.chars = stats.characters;
                      this.words = stats.words;
                    },
                  },
                  mediaEmbed: {
                    previewsInData: false,
                    removeProviders: [
                      "instagram",
                      "twitter",
                      "googleMaps",
                      "flickr",
                      "facebook",
                      "dailymotion",
                      "spotify",
                    ],
                    // see https://github.com/ckeditor/ckeditor5/blob/master/packages/ckeditor5-media-embed/src/mediaembedediting.js
                    // providers: ['youtube', {
                    //     // display local videos in oembed tags
                    //     name: 'localVideo',
                    //     url: /^(\/(?:content|storage)\/.*)/,
                    //     html: match => {
                    //         let path = match[ 1 ];

                    //         return (
                    //             '<video controls allowfullscreen style="max-width: 90%; margin: 0 auto; display: block;">' +
                    //                 `<source src="${ path }" type="video/mp4">` +
                    //                 `<p>Votre navigateur ne prend pas en charge les vidéos HTML5. Voici <a href="${ path }">un lien pour télécharger la vidéo</a>.</p>` +
                    //             '</video>'
                    //         );
                    //     }
                    // }]
                  },
                  toolbar: {
                    items: [
                      "heading",
                      "|",
                      "bold",
                      "italic",
                      "underline",
                      "link",
                      "bulletedList",
                      "numberedList",
                      "|",
                      "outdent",
                      "indent",
                      "alignment",
                      "|",
                      "undo",
                      "redo",
                      "|",
                      // 'laravelFileManager', 'blockQuote','|',
                      "insertImage",
                      "mediaEmbed",
                      "blockQuote",
                      "|",
                      "insertTable",
                      "tableColumn",
                      "tableRow",
                      "mergeTableCells",
                      "|",
                      // 'videoStyle:alignLeft', 'videoStyle:alignCenter', 'videoStyle:alignRight', 'videoResize',
                      // '|',
                      // 'videoResize:50',
                      // 'videoResize:75',
                      // 'videoResize:original',
                      "fontColor",
                      "fontFamily",
                      "fontSize",
                      "horizontalLine",
                      "|",
                      "removeFormat",
                      "|",
                      "undo",
                      "redo",
                      "|",
                      "sourceEditing",
                    ],
                  },
                  table: {
                    contentToolbar: [
                      "tableColumn",
                      "tableRow",
                      "mergeTableCells",
                      "tableProperties",
                      "tableCellProperties",
                      "toggleTableCaption",
                    ],

                    // Configuration of the TableProperties plugin.
                    tableProperties: {
                      // ...
                    },

                    // Configuration of the TableCellProperties plugin.
                    tableCellProperties: {
                      // ...
                    },
                  },
                  language: "fr",
                  image: {
                    toolbar: [
                      "imageTextAlternative",
                      "imageStyle:inline",
                      "imageStyle:block",
                      "imageStyle:side",
                      "linkImage",
                    ],
                  },
                  video: {
                    resizeUnit: "px",
                    // Configure the available video resize options.
                    resizeOptions: [
                      {
                        name: "videoResize:original",
                        value: null,
                        label: "Original",
                        icon: "original",
                      },
                      {
                        name: "videoResize:50",
                        value: 50,
                        label: "50",
                        icon: "medium",
                      },
                      {
                        name: "videoResize:75",
                        value: "75",
                        label: "75",
                        icon: "large",
                      },
                    ],
                  },
                })
                // @ts-ignore
                .then((editor) => {
                  editor.setData(self.internalText);
                  // @ts-ignore NON reactive prop
                  self.editor = editor;
                  editor.model.document.on("change:data", () => {
                    // * Keep track of internal editor
                    let data = editor.getData();
                    self.internalText = data;
                    self.$emit("input", data);
                  });
                })
                // @ts-ignore
                .catch((error) => {
                  console.error("Oops, something went wrong!");
                  console.error(
                    "Please, report the following error on https://github.com/ckeditor/ckeditor5/issues with the build id and the error stack trace:"
                  );
                  console.warn("Build id: x0tjc6omqq4d-iydt761tuj51");
                  console.error(error);
                });
            };

      // @ts-ignore
      if (!window.ClassicEditor) {
        loadScriptOnlyOnce(
          "/crud-policies/vendor/crud-policies/js/ckeditor.js",
          () =>
            // @ts-ignore
            initEditor(window.CRUDClassicEditor)
        );
      } else {
        // @ts-ignore
        initEditor(window.ClassicEditor);
      }
    },
  },
});
</script>

<style lang="scss">
.wordCounter {
  color: initial;
  border: 1px solid rgb(196, 196, 196);
  border-bottom: 0;
  background-color: #fafafa;
  .chars {
    margin-left: 1rem;
  }
}
</style>

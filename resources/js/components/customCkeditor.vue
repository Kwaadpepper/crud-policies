<template>
    <div>
        <div class="wordCounter">
                <span class="words"><u>{{ __('Mots') }}</u>&nbsp;:&nbsp;<b>{{ words }}</b></span>
                <span class="chars"><u>{{ __('Caractères') }}</u>&nbsp;:&nbsp;<b>{{ chars }}</b></span>
        </div>
        <div ref="ckeditor"></div>
        <input :id="internalId" class="form-control" type="hidden" :name="name" :value="internalText" :required="internalRequired">
    </div>
</template>

<script>
import './../node-vendor/ckeditor/build/ckeditor'
import { CKUploadAdaptater } from '../modules/CkUploadAdaptater'

export default {
    mounted() {
        let data = JSON.parse(this.$parent.$options.json)
        this.name = data.name
        this.internalText = data.value
        this.internalId = data.id ? data.id : ''
        this.internalRequired = Boolean(data.required)
        this.makeSuperBuild()
    },
    data() {
        return {
            name: null,
            internalText: '',
            internalId: '',
            internalRequired: false,
            editor: null,
            words: 0,
            chars: 0
        }
    },
    methods: {
        makeSuperBuild() {
            const self = this
                ClassicEditor
                    .create(self.$refs.ckeditor, {
                    extraPlugins: [(editor) => {
                        editor.plugins.get( 'FileRepository' ).createUploadAdapter = ( loader ) => {
                            // Configure the URL to the upload script in your back-end here!
                            return new CKUploadAdaptater(loader);
                        }
                    }],
                    wordCount: {
                        onUpdate: stats => {
                            this.chars = stats.characters
                            this.words = stats.words
                        }
                    },
                    mediaEmbed: {
                        previewsInData: false,
                        removeProviders: [
                            'instagram', 'twitter', 'googleMaps', 'flickr', 'facebook',
                            'dailymotion', 'spotify'
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
                            'heading', '|',
                            'bold','italic','underline','link','bulletedList','numberedList','|',
                            'outdent','indent','alignment','|',
                            'undo', 'redo', '|',
                            // 'laravelFileManager', 'blockQuote','|',
                            'insertImage', 'mediaEmbed', 'blockQuote','|',
                            'insertTable', 'tableColumn', 'tableRow', 'mergeTableCells' ,'|',
                            // 'videoStyle:alignLeft', 'videoStyle:alignCenter', 'videoStyle:alignRight', 'videoResize',
                            // '|',
                            // 'videoResize:50',
                            // 'videoResize:75',
                            // 'videoResize:original',
                            'fontColor','fontFamily','fontSize','horizontalLine','|',
                            'removeFormat','|',
                            'undo','redo', '|',
                            'sourceEditing'
                        ]
                    },
                    table: {
                        contentToolbar: [
                            'tableColumn', 'tableRow', 'mergeTableCells',
                            'tableProperties', 'tableCellProperties',
                            'toggleTableCaption'
                        ],

                        // Configuration of the TableProperties plugin.
                        tableProperties: {
                            // ...
                        },

                        // Configuration of the TableCellProperties plugin.
                        tableCellProperties: {
                            // ...
                        }
                    },
                    language: 'fr',
                    image: {
                        toolbar: [
                            'imageTextAlternative',
                            'imageStyle:inline',
                            'imageStyle:block',
                            'imageStyle:side',
                            'linkImage'
                        ]
                    },
                    video: {
                        resizeUnit: 'px',
                        // Configure the available video resize options.
                        resizeOptions: [
                            {
                                name: 'videoResize:original',
                                value: null,
                                label: 'Original',
                                icon: 'original'
                            },
                            {
                                name: 'videoResize:50',
                                value: 50,
                                label: '50',
                                icon: 'medium'
                            },
                            {
                                name: 'videoResize:75',
                                value: '75',
                                label: '75',
                                icon: 'large'
                            }
                        ]
                    }
                })
                .then( editor => {
                    editor.setData(self.internalText)
                    self.editor = editor
                    editor.model.document.on('change:data', () => {
                        // * Keep track of internal editor
                        let data = editor.getData()
                        self.internalText = data
                        self.$emit('input', data)
                    })
                })
                .catch( error => {
                    console.error( 'Oops, something went wrong!' );
                    console.error( 'Please, report the following error on https://github.com/ckeditor/ckeditor5/issues with the build id and the error stack trace:' );
                    console.warn( 'Build id: x0tjc6omqq4d-iydt761tuj51' );
                    console.error( error );
                });
        }
    }
}
</script>

<style lang="scss" scoped>
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

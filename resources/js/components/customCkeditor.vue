<template>
    <div>
        <div v-if="!old" class="wordCounter">
                <span class="words"><u>Mots</u>&nbsp;:&nbsp;<b>{{ words }}</b></span>
                <span class="chars"><u>Caractères</u>&nbsp;:&nbsp;<b>{{ chars }}</b></span>
        </div>
        <div ref="ckeditor"></div>
        <input :id="internalId" class="form-control" type="hidden" :name="name" :value="text" :required="internalRequired">
    </div>
</template>

<script>
    import { CKUploadAdaptater } from '../modules/CkUploadAdaptater'

    export default {
        props: [
            'id',
            'readonly',
            'required',
            'name',
            'dbtext',
            'old'
        ],
        model: {
            prop: 'text',
            event: 'input'
        },
        mounted() {
            this.internalId = this.id ? this.id : ''
            this.internalRequired = this.required ? this.required : false
            this.text = this.dbtext
            this.makeSuperBuild()
        },
        data() {
            return {
                text: '',
                internalId: '',
                internalRequired: false,
                editor: null,
                words: 0,
                chars: 0
            }
        },
        methods: {
            makeSuperBuild() {
                let self = this,
                    initEditor = () => {
                        ClassicEditor
                        .create(self.$refs.ckeditor, {
                            allowedContent: true,
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
                                    'spotify'
                                ]
                            },
                            toolbar: {
                                items: [
                                    'heading', '|',
                                    'bold','italic','underline','link','bulletedList','numberedList','|',
                                    'outdent','indent','alignment','|',
                                    'imageUpload','blockQuote','insertTable','|',
                                    'fontColor','fontFamily','fontSize','horizontalLine','|',
                                    'removeFormat','|',
                                    'undo','redo'
                                ]
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
                            }
                        })
                        .then( editor => {
                            editor.setData(this.text)
                            this.editor = editor.model.document.on('change:data', () => {
                                let data = editor.getData()
                                self.$set(self, 'text', data)
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
                if(typeof ClassicEditor === 'undefined') {
                    import('../node-vendor/ckeditor/build/ckeditor').then(initEditor)
                } else {
                    initEditor()
                }

            }
        }
    }
</script>

<style lang="scss">
.wordCounter {
    color: initial;
    border: 1px solid rgb(196, 196, 196);
    border-bottom: 0;
    background-color: #fafafa;
    position: relative;
    .words {
        position: relative;
        left: 2rem;
    }
    .chars {
        position: absolute;
        left: 9rem;
    }
}
</style>

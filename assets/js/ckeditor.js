import { ClassicEditor } from '@ckeditor/ckeditor5-editor-classic';
import { Alignment } from '@ckeditor/ckeditor5-alignment';
import { Autoformat } from '@ckeditor/ckeditor5-autoformat';
import { Bold, Code, Italic, Strikethrough, Subscript, Superscript, Underline } from '@ckeditor/ckeditor5-basic-styles';
import { BlockQuote } from '@ckeditor/ckeditor5-block-quote';
import { CloudServices } from '@ckeditor/ckeditor5-cloud-services';
import { CodeBlock } from '@ckeditor/ckeditor5-code-block';
import { Essentials } from '@ckeditor/ckeditor5-essentials';
import { ExportPdf } from '@ckeditor/ckeditor5-export-pdf';
import { ExportWord } from '@ckeditor/ckeditor5-export-word';
import { FindAndReplace } from '@ckeditor/ckeditor5-find-and-replace';
import { Font } from '@ckeditor/ckeditor5-font';
import { GeneralHtmlSupport } from '@ckeditor/ckeditor5-html-support';
import { Heading } from '@ckeditor/ckeditor5-heading';
import { Highlight } from '@ckeditor/ckeditor5-highlight';
import { HorizontalLine } from '@ckeditor/ckeditor5-horizontal-line';
import { HtmlEmbed } from '@ckeditor/ckeditor5-html-embed';
import { MediaEmbed } from '@ckeditor/ckeditor5-media-embed';
import { Paragraph } from '@ckeditor/ckeditor5-paragraph';
import { Table, TableCaption, TableCellProperties, TableColumnResize, TableProperties, TableToolbar } from '@ckeditor/ckeditor5-table';
import { AutoLink, Link, LinkImage } from '@ckeditor/ckeditor5-link';
import { Indent, IndentBlock } from '@ckeditor/ckeditor5-indent';
import { Mention } from '@ckeditor/ckeditor5-mention';
import { PageBreak } from '@ckeditor/ckeditor5-page-break';
import { RemoveFormat } from '@ckeditor/ckeditor5-remove-format';
import { ShowBlocks } from '@ckeditor/ckeditor5-show-blocks';
import { SourceEditing } from '@ckeditor/ckeditor5-source-editing';
import { SpecialCharacters, SpecialCharactersEssentials } from '@ckeditor/ckeditor5-special-characters';
import { Style } from '@ckeditor/ckeditor5-style';
import { TextTransformation } from '@ckeditor/ckeditor5-typing';
import { WordCount } from '@ckeditor/ckeditor5-word-count';
import WProofreader from '@webspellchecker/wproofreader-ckeditor5/src/wproofreader';
import '@ckeditor/ckeditor5-theme-lark'

import { AutoImage,
        Image,
        ImageCaption,
        ImageInsert,
        ImageResize,
        ImageStyle,
        ImageToolbar,
        ImageUpload,
        PictureEditing
        } from '@ckeditor/ckeditor5-image';
// templates icons
//
//import articleImageRightIcon from '../../assets/img/article-image-right.svg';
//import financialReportIcon from '../../assets/img/financial-report.svg';
//import formalLetterIcon from '../../assets/img/formal-letter.svg';
//import resumeIcon from '../../assets/img/resume.svg';
//import richTableIcon from '../../assets/img/rich-table.svg';
//import todoIcon from '../../assets/img/todo.svg';

ClassicEditor
        .create(document.querySelector('#faq_content'), {
            plugins: [
                Autoformat, BlockQuote, Bold, Heading, Image, ImageCaption,
                ImageStyle, ImageToolbar, Indent, Italic, Link, MediaEmbed,
                Paragraph, Table, TableToolbar, Alignment, AutoImage, AutoLink,
                Essentials, FindAndReplace, Font, Highlight, HorizontalLine,
                HtmlEmbed, ImageInsert, ImageResize, ImageUpload, IndentBlock,
                GeneralHtmlSupport,
                LinkImage, Mention, PageBreak,
                PictureEditing, RemoveFormat, ShowBlocks, SourceEditing,
                SpecialCharacters, SpecialCharactersEssentials, Style, Strikethrough, Subscript, Superscript,
                TableCaption, TableCellProperties, TableColumnResize,
                TableProperties, TextTransformation,
                Underline, WordCount
            ],
            toolbar: {
                items: [
                    'undo', 'redo',
                    'showBlocks', 'formatPainter', 'findAndReplace', 'selectAll',
                    '|',
                    'heading',
                    '|',
                    'style',
                    '|',
                    'fontSize', 'fontFamily', 'fontColor', 'fontBackgroundColor',
                    '-',
                    'bold', 'italic', 'underline',
                    {
                        label: 'Formatting',
                        icon: 'text',
                        items: ['strikethrough', 'subscript', 'superscript', 'code', 'horizontalLine', '|', 'removeFormat']
                    },
                    'specialCharacters', 'pageBreak',
                    '|',
                    'link', 'insertImage', 'insertTable', 'tableOfContents',
                    {
                        label: 'Insert',
                        icon: 'plus',
                        items: ['highlight', 'blockQuote', 'mediaEmbed', 'codeBlock', 'htmlEmbed']
                    },
                    '|',
                    'alignment',
                    '|',
                    'bulletedList', 'numberedList', 'todoList', 'outdent', 'indent',
                    '|',
                    'sourceEditing'
                ],
                shouldNotGroupWhenFull: true
            },
            htmlSupport: {
                allow: [
                    {
                        name: /^.*$/,
                        styles: true,
                        attributes: true,
                        classes: true
                    }
                ]
            },
            style: {
                definitions: [
                    {
                        name: 'Article category',
                        element: 'h3',
                        classes: ['category']
                    },
                    {
                        name: 'Title',
                        element: 'h2',
                        classes: ['document-title']
                    },
                    {
                        name: 'Subtitle',
                        element: 'h3',
                        classes: ['document-subtitle']
                    },
                    {
                        name: 'Info box',
                        element: 'p',
                        classes: ['info-box']
                    },
                    {
                        name: 'Side quote',
                        element: 'blockquote',
                        classes: ['side-quote']
                    },
                    {
                        name: 'Marker',
                        element: 'span',
                        classes: ['marker']
                    },
                    {
                        name: 'Spoiler',
                        element: 'span',
                        classes: ['spoiler']
                    },
                    {
                        name: 'Code (dark)',
                        element: 'pre',
                        classes: ['fancy-code', 'fancy-code-dark']
                    },
                    {
                        name: 'Code (bright)',
                        element: 'pre',
                        classes: ['fancy-code', 'fancy-code-bright']
                    }
                ]
            },
            fontFamily: {
                supportAllValues: true
            },
            fontSize: {
                options: [10, 12, 14, 'default', 18, 20, 22],
                supportAllValues: true
            },
            htmlEmbed: {
                showPreviews: true
            },
            image: {
                styles: [
                    'alignCenter',
                    'alignLeft',
                    'alignRight'
                ],
                resizeOptions: [
                    {
                        name: 'resizeImage:original',
                        label: 'Original',
                        value: null
                    },
                    {
                        name: 'resizeImage:50',
                        label: '50%',
                        value: '50'
                    },
                    {
                        name: 'resizeImage:75',
                        label: '75%',
                        value: '75'
                    }
                ],
                toolbar: [
                    'imageTextAlternative', 'toggleImageCaption', '|',
                    'imageStyle:inline', 'imageStyle:wrapText', 'imageStyle:breakText', 'imageStyle:side', '|',
                    'resizeImage'
                ],
                insert: {
                    integrations: [
                        'insertImageViaUrl'
                    ]
                }
            },
            list: {
                properties: {
                    styles: true,
                    startIndex: true,
                    reversed: true
                }
            },
            link: {
                decorators: {
                    addTargetToExternalLinks: true,
                    defaultProtocol: 'https://',
                    toggleDownloadable: {
                        mode: 'manual',
                        label: 'Downloadable',
                        attributes: {
                            download: 'file'
                        }
                    }
                }
            },
            table: {
                contentToolbar: [
                    'tableColumn', 'tableRow', 'mergeTableCells', 'tableProperties', 'tableCellProperties', 'toggleTableCaption'
                ]
            }
        })
        .then(editor => {
            window.editor = editor;
            // Prevent showing a warning notification when user is pasting a content from MS Word or Google Docs.
            window.preventPasteFromOfficeNotification = true;

            document.querySelector('.ck.ck-editor__main').appendChild(editor.plugins.get('WordCount').wordCountContainer);
        })
        .catch(err => {
            console.error(err);
        });

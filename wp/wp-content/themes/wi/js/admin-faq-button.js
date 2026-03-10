/* global tinymce */

(function () {
    tinymce.create('tinymce.plugins.wi_faq_snippet', {
        init: function (editor) {
            editor.addButton('wi_faq_snippet', {
                text: 'FAQ',
                tooltip: 'Insert FAQ shortcode',
                icon: false,
                onclick: function () {
                    var snippet = '' +
                        '[faq_list heading="FAQ (opcjonalny tytuł)"]\n' +
                        '\t[faq_item question="Pytanie 1"]Odpowiedź 1[/faq_item]\n' +
                        '\t[faq_item question="Pytanie 2"]Odpowiedź 2[/faq_item]\n' +
                        '[/faq_list]\n';

                    editor.insertContent(snippet);
                },
            });
        },
        createControl: function () {
            return null;
        },
    });

    tinymce.PluginManager.add('wi_faq_snippet', tinymce.plugins.wi_faq_snippet);
})();


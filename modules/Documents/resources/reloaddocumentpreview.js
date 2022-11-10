function reloadDocumentPreview() {
    let doc_Preview_iframe = $('#pdfPreviewiframe');
    let source = doc_Preview_iframe.attr('src');
    doc_Preview_iframe.attr('src', '');
    setTimeout(function(){
        doc_Preview_iframe.attr('src', source);
    }, 500);
}

corebosjshook.after(window, 'corebosjshook_runBAWorkflow', reloadDocumentPreview);
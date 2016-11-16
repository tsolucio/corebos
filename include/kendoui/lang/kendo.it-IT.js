/*
* Kendo UI Localization Project for v2013.3.1316
* Copyright 2014 Telerik AD. All rights reserved.
* 
* Italian (it-IT) Language Pack
*
* Project home  : https://github.com/loudenvier/kendo-global
* Kendo UI home : http://kendoui.com
* Author        : Paolo Ascari 
*               : Giuliano Comugnaro
*
* This project is released to the public domain, although one must abide to the 
* licensing terms set forth by Telerik to use Kendo UI, as shown bellow.
*
* Telerik's original licensing terms:
* -----------------------------------
* Kendo UI Web commercial licenses may be obtained at
* https://www.kendoui.com/purchase/license-agreement/kendo-ui-web-commercial.aspx
* If you do not own a commercial license, this file shall be governed by the
* GNU General Public License (GPL) version 3.
* For GPL requirements, please review: http://www.gnu.org/copyleft/gpl.html
*/

kendo.ui.Locale = "Italiano (it-IT)";
kendo.ui.ColumnMenu.prototype.options.messages =
  $.extend(kendo.ui.ColumnMenu.prototype.options.messages, {

      /* COLUMN MENU MESSAGES 
       ****************************************************************************/
      sortAscending: "Ordina in modo crescente",
      sortDescending: "Ordina in modo decrescente",
      filter: "Filtra",
      columns: "Colonne",
      done: "Eseguito",
      settings: "Impostazioni colonna"
      /***************************************************************************/
  });

kendo.ui.Groupable.prototype.options.messages =
  $.extend(kendo.ui.Groupable.prototype.options.messages, {

      /* GRID GROUP PANEL MESSAGES 
       ****************************************************************************/
      empty: "Trascina qui un'intestazione per raggruppare i dati in base a quella colonna"
      /***************************************************************************/
  });

kendo.ui.FilterMenu.prototype.options.messages =
  $.extend(kendo.ui.FilterMenu.prototype.options.messages, {

      /* FILTER MENU MESSAGES 
       ***************************************************************************/
      info: "Mostra elementi il cui valore:", // sets the text on top of the filter menu

      filter: "Filtra",                       // sets the text for the "Filter" button
      clear: "Rimuovi Filtri",                // sets the text for the "Clear" button
      isTrue: "E' Vero",                      // sets the text for "isTrue" radio button
      isFalse: "E' Falso",                    // sets the text for "isFalse" radio button
      and: "Ed anche",
      or: "Oppure",
      selectValue: "-Seleziona-",
      operator: "Operator",
      value: "Value",
      cancel: "Cancel"
      /***************************************************************************/
  });

kendo.ui.FilterMenu.prototype.options.operators =
  $.extend(kendo.ui.FilterMenu.prototype.options.operators, {

      /* FILTER MENU OPERATORS (for each supported data type) 
       ****************************************************************************/
      string: {
          eq: "E' uguale a",
          neq: "Non è uguale a",
          startswith: "Inizia con",
          contains: "Contiene",
          doesnotcontain: "Non contiene",
          endswith: "Finisce con"
      },
      number: {
          eq: "E' uguale a",
          neq: "Non è uguale a",
          gte: "E' maggiore di o uguale a",
          gt: "E' maggiore di",
          lte: "E' minore di o uguale a",
          lt: "E' minore di"
      },
      date: {
          eq: "E' uguale a",
          neq: "Non è uguale a",
          gte: "E' successiva o uguale a",
          gt: "E' successiva a",
          lte: "E' antecedente o uguale a",
          lt: "E' antecedente a"
      },
      enums: {
          eq: "E' uguale a",
          neq: "Non è uguale a"
      }
      /***************************************************************************/
  });

kendo.ui.Pager.prototype.options.messages =
  $.extend(kendo.ui.Pager.prototype.options.messages, {

      /* PAGER MESSAGES 
       ****************************************************************************/
      display: "{0} - {1} di {2} elementi",
      empty: "Nessun elemento da visualizzare",
      page: "Pagina",
      of: "di {0}",
      itemsPerPage: "elementi per pagina",
      first: "Vai alla prima pagina",
      previous: "Vai alla pagina precedente",
      next: "Vai alla pagina successiva",
      last: "Vai all'ultima pagina",
      refresh: "Aggiorna"
      /***************************************************************************/
  });

kendo.ui.Validator.prototype.options.messages =
  $.extend(kendo.ui.Validator.prototype.options.messages, {

      /* VALIDATOR MESSAGES 
       ****************************************************************************/
      required: "{0} è obbligatorio",
      pattern: "{0} non è valido",
      min: "{0} dev'essere maggiore di o uguale a {1}",
      max: "{0} dev'essere minore di o uguale a {1}",
      step: "{0} non è valido",
      email: "{0} non è un'indirizzo e-mail valido",
      url: "{0} non è un'indirizzo web valido",
      date: "{0} non è una data valida"
      /***************************************************************************/
  });

kendo.ui.ImageBrowser.prototype.options.messages =
  $.extend(kendo.ui.ImageBrowser.prototype.options.messages, {

      /* IMAGE BROWSER MESSAGES 
       ****************************************************************************/
      uploadFile: "Caricamento file",
      orderBy: "Ordina per",
      orderByName: "Nome",
      orderBySize: "Dimensione",
      directoryNotFound: "Una cartella con questo nome non è stata trovata.",
      emptyFolder: "Cartella Vuota",
      deleteFile: 'Sei sicuro di voler cancellare "{0}"?',
      invalidFileType: "Il file selezionato \"{0}\" non è valido. I tipi di file supportati sono {1}.",
      overwriteFile: "Un file con il nome \"{0}\" esiste già nella cartella corrente. Vuoi sovrascriverlo?",
      dropFilesHere: "Trascina un file qui per caricarlo",
      search: "Cerca"
      /***************************************************************************/
  });

kendo.ui.Editor.prototype.options.messages =
  $.extend(kendo.ui.Editor.prototype.options.messages, {

      /* EDITOR MESSAGES 
       ****************************************************************************/
      bold: "Grassetto",
      italic: "Corsivo",
      underline: "Sottolineato",
      strikethrough: "Barrato",
      superscript: "Apice",
      subscript: "Pedice",
      justifyCenter: "Centra testo",
      justifyLeft: "Allinea testo a sinistra",
      justifyRight: "Allinea testo a destra",
      justifyFull: "Giustifica testo",
      insertUnorderedList: "Inserisci lista non ordinata",
      insertOrderedList: "Inserisci lista ordinata",
      indent: "Aumenta rientro",
      outdent: "Riduci rientro",
      createLink: "Inserisci collegamento",
      unlink: "Rimuovi collegamento",
      insertImage: "Inserisci immagine",
      insertHtml: "Inserisci HTML",
      viewHtml: "Visualizza l'HTML",
      fontName: "Seleziona il tipo di caratteri",
      fontNameInherit: "(tipo ereditato)",
      fontSize: "Seleziona la dimensione dei caratteri",
      fontSizeInherit: "(dimensione ereditata)",
      formatBlock: "Formatta",
      formatting: "Formatta",
      foreColor: "Colore",
      backColor: "Colore di sfondo",
      style: "Stili",
      emptyFolder: "Cartella Vuota",
      uploadFile: "Carica file",
      orderBy: "Ordina per:",
      orderBySize: "Dimensione",
      orderByName: "Nome",
      invalidFileType: "Il file selezionato \"{0}\" non è valido. I tipi di file supportati sono {1}.",
      deleteFile: 'Sei sicuro di voler cancellare "{0}"?',
      overwriteFile: 'Un file con il nome "{0}" esiste già nella cartella corrente. Vuoi sovrascriverlo?',
      directoryNotFound: "Una cartella con questo nome non è stata trovata.",
      imageWebAddress: "Indirizzo Web",
      imageAltText: "Testo alternativo",
      linkWebAddress: "Indirizzo Web",
      linkText: "Testo",
      linkToolTip: "ToolTip",
      linkOpenInNewWindow: "Apri il link in una nuova finestra",
      dialogUpdate: "Aggiorna",
      dialogInsert: "Inserisci",
      dialogButtonSeparator: "o",
      dialogCancel: "Annulla",
      createTable: "Crea tabella",
      addColumnLeft: "Aggiungi una colonna a sinistra",
      addColumnRight: "Aggiungi una colonna a destra",
      addRowAbove: "Aggiungi una riga sopra",
      addRowBelow: "Aggiungi una riga sotto",
      deleteRow: "Cancella riga",
      deleteColumn: "Cancella colonna"
      /***************************************************************************/
  });

kendo.ui.NumericTextBox.prototype.options =
    $.extend(kendo.ui.NumericTextBox.prototype.options, {

        /* NUMERIC TEXT BOX OR INTEGER TEXT BOX MESSAGES
        ****************************************************************************/
        upArrowText: "Incrementa il valore",
        downArrowText: "Decrementa il valore"
        /***************************************************************************/
    });

kendo.ui.Upload.prototype.options.localization =
	$.extend(kendo.ui.Upload.prototype.options.localization, {

	    /* UPLOAD LOCALIZATION
         ****************************************************************************/
	    select: "Seleziona file...",
	    cancel: "Annulla",
	    retry: "Riprova",
	    remove: "Rimuovi",
	    uploadSelectedFiles: "Carica file",
	    dropFilesHere: "trascina un file qui per caricarlo",
	    statusUploading: "caricamento in corso",
	    statusUploaded: "caricato",
	    statusWarning: "attenzione",
	    statusFailed: "fallito",
	    headerStatusUploading: "Caricamento in corso...",
	    headerStatusUploaded: "Eseguito"
	    /***************************************************************************/
	});

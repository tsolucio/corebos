# bsmSelect - Better Select Multiple #

based on asmSelect - Alternate Select Multiple by Ryan Cramer

  * [Google code project](http://code.google.com/p/jquery-asmselect/)
  * [Github page](http://github.com/ryancramerdesign/jquery-asmSelect)
  * [Ryan's article about asmSelect](http://www.ryancramer.com/journal/entries/select_multiple/)
  * the original README can be found in the project root folder

## Demo ##

[bsmSelect demo](http://www.suumit.com/projects/bsmSelect/examples/index.html)

## Usage ##

Include jquery, bsmSelect, and css in document head:

    <script type="text/javascript" src="jquery.js"></script>
    <script type="text/javascript" src="jquery.bsmselect.js"></script>
    <link rel="stylesheet" type="text/css" href="jquery.bsmselect.css" />

Use a jQuery selector in your document ready function:

    jQuery(function($) {
        $("select[multiple]").bsmSelect();
    });

If desired, you can specify options when you call the plugin:

    jQuery(function($) {
        $("select[multiple]").bsmSelect({
            addItemTarget: 'top'
        });
    });

The newly created select default option is the original select title attribute:

    <select name="cities" multiple="multiple" title="Please select a city">
    ...
    </select>

## Requirements ##

* jQuery 1.4+ (you might need a newer version for IE9 compatibility)

## Options ##

### Primary Options ###

* listType:

  * Specify what list will be created or used as part of the bsmSelect.
  * Can accept a callback that accepts the original <select> as an argument and
    returns a jQuery object with a single list.
  * Allowed values:
      * 'ol'
      * 'ul'
      * function(originalSelect) { // your code; return $('&lt;ul&gt;'); }
  * Default: 'ol'

* highlightEffect:

  * Show a quick highlight of what item was added or removed?
  * Allowed values:
    * an animation function
  * Default: $.noop (no effect)

* showEffect:

  * Animate the addition of an item to the list
  * Allowed values:
    * an animation function
  * Default: $.bsmSelect.effects.show

* hideEffect:

  * Animate the removal of an items from the list
  * Allowed values:
    * an animation function
  * Default: $.bsmSelect.effects.remove

* hideWhenAdded:

  * Stop showing in select after item has been added?
  * Allowed values: true or false
  * Default: false
  * Note: Only functional in Firefox 3 at this time.

* addItemTarget:

  * Where to place new selected items that are added to the list.
  * Allowed values: 'top' or 'bottom' or 'original' to keep the original select
    sort order
  * Default: 'bottom'
  * Note: When using the 'original' mode, the sort order can be overriden by setting
    the 'bsm-order' data on each option.

* debugMode:

  * Keeps original select multiple visible so that you can monitor live changes
    made to it when debugging.
  * Default: false

* extractLabel:

  * A function to compute the list element name from the option object
  * Default: extract the option html

* plugins

  * An array of plugins objects to enable (they only are required to have an `init`
    method which is called on init with the Bsmselect instance as single argument).
  * Default: an empty array (no plugin enabled by default)

### Text Labels ###

* title

  * Text used for the default select label (when original select title attribute
    is not set)
  * Default: 'Select...'

* removeLabel:

  * Text used for the remove link of each list item.
  * Default: 'remove'

* highlightAddedLabel:

  * Text that precedes highlight of item added.
  * Default: 'Added: '

* highlightRemovedLabel:

  * Text that precedes highlight of item removed.
  * Default: 'Removed: '

### Modifiable CSS Classes ###

* containerClass:

  * Class for container that wraps the entire bsmSelect.
  * Default: 'bsmContainer'

* selectClass:

  * Class for the newly created <select>.
  * Default: 'bsmSelect'

* listClass:

  * Class for the newly created list of listType (ol or ul).
  * Default: 'bsmList'

* listSortableClass:

  * Another class given to the list when sortable is active.
  * Default: 'bsmListSortable'

* listItemClass:

  * Class given to the <li> list items.
  * Default: 'bsmListItem'

* listItemLabelClass:

  * Class for the label text that appears in list items.
  * Default: 'bsmListItemLabel'

* removeClass:

  * Class given to the remove link in each list item.
  * Any element found in the <li> with this class will remove it.
  * If you give the <li> this class, clicking anywhere on the <li> will remove it.
  * Default: 'bsmListItemRemove'

* highlightClass:

  * Class given to the highlight <span>.
  * Default: 'bsmHighlight'

## Authors and contributors ##

  * [Ryan Cramer](http://www.ryancramer.com/) is the author of the original asmSelect
  * [Victor Berchet](http://github.com/vicb) is the author of bsmSelect
  * [Andy Fowler](http://github.com/andyfowler) has contributed many enhancements
  * [Cracky](https://github.com/Cracky)
  * [Marc Busqué](https://github.com/laMarciana) has contributed to fix
    [issue #21](https://github.com/vicb/bsmSelect/issues/21) and with minimal CSS
  * [DrewBe121212](https://github.com/DrewBe121212) has fixed issues 28 et 29.

## History ##

see [history.md](history.md).

## Related Projects ##

  * [Chosen](https://github.com/harvesthq/chosen/)


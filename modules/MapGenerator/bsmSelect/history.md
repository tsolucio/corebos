# bsmSelect #

v1.4.7 - 2013-12-19

  * Allow preventing default actions (add/drop) by preventing the default action on
    the original select 'change' event

v1.4.6 - 2013-03-02

  * Fix issue #29: allow options.listItemClass to contain multiple space separated
    CSS classes

v1.4.5 - 2013-02-18

  * Fix issue #28: jQuery.data(el, data) returns undefined when the data attribute
    is not defined on the element.

v1.4.4 - 2012-01-19

  * Fix issue #21, that's the scroll problem, adding the css class '.bsmScrollWorkaround'
    and applying it with js when needed
  * Minimal CSS modification: set width to auto in list items

v1.4.3 - 2011-05-05

  * Fix the position when appending to the list (GH-9)

v1.4.2 - 2011-02-22

  * Add the 'original' mode for the 'addItemTarget' option

v1.4.1 - 2010-11-26

  * Do not force refresh the select for IE > 7 to remove flickering (this bug
    seems to be gone from IE8)

v1.4.0 - 2010-09-05

  * API break: animate & highlight options (the compatibility plugin might be used
    for backward compatibility),
  * core (bsmSelect) code cleanup,
  * store relations in element data.

v1.3.0 - 2010-09-03

  * API BREAK: $.fn.bsmSelect moved to $.bsmSelect,
  * new basic plugin infrastructure,
  * restore the sortable functionality through a plugin.

v1.2.2 - 2010-08-27

  * ensure id uniqueness (fix github issue #3)

v1.2.1 - 2010-08-14

  * fix the highlight effect
  * a few tweaks
  * syntax

v1.2.0 - 2010-08-13

  * refactoring,
  * drop of the sortable functionality

v1.1.1 - 2010-07-26:

  * Latest changes from Ryan Cramer's asmSelect
  * Enhancements from Andy Fowler
  * improved custom animations
  * support for optgroup
  * ability to set the default select title via the configuration
  * make the original label point to the new select
  * ability to customize the way list label gets extracted from the option

v1.0 - 2010-07-02:

  * Renamed asmSelect to bsmSelect
  * Refactor the code in order to implement plugin best practices
  * Ability to use custom animations (see options and examples)

# bsmSelect plugins #

## sortable ##

Allow sorting the item list.

v1.1.2 - 2011-11-14

  * Fix for strict mode (do not use arguments.callee)

v1.1.1 - 2010-11-17

  * Fix issue 5: "Multiple selectors will cause values to be applied to first form
    "element"

v1.1.0 - 2010-09-05

  * Can be instanciated without the new keyword,
  * Ability to override default options,
  * Reflect core code updates.

v1.0.0 - 2010-09-03

  * initial relase

## compatibility ##

Allow backward compatibility for animate & highlight options (dropped after bsmSelect
v1.3)

v1.0.1 - 2011-11-14

  * Fix for strict mode (do not use arguments.callee)

v1.0.0 - 2010-09-05

  * intial release

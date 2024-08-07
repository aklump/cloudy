<!--
id: localize
tags: usage
-->

# Localization/Translation

Note: this is not available until after the configuration has been bootstrapped.

## Translate or reword

You can translate or reword certain strings with this feature

    translate:
      ids:
      - Completed successfully.
      - Failed.
      strings:
        en:
        - Installation succeeded.
        - Installation failed.


* The ids are the strings that appear normally.
* To begin a translation, copy the entire ids array as `translate.strings.LANG` and then alter the strings you mean to.  The value of `LANG` must be a [two or three letter ISO 639 language code](https://www.loc.gov/standards/iso639-2/php/code_list.php).
* The indexes of the ids array must match with the `translate.strings.LANG` array.

## Implementation

To implement localization in a script, do like this:

     echo_title $(translate "Welcome to your new script!")
     
Then add that to `translate.ids`:

    translate:
      ids:
      - Completed successfully.
      - Failed. 
      - Welcome to your new script!    

# Echo $array_csv__array as CSV
#
# @global array $array_csv__array
#
# @option --prose Use comma+space and then the word "all" as the final separator as when writing English prose, e.g. "do, re and mi".
# @option --quotes Wrap each item with double quotes.
# @option --single-quotes Wrap each item with single quotes.
#
# @echo The CSV string
#
# @code
#   array_csv__array=('foo bar' 'baz' zulu)
#   csv=$(array_csv)
# @endcode
function array_csv()

<!--
id: strings
tags: bash
-->

# String Manipulation

Taken from [Advanced Bash-Scripting Guide: Chapter 10. Manipulating Variables](https://www.tldp.org/LDP/abs/html/string-manipulation.html)

## Trim the dot from right side of string

```bash
${string%.}
```
    
## Trim the dot from left side of string

```bash
${string#.}
```

## Trim all leading and trailing whitespace

```bash
string="   foo    "
string=${foo## }
string=${foo%% }
# "$string" == "foo"
```    

Or use the Cloudy helpers:

```bash
string="   foo    "
string="$(ltrim "$string")"
string="$(rtrim "$string")"
# "$string" == "foo"
```

## Get string length

```bash
${#string}
```

# Currency Converter for Alfred App

Currency Converter is a workflow for Alfred App on Mac. This is based on the Google currency conversion syntax, such as `30 USD in GBP`.

The basic way to use the converter is to call Alfred with:

> 30 USD in EUR

Note that `in` and `to` are interchangable. The default currency is GBP (can be changed) so the following will convert from USD to GBP:

> 30USD

Another shorthand is:

> 30$ to €

And even shorter:

> 30$ €

All FX rates come from [fixer.io](http://fixer.io/).

## Usage

The alfred command is `c`, and hitting return will copy the converted currency value to the clipboard.

## Installation

[Download](https://github.com/remy/alfred-currency/raw/master/Currency%20Converter.alfredworkflow) and open the workflow file

## Requirements

You must installed:

 - [Alfred 2.0](http://www.alfredapp.com/) or higher
 - [Alfred PowerPack](http://www.alfredapp.com/powerpack/)

## Author

- [Remy sharp](https://remysharp.com)
- Søren Louv-Jansen, [Konscript](http://www.konscript.com) ([original author](https://github.com/sqren/alfred-currency))

#!/bin/sh
php-cs-fixer fix ./src --fixers=psr0,encoding,short_tag,braces,elseif,eof_ending,function_declaration,indentation,line_after_namespace,linefeed,lowercase_constants,lowercase_keywords,multiple_use,parenthesis,php_closing_tag,trailing_spaces,visibility,duplicate_semicolon,extra_empty_lines,include,multiline_array_trailing_comma,namespace_no_leading_whitespace,new_with_braces,object_operator,operators_spaces,remove_lines_between_uses,return,single_array_no_trailing_comma,spaces_before_semicolon,spaces_cast,standardize_not_equal,ternary_spaces,unused_use,whitespacy_lines,yoda_conditions,concat_with_spaces,ordered_use,short_array_syntax -vvv

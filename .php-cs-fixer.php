<?php

declare(strict_types=1);

$header = <<<'EOF'

    * MineAdmin is committed to providing solutions for quickly building web applications
    * Please view the LICENSE file that was distributed with this source code,
    * For the full copyright and license information.
    * Thank you very much for using MineAdmin.

    * @Author X.Mo <root@imoi.cn>
    * @Link   https://www.mineadmin.com/
    * @Github  https://github.com/kanyxmo
    * @Document https://doc.mineadmin.com/

    EOF;

$config = new PhpCsFixer\Config();

return $config
    ->setRules([
        '@DoctrineAnnotation' => true,
        '@PhpCsFixer' => true,
        '@PHP80Migration' => true,
        '@PHP81Migration' => true,
        '@Symfony' => true,
        '@PSR12' => true,
        'header_comment' => ['header' => $header, 'comment_type' => 'PHPDoc', 'location' => 'after_declare_strict'],
        'new_with_braces' => false,
        'multiline_whitespace_before_semicolons' => true,
        'braces' => [
            'allow_single_line_closure' => true,
            'allow_single_line_anonymous_class_with_empty_body' => true,
        ],
        'non_printable_character' => ['use_escape_sequences_in_strings' => true],
        'encoding' => true,
        'octal_notation' => false, // 0123 to 0o123
        'psr_autoloading' => true,
        'class_reference_name_casing' => true,
        'integer_literal_case' => true,
        'lowercase_keywords' => true,
        'declare_equal_normalize' => true,
        'function_typehint_space' => true,
        'include' => true,
        'lowercase_cast' => true,
        'modernize_types_casting' => true,
        'class_attributes_separation' => [
            'elements' => [
                'const' => 'only_if_meta',
                'method' => 'only_if_meta',
                'property' => 'only_if_meta',
                'trait_import' => 'only_if_meta',
            ],
        ],
        'class_definition' => [
            'single_line' => true,
            'single_item_single_line' => true,
            'space_before_parenthesis' => true,
            // 'inline_constructor_arguments' => true,
        ],
        'short_scalar_cast' => true,
        'no_short_bool_cast' => true,
        'no_unset_cast' => true,
        'no_multiline_whitespace_around_double_arrow' => true,
        'no_spaces_around_offset' => true,
        'no_whitespace_before_comma_in_array' => true,
        'object_operator_without_whitespace' => true,
        'single_blank_line_before_namespace' => true,
        'ternary_operator_spaces' => true,
        'trailing_comma_in_multiline' => true,
        'trim_array_spaces' => true,
        'unary_operator_spaces' => true,
        'whitespace_after_comma_in_array' => true,
        'single_trait_insert_per_statement' => false,
        'array_syntax' => [
            'syntax' => 'short',
        ],
        'list_syntax' => [
            'syntax' => 'short',
        ],
        'no_trailing_comma_in_singleline_array' => true,
        'no_whitespace_before_comma_in_array' => true,
        'normalize_index_brace' => true,
        'general_phpdoc_annotation_remove' => [
            'annotations' => [
                // 'author',
            ],
        ],
        'ordered_imports' => [
            'imports_order' => [
                'class', 'function', 'const',
            ],
            // 'sort_algorithm' => 'alpha',
        ],

        'yoda_style' => [
            'always_move_variable' => false,
            'equal' => false,
            'identical' => false,
        ],

        // 'constant_case' => [
        //     'case' => 'lower',
        // ],
        'array_push' => true,
        'mb_str_functions' => false,
        'ereg_to_preg' => true,
        'modernize_strpos' => true,
        'no_mixed_echo_print' => true,
        'pow_to_exponentiation' => true,
        'random_api_migration' => true,
        'set_type_to_cast' => true,
        'no_useless_else' => true, // 删除没有使用的else节点
        'no_useless_return' => true, // 删除没有使用的return语句
        'self_accessor' => true, // 在当前类中使用 self 代替类名
        'php_unit_construct' => true,
        'single_quote' => true, // 简单字符串应该使用单引号代替双引号
        'no_unused_imports' => true, // 删除没用到的use
        'no_singleline_whitespace_before_semicolons' => true, // 禁止只有单行空格和分号的写法
        'no_empty_statement' => true, // 多余的分号
        'no_whitespace_in_blank_line' => true, // 删除空行中的空格
        'standardize_not_equals' => true, // 使用 <> 代替 !=
        'combine_consecutive_unsets' => true, // 当多个 unset 使用的时候，合并处理
        'concat_space' => ['spacing' => 'one'], // .拼接必须有空格分割
        'array_indentation' => true, // 数组的每个元素必须缩进一次
        'no_superfluous_phpdoc_tags' => false, // 移出没有用的注释
        'blank_line_before_statement' => [
            'statements' => [
                'break',
                'continue',
                'declare',
                'return',
                'throw',
                'try',
            ],
        ],
        'lowercase_static_reference' => true, // 静态调用为小写
        'magic_constant_casing' => true,
        'magic_method_casing' => true,
        'native_function_casing' => true,
        'native_function_type_declaration_casing' => true,
        'cast_spaces' => true,
        'no_blank_lines_after_class_opening' => true,
        'no_null_property_initialization' => true,
        'phpdoc_separation' => false, // 不同注释部分按照单空行隔开
        'phpdoc_single_line_var_spacing' => true,
        'no_php4_constructor' => true,
        'class_attributes_separation' => true,
        'declare_strict_types' => true,
        'linebreak_after_opening_tag' => true,
        'not_operator_with_successor_space' => true,
        'not_operator_with_space' => false,
        'ordered_class_elements' => [
            'order' => [
                'use_trait',
                'public',
                'protected',
                'private',
                // 'case',
                'property',
                'property_static',
                'property_public',
                'property_public_static',
                'property_public_readonly',
                'property_protected',
                'property_protected_readonly',
                'property_protected_static',
                'property_private',
                'property_private_readonly',
                'property_private_static',
                'constant',
                'constant_public',
                'constant_protected',
                'constant_private',
                'construct',
                'destruct',
                'magic',
                'method',
                'method_abstract',
                'method_static',
                'method_public',
                'method_public_abstract',
                'method_public_static',
                'method_public_abstract_static',
                'method_protected',
                'method_protected_abstract',
                'method_protected_static',
                'method_protected_abstract_static',
                'method_private',
                'method_private_abstract',
                'method_private_static',
                'method_private_abstract_static',
            ],
            // 'sort_algorithm' => 'alpha',
        ],
        'ordered_interfaces' => true,
        'protected_to_private' => true,
        'ordered_traits' => true,
        'self_static_accessor' => true,
        'php_unit_strict' => false,
        'single_class_element_per_statement' => true,
        'single_trait_insert_per_statement' => true,
        'visibility_required' => true,
        'date_time_immutable' => true,
        'comment_to_phpdoc' => true,
        'multiline_comment_opening_closing' => false,
        'no_empty_comment' => true,
        'no_trailing_whitespace_in_comment' => true,
        // 'single_line_comment_spacing' => true,
        'single_line_comment_style' => ['comment_types' => [
            'hash',
            'asterisk',
        ]],
        'control_structure_continuation_position' => [
            'position' => 'same_line',
        ],
        'elseif' => true,
        'no_break_comment' => true,
        'no_trailing_comma_in_list_call' => true,
        'simplified_if_return' => true,
        'doctrine_annotation_array_assignment' => ['operator' => ':'],
        'combine_nested_dirname' => true,
        'fopen_flag_order' => true,
        'implode_call' => true,
        /*
         *      #var int
         *-    private $foo;
         *+    private int $foo;
         */
        'phpdoc_to_property_type' => false, // ['scalar_types' => false],
        'phpdoc_to_return_type' => [
            'scalar_types' => true,
        ],
        'regular_callable_call' => true,
        'use_arrow_functions' => true,
        'void_return' => true,
        'fully_qualified_strict_types' => true,
        'global_namespace_import' => ['import_classes' => true, 'import_constants' => true, 'import_functions' => true],
        'no_leading_import_slash' => true,
        'no_unneeded_import_alias' => true,
        'combine_consecutive_issets' => true,
        'declare_parentheses' => true,
        'dir_constant' => true,
        'explicit_indirect_variable' => true,
        'get_class_to_class_keyword' => true,
        'is_null' => true,
        'list_syntax' => ['syntax' => 'short'],
        'blank_line_after_namespace' => true,
        'clean_namespace' => true,
        'no_leading_namespace_whitespace' => true,
        'single_blank_line_before_namespace' => true,
        'no_homoglyph_names' => true,
        'assign_null_coalescing_to_coalesce_equal' => true,
        'binary_operator_spaces' => [
            'operators' => [
                '=' => 'single_space',
                'xor' => null,
                '+=' => 'align_single_space',
                '===' => 'align_single_space_minimal',
                '=>' => 'single_space',
                // '=>'  => 'align',
                // ['=', '*', '/', '%', '<', '>', '|', '^', '+', '-', '&', '&=', '&&', '||', '.=', '/=', '=>', '==', '>=', '===', '!=', '<>', '!==', '<=', 'and', 'or', 'xor',' -=', '%=', '*=', '|=', '+=', '<<', '<<=', '>>', '>>=', '^=', '**',' **=', '<=>', '??', '??=']  => 'single_space',
            ],
        ],
        'concat_space' => ['spacing' => 'one'],
        'logical_operators' => true,
        'no_space_around_double_colon' => true,
        'object_operator_without_whitespace' => true,
        'operator_linebreak' => true,
        'standardize_increment' => true,
        'standardize_not_equals' => true,
        'ternary_operator_spaces' => true,
        'ternary_to_elvis_operator' => true,
        'ternary_to_null_coalescing' => true,
        'unary_operator_spaces' => true,
        'blank_line_after_opening_tag' => true,
        'full_opening_tag' => true,
        'no_closing_tag' => true,
        'align_multiline_comment' => ['comment_type' => 'phpdocs_only'],
        'no_blank_lines_after_phpdoc' => true,
        'phpdoc_add_missing_param_annotation' => true,
        'phpdoc_align' => [
            'tags' => ['method', 'param', 'property', 'property-read', 'property-write', 'return', 'throws', 'type', 'var'],
            'align' => 'left', // 'left', 'vertical'
        ],
        'phpdoc_indent' => true,
        'phpdoc_annotation_without_dot' => false,
        'phpdoc_inline_tag_normalizer' => true,
        'phpdoc_line_span' => [
            'const' => 'multi',
            'property' => 'multi',
            'method' => 'multi', // multi', 'single', null
        ],
        'phpdoc_order' => true,
        'phpdoc_separation' => true,
        'phpdoc_single_line_var_spacing' => true,
        'phpdoc_trim' => true,
        'return_assignment' => true,
        'multiline_whitespace_before_semicolons' => ['strategy' => 'no_multi_line'],
        'no_empty_statement' => true,
        'no_singleline_whitespace_before_semicolons' => true,
        'semicolon_after_instruction' => true,
        'explicit_string_variable' => true,
        'heredoc_to_nowdoc' => true,
        'simple_to_complex_string_variable' => true,
        'string_length_to_empty' => true,
        'array_indentation' => true,
        'compact_nullable_typehint' => true,
        'heredoc_indentation' => [
            'indentation' => 'start_plus_one',
        ],
        'indentation_type' => true,
        'line_ending' => true,
        'method_chaining_indentation' => true,
        'no_extra_blank_lines' => true,
        'no_spaces_around_offset' => true,
        'no_spaces_inside_parenthesis' => true,
        'no_trailing_whitespace' => true,
        'no_whitespace_in_blank_line' => true,
        'single_blank_line_at_eof' => true,
    ])
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->exclude('bin')
            ->exclude('public')
            ->exclude('runtime')
            ->exclude('vendor')
            ->exclude('hyperf')
            ->exclude('storage')
            ->in(__DIR__)
    )
    ->setRiskyAllowed(true)
    ->setUsingCache(false)
    ->setLineEnding("\n");
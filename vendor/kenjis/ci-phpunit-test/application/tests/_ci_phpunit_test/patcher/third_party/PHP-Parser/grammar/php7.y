%pure_parser
%expect 2

%tokens

%%

start:
    top_statement_list                                      { $$ = $this->handleNamespaces($1); }
;

top_statement_list:
      top_statement_list top_statement                      { pushNormalizing($1, $2); }
    | /* empty */                                           { init(); }
;

reserved_non_modifiers:
      T_INCLUDE | T_INCLUDE_ONCE | T_EVAL | T_REQUIRE | T_REQUIRE_ONCE | T_LOGICAL_OR | T_LOGICAL_XOR | T_LOGICAL_AND
    | T_INSTANCEOF | T_NEW | T_CLONE | T_EXIT | T_IF | T_ELSEIF | T_ELSE | T_ENDIF | T_ECHO | T_DO | T_WHILE
    | T_ENDWHILE | T_FOR | T_ENDFOR | T_FOREACH | T_ENDFOREACH | T_DECLARE | T_ENDDECLARE | T_AS | T_TRY | T_CATCH
    | T_FINALLY | T_THROW | T_USE | T_INSTEADOF | T_GLOBAL | T_VAR | T_UNSET | T_ISSET | T_EMPTY | T_CONTINUE | T_GOTO
    | T_FUNCTION | T_CONST | T_RETURN | T_PRINT | T_YIELD | T_LIST | T_SWITCH | T_ENDSWITCH | T_CASE | T_DEFAULT
    | T_BREAK | T_ARRAY | T_CALLABLE | T_EXTENDS | T_IMPLEMENTS | T_NAMESPACE | T_TRAIT | T_INTERFACE | T_CLASS
    | T_CLASS_C | T_TRAIT_C | T_FUNC_C | T_METHOD_C | T_LINE | T_FILE | T_DIR | T_NS_C | T_HALT_COMPILER
;

semi_reserved:
      reserved_non_modifiers
    | T_STATIC | T_ABSTRACT | T_FINAL | T_PRIVATE | T_PROTECTED | T_PUBLIC
;

identifier:
      T_STRING                                              { $$ = $1; }
    | semi_reserved                                         { $$ = $1; }
;

namespace_name_parts:
      T_STRING                                              { init($1); }
    | namespace_name_parts T_NS_SEPARATOR T_STRING          { push($1, $3); }
;

namespace_name:
      namespace_name_parts                                  { $$ = Name[$1]; }
;

top_statement:
      statement                                             { $$ = $1; }
    | function_declaration_statement                        { $$ = $1; }
    | class_declaration_statement                           { $$ = $1; }
    | T_HALT_COMPILER
          { $$ = Stmt\HaltCompiler[$this->lexer->handleHaltCompiler()]; }
    | T_NAMESPACE namespace_name ';'                        { $$ = Stmt\Namespace_[$2, null]; }
    | T_NAMESPACE namespace_name '{' top_statement_list '}' { $$ = Stmt\Namespace_[$2, $4]; }
    | T_NAMESPACE '{' top_statement_list '}'                { $$ = Stmt\Namespace_[null,     $3]; }
    | T_USE use_declarations ';'                            { $$ = Stmt\Use_[$2, Stmt\Use_::TYPE_NORMAL]; }
    | T_USE use_type use_declarations ';'                   { $$ = Stmt\Use_[$3, $2]; }
    | group_use_declaration ';'                             { $$ = $1; }
    | T_CONST constant_declaration_list ';'                 { $$ = Stmt\Const_[$2]; }
;

use_type:
      T_FUNCTION                                            { $$ = Stmt\Use_::TYPE_FUNCTION; }
    | T_CONST                                               { $$ = Stmt\Use_::TYPE_CONSTANT; }
;

/* Using namespace_name_parts here to avoid s/r conflict on T_NS_SEPARATOR */
group_use_declaration:
      T_USE use_type namespace_name_parts T_NS_SEPARATOR '{' unprefixed_use_declarations '}'
          { $$ = Stmt\GroupUse[Name[$3], $6, $2]; }
    | T_USE use_type T_NS_SEPARATOR namespace_name_parts T_NS_SEPARATOR '{' unprefixed_use_declarations '}'
          { $$ = Stmt\GroupUse[Name[$4], $7, $2]; }
    | T_USE namespace_name_parts T_NS_SEPARATOR '{' inline_use_declarations '}'
          { $$ = Stmt\GroupUse[Name[$2], $5, Stmt\Use_::TYPE_UNKNOWN]; }
    | T_USE T_NS_SEPARATOR namespace_name_parts T_NS_SEPARATOR '{' inline_use_declarations '}'
          { $$ = Stmt\GroupUse[Name[$3], $6, Stmt\Use_::TYPE_UNKNOWN]; }
;

unprefixed_use_declarations:
      unprefixed_use_declarations ',' unprefixed_use_declaration
          { push($1, $3); }
    | unprefixed_use_declaration                            { init($1); }
;

use_declarations:
      use_declarations ',' use_declaration                  { push($1, $3); }
    | use_declaration                                       { init($1); }
;

inline_use_declarations:
      inline_use_declarations ',' inline_use_declaration    { push($1, $3); }
    | inline_use_declaration                                { init($1); }
;

unprefixed_use_declaration:
      namespace_name                                        { $$ = Stmt\UseUse[$1, null, Stmt\Use_::TYPE_UNKNOWN]; }
    | namespace_name T_AS T_STRING                          { $$ = Stmt\UseUse[$1, $3, Stmt\Use_::TYPE_UNKNOWN]; }
;

use_declaration:
      unprefixed_use_declaration                            { $$ = $1; }
    | T_NS_SEPARATOR unprefixed_use_declaration             { $$ = $2; }
;

inline_use_declaration:
      unprefixed_use_declaration                            { $$ = $1; $$->type = Stmt\Use_::TYPE_NORMAL; }
    | use_type unprefixed_use_declaration                   { $$ = $2; $$->type = $1; }
;

constant_declaration_list:
      constant_declaration_list ',' constant_declaration    { push($1, $3); }
    | constant_declaration                                  { init($1); }
;

constant_declaration:
    T_STRING '=' expr                                       { $$ = Node\Const_[$1, $3]; }
;

class_const_list:
      class_const_list ',' class_const                      { push($1, $3); }
    | class_const                                           { init($1); }
;

class_const:
    identifier '=' expr                                     { $$ = Node\Const_[$1, $3]; }
;

inner_statement_list:
      inner_statement_list inner_statement                  { pushNormalizing($1, $2); }
    | /* empty */                                           { init(); }
;

inner_statement:
      statement                                             { $$ = $1; }
    | function_declaration_statement                        { $$ = $1; }
    | class_declaration_statement                           { $$ = $1; }
    | T_HALT_COMPILER
          { throw new Error('__HALT_COMPILER() can only be used from the outermost scope', attributes()); }
;

non_empty_statement:
      '{' inner_statement_list '}'                          { $$ = $2; }
    | T_IF '(' expr ')' statement elseif_list else_single
          { $$ = Stmt\If_[$3, ['stmts' => toArray($5), 'elseifs' => $6, 'else' => $7]]; }
    | T_IF '(' expr ')' ':' inner_statement_list new_elseif_list new_else_single T_ENDIF ';'
          { $$ = Stmt\If_[$3, ['stmts' => $6, 'elseifs' => $7, 'else' => $8]]; }
    | T_WHILE '(' expr ')' while_statement                  { $$ = Stmt\While_[$3, $5]; }
    | T_DO statement T_WHILE '(' expr ')' ';'               { $$ = Stmt\Do_   [$5, toArray($2)]; }
    | T_FOR '(' for_expr ';'  for_expr ';' for_expr ')' for_statement
          { $$ = Stmt\For_[['init' => $3, 'cond' => $5, 'loop' => $7, 'stmts' => $9]]; }
    | T_SWITCH '(' expr ')' switch_case_list                { $$ = Stmt\Switch_[$3, $5]; }
    | T_BREAK optional_expr ';'                             { $$ = Stmt\Break_[$2]; }
    | T_CONTINUE optional_expr ';'                          { $$ = Stmt\Continue_[$2]; }
    | T_RETURN optional_expr ';'                            { $$ = Stmt\Return_[$2]; }
    | T_GLOBAL global_var_list ';'                          { $$ = Stmt\Global_[$2]; }
    | T_STATIC static_var_list ';'                          { $$ = Stmt\Static_[$2]; }
    | T_ECHO expr_list ';'                                  { $$ = Stmt\Echo_[$2]; }
    | T_INLINE_HTML                                         { $$ = Stmt\InlineHTML[$1]; }
    | expr ';'                                              { $$ = $1; }
    | T_UNSET '(' variables_list ')' ';'                    { $$ = Stmt\Unset_[$3]; }
    | T_FOREACH '(' expr T_AS foreach_variable ')' foreach_statement
          { $$ = Stmt\Foreach_[$3, $5[0], ['keyVar' => null, 'byRef' => $5[1], 'stmts' => $7]]; }
    | T_FOREACH '(' expr T_AS variable T_DOUBLE_ARROW foreach_variable ')' foreach_statement
          { $$ = Stmt\Foreach_[$3, $7[0], ['keyVar' => $5, 'byRef' => $7[1], 'stmts' => $9]]; }
    | T_DECLARE '(' declare_list ')' declare_statement      { $$ = Stmt\Declare_[$3, $5]; }
    | T_TRY '{' inner_statement_list '}' catches optional_finally
          { $$ = Stmt\TryCatch[$3, $5, $6]; }
    | T_THROW expr ';'                                      { $$ = Stmt\Throw_[$2]; }
    | T_GOTO T_STRING ';'                                   { $$ = Stmt\Goto_[$2]; }
    | T_STRING ':'                                          { $$ = Stmt\Label[$1]; }
    | error                                                 { $$ = array(); /* means: no statement */ }
;

statement:
      non_empty_statement                                   { $$ = $1; }
    | ';'                                                   { $$ = array(); /* means: no statement */ }
;

catches:
      /* empty */                                           { init(); }
    | catches catch                                         { push($1, $2); }
;

catch:
    T_CATCH '(' name T_VARIABLE ')' '{' inner_statement_list '}'
        { $$ = Stmt\Catch_[$3, parseVar($4), $7]; }
;

optional_finally:
      /* empty */                                           { $$ = null; }
    | T_FINALLY '{' inner_statement_list '}'                { $$ = $3; }
;

variables_list:
      variable                                              { init($1); }
    | variables_list ',' variable                           { push($1, $3); }
;

optional_ref:
      /* empty */                                           { $$ = false; }
    | '&'                                                   { $$ = true; }
;

optional_ellipsis:
      /* empty */                                           { $$ = false; }
    | T_ELLIPSIS                                            { $$ = true; }
;

function_declaration_statement:
    T_FUNCTION optional_ref T_STRING '(' parameter_list ')' optional_return_type '{' inner_statement_list '}'
        { $$ = Stmt\Function_[$3, ['byRef' => $2, 'params' => $5, 'returnType' => $7, 'stmts' => $9]]; }
;

class_declaration_statement:
      class_entry_type T_STRING extends_from implements_list '{' class_statement_list '}'
          { $$ = Stmt\Class_[$2, ['type' => $1, 'extends' => $3, 'implements' => $4, 'stmts' => $6]]; }
    | T_INTERFACE T_STRING interface_extends_list '{' class_statement_list '}'
          { $$ = Stmt\Interface_[$2, ['extends' => $3, 'stmts' => $5]]; }
    | T_TRAIT T_STRING '{' class_statement_list '}'
          { $$ = Stmt\Trait_[$2, $4]; }
;

class_entry_type:
      T_CLASS                                               { $$ = 0; }
    | T_ABSTRACT T_CLASS                                    { $$ = Stmt\Class_::MODIFIER_ABSTRACT; }
    | T_FINAL T_CLASS                                       { $$ = Stmt\Class_::MODIFIER_FINAL; }
;

extends_from:
      /* empty */                                           { $$ = null; }
    | T_EXTENDS name                                        { $$ = $2; }
;

interface_extends_list:
      /* empty */                                           { $$ = array(); }
    | T_EXTENDS name_list                                   { $$ = $2; }
;

implements_list:
      /* empty */                                           { $$ = array(); }
    | T_IMPLEMENTS name_list                                { $$ = $2; }
;

name_list:
      name                                                  { init($1); }
    | name_list ',' name                                    { push($1, $3); }
;

for_statement:
      statement                                             { $$ = toArray($1); }
    | ':' inner_statement_list T_ENDFOR ';'                 { $$ = $2; }
;

foreach_statement:
      statement                                             { $$ = toArray($1); }
    | ':' inner_statement_list T_ENDFOREACH ';'             { $$ = $2; }
;

declare_statement:
      non_empty_statement                                   { $$ = toArray($1); }
    | ';'                                                   { $$ = null; }
    | ':' inner_statement_list T_ENDDECLARE ';'             { $$ = $2; }
;

declare_list:
      declare_list_element                                  { init($1); }
    | declare_list ',' declare_list_element                 { push($1, $3); }
;

declare_list_element:
      T_STRING '=' expr                                     { $$ = Stmt\DeclareDeclare[$1, $3]; }
;

switch_case_list:
      '{' case_list '}'                                     { $$ = $2; }
    | '{' ';' case_list '}'                                 { $$ = $3; }
    | ':' case_list T_ENDSWITCH ';'                         { $$ = $2; }
    | ':' ';' case_list T_ENDSWITCH ';'                     { $$ = $3; }
;

case_list:
      /* empty */                                           { init(); }
    | case_list case                                        { push($1, $2); }
;

case:
      T_CASE expr case_separator inner_statement_list       { $$ = Stmt\Case_[$2, $4]; }
    | T_DEFAULT case_separator inner_statement_list         { $$ = Stmt\Case_[null, $3]; }
;

case_separator:
      ':'
    | ';'
;

while_statement:
      statement                                             { $$ = toArray($1); }
    | ':' inner_statement_list T_ENDWHILE ';'               { $$ = $2; }
;

elseif_list:
      /* empty */                                           { init(); }
    | elseif_list elseif                                    { push($1, $2); }
;

elseif:
      T_ELSEIF '(' expr ')' statement                       { $$ = Stmt\ElseIf_[$3, toArray($5)]; }
;

new_elseif_list:
      /* empty */                                           { init(); }
    | new_elseif_list new_elseif                            { push($1, $2); }
;

new_elseif:
     T_ELSEIF '(' expr ')' ':' inner_statement_list         { $$ = Stmt\ElseIf_[$3, $6]; }
;

else_single:
      /* empty */                                           { $$ = null; }
    | T_ELSE statement                                      { $$ = Stmt\Else_[toArray($2)]; }
;

new_else_single:
      /* empty */                                           { $$ = null; }
    | T_ELSE ':' inner_statement_list                       { $$ = Stmt\Else_[$3]; }
;

foreach_variable:
      variable                                              { $$ = array($1, false); }
    | '&' variable                                          { $$ = array($2, true); }
    | list_expr                                             { $$ = array($1, false); }
;

parameter_list:
      non_empty_parameter_list                              { $$ = $1; }
    | /* empty */                                           { $$ = array(); }
;

non_empty_parameter_list:
      parameter                                             { init($1); }
    | non_empty_parameter_list ',' parameter                { push($1, $3); }
;

parameter:
      optional_param_type optional_ref optional_ellipsis T_VARIABLE
          { $$ = Node\Param[parseVar($4), null, $1, $2, $3]; }
    | optional_param_type optional_ref optional_ellipsis T_VARIABLE '=' expr
          { $$ = Node\Param[parseVar($4), $6, $1, $2, $3]; }
;

type:
      name                                                  { $$ = $this->handleScalarTypes($1); }
    | T_ARRAY                                               { $$ = 'array'; }
    | T_CALLABLE                                            { $$ = 'callable'; }
;

optional_param_type:
      /* empty */                                           { $$ = null; }
    | type                                                  { $$ = $1; }
;

optional_return_type:
      /* empty */                                           { $$ = null; }
    | ':' type                                              { $$ = $2; }
;

argument_list:
      '(' ')'                                               { $$ = array(); }
    | '(' non_empty_argument_list ')'                       { $$ = $2; }
;

non_empty_argument_list:
      argument                                              { init($1); }
    | non_empty_argument_list ',' argument                  { push($1, $3); }
;

argument:
      expr                                                  { $$ = Node\Arg[$1, false, false]; }
    | '&' variable                                          { $$ = Node\Arg[$2, true, false]; }
    | T_ELLIPSIS expr                                       { $$ = Node\Arg[$2, false, true]; }
;

global_var_list:
      global_var_list ',' global_var                        { push($1, $3); }
    | global_var                                            { init($1); }
;

global_var:
      simple_variable                                       { $$ = Expr\Variable[$1]; }
;

static_var_list:
      static_var_list ',' static_var                        { push($1, $3); }
    | static_var                                            { init($1); }
;

static_var:
      T_VARIABLE                                            { $$ = Stmt\StaticVar[parseVar($1), null]; }
    | T_VARIABLE '=' expr                                   { $$ = Stmt\StaticVar[parseVar($1), $3]; }
;

class_statement_list:
      class_statement_list class_statement                  { push($1, $2); }
    | /* empty */                                           { init(); }
;

class_statement:
      variable_modifiers property_declaration_list ';'      { $$ = Stmt\Property[$1, $2]; }
    | T_CONST class_const_list ';'                          { $$ = Stmt\ClassConst[$2]; }
    | method_modifiers T_FUNCTION optional_ref identifier '(' parameter_list ')' optional_return_type method_body
          { $$ = Stmt\ClassMethod[$4, ['type' => $1, 'byRef' => $3, 'params' => $6, 'returnType' => $8, 'stmts' => $9]]; }
    | T_USE name_list trait_adaptations                     { $$ = Stmt\TraitUse[$2, $3]; }
;

trait_adaptations:
      ';'                                                   { $$ = array(); }
    | '{' trait_adaptation_list '}'                         { $$ = $2; }
;

trait_adaptation_list:
      /* empty */                                           { init(); }
    | trait_adaptation_list trait_adaptation                { push($1, $2); }
;

trait_adaptation:
      trait_method_reference_fully_qualified T_INSTEADOF name_list ';'
          { $$ = Stmt\TraitUseAdaptation\Precedence[$1[0], $1[1], $3]; }
    | trait_method_reference T_AS member_modifier identifier ';'
          { $$ = Stmt\TraitUseAdaptation\Alias[$1[0], $1[1], $3, $4]; }
    | trait_method_reference T_AS member_modifier ';'
          { $$ = Stmt\TraitUseAdaptation\Alias[$1[0], $1[1], $3, null]; }
    | trait_method_reference T_AS T_STRING ';'
          { $$ = Stmt\TraitUseAdaptation\Alias[$1[0], $1[1], null, $3]; }
    | trait_method_reference T_AS reserved_non_modifiers ';'
          { $$ = Stmt\TraitUseAdaptation\Alias[$1[0], $1[1], null, $3]; }
;

trait_method_reference_fully_qualified:
      name T_PAAMAYIM_NEKUDOTAYIM identifier                { $$ = array($1, $3); }
;
trait_method_reference:
      trait_method_reference_fully_qualified                { $$ = $1; }
    | identifier                                            { $$ = array(null, $1); }
;

method_body:
      ';' /* abstract method */                             { $$ = null; }
    | '{' inner_statement_list '}'                          { $$ = $2; }
;

variable_modifiers:
      non_empty_member_modifiers                            { $$ = $1; }
    | T_VAR                                                 { $$ = 0; }
;

method_modifiers:
      /* empty */                                           { $$ = 0; }
    | non_empty_member_modifiers                            { $$ = $1; }
;

non_empty_member_modifiers:
      member_modifier                                       { $$ = $1; }
    | non_empty_member_modifiers member_modifier            { Stmt\Class_::verifyModifier($1, $2); $$ = $1 | $2; }
;

member_modifier:
      T_PUBLIC                                              { $$ = Stmt\Class_::MODIFIER_PUBLIC; }
    | T_PROTECTED                                           { $$ = Stmt\Class_::MODIFIER_PROTECTED; }
    | T_PRIVATE                                             { $$ = Stmt\Class_::MODIFIER_PRIVATE; }
    | T_STATIC                                              { $$ = Stmt\Class_::MODIFIER_STATIC; }
    | T_ABSTRACT                                            { $$ = Stmt\Class_::MODIFIER_ABSTRACT; }
    | T_FINAL                                               { $$ = Stmt\Class_::MODIFIER_FINAL; }
;

property_declaration_list:
      property_declaration                                  { init($1); }
    | property_declaration_list ',' property_declaration    { push($1, $3); }
;

property_declaration:
      T_VARIABLE                                            { $$ = Stmt\PropertyProperty[parseVar($1), null]; }
    | T_VARIABLE '=' expr                                   { $$ = Stmt\PropertyProperty[parseVar($1), $3]; }
;

expr_list:
      expr_list ',' expr                                    { push($1, $3); }
    | expr                                                  { init($1); }
;

for_expr:
      /* empty */                                           { $$ = array(); }
    | expr_list                                             { $$ = $1; }
;

expr:
      variable                                              { $$ = $1; }
    | list_expr '=' expr                                    { $$ = Expr\Assign[$1, $3]; }
    | variable '=' expr                                     { $$ = Expr\Assign[$1, $3]; }
    | variable '=' '&' variable                             { $$ = Expr\AssignRef[$1, $4]; }
    | variable '=' '&' new_expr                             { $$ = Expr\AssignRef[$1, $4]; }
    | new_expr                                              { $$ = $1; }
    | T_CLONE expr                                          { $$ = Expr\Clone_[$2]; }
    | variable T_PLUS_EQUAL expr                            { $$ = Expr\AssignOp\Plus      [$1, $3]; }
    | variable T_MINUS_EQUAL expr                           { $$ = Expr\AssignOp\Minus     [$1, $3]; }
    | variable T_MUL_EQUAL expr                             { $$ = Expr\AssignOp\Mul       [$1, $3]; }
    | variable T_DIV_EQUAL expr                             { $$ = Expr\AssignOp\Div       [$1, $3]; }
    | variable T_CONCAT_EQUAL expr                          { $$ = Expr\AssignOp\Concat    [$1, $3]; }
    | variable T_MOD_EQUAL expr                             { $$ = Expr\AssignOp\Mod       [$1, $3]; }
    | variable T_AND_EQUAL expr                             { $$ = Expr\AssignOp\BitwiseAnd[$1, $3]; }
    | variable T_OR_EQUAL expr                              { $$ = Expr\AssignOp\BitwiseOr [$1, $3]; }
    | variable T_XOR_EQUAL expr                             { $$ = Expr\AssignOp\BitwiseXor[$1, $3]; }
    | variable T_SL_EQUAL expr                              { $$ = Expr\AssignOp\ShiftLeft [$1, $3]; }
    | variable T_SR_EQUAL expr                              { $$ = Expr\AssignOp\ShiftRight[$1, $3]; }
    | variable T_POW_EQUAL expr                             { $$ = Expr\AssignOp\Pow       [$1, $3]; }
    | variable T_INC                                        { $$ = Expr\PostInc[$1]; }
    | T_INC variable                                        { $$ = Expr\PreInc [$2]; }
    | variable T_DEC                                        { $$ = Expr\PostDec[$1]; }
    | T_DEC variable                                        { $$ = Expr\PreDec [$2]; }
    | expr T_BOOLEAN_OR expr                                { $$ = Expr\BinaryOp\BooleanOr [$1, $3]; }
    | expr T_BOOLEAN_AND expr                               { $$ = Expr\BinaryOp\BooleanAnd[$1, $3]; }
    | expr T_LOGICAL_OR expr                                { $$ = Expr\BinaryOp\LogicalOr [$1, $3]; }
    | expr T_LOGICAL_AND expr                               { $$ = Expr\BinaryOp\LogicalAnd[$1, $3]; }
    | expr T_LOGICAL_XOR expr                               { $$ = Expr\BinaryOp\LogicalXor[$1, $3]; }
    | expr '|' expr                                         { $$ = Expr\BinaryOp\BitwiseOr [$1, $3]; }
    | expr '&' expr                                         { $$ = Expr\BinaryOp\BitwiseAnd[$1, $3]; }
    | expr '^' expr                                         { $$ = Expr\BinaryOp\BitwiseXor[$1, $3]; }
    | expr '.' expr                                         { $$ = Expr\BinaryOp\Concat    [$1, $3]; }
    | expr '+' expr                                         { $$ = Expr\BinaryOp\Plus      [$1, $3]; }
    | expr '-' expr                                         { $$ = Expr\BinaryOp\Minus     [$1, $3]; }
    | expr '*' expr                                         { $$ = Expr\BinaryOp\Mul       [$1, $3]; }
    | expr '/' expr                                         { $$ = Expr\BinaryOp\Div       [$1, $3]; }
    | expr '%' expr                                         { $$ = Expr\BinaryOp\Mod       [$1, $3]; }
    | expr T_SL expr                                        { $$ = Expr\BinaryOp\ShiftLeft [$1, $3]; }
    | expr T_SR expr                                        { $$ = Expr\BinaryOp\ShiftRight[$1, $3]; }
    | expr T_POW expr                                       { $$ = Expr\BinaryOp\Pow       [$1, $3]; }
    | '+' expr %prec T_INC                                  { $$ = Expr\UnaryPlus [$2]; }
    | '-' expr %prec T_INC                                  { $$ = Expr\UnaryMinus[$2]; }
    | '!' expr                                              { $$ = Expr\BooleanNot[$2]; }
    | '~' expr                                              { $$ = Expr\BitwiseNot[$2]; }
    | expr T_IS_IDENTICAL expr                              { $$ = Expr\BinaryOp\Identical     [$1, $3]; }
    | expr T_IS_NOT_IDENTICAL expr                          { $$ = Expr\BinaryOp\NotIdentical  [$1, $3]; }
    | expr T_IS_EQUAL expr                                  { $$ = Expr\BinaryOp\Equal         [$1, $3]; }
    | expr T_IS_NOT_EQUAL expr                              { $$ = Expr\BinaryOp\NotEqual      [$1, $3]; }
    | expr T_SPACESHIP expr                                 { $$ = Expr\BinaryOp\Spaceship     [$1, $3]; }
    | expr '<' expr                                         { $$ = Expr\BinaryOp\Smaller       [$1, $3]; }
    | expr T_IS_SMALLER_OR_EQUAL expr                       { $$ = Expr\BinaryOp\SmallerOrEqual[$1, $3]; }
    | expr '>' expr                                         { $$ = Expr\BinaryOp\Greater       [$1, $3]; }
    | expr T_IS_GREATER_OR_EQUAL expr                       { $$ = Expr\BinaryOp\GreaterOrEqual[$1, $3]; }
    | expr T_INSTANCEOF class_name_reference                { $$ = Expr\Instanceof_[$1, $3]; }
    | '(' expr ')'                                          { $$ = $2; }
    | expr '?' expr ':' expr                                { $$ = Expr\Ternary[$1, $3,   $5]; }
    | expr '?' ':' expr                                     { $$ = Expr\Ternary[$1, null, $4]; }
    | expr T_COALESCE expr                                  { $$ = Expr\BinaryOp\Coalesce[$1, $3]; }
    | T_ISSET '(' variables_list ')'                        { $$ = Expr\Isset_[$3]; }
    | T_EMPTY '(' expr ')'                                  { $$ = Expr\Empty_[$3]; }
    | T_INCLUDE expr                                        { $$ = Expr\Include_[$2, Expr\Include_::TYPE_INCLUDE]; }
    | T_INCLUDE_ONCE expr                                   { $$ = Expr\Include_[$2, Expr\Include_::TYPE_INCLUDE_ONCE]; }
    | T_EVAL '(' expr ')'                                   { $$ = Expr\Eval_[$3]; }
    | T_REQUIRE expr                                        { $$ = Expr\Include_[$2, Expr\Include_::TYPE_REQUIRE]; }
    | T_REQUIRE_ONCE expr                                   { $$ = Expr\Include_[$2, Expr\Include_::TYPE_REQUIRE_ONCE]; }
    | T_INT_CAST expr                                       { $$ = Expr\Cast\Int_    [$2]; }
    | T_DOUBLE_CAST expr                                    { $$ = Expr\Cast\Double  [$2]; }
    | T_STRING_CAST expr                                    { $$ = Expr\Cast\String_ [$2]; }
    | T_ARRAY_CAST expr                                     { $$ = Expr\Cast\Array_  [$2]; }
    | T_OBJECT_CAST expr                                    { $$ = Expr\Cast\Object_ [$2]; }
    | T_BOOL_CAST expr                                      { $$ = Expr\Cast\Bool_   [$2]; }
    | T_UNSET_CAST expr                                     { $$ = Expr\Cast\Unset_  [$2]; }
    | T_EXIT exit_expr                                      { $$ = Expr\Exit_        [$2]; }
    | '@' expr                                              { $$ = Expr\ErrorSuppress[$2]; }
    | scalar                                                { $$ = $1; }
    | '`' backticks_expr '`'                                { $$ = Expr\ShellExec[$2]; }
    | T_PRINT expr                                          { $$ = Expr\Print_[$2]; }
    | T_YIELD                                               { $$ = Expr\Yield_[null, null]; }
    | T_YIELD expr                                          { $$ = Expr\Yield_[$2, null]; }
    | T_YIELD expr T_DOUBLE_ARROW expr                      { $$ = Expr\Yield_[$4, $2]; }
    | T_YIELD_FROM expr                                     { $$ = Expr\YieldFrom[$2]; }
    | T_FUNCTION optional_ref '(' parameter_list ')' lexical_vars optional_return_type
      '{' inner_statement_list '}'
          { $$ = Expr\Closure[['static' => false, 'byRef' => $2, 'params' => $4, 'uses' => $6, 'returnType' => $7, 'stmts' => $9]]; }
    | T_STATIC T_FUNCTION optional_ref '(' parameter_list ')' lexical_vars optional_return_type
      '{' inner_statement_list '}'
          { $$ = Expr\Closure[['static' => true, 'byRef' => $3, 'params' => $5, 'uses' => $7, 'returnType' => $8, 'stmts' => $10]]; }
;

anonymous_class:
      T_CLASS ctor_arguments extends_from implements_list '{' class_statement_list '}'
          { $$ = array(Stmt\Class_[null, ['type' => 0, 'extends' => $3, 'implements' => $4, 'stmts' => $6]], $2); }

new_expr:
      T_NEW class_name_reference ctor_arguments             { $$ = Expr\New_[$2, $3]; }
    | T_NEW anonymous_class
          { list($class, $ctorArgs) = $2; $$ = Expr\New_[$class, $ctorArgs]; }
;

lexical_vars:
      /* empty */                                           { $$ = array(); }
    | T_USE '(' lexical_var_list ')'                        { $$ = $3; }
;

lexical_var_list:
      lexical_var                                           { init($1); }
    | lexical_var_list ',' lexical_var                      { push($1, $3); }
;

lexical_var:
      optional_ref T_VARIABLE                               { $$ = Expr\ClosureUse[parseVar($2), $1]; }
;

function_call:
      name argument_list                                    { $$ = Expr\FuncCall[$1, $2]; }
    | callable_expr argument_list                           { $$ = Expr\FuncCall[$1, $2]; }
    | class_name_or_var T_PAAMAYIM_NEKUDOTAYIM member_name argument_list
          { $$ = Expr\StaticCall[$1, $3, $4]; }
;

class_name:
      T_STATIC                                              { $$ = Name[$1]; }
    | name                                                  { $$ = $1; }
;

name:
      namespace_name_parts                                  { $$ = Name[$1]; }
    | T_NS_SEPARATOR namespace_name_parts                   { $$ = Name\FullyQualified[$2]; }
    | T_NAMESPACE T_NS_SEPARATOR namespace_name_parts       { $$ = Name\Relative[$3]; }
;

class_name_reference:
      class_name                                            { $$ = $1; }
    | new_variable                                          { $$ = $1; }
;

class_name_or_var:
      class_name                                            { $$ = $1; }
    | dereferencable                                        { $$ = $1; }
;

exit_expr:
      /* empty */                                           { $$ = null; }
    | '(' optional_expr ')'                                 { $$ = $2; }
;

backticks_expr:
      /* empty */                                           { $$ = array(); }
    | T_ENCAPSED_AND_WHITESPACE
          { $$ = array(Scalar\EncapsedStringPart[Scalar\String_::parseEscapeSequences($1, '`')]); }
    | encaps_list                                           { parseEncapsed($1, '`', true); $$ = $1; }
;

ctor_arguments:
      /* empty */                                           { $$ = array(); }
    | argument_list                                         { $$ = $1; }
;

constant:
      name                                                  { $$ = Expr\ConstFetch[$1]; }
    | class_name_or_var T_PAAMAYIM_NEKUDOTAYIM identifier
          { $$ = Expr\ClassConstFetch[$1, $3]; }
;

dereferencable_scalar:
      T_ARRAY '(' array_pair_list ')'                       { $$ = Expr\Array_[$3]; }
    | '[' array_pair_list ']'                               { $$ = Expr\Array_[$2]; }
    | T_CONSTANT_ENCAPSED_STRING                            { $$ = Scalar\String_[Scalar\String_::parse($1)]; }
;

scalar:
      T_LNUMBER                                             { $$ = Scalar\LNumber[Scalar\LNumber::parse($1)]; }
    | T_DNUMBER                                             { $$ = Scalar\DNumber[Scalar\DNumber::parse($1)]; }
    | T_LINE                                                { $$ = Scalar\MagicConst\Line[]; }
    | T_FILE                                                { $$ = Scalar\MagicConst\File[]; }
    | T_DIR                                                 { $$ = Scalar\MagicConst\Dir[]; }
    | T_CLASS_C                                             { $$ = Scalar\MagicConst\Class_[]; }
    | T_TRAIT_C                                             { $$ = Scalar\MagicConst\Trait_[]; }
    | T_METHOD_C                                            { $$ = Scalar\MagicConst\Method[]; }
    | T_FUNC_C                                              { $$ = Scalar\MagicConst\Function_[]; }
    | T_NS_C                                                { $$ = Scalar\MagicConst\Namespace_[]; }
    | dereferencable_scalar                                 { $$ = $1; }
    | constant                                              { $$ = $1; }
    | T_START_HEREDOC T_ENCAPSED_AND_WHITESPACE T_END_HEREDOC
          { $$ = Scalar\String_[Scalar\String_::parseDocString($1, $2)]; }
    | T_START_HEREDOC T_END_HEREDOC
          { $$ = Scalar\String_['']; }
    | '"' encaps_list '"'
          { parseEncapsed($2, '"', true); $$ = Scalar\Encapsed[$2]; }
    | T_START_HEREDOC encaps_list T_END_HEREDOC
          { parseEncapsedDoc($2, true); $$ = Scalar\Encapsed[$2]; }
;

optional_comma:
      /* empty */
    | ','
;

optional_expr:
      /* empty */                                           { $$ = null; }
    | expr                                                  { $$ = $1; }
;

dereferencable:
      variable                                              { $$ = $1; }
    | '(' expr ')'                                          { $$ = $2; }
    | dereferencable_scalar                                 { $$ = $1; }
;

callable_expr:
      callable_variable                                     { $$ = $1; }
    | '(' expr ')'                                          { $$ = $2; }
    | dereferencable_scalar                                 { $$ = $1; }
;

callable_variable:
      simple_variable                                       { $$ = Expr\Variable[$1]; }
    | dereferencable '[' optional_expr ']'                  { $$ = Expr\ArrayDimFetch[$1, $3]; }
    | constant '[' optional_expr ']'                        { $$ = Expr\ArrayDimFetch[$1, $3]; }
    | dereferencable '{' expr '}'                           { $$ = Expr\ArrayDimFetch[$1, $3]; }
    | function_call                                         { $$ = $1; }
    | dereferencable T_OBJECT_OPERATOR property_name argument_list
          { $$ = Expr\MethodCall[$1, $3, $4]; }
;

variable:
      callable_variable                                     { $$ = $1; }
    | static_member                                         { $$ = $1; }
    | dereferencable T_OBJECT_OPERATOR property_name        { $$ = Expr\PropertyFetch[$1, $3]; }
;

simple_variable:
      T_VARIABLE                                            { $$ = parseVar($1); }
    | '$' '{' expr '}'                                      { $$ = $3; }
    | '$' simple_variable                                   { $$ = Expr\Variable[$2]; }
;

static_member:
      class_name_or_var T_PAAMAYIM_NEKUDOTAYIM simple_variable
          { $$ = Expr\StaticPropertyFetch[$1, $3]; }
;

new_variable:
      simple_variable                                       { $$ = Expr\Variable[$1]; }
    | new_variable '[' optional_expr ']'                    { $$ = Expr\ArrayDimFetch[$1, $3]; }
    | new_variable '{' expr '}'                             { $$ = Expr\ArrayDimFetch[$1, $3]; }
    | new_variable T_OBJECT_OPERATOR property_name          { $$ = Expr\PropertyFetch[$1, $3]; }
    | class_name T_PAAMAYIM_NEKUDOTAYIM simple_variable     { $$ = Expr\StaticPropertyFetch[$1, $3]; }
    | new_variable T_PAAMAYIM_NEKUDOTAYIM simple_variable   { $$ = Expr\StaticPropertyFetch[$1, $3]; }
;

member_name:
      identifier                                            { $$ = $1; }
    | '{' expr '}'	                                        { $$ = $2; }
    | simple_variable	                                    { $$ = Expr\Variable[$1]; }
;

property_name:
      T_STRING                                              { $$ = $1; }
    | '{' expr '}'	                                        { $$ = $2; }
    | simple_variable	                                    { $$ = Expr\Variable[$1]; }
;

list_expr:
      T_LIST '(' list_expr_elements ')'                     { $$ = Expr\List_[$3]; }
;

list_expr_elements:
      list_expr_elements ',' list_expr_element              { push($1, $3); }
    | list_expr_element                                     { init($1); }
;

list_expr_element:
      variable                                              { $$ = $1; }
    | list_expr                                             { $$ = $1; }
    | /* empty */                                           { $$ = null; }
;

array_pair_list:
      /* empty */                                           { $$ = array(); }
    | non_empty_array_pair_list optional_comma              { $$ = $1; }
;

non_empty_array_pair_list:
      non_empty_array_pair_list ',' array_pair              { push($1, $3); }
    | array_pair                                            { init($1); }
;

array_pair:
      expr T_DOUBLE_ARROW expr                              { $$ = Expr\ArrayItem[$3, $1,   false]; }
    | expr                                                  { $$ = Expr\ArrayItem[$1, null, false]; }
    | expr T_DOUBLE_ARROW '&' variable                      { $$ = Expr\ArrayItem[$4, $1,   true]; }
    | '&' variable                                          { $$ = Expr\ArrayItem[$2, null, true]; }
;

encaps_list:
      encaps_list encaps_var                                { push($1, $2); }
    | encaps_list encaps_string_part                        { push($1, $2); }
    | encaps_var                                            { init($1); }
    | encaps_string_part encaps_var                         { init($1, $2); }
;

encaps_string_part:
      T_ENCAPSED_AND_WHITESPACE                             { $$ = Scalar\EncapsedStringPart[$1]; }
;

encaps_var:
      T_VARIABLE                                            { $$ = Expr\Variable[parseVar($1)]; }
    | T_VARIABLE '[' encaps_var_offset ']'                  { $$ = Expr\ArrayDimFetch[Expr\Variable[parseVar($1)], $3]; }
    | T_VARIABLE T_OBJECT_OPERATOR T_STRING                 { $$ = Expr\PropertyFetch[Expr\Variable[parseVar($1)], $3]; }
    | T_DOLLAR_OPEN_CURLY_BRACES expr '}'                   { $$ = Expr\Variable[$2]; }
    | T_DOLLAR_OPEN_CURLY_BRACES T_STRING_VARNAME '}'       { $$ = Expr\Variable[$2]; }
    | T_DOLLAR_OPEN_CURLY_BRACES T_STRING_VARNAME '[' expr ']' '}'
          { $$ = Expr\ArrayDimFetch[Expr\Variable[$2], $4]; }
    | T_CURLY_OPEN variable '}'                             { $$ = $2; }
;

encaps_var_offset:
      T_STRING                                              { $$ = Scalar\String_[$1]; }
    | T_NUM_STRING                                          { $$ = Scalar\String_[$1]; }
    | T_VARIABLE                                            { $$ = Expr\Variable[parseVar($1)]; }
;

%%

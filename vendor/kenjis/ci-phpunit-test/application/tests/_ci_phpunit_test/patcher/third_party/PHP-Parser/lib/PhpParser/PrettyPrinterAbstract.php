<?php

namespace PhpParser;

use PhpParser\Node\Expr;
use PhpParser\Node\Stmt;

abstract class PrettyPrinterAbstract
{
    protected $precedenceMap = array(
        // [precedence, associativity] where for the latter -1 is %left, 0 is %nonassoc and 1 is %right
        'Expr_BinaryOp_Pow'            => array(  0,  1),
        'Expr_BitwiseNot'              => array( 10,  1),
        'Expr_PreInc'                  => array( 10,  1),
        'Expr_PreDec'                  => array( 10,  1),
        'Expr_PostInc'                 => array( 10, -1),
        'Expr_PostDec'                 => array( 10, -1),
        'Expr_UnaryPlus'               => array( 10,  1),
        'Expr_UnaryMinus'              => array( 10,  1),
        'Expr_Cast_Int'                => array( 10,  1),
        'Expr_Cast_Double'             => array( 10,  1),
        'Expr_Cast_String'             => array( 10,  1),
        'Expr_Cast_Array'              => array( 10,  1),
        'Expr_Cast_Object'             => array( 10,  1),
        'Expr_Cast_Bool'               => array( 10,  1),
        'Expr_Cast_Unset'              => array( 10,  1),
        'Expr_ErrorSuppress'           => array( 10,  1),
        'Expr_Instanceof'              => array( 20,  0),
        'Expr_BooleanNot'              => array( 30,  1),
        'Expr_BinaryOp_Mul'            => array( 40, -1),
        'Expr_BinaryOp_Div'            => array( 40, -1),
        'Expr_BinaryOp_Mod'            => array( 40, -1),
        'Expr_BinaryOp_Plus'           => array( 50, -1),
        'Expr_BinaryOp_Minus'          => array( 50, -1),
        'Expr_BinaryOp_Concat'         => array( 50, -1),
        'Expr_BinaryOp_ShiftLeft'      => array( 60, -1),
        'Expr_BinaryOp_ShiftRight'     => array( 60, -1),
        'Expr_BinaryOp_Smaller'        => array( 70,  0),
        'Expr_BinaryOp_SmallerOrEqual' => array( 70,  0),
        'Expr_BinaryOp_Greater'        => array( 70,  0),
        'Expr_BinaryOp_GreaterOrEqual' => array( 70,  0),
        'Expr_BinaryOp_Equal'          => array( 80,  0),
        'Expr_BinaryOp_NotEqual'       => array( 80,  0),
        'Expr_BinaryOp_Identical'      => array( 80,  0),
        'Expr_BinaryOp_NotIdentical'   => array( 80,  0),
        'Expr_BinaryOp_Spaceship'      => array( 80,  0),
        'Expr_BinaryOp_BitwiseAnd'     => array( 90, -1),
        'Expr_BinaryOp_BitwiseXor'     => array(100, -1),
        'Expr_BinaryOp_BitwiseOr'      => array(110, -1),
        'Expr_BinaryOp_BooleanAnd'     => array(120, -1),
        'Expr_BinaryOp_BooleanOr'      => array(130, -1),
        'Expr_BinaryOp_Coalesce'       => array(140,  1),
        'Expr_Ternary'                 => array(150, -1),
        // parser uses %left for assignments, but they really behave as %right
        'Expr_Assign'                  => array(160,  1),
        'Expr_AssignRef'               => array(160,  1),
        'Expr_AssignOp_Plus'           => array(160,  1),
        'Expr_AssignOp_Minus'          => array(160,  1),
        'Expr_AssignOp_Mul'            => array(160,  1),
        'Expr_AssignOp_Div'            => array(160,  1),
        'Expr_AssignOp_Concat'         => array(160,  1),
        'Expr_AssignOp_Mod'            => array(160,  1),
        'Expr_AssignOp_BitwiseAnd'     => array(160,  1),
        'Expr_AssignOp_BitwiseOr'      => array(160,  1),
        'Expr_AssignOp_BitwiseXor'     => array(160,  1),
        'Expr_AssignOp_ShiftLeft'      => array(160,  1),
        'Expr_AssignOp_ShiftRight'     => array(160,  1),
        'Expr_AssignOp_Pow'            => array(160,  1),
        'Expr_YieldFrom'               => array(165,  1),
        'Expr_Print'                   => array(168,  1),
        'Expr_BinaryOp_LogicalAnd'     => array(170, -1),
        'Expr_BinaryOp_LogicalXor'     => array(180, -1),
        'Expr_BinaryOp_LogicalOr'      => array(190, -1),
        'Expr_Include'                 => array(200, -1),
    );

    protected $noIndentToken;
    protected $canUseSemicolonNamespaces;
    protected $options;

    /**
     * Creates a pretty printer instance using the given options.
     *
     * Supported options:
     *  * bool $shortArraySyntax = false: Whether to use [] instead of array()
     *
     * @param array $options Dictionary of formatting options
     */
    public function __construct(array $options = []) {
        $this->noIndentToken = '_NO_INDENT_' . mt_rand();

        $defaultOptions = ['shortArraySyntax' => false];
        $this->options = $options + $defaultOptions;
    }

    /**
     * Pretty prints an array of statements.
     *
     * @param Node[] $stmts Array of statements
     *
     * @return string Pretty printed statements
     */
    public function prettyPrint(array $stmts) {
        $this->preprocessNodes($stmts);

        return ltrim(str_replace("\n" . $this->noIndentToken, "\n", $this->pStmts($stmts, false)));
    }

    /**
     * Pretty prints an expression.
     *
     * @param Expr $node Expression node
     *
     * @return string Pretty printed node
     */
    public function prettyPrintExpr(Expr $node) {
        return str_replace("\n" . $this->noIndentToken, "\n", $this->p($node));
    }

    /**
     * Pretty prints a file of statements (includes the opening <?php tag if it is required).
     *
     * @param Node[] $stmts Array of statements
     *
     * @return string Pretty printed statements
     */
    public function prettyPrintFile(array $stmts) {
        if (!$stmts) {
            return "<?php\n\n";
        }

        $p = "<?php\n\n" . $this->prettyPrint($stmts);

        if ($stmts[0] instanceof Stmt\InlineHTML) {
            $p = preg_replace('/^<\?php\s+\?>\n?/', '', $p);
        }
        if ($stmts[count($stmts) - 1] instanceof Stmt\InlineHTML) {
            $p = preg_replace('/<\?php$/', '', rtrim($p));
        }

        return $p;
    }

    /**
     * Preprocesses the top-level nodes to initialize pretty printer state.
     *
     * @param Node[] $nodes Array of nodes
     */
    protected function preprocessNodes(array $nodes) {
        /* We can use semicolon-namespaces unless there is a global namespace declaration */
        $this->canUseSemicolonNamespaces = true;
        foreach ($nodes as $node) {
            if ($node instanceof Stmt\Namespace_ && null === $node->name) {
                $this->canUseSemicolonNamespaces = false;
            }
        }
    }

    /**
     * Pretty prints an array of nodes (statements) and indents them optionally.
     *
     * @param Node[] $nodes  Array of nodes
     * @param bool   $indent Whether to indent the printed nodes
     *
     * @return string Pretty printed statements
     */
    protected function pStmts(array $nodes, $indent = true) {
        $result = '';
        foreach ($nodes as $node) {
            $result .= "\n"
                    . $this->pComments($node->getAttribute('comments', array()))
                    . $this->p($node)
                    . ($node instanceof Expr ? ';' : '');
        }

        if ($indent) {
            return preg_replace('~\n(?!$|' . $this->noIndentToken . ')~', "\n    ", $result);
        } else {
            return $result;
        }
    }

    /**
     * Pretty prints a node.
     *
     * @param Node $node Node to be pretty printed
     *
     * @return string Pretty printed node
     */
    protected function p(Node $node) {
        return $this->{'p' . $node->getType()}($node);
    }

    protected function pInfixOp($type, Node $leftNode, $operatorString, Node $rightNode) {
        list($precedence, $associativity) = $this->precedenceMap[$type];

        return $this->pPrec($leftNode, $precedence, $associativity, -1)
             . $operatorString
             . $this->pPrec($rightNode, $precedence, $associativity, 1);
    }

    protected function pPrefixOp($type, $operatorString, Node $node) {
        list($precedence, $associativity) = $this->precedenceMap[$type];
        return $operatorString . $this->pPrec($node, $precedence, $associativity, 1);
    }

    protected function pPostfixOp($type, Node $node, $operatorString) {
        list($precedence, $associativity) = $this->precedenceMap[$type];
        return $this->pPrec($node, $precedence, $associativity, -1) . $operatorString;
    }

    /**
     * Prints an expression node with the least amount of parentheses necessary to preserve the meaning.
     *
     * @param Node $node                Node to pretty print
     * @param int  $parentPrecedence    Precedence of the parent operator
     * @param int  $parentAssociativity Associativity of parent operator
     *                                  (-1 is left, 0 is nonassoc, 1 is right)
     * @param int  $childPosition       Position of the node relative to the operator
     *                                  (-1 is left, 1 is right)
     *
     * @return string The pretty printed node
     */
    protected function pPrec(Node $node, $parentPrecedence, $parentAssociativity, $childPosition) {
        $type = $node->getType();
        if (isset($this->precedenceMap[$type])) {
            $childPrecedence = $this->precedenceMap[$type][0];
            if ($childPrecedence > $parentPrecedence
                || ($parentPrecedence == $childPrecedence && $parentAssociativity != $childPosition)
            ) {
                return '(' . $this->{'p' . $type}($node) . ')';
            }
        }

        return $this->{'p' . $type}($node);
    }

    /**
     * Pretty prints an array of nodes and implodes the printed values.
     *
     * @param Node[] $nodes Array of Nodes to be printed
     * @param string $glue  Character to implode with
     *
     * @return string Imploded pretty printed nodes
     */
    protected function pImplode(array $nodes, $glue = '') {
        $pNodes = array();
        foreach ($nodes as $node) {
            $pNodes[] = $this->p($node);
        }

        return implode($glue, $pNodes);
    }

    /**
     * Pretty prints an array of nodes and implodes the printed values with commas.
     *
     * @param Node[] $nodes Array of Nodes to be printed
     *
     * @return string Comma separated pretty printed nodes
     */
    protected function pCommaSeparated(array $nodes) {
        return $this->pImplode($nodes, ', ');
    }

    /**
     * Signals the pretty printer that a string shall not be indented.
     *
     * @param string $string Not to be indented string
     *
     * @return string String marked with $this->noIndentToken's.
     */
    protected function pNoIndent($string) {
        return str_replace("\n", "\n" . $this->noIndentToken, $string);
    }

    /**
     * Prints reformatted text of the passed comments.
     *
     * @param Comment[] $comments List of comments
     *
     * @return string Reformatted text of comments
     */
    protected function pComments(array $comments) {
        $result = '';

        foreach ($comments as $comment) {
            $result .= $comment->getReformattedText() . "\n";
        }

        return $result;
    }
}

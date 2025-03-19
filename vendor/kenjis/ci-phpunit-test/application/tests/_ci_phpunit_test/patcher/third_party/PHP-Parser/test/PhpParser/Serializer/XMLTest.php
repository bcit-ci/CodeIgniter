<?php

namespace PhpParser\Serializer;

use PhpParser;

class XMLTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers PhpParser\Serializer\XML<extended>
     */
    public function testSerialize() {
        $code = <<<CODE
<?php
// comment
/** doc comment */
function functionName(&\$a = 0, \$b = 1.0) {
    echo 'Foo';
}
CODE;
        $xml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<AST xmlns:node="http://nikic.github.com/PHPParser/XML/node" xmlns:subNode="http://nikic.github.com/PHPParser/XML/subNode" xmlns:attribute="http://nikic.github.com/PHPParser/XML/attribute" xmlns:scalar="http://nikic.github.com/PHPParser/XML/scalar">
 <scalar:array>
  <node:Stmt_Function>
   <attribute:comments>
    <scalar:array>
     <comment isDocComment="false" line="2">// comment
</comment>
     <comment isDocComment="true" line="3">/** doc comment */</comment>
    </scalar:array>
   </attribute:comments>
   <attribute:startLine>
    <scalar:int>4</scalar:int>
   </attribute:startLine>
   <attribute:endLine>
    <scalar:int>6</scalar:int>
   </attribute:endLine>
   <subNode:byRef>
    <scalar:false/>
   </subNode:byRef>
   <subNode:name>
    <scalar:string>functionName</scalar:string>
   </subNode:name>
   <subNode:params>
    <scalar:array>
     <node:Param>
      <attribute:startLine>
       <scalar:int>4</scalar:int>
      </attribute:startLine>
      <attribute:endLine>
       <scalar:int>4</scalar:int>
      </attribute:endLine>
      <subNode:type>
       <scalar:null/>
      </subNode:type>
      <subNode:byRef>
       <scalar:true/>
      </subNode:byRef>
      <subNode:variadic>
       <scalar:false/>
      </subNode:variadic>
      <subNode:name>
       <scalar:string>a</scalar:string>
      </subNode:name>
      <subNode:default>
       <node:Scalar_LNumber>
        <attribute:startLine>
         <scalar:int>4</scalar:int>
        </attribute:startLine>
        <attribute:endLine>
         <scalar:int>4</scalar:int>
        </attribute:endLine>
        <subNode:value>
         <scalar:int>0</scalar:int>
        </subNode:value>
       </node:Scalar_LNumber>
      </subNode:default>
     </node:Param>
     <node:Param>
      <attribute:startLine>
       <scalar:int>4</scalar:int>
      </attribute:startLine>
      <attribute:endLine>
       <scalar:int>4</scalar:int>
      </attribute:endLine>
      <subNode:type>
       <scalar:null/>
      </subNode:type>
      <subNode:byRef>
       <scalar:false/>
      </subNode:byRef>
      <subNode:variadic>
       <scalar:false/>
      </subNode:variadic>
      <subNode:name>
       <scalar:string>b</scalar:string>
      </subNode:name>
      <subNode:default>
       <node:Scalar_DNumber>
        <attribute:startLine>
         <scalar:int>4</scalar:int>
        </attribute:startLine>
        <attribute:endLine>
         <scalar:int>4</scalar:int>
        </attribute:endLine>
        <subNode:value>
         <scalar:float>1</scalar:float>
        </subNode:value>
       </node:Scalar_DNumber>
      </subNode:default>
     </node:Param>
    </scalar:array>
   </subNode:params>
   <subNode:returnType>
    <scalar:null/>
   </subNode:returnType>
   <subNode:stmts>
    <scalar:array>
     <node:Stmt_Echo>
      <attribute:startLine>
       <scalar:int>5</scalar:int>
      </attribute:startLine>
      <attribute:endLine>
       <scalar:int>5</scalar:int>
      </attribute:endLine>
      <subNode:exprs>
       <scalar:array>
        <node:Scalar_String>
         <attribute:startLine>
          <scalar:int>5</scalar:int>
         </attribute:startLine>
         <attribute:endLine>
          <scalar:int>5</scalar:int>
         </attribute:endLine>
         <subNode:value>
          <scalar:string>Foo</scalar:string>
         </subNode:value>
        </node:Scalar_String>
       </scalar:array>
      </subNode:exprs>
     </node:Stmt_Echo>
    </scalar:array>
   </subNode:stmts>
  </node:Stmt_Function>
 </scalar:array>
</AST>
XML;

        $parser     = new PhpParser\Parser\Php7(new PhpParser\Lexer);
        $serializer = new XML;

        $code = str_replace("\r\n", "\n", $code);
        $stmts = $parser->parse($code);
        $this->assertXmlStringEqualsXmlString($xml, $serializer->serialize($stmts));
    }

    /**
     * @expectedException        \InvalidArgumentException
     * @expectedExceptionMessage Unexpected node type
     */
    public function testError() {
        $serializer = new XML;
        $serializer->serialize(array(new \stdClass));
    }
}

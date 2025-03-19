<?php
/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license. For more information, see
 * <http://www.doctrine-project.org>.
 */

namespace Doctrine\DBAL\Schema\Visitor;

use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\ForeignKeyConstraint;

/**
 * Create a Graphviz output of a Schema.
 */
class Graphviz extends AbstractVisitor
{
    /**
     * @var string
     */
    private $output = '';

    /**
     * {@inheritdoc}
     */
    public function acceptForeignKey(Table $localTable, ForeignKeyConstraint $fkConstraint)
    {
        $this->output .= $this->createNodeRelation(
            $fkConstraint->getLocalTableName() . ":col" . current($fkConstraint->getLocalColumns()).":se",
            $fkConstraint->getForeignTableName() . ":col" . current($fkConstraint->getForeignColumns()).":se",
            array(
                'dir'       => 'back',
                'arrowtail' => 'dot',
                'arrowhead' => 'normal',
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function acceptSchema(Schema $schema)
    {
        $this->output  = 'digraph "' . sha1(mt_rand()) . '" {' . "\n";
        $this->output .= 'splines = true;' . "\n";
        $this->output .= 'overlap = false;' . "\n";
        $this->output .= 'outputorder=edgesfirst;'."\n";
        $this->output .= 'mindist = 0.6;' . "\n";
        $this->output .= 'sep = .2;' . "\n";
    }

    /**
     * {@inheritdoc}
     */
    public function acceptTable(Table $table)
    {
        $this->output .= $this->createNode(
            $table->getName(),
            array(
                'label' => $this->createTableLabel($table),
                'shape' => 'plaintext',
            )
        );
    }

    /**
     * @param \Doctrine\DBAL\Schema\Table $table
     *
     * @return string
     */
    private function createTableLabel(Table $table)
    {
        // Start the table
        $label = '<<TABLE CELLSPACING="0" BORDER="1" ALIGN="LEFT">';

        // The title
        $label .= '<TR><TD BORDER="1" COLSPAN="3" ALIGN="CENTER" BGCOLOR="#fcaf3e"><FONT COLOR="#2e3436" FACE="Helvetica" POINT-SIZE="12">' . $table->getName() . '</FONT></TD></TR>';

        // The attributes block
        foreach ($table->getColumns() as $column) {
            $columnLabel = $column->getName();

            $label .= '<TR>';
            $label .= '<TD BORDER="0" ALIGN="LEFT" BGCOLOR="#eeeeec">';
            $label .= '<FONT COLOR="#2e3436" FACE="Helvetica" POINT-SIZE="12">' . $columnLabel . '</FONT>';
            $label .= '</TD><TD BORDER="0" ALIGN="LEFT" BGCOLOR="#eeeeec"><FONT COLOR="#2e3436" FACE="Helvetica" POINT-SIZE="10">' . strtolower($column->getType()) . '</FONT></TD>';
            $label .= '<TD BORDER="0" ALIGN="RIGHT" BGCOLOR="#eeeeec" PORT="col'.$column->getName().'">';
            if ($table->hasPrimaryKey() && in_array($column->getName(), $table->getPrimaryKey()->getColumns())) {
                $label .= "\xe2\x9c\xb7";
            }
            $label .= '</TD></TR>';
        }

        // End the table
        $label .= '</TABLE>>';

        return $label;
    }

    /**
     * @param string $name
     * @param array  $options
     *
     * @return string
     */
    private function createNode($name, $options)
    {
        $node = $name . " [";
        foreach ($options as $key => $value) {
            $node .= $key . '=' . $value . ' ';
        }
        $node .= "]\n";

        return $node;
    }

    /**
     * @param string $node1
     * @param string $node2
     * @param array  $options
     *
     * @return string
     */
    private function createNodeRelation($node1, $node2, $options)
    {
        $relation = $node1 . ' -> ' . $node2 . ' [';
        foreach ($options as $key => $value) {
            $relation .= $key . '=' . $value . ' ';
        }
        $relation .= "]\n";

        return $relation;
    }

    /**
     * Get Graphviz Output
     *
     * @return string
     */
    public function getOutput()
    {
        return $this->output . "}";
    }

    /**
     * Writes dot language output to a file. This should usually be a *.dot file.
     *
     * You have to convert the output into a viewable format. For example use "neato" on linux systems
     * and execute:
     *
     *  neato -Tpng -o er.png er.dot
     *
     * @param string $filename
     *
     * @return void
     */
    public function write($filename)
    {
        file_put_contents($filename, $this->getOutput());
    }
}

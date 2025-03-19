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
use Doctrine\DBAL\Schema\TableDiff;
use Doctrine\DBAL\Schema\ForeignKeyConstraint;
use Doctrine\DBAL\Schema\Sequence;

/**
 * Visit a SchemaDiff.
 *
 * @link    www.doctrine-project.org
 * @since   2.4
 * @author  Benjamin Eberlei <kontakt@beberlei.de>
 */
interface SchemaDiffVisitor
{
    /**
     * Visit an orphaned foreign key whose table was deleted.
     *
     * @param \Doctrine\DBAL\Schema\ForeignKeyConstraint $foreignKey
     */
    function visitOrphanedForeignKey(ForeignKeyConstraint $foreignKey);

    /**
     * Visit a sequence that has changed.
     *
     * @param \Doctrine\DBAL\Schema\Sequence $sequence
     */
    function visitChangedSequence(Sequence $sequence);

    /**
     * Visit a sequence that has been removed.
     *
     * @param \Doctrine\DBAL\Schema\Sequence $sequence
     */
    function visitRemovedSequence(Sequence $sequence);

    /**
     * @param \Doctrine\DBAL\Schema\Sequence $sequence
     */
    function visitNewSequence(Sequence $sequence);

    /**
     * @param \Doctrine\DBAL\Schema\Table $table
     */
    function visitNewTable(Table $table);

    /**
     * @param \Doctrine\DBAL\Schema\Table                $table
     * @param \Doctrine\DBAL\Schema\ForeignKeyConstraint $foreignKey
     */
    function visitNewTableForeignKey(Table $table, ForeignKeyConstraint $foreignKey);

    /**
     * @param \Doctrine\DBAL\Schema\Table $table
     */
    function visitRemovedTable(Table $table);

    /**
     * @param \Doctrine\DBAL\Schema\TableDiff $tableDiff
     */
    function visitChangedTable(TableDiff $tableDiff);
}

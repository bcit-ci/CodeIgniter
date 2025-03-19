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

namespace Doctrine\DBAL\Schema\Synchronizer;

use Doctrine\DBAL\Schema\Schema;

/**
 * The synchronizer knows how to synchronize a schema with the configured
 * database.
 *
 * @author Benjamin Eberlei <kontakt@beberlei.de>
 */
interface SchemaSynchronizer
{
    /**
     * Gets the SQL statements that can be executed to create the schema.
     *
     * @param \Doctrine\DBAL\Schema\Schema $createSchema
     *
     * @return array
     */
    function getCreateSchema(Schema $createSchema);

    /**
     * Gets the SQL Statements to update given schema with the underlying db.
     *
     * @param \Doctrine\DBAL\Schema\Schema $toSchema
     * @param boolean                      $noDrops
     *
     * @return array
     */
    function getUpdateSchema(Schema $toSchema, $noDrops = false);

    /**
     * Gets the SQL Statements to drop the given schema from underlying db.
     *
     * @param \Doctrine\DBAL\Schema\Schema $dropSchema
     *
     * @return array
     */
    function getDropSchema(Schema $dropSchema);

    /**
     * Gets the SQL statements to drop all schema assets from underlying db.
     *
     * @return array
     */
    function getDropAllSchema();

    /**
     * Creates the Schema.
     *
     * @param \Doctrine\DBAL\Schema\Schema $createSchema
     *
     * @return void
     */
    function createSchema(Schema $createSchema);

    /**
     * Updates the Schema to new schema version.
     *
     * @param \Doctrine\DBAL\Schema\Schema $toSchema
     * @param boolean                      $noDrops
     *
     * @return void
     */
    function updateSchema(Schema $toSchema, $noDrops = false);

    /**
     * Drops the given database schema from the underlying db.
     *
     * @param \Doctrine\DBAL\Schema\Schema $dropSchema
     *
     * @return void
     */
    function dropSchema(Schema $dropSchema);

    /**
     * Drops all assets from the underlying db.
     *
     * @return void
     */
    function dropAllSchema();
}

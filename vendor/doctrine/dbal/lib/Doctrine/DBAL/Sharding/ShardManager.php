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

namespace Doctrine\DBAL\Sharding;

/**
 * Sharding Manager gives access to APIs to implementing sharding on top of
 * Doctrine\DBAL\Connection instances.
 *
 * For simplicity and developer ease-of-use (and understanding) the sharding
 * API only covers single shard queries, no fan-out support. It is primarily
 * suited for multi-tenant applications.
 *
 * The assumption about sharding here
 * is that a distribution value can be found that gives access to all the
 * necessary data for all use-cases. Switching between shards should be done with
 * caution, especially if lazy loading is implemented. Any query is always
 * executed against the last shard that was selected. If a query is created for
 * a shard Y but then a shard X is selected when its actually executed you
 * will hit the wrong shard.
 *
 * @author Benjamin Eberlei <kontakt@beberlei.de>
 */
interface ShardManager
{
    /**
     * Selects global database with global data.
     *
     * This is the default database that is connected when no shard is
     * selected.
     *
     * @return void
     */
    function selectGlobal();

    /**
     * Selects the shard against which the queries after this statement will be issued.
     *
     * @param string $distributionValue
     *
     * @return void
     *
     * @throws \Doctrine\DBAL\Sharding\ShardingException If no value is passed as shard identifier.
     */
    function selectShard($distributionValue);

    /**
     * Gets the distribution value currently used for sharding.
     *
     * @return string
     */
    function getCurrentDistributionValue();

    /**
     * Gets information about the amount of shards and other details.
     *
     * Format is implementation specific, each shard is one element and has an
     * 'id' attribute at least.
     *
     * @return array
     */
    function getShards();

    /**
     * Queries all shards in undefined order and return the results appended to
     * each other. Restore the previous distribution value after execution.
     *
     * Using {@link \Doctrine\DBAL\Connection::fetchAll} to retrieve rows internally.
     *
     * @param string $sql
     * @param array  $params
     * @param array  $types
     *
     * @return array
     */
    function queryAll($sql, array $params, array $types);
}

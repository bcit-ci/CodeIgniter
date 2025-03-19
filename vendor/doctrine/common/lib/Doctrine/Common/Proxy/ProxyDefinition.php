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

namespace Doctrine\Common\Proxy;

/**
 * Definition structure how to create a proxy.
 *
 * @author Benjamin Eberlei <kontakt@beberlei.de>
 */
class ProxyDefinition
{
    /**
     * @var string
     */
    public $proxyClassName;

    /**
     * @var array
     */
    public $identifierFields;

    /**
     * @var \ReflectionProperty[]
     */
    public $reflectionFields;

    /**
     * @var callable
     */
    public $initializer;

    /**
     * @var callable
     */
    public $cloner;

    /**
     * @param string   $proxyClassName
     * @param array    $identifierFields
     * @param array    $reflectionFields
     * @param callable $initializer
     * @param callable $cloner
     */
    public function __construct($proxyClassName, array $identifierFields, array $reflectionFields, $initializer, $cloner)
    {
        $this->proxyClassName   = $proxyClassName;
        $this->identifierFields = $identifierFields;
        $this->reflectionFields = $reflectionFields;
        $this->initializer      = $initializer;
        $this->cloner           = $cloner;
    }
}


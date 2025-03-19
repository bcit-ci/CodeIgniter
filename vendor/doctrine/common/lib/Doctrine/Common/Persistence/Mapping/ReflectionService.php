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

namespace Doctrine\Common\Persistence\Mapping;

/**
 * Very simple reflection service abstraction.
 *
 * This is required inside metadata layers that may require either
 * static or runtime reflection.
 *
 * @author Benjamin Eberlei <kontakt@beberlei.de>
 */
interface ReflectionService
{
    /**
     * Returns an array of the parent classes (not interfaces) for the given class.
     *
     * @param string $class
     *
     * @throws \Doctrine\Common\Persistence\Mapping\MappingException
     *
     * @return array
     */
    public function getParentClasses($class);

    /**
     * Returns the shortname of a class.
     *
     * @param string $class
     *
     * @return string
     */
    public function getClassShortName($class);

    /**
     * @param string $class
     *
     * @return string
     */
    public function getClassNamespace($class);

    /**
     * Returns a reflection class instance or null.
     *
     * @param string $class
     *
     * @return \ReflectionClass|null
     */
    public function getClass($class);

    /**
     * Returns an accessible property (setAccessible(true)) or null.
     *
     * @param string $class
     * @param string $property
     *
     * @return \ReflectionProperty|null
     */
    public function getAccessibleProperty($class, $property);

    /**
     * Checks if the class have a public method with the given name.
     *
     * @param mixed $class
     * @param mixed $method
     *
     * @return bool
     */
    public function hasPublicMethod($class, $method);
}

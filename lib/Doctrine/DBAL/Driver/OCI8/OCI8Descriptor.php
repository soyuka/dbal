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

namespace Doctrine\DBAL\Driver\OCI8;

/**
 * This class is a wrapper around Oci-Lob
 * It is used in doctrine Types that matches Lobs so that the correct descriptor
 * can be binded (either CLOB or BLOB)
 */
final class OCI8Descriptor
{
    private $_value;
    private $_type;

    /**
     * @param mixed   $value the LOB value
     * @param integer $type  the LOB type OCI_TEMP_CLOB or OCI_TEMP_BLOB
     */
    public function __construct($value, int $type) {
        $this->_value = $value;
        $this->_type = $type;
    }

    /**
     * Get an Oci-Lob descriptor
     * @param resource $dbh the connection resource, comes from `oci_connect`
     * @return Oci-Lob
     */
    public function getDescriptor($dbh)
    {
        $descriptor = oci_new_descriptor($dbh, OCI_D_LOB);
        $descriptor->writeTemporary($this->_value, $this->_type);

        return $descriptor;
    }

    /**
     * Return the binding type according to the value LOB type
     * @return integer
     */
    public function getBindingType() {
        if ($this->_type === OCI_TEMP_CLOB) {
            return OCI_B_CLOB;
        }

        if ($this->_type === OCI_TEMP_BLOB) {
            return OCI_B_BLOB;
        }

        throw new \LogicException("Lob type for the value must be either BLOB or CLOB");
    }
}

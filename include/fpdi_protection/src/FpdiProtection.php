<?php
/**
 * This file is part of FpdiProtection
 *
 * @package   setasign\FpdiProtection
 * @copyright Copyright (c) 2017 Setasign - Jan Slabon (https://www.setasign.com)
 * @license   http://opensource.org/licenses/mit-license The MIT License
 * @version   2.0.0
 */

namespace setasign\FpdiProtection;

use setasign\Fpdi\PdfParser\Filter\AsciiHex;
use setasign\Fpdi\PdfParser\Type\PdfHexString;
use setasign\Fpdi\PdfParser\Type\PdfNumeric;
use setasign\Fpdi\PdfParser\Type\PdfStream;
use setasign\Fpdi\PdfParser\Type\PdfString;
use setasign\Fpdi\PdfParser\Type\PdfType;

/**
 * Class FpdiProtection
 *
 * @package setasign\FpdiProtection
 */
class FpdiProtection extends \setasign\Fpdi\Fpdi
{
    /**
     * Permission constant.
     *
     * For handlers of revision 2: Print the document.
     *
     * Handlers of a revision of 3 or greater: Print the document (possibly not at the highest quality level, depending
     * on whether {@link SetaPDF_Core_SecHandler::PERM_DIGITAL_PRINT} is also set).
     *
     * @see PDF 32000-1:2008 - Table 22 - User access permissions
     * @var integer
     */
    const PERM_PRINT = 4; // 3

    /**
     * Permission constant.
     *
     * Modify the contents of the document by operations other than those controlled by
     * {@link SetaPDF_Core_SecHandler::PERM_ANNOT}, {@link SetaPDF_Core_SecHandler::PERM_FILL_FORM} and
     * {@link SetaPDF_Core_SecHandler::PERM_ASSEMBLE}.
     *
     * @see PDF 32000-1:2008 - Table 22 - User access permissions
     * @var integer
     */
    const PERM_MODIFY = 8; // 4

    /**
     * Permission constant.
     *
     * For handlers of revision 2: Copy or otherwise extract text and graphics from the document, including extracting
     * text and graphics (in support of accessibility to users with disabilities or for other purposes).
     *
     * For handlers of revision 3 or greater: Copy or otherwise extract text and graphics from the document by
     * operations other than that controlled by bit {@link SetaPDF_Core_SecHandler::PERM_ACCESSIBILITY}.
     *
     * @see PDF 32000-1:2008 - Table 22 - User access permissions
     * @var integer
     */
    const PERM_COPY = 16; // 5

    /**
     * Permission constant.
     *
     * Add or modify text annotations, fill in interactive form fields, and, if {@link SetaPDF_Core_SecHandler::PERM_MODIFY}
     * is also set, create or modify interactive form fields (including signature fields).
     *
     * @see PDF 32000-1:2008 - Table 22 - User access permissions
     * @var integer
     */
    const PERM_ANNOT = 32; // 6

    /**
     * Permission constant.
     *
     * For handlers of revision 3 or greater: Fill in existing interactive form fields (including signature fields),
     * even if {@link SetaPDF_Core_SecHandler::PERM_ANNOT} is not set.
     *
     * @see PDF 32000-1:2008 - Table 22 - User access permissions
     * @var integer
     */
    const PERM_FILL_FORM = 256; // 9

    /**
     * Permission constant.
     *
     * For handlers of revision 3 or greater: Extract text and graphics (in support of accessibility to users with
     * disabilities or for other purposes).
     *
     * @see PDF 32000-1:2008 - Table 22 - User access permissions
     * @var integer
     */
    const PERM_ACCESSIBILITY = 512; // 10

    /**
     * Permission constant.
     *
     * For handlers of revision 3 or greater: Assemble the document (insert, rotate, or delete pages and create
     * bookmarks or thumbnail images), even if {@link SetaPDF_Core_SecHandler::PERM_MODIFY} is not set.
     *
     * @see PDF 32000-1:2008 - Table 22 - User access permissions
     * @var integer
     */
    const PERM_ASSEMBLE = 1024; // 11

    /**
     * Permission constant.
     *
     * Print the document to a representation from which a faithful digital copy of the PDF content could be generated.
     * When this is not set (and {@link SetaPDF_Core_SecHandler::PERM_PRINT} is set), printing is limited to a low-level
     * representation of the appearance, possibly of degraded quality.
     *
     * @see PDF 32000-1:2008 - Table 22 - User access permissions
     * @var integer
     */
    const PERM_DIGITAL_PRINT = 2048; // 12

    /**
     * Whether document is protected or not
     *
     * @var bool
     */
    protected $encrypted = false;

    /**
     * The security handler revision
     *
     * @var int
     */
    protected $revision = 3;

    /**
     * U entry in pdf document
     *
     * @var string
     */
    protected $uValue;

    /**
     * O entry in pdf document
     *
     * @var string
     */
    protected $oValue;

    /**
     * P entry in pdf document
     *
     * @var string
     */
    protected $pValue;

    /**
     * The encryption object number
     *
     * @var integer
     */
    protected $encObjectNumber;

    /**
     * The encryption key
     *
     * @var string
     */
    protected $encryptionKey;

    /**
     * The key length
     *
     * @var int
     */
    protected $keyLength = 16;

    /**
     * The padding string
     *
     * @var string
     */
    protected $padding;

    /**
     * The current written object number
     *
     * @var integer
     */
    protected $currentObjectNumber;

    /**
     * @var string
     */
    protected $fileIdentifier;

    /**
     * FpdiProtection constructor.
     *
     * @param string $orientation
     * @param string $unit
     * @param string $size
     */
    public function __construct($orientation = 'P', $unit = 'mm', $size = 'A4')
    {
        parent::__construct($orientation, $unit, $size);

        $randomBytes = function_exists('random_bytes') ? \random_bytes(32) : \mt_rand();
        $this->fileIdentifier = md5(__FILE__ . \php_sapi_name() . \phpversion() . $randomBytes, true);
    }

    /**
     * Set permissions as well as user and owner passwords
     *
     * @param int|array $permissions An array of permission values (see class constants) or the sum of the constant
     *                               values. If a value is present it means that the permission is granted.
     * @param string $userPass If a user password is set, user will be prompted before document is opened.
     * @param null $ownerPass If an owner password is set, document can be opened in privilege mode with no
     *                        restriction if that password is entered.
     * @param int $revision The revision number of the security handler (2 = RC4-40bits, 3 = RC4-128bits)
     * @return string The owner password
     */
    public function setProtection($permissions, $userPass = '', $ownerPass = null, $revision = 3)
    {
        if ($revision < 2 || $revision > 3) {
            throw new \InvalidArgumentException('Only revision 2 or 3 are supported.');
        }

        if ($revision === 3) {
            $this->setMinPdfVersion('1.4');
        }

        $this->pValue = $this->sanitizePermissionsValue($permissions, $revision);

        if ($ownerPass === null || $ownerPass === '') {
            $ownerPass = function_exists('random_bytes') ? \random_bytes(32) : uniqid(rand());
        }

        $this->encrypted = true;
        $this->revision = $revision;
        $this->keyLength = $revision === 3 ? 16 : 5;
        $this->padding = "\x28\xBF\x4E\x5E\x4E\x75\x8A\x41\x64\x00\x4E\x56\xFF\xFA\x01\x08"
            . "\x2E\x2E\x00\xB6\xD0\x68\x3E\x80\x2F\x0C\xA9\xFE\x64\x53\x69\x7A";

        $this->oValue = $this->computeOValue($userPass, $ownerPass);
        $this->encryptionKey = $this->computeEncryptionKey($userPass);
        $this->uValue = $this->computeUValue($this->encryptionKey);

        return $ownerPass;
    }

    /**
     * Ensures a valid permission value.
     *
     * @param int[]|int$permissions
     * @param int $revision
     * @return int
     */
    public function sanitizePermissionsValue($permissions, $revision)
    {
        if (is_array($permissions)) {
            $legacyOptions = ['print' => 4, 'modify' => 8, 'copy' => 16, 'annot-forms' => 32];
            foreach ($permissions as $key => $value) {
                if (isset($legacyOptions[$value])) {
                    $permissions[$key] = $legacyOptions[$value];
                } elseif (!is_int($value)) {
                    throw new \InvalidArgumentException(
                        sprintf('Invalid permission value: %s', $value)
                    );
                }
            }

            $permissions = array_sum($permissions);
        }

        $permissions = (int)$permissions;

        $allowed = self::PERM_PRINT
            | self::PERM_MODIFY
            | self::PERM_COPY
            | self::PERM_ANNOT;

        if ($revision > 2) {
            $allowed |= self::PERM_FILL_FORM
                | self::PERM_ACCESSIBILITY
                | self::PERM_ASSEMBLE
                | self::PERM_DIGITAL_PRINT;
        }

        if (($allowed & $permissions) !== $permissions) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Permission flags (%s) are not allowed for this security handler revision %s.',
                    $permissions,
                    $revision
                )
            );
        }

        $permissions = 61632 | 0xFFFF0000 | $permissions;
        if ($revision < 3) {
            // 3840 = bit 9 to 12 to 1 - we are < revision 3
            $permissions |= 3840;
        }

        // PDF integer are 32 bit integers. Ensure this.
        if (PHP_INT_SIZE === 4 || ($permissions) < (2147483647)) {
            return $permissions;
        }

        return ($permissions | (4294967295 << 32));
    }

    /**
     * Compute the O value.
     *
     * @param string $userPassword
     * @param string $ownerPassword
     * @return string
      */
    protected function computeOValue($userPassword, $ownerPassword = '')
    {
        $revision = $this->revision;
        // Algorithm 3: Computing the encryption dictionary’s O (owner password) value

        // a) Pad or truncate the owner password string as described in step (a) of
        //    "Algorithm 2: Computing an encryption key". If there is no owner password,
        //    use the user password instead.
        if ('' === $ownerPassword)
            $ownerPassword = $userPassword;

        $s = substr($ownerPassword . $this->padding, 0, 32);

        // b) Initialize the MD5 hash function and pass the result of step (a) as input to
        //    this function.
        $s = md5($s, true);

        // c) (Security handlers of revision 3 or greater) Do the following 50 times:
        //    Take the output from the previous MD5 hash and pass it as input into a new MD5 hash.
        if ($revision >= 3) {
            for ($i = 0; $i < 50; $i++)
                $s = md5($s, true);
        }

        // d) Create an RC4 encryption key using the first n bytes of the output from the
        //    final MD5 hash, where n shall always be 5 for security handlers of revision 2
        //    but, for security handlers of revision 3 or greater, shall depend on the value
        //    of the encryption dictionary’s Length entry.
        $encryptionKey = substr($s, 0, $this->keyLength);

        // e) Pad or truncate the user password string as described in step (a) of
        //    "Algorithm 2: Computing an encryption key".
        $s = substr($userPassword . $this->padding, 0, 32);

        // f) Encrypt the result of step (e), using an RC4 encryption function with
        //    the encryption key obtained in step (d).
        $s = $this->arcfour($encryptionKey, $s);

        // g) (Security handlers of revision 3 or greater) Do the following 19 times:
        //    Take the output from the previous invocation of the RC4 function and pass
        //    it as input to a new invocation of the function; use an encryption key
        //    generated by taking each byte of the encryption key obtained in step (d)
        //    and performing an XOR (exclusive or) operation between that byte and the
        //    single-byte value of the iteration counter (from 1 to 19).
        if ($revision >= 3) {
            for ($i = 1; $i <= 19; $i++) {
                $tmp = array();
                for ($j = 0; $j < $this->keyLength; $j++) {
                    $tmp[$j] = ord($encryptionKey[$j]) ^ $i;
                    $tmp[$j] = chr($tmp[$j]);
                }
                $s = $this->arcfour(join('', $tmp), $s);
            }
        }

        // h) Store the output from the final invocation of the RC4 function as the value
        //    of the O entry in the encryption dictionary.
        return $s;
    }

    /**
     * Compute the encryption key based on a password.
     *
     * @param string $password
     * @return string
     */
    protected function computeEncryptionKey($password = '')
    {
        $revision = $this->revision;

        // TODO: The password string is generated from OS codepage characters by first
        // converting the string to PDFDocEncoding. If the input is Unicode, first convert
        // to a codepage encoding, and then to PDFDocEncoding for backward compatibility.


        // Algorithm 2: Computing an encryption key
        // a) Pad or truncate the password string to exactly 32 bytes.
        // b) Initialize the MD5 hash function and pass the result of step (a) as input to this function.
        $s = substr($password . $this->padding, 0, 32);

        // c) Pass the value of the encryption dictionary’s O entry to the MD5 hash function.
        //    ("Algorithm 3: Computing the encryption dictionary’s O (owner password) value" shows how the O value is computed.)
        $s .= $this->oValue;

        // d) Convert the integer value of the P entry to a 32-bit unsigned binary number and pass these
        //    bytes to the MD5 hash function, low-order byte first.
        $pValue = (int)(float)$this->pValue;
        $s .= pack("V", $pValue);

        // e) Pass the first element of the file’s file identifier array (the value of the ID
        //    entry in the document’s trailer dictionary; see Table 15) to the MD5 hash function.
        $s .= $this->fileIdentifier;

        // f) (Security handlers of revision 4 or greater) If document metadata is not
        //    being encrypted, pass 4 bytes with the value 0xFFFFFFFF to the MD5 hash function.
        // ...

        // g) Finish the hash.
        $s = md5($s, true);

        // h) (Security handlers of revision 3 or greater) Do the following 50 times:
        //    Take the output from the previous MD5 hash and pass the first n bytes of
        //    the output as input into a new MD5 hash, where n is the number of bytes
        //    of the encryption key as defined by the value of the encryption dictionary’s
        //    Length entry.
        if ($revision >= 3) {
            for ($i = 0; $i < 50; $i++)
                $s = md5(substr($s, 0, $this->keyLength), true);
        }

        // i) Set the encryption key to the first n bytes of the output from the final
        //    MD5 hash, where n shall always be 5 for security handlers of revision 2 but,
        //    for security handlers of revision 3 or greater, shall depend on the value of
        //    the encryption dictionary’s Length entry.

        return substr($s, 0, $this->keyLength); // key length is calculated automatically if it is missing (5)
    }

    /**
     * Compute the U value.
     *
     * @param string $encryptionKey
     * @return string
     */
    protected function computeUValue($encryptionKey)
    {
        $revision = $this->revision;
        // Algorithm 4: Computing the encryption dictionary’s U (user password)
        // value (Security handlers of revision 2)
        if ($revision < 3) {
            return $this->arcfour($encryptionKey, $this->padding);
        }

        // Algorithm 5: Computing the encryption dictionary’s U (user password)
        // value (Security handlers of revision 3 or greater)

        // a) Create an encryption key based on the user password string, as described
        //    in "Algorithm 2: Computing an encryption key".
        //    passed through $encryptionKey-parameter

        // b) Initialize the MD5 hash function and pass the 32-byte padding string shown
        //    in step (a) of "Algorithm 2: Computing an encryption key" as input to
        //    this function.
        $s = $this->padding;

        // c) Pass the first element of the file’s file identifier array (the value of
        //    the ID entry in the document’s trailer dictionary; see Table 15) to the
        //    hash function and finish the hash.
        $s .= $this->fileIdentifier;
        $s = md5($s, true);

        // d) Encrypt the 16-byte result of the hash, using an RC4 encryption function
        //    with the encryption key from step (a).
        $s = $this->arcfour($encryptionKey, $s);

        // e) Do the following 19 times: Take the output from the previous invocation
        //    of the RC4 function and pass it as input to a new invocation of the function;
        //    use an encryption key generated by taking each byte of the original encryption
        //    key obtained in step (a) and performing an XOR (exclusive or) operation
        //    between that byte and the single-byte value of the iteration counter (from 1 to 19).
        $length = strlen($encryptionKey);
        for($i = 1; $i <= 19; $i++) {
            $tmp = array();
            for($j = 0; $j < $length; $j++) {
                $tmp[$j] = ord($encryptionKey[$j]) ^ $i;
                $tmp[$j] = chr($tmp[$j]);
            }
            $s = $this->arcfour(join('', $tmp), $s);
        }

        // f) Append 16 bytes of arbitrary padding to the output from the final invocation
        //    of the RC4 function and store the 32-byte result as the value of the U entry
        //    in the encryption dictionary.
        return $s . str_repeat("\0", 16);
    }

    /**
     * Encrypt data using Arcfour.
     *
     * @param string $key
     * @param string $data
     * @return string
     */
    protected function arcfour($key, $data)
    {
        return openssl_encrypt($data, 'RC4-40', $key, OPENSSL_RAW_DATA, '');
    }

    /**
     * Writes a PdfType object to the resulting buffer.
     *
     * @param PdfType $value
     */
    protected function writePdfType(PdfType $value)
    {
        if (!$this->encrypted) {
            parent::writePdfType($value);
            return;
        }

        if ($value instanceof PdfString) {
            $string = PdfString::unescape($value->value);
            $string = $this->arcfour($this->getEncryptionKeyByObjectNumber($this->currentObjectNumber), $string);
            $value->value = $this->_escape($string);

        } elseif ($value instanceof PdfHexString) {
            $filter = new AsciiHex();
            $string = $filter->decode($value->value);
            $string = $this->arcfour($this->getEncryptionKeyByObjectNumber($this->currentObjectNumber), $string);
            $value->value = $filter->encode($string, true);

        } elseif ($value instanceof PdfStream) {
            $stream = $value->getStream();
            $stream = $this->arcfour($this->getEncryptionKeyByObjectNumber($this->currentObjectNumber), $stream);
            $dictionary = $value->value;
            $dictionary->value['Length'] = PdfNumeric::create(strlen($stream));
            $value = PdfStream::create($dictionary, $stream);
        }

        parent::writePdfType($value);
    }

    /**
     * Computes the encryption key by an object number
     *
     * @param int $objectNumber
     * @return bool|string
     */
    protected function getEncryptionKeyByObjectNumber($objectNumber)
    {
        return substr(
            substr(
                md5($this->encryptionKey . pack('VXxx', $objectNumber), true),
                0,
                $this->keyLength + 5
            ),
            0,
            16
        );
    }

    /**
     * @param null $n
     */
    protected function _newobj($n = null)
    {
        parent::_newobj($n);
        if ($n === null) {
            $this->currentObjectNumber = $this->n;
        } else {
            $this->currentObjectNumber = $n;
        }
    }

    /**
     * @param string $s
     */
    protected function _putstream($s)
    {
        if ($this->encrypted) {
            $s = $this->arcfour($this->getEncryptionKeyByObjectNumber($this->n), $s);
        }

        parent::_putstream($s);
    }

    /**
     * @param string $s
     * @return string
     */
    protected function _textstring($s)
    {
        if (!$this->_isascii($s)) {
            $s = $this->_UTF8toUTF16($s);
        }

        if ($this->encrypted) {
            $s = $this->arcfour($this->getEncryptionKeyByObjectNumber($this->n), $s);
        }

        return '(' . $this->_escape($s) . ')';
    }

    protected function _putresources()
    {
        parent::_putresources();
        if ($this->encrypted) {
            $this->_newobj();
            $this->encObjectNumber = $this->n;
            $this->_put('<<');
            $this->_putencryption();
            $this->_put('>>');
            $this->_put('endobj');
        }
    }

    /**
     * Writes the values of the encryption dictionary.
     */
    protected function _putencryption()
    {
        $this->_put('/Filter /Standard');
        $this->_put('/V ' . ($this->revision === 3 ? '2' : '1'));
        $this->_put('/R ' . ($this->revision === 3 ? '3' : '2'));
        if ($this->revision === 3) {
            $this->_put('/Length 128');
        }
        $this->_put('/O (' . $this->_escape($this->oValue) . ')');
        $this->_put('/U (' . $this->_escape($this->uValue) . ')');
        $this->_put('/P ' . $this->pValue);
    }

    protected function _puttrailer()
    {
        parent::_puttrailer();
        if ($this->encrypted) {
            $this->_put('/Encrypt ' . $this->encObjectNumber . ' 0 R');
            $filter = new AsciiHex();
            $fileIdentifier = $filter->encode($this->fileIdentifier, true);
            $this->_put('/ID [<' . $fileIdentifier . '><' . $fileIdentifier . '>]');
        }
    }
}
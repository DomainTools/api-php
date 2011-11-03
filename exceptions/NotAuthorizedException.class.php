<?php

/**
 * This file is part of the domaintoolsAPI_php_wrapper package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class NotAuthorizedException extends ServiceException {
    public function __construct($message='', $code=0) {
        parent::__construct($message, $code);
    }
}
?>


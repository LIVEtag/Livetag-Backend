<?php
/**
 * Copyright © 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace rest\tests;

use yii\swiftmailer\Message as MailerMessage;

class Message extends MailerMessage
{
    /**
     * @var string
     */
    private $_htmlBody;

    /**
     * @var string
     */
    private $_textBody;

    public function setHtmlBody($html)
    {
        $this->_htmlBody = $html;
        parent::setHtmlBody($html);
    }

    public function setTextBody($text)
    {
        $this->_textBody = $text;
        parent::setTextBody($text);
    }

    /**
     * Returns text of message
     *
     * @return string
     */
    public function getBody()
    {
        return $this->_htmlBody;
    }

    /**
     * @return mixed
     */
    public function getHtmlBody()
    {
        return $this->_htmlBody;
    }

    /**
     * @return mixed
     */
    public function getTextBody()
    {
        return $this->_textBody;
    }
}

<?php
namespace Hummer\Component\Log\Writer;

interface IStrategy{
    /**
     *  AcceptData
     **/
    public function writeIn($aRow);

    /**
     * Everty Query GUID
     **/
    public function setGUID($sGUID);
}

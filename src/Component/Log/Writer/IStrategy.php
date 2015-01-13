<?php
namespace Hummer\Component\Log\Writer;

interface IStrategy{

    /**
     *  AcceptData
     **/
    public function acceptData($aRow);

    /**
     * Everty Query GUID
     **/
    public function setGUID($sGUID);
}

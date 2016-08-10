<?php

class Ynrestapi_Helper_Like extends Ynrestapi_Helper_Base
{
    public function field_listing()
    {
        // force to owner
        $this->data = $this->field_owner(true);
    }
}

<?php
    if (isset($this->number) && $this->number) {
        $limit = 1e5;
        if ($this->number >= $limit) {
            echo round($this->number/1e3) . 'k';
        } else {
            echo $this->number;
        }
    } else {
        echo '0';
    }
?>
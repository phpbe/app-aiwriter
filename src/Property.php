<?php

namespace Be\App\AiWriter;


class Property extends \Be\App\Property
{

    protected string $label = 'AiWriter';
    protected string $icon = 'bi-person-bounding-box';
    protected string $description = 'AI 作家';

    public function __construct() {
        parent::__construct(__FILE__);
    }

}

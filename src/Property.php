<?php

namespace Be\App\AiWriter;


class Property extends \Be\App\Property
{

    protected string $label = 'AI 作家';
    protected string $icon = 'bi-person-bounding-box';
    protected string $description = '借助人工智能（如OpenAI ChatGPT）自动化海量生产内容，创作，改编等。';

    public function __construct() {
        parent::__construct(__FILE__);
    }

}

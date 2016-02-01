<?php

namespace Uppdragshuset\AO\Repository\Contracts;

interface Presenter
{
    /**
     * Prepare data to present
     *
     * @param $data
     * @return mixed
     */
    public function present($data);
}
<?php
/**
 * Copyright © 2020 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace backend\models\Product;

use common\models\Product\Product as BaseModel;

/**
 * Represents the backend version of `common\models\Product\Product`.
 */
class Product extends BaseModel
{
    /**
     * get options of product in html format
     * @param array $options
     * @return mixed
     */
    public function getProductOptionsInHTML(array $options)
    {
        if (empty($options)) {
            return null;
        }
        $lastItem = array_pop($options);
        if (!\is_array($lastItem) && empty($lastItem)) {
            return null;
        }
        $headers = array_keys($lastItem);
        $options[] = $lastItem;
        $html = '';
        $html .= '<table class="table table-condensed">';
        $html .= '<thead>';
        $html .= '<tr>';

        foreach ($headers as $headValue) {
            $html .= '<th style="text-transform:capitalize">'.$headValue.'</th>';
        }
        $html .= '</tr>';
        $html .= '</thead>';
        $html .= '<tbody>';

        foreach ($options as $optionItems) {
            $html .= '<tr>';
            foreach ($optionItems as $optionValue) {
                $html .= '<td>';
                $html .= $optionValue;
                $html .= '</td>';
            }
            $html .= '</tr>';
        }
        $html .= '</tbody>';
        $html .= '</table>';

        return $html;
    }

    /**
     * @return string|null
     */
    public function getStatusName(): ?string
    {
        return ArrayHelper::getValue(self::STATUSES, $this->status);
    }
}

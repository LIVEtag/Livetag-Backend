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
        $html .= '<table>';
        $html .= '<thead>';
        $html .= '<tr>';
        
        foreach ($headers as $headValue) {
            $html .= "<th class='text-center col-xs-4 col-md-4 col-lg-4' >{$headValue}</th>";
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
}

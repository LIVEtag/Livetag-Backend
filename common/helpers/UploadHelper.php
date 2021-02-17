<?php
/**
 * Copyright © 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace common\helpers;

use common\models\forms\Product\ProductsUploadForm;
use yii\base\Model;

/**
 * UploadHelper
 */
class UploadHelper extends Model
{
    /**
     * @param array $header
     * @return array
     */
    public static function validateHeaderCsv(array $header): array
    {
        $errorsPack = [];
        //get current mapper of headers
        $headerTitles = ProductsUploadForm::MAP_HEADER_OPTIONS;
        //get required option in header
        $headerOptionsRequired = ProductsUploadForm::OPTION_REQUIRED;
        //get required field (not option) in header
        $headerRequired = ProductsUploadForm::HEADER_REQUIRED;
        //new header Array without options
        $newHeader = [];
        foreach ($header as $csvKeyField => $csvValueField) {
            if (!preg_match('/\boption\b/', $csvValueField)) {
                //set new header after upload without options key => value
                $newHeader[$csvKeyField] = strtolower(trim($csvValueField));
            }
        }
        self::validateRequiredHeaderCsv($headerRequired, $headerOptionsRequired, $newHeader, $errorsPack);
        self::validateMappingHeaderCsv($newHeader, $headerTitles, $errorsPack);
        
        return $errorsPack;
    }
    
    /**
     * @param array $headerRequired
     * @param array $headerOptionsRequired
     * @param array $newHeader
     * @param array $errorsPack
     * @return void
     */
    public static function validateRequiredHeaderCsv(array $headerRequired, array $headerOptionsRequired, array $newHeader, array &$errorsPack): void
    {
        //check if exists required headers in option
        if (\is_array($headerOptionsRequired) && \is_array($newHeader)) {
            $missingRequiredOption = array_diff($headerOptionsRequired, $newHeader);
            $missingRequiredHeader = array_diff($headerRequired, $newHeader);
            if ($missingRequiredOption && \is_array($missingRequiredOption) && !empty($newHeader)) {
                $errorsPack['errors'][] = [
                    'line' => 0,
                    'error' => 'Header has missing elements for options: '. implode(',', $missingRequiredOption)
                ];
            }
    
            if ($missingRequiredHeader && \is_array($missingRequiredHeader) && !empty($newHeader)) {
                $errorsPack['errors'][] = [
                    'line' => 0,
                    'error' => 'Header has missing elements: '. implode(',', $missingRequiredHeader)
                ];
            }
        }
    }
    
    /**
     * @param array $newHeader
     * @param array $headerOptions
     * @param array $errorsPack
     * @return void
     */
    public static function validateMappingHeaderCsv(array $newHeader, array $headerOptions, array &$errorsPack): void
    {
        //check if element of new headers without options has correct mapping
        foreach ($newHeader as $newValueHeader) {
            if (!\in_array($newValueHeader, $headerOptions, true)) {
                $errorsPack['errors'][] = [
                    'line' => 0,
                    'error' => 'Header of csv file is incorrect'
                ];
            }
        }
    }
    
    /**
     * @param $response
     * @return array
     */
    public static function fileErrorUploadResponse($response): array
    {
        $errors = [];
        if (\is_array($response) && $response) {
            $errorsPack = [];
            $lines = [];
            foreach ($response as $errorValues) {
                if (\is_array($errorValues)) {
                    foreach ($errorValues as $errorItem) {
                        if (isset($errorItem['error']) && \is_array($errorItem)) {
                            $errorsPack[] = $errorItem['error'];
                            $lines[] = $errorItem['line'];
                        }
                    }
                }
            }
            if ($errorsPack && $lines) {
                $errors[] = implode('<br>', $errorsPack);
                $lines = implode(', ', array_unique($lines));
                array_unshift(
                    $errors,
                    "There are errors in the file. Please correct inconsistencies in line/s {$lines} and re-upload the file to display the products."
                );
            }
        }
        return $errors;
    }
}

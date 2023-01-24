<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<?php
\Bitrix\Main\Loader::includeModule('iblock');

function clearData($item)
{
    return preg_replace('/[^0-9+]/', '', $item);
}

$res = \Bitrix\Iblock\Elements\ElementApimapsTable::getList([
    'select' => [
        'ID',
        'PROP:OFFICE_' => 'OFFICE',
        'PROP:PHONE_' => 'PHONE',
        'PROP:EMAIL_' => 'EMAIL',
        'PROP:COORDINATES_' => 'COORDINATES',
        'PROP:CITY_' => 'CITY',
    ],
    'filter' => [
        "ACTIVE" => 'Y',
    ],
    'order' => [
        'SORT' => 'ASC'
    ],
    "cache" => ["ttl" => 36000000],
])->fetchAll();

if (count($res) == 0){
    \Bitrix\Iblock\Elements\ElementApimapsTable::getEntity()->cleanCache();
}
foreach ($res as $ob){
    $arResult['ITEMS'][] = [
        'office' => $ob['PROP:OFFICE_VALUE'],
        'phone' => $ob['PROP:PHONE_VALUE'],
        'email' => $ob['PROP:EMAIL_VALUE'],
        'coordinates' => $ob['PROP:COORDINATES_VALUE'],
        'city' => $ob['PROP:CITY_VALUE'],
        'clearphone' => clearData($ob['PROP:PHONE_VALUE']),

    ];
}

$this->IncludeComponentTemplate();

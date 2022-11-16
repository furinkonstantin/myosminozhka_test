<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

/** @var array $arCurrentValues */

use Bitrix\Main,
    Bitrix\Main\Localization\Loc as Loc,
    Bitrix\Main\Config\Option;

Loc::loadMessages(__FILE__);

try
{
    $rsLangs = CLanguage::GetList($by='lid', $order='desc');
    $arLangs = [];
    $additionalLangParams = [];
    $arFirstLang = [];
    $i = 0;
    while ($arLang = $rsLangs->Fetch())
    {
        if($i == 0)
        {
            $arFirstLang = $arLang;
        }
        $arLangs[$arLang['LID']] = $arLang['NAME'];
        $i++;
    }
    
    $lid = $arCurrentValues['LANG_ID'];
    $nameLang = $arLangs[$arCurrentValues['LANG_ID']];
    if (empty($lid))
    {
        $lid = $arFirstLang['LID'];
        $nameLang = $arFirstLang['NAME'];
    }
    
    $additionalLangParams['PERCENTS_FOR_AUTHORIZED_USERS_' . ToUpper($lid)] = 
    [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('MYOSMINOZHKA_PERCENTS_FOR_AUTHORIZED_USERS', ['LANG' => $nameLang]),
        'TYPE' => 'STRING',
        'MULTIPLE' => 'Y',
        'DEFAULT' => ''
    ];
    $additionalLangParams['PERCENTS_FOR_UNAUTHORIZED_USERS_' . ToUpper($lid)] =
    [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('MYOSMINOZHKA_PERCENTS_FOR_UNAUTHORIZED_USERS', ['LANG' => $nameLang]),
        'TYPE' => 'STRING',
        'MULTIPLE' => 'Y',
        'DEFAULT' => ''
    ];
    
	$arComponentParameters = array(
		'GROUPS' => array(
		),
		'PARAMETERS' => array(
            'LANG_ID' => [
                'PARENT' => 'BASE',
                'NAME' => Loc::getMessage('MYOSMINOZHKA_LANG_ID'),
                'TYPE' => 'LIST',
                'VALUES' => $arLangs,
                'DEFAULT' => '',
                'REFRESH' => 'Y'
            ],
            'CACHE_TIME'  => ['DEFAULT'=>86400]
        )
    );
    if ($additionalLangParams)
    {
        foreach($additionalLangParams as $code => $additionalLangParam)
        {
            $arComponentParameters['PARAMETERS'][$code] = $additionalLangParam;
        }
    }
}
catch (Main\LoaderException $e)
{
	ShowError($e->getMessage());
}
?>
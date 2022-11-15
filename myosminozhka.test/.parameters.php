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
    while ($arLang = $rsLangs->Fetch())
    {
        $arLangs[$arLang['LID']] = $arLang['NAME'];
        if ($arCurrentValues['LANG_ID'] == $arLang['LID']) {
            $additionalLangParams['PERCENTS_FOR_AUTHORIZED_USERS_' . ToUpper($arLang['LID'])] = 
            [
                'PARENT' => 'BASE',
                'NAME' => Loc::getMessage('MYOSMINOZHKA_PERCENTS_FOR_AUTHORIZED_USERS', ['LANG' => $arLang['NAME']]),
                'TYPE' => 'STRING',
                'MULTIPLE' => 'Y',
                'DEFAULT' => ''
            ];
            $additionalLangParams['PERCENTS_FOR_UNAUTHORIZED_USERS_' . ToUpper($arLang['LID'])] =
            [
                'PARENT' => 'BASE',
                'NAME' => Loc::getMessage('MYOSMINOZHKA_PERCENTS_FOR_UNAUTHORIZED_USERS', ['LANG' => $arLang['NAME']]),
                'TYPE' => 'STRING',
                'MULTIPLE' => 'Y',
                'DEFAULT' => ''
            ];
        }
    }

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
    $arComponentParameters['PARAMETERS'] = array_merge($arComponentParameters['PARAMETERS'], $additionalLangParams);
}
catch (Main\LoaderException $e)
{
	ShowError($e->getMessage());
}
?>
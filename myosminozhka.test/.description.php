<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\Localization\Loc as Loc;

Loc::loadMessages(__FILE__);

$arComponentDescription = array(
	"NAME" => Loc::getMessage('MYOSMINOZHKA_NAME'),
	"DESCRIPTION" => Loc::getMessage('MYOSMINOZHKA_DESCRIPTION'),
	"ICON" => '/images/icon.gif',
	"SORT" => 20,
	"PATH" => array(
		"ID" => 'myosminozhka',
		"NAME" => Loc::getMessage('MYOSMINOZHKA_GROUP_NAME'),
		"SORT" => 10
	),
);

?>
<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/classes/general/xml.php');

use Bitrix\Main,
    Bitrix\Main\Localization\Loc as Loc,
    Bitrix\Main\Type\DateTime,
    Bitrix\Main\Application,
    Bitrix\Main\Web\Uri,
    Bitrix\Main\Web\HttpClient,
    Bitrix\Main\Data\Cache,
    Bitrix\Main\Engine\CurrentUser;

class MyosminozhkaTestComponent extends \CBitrixComponent
{
    
    const PATH_CBR_CURRENCIES = 'http://www.cbr.ru/scripts/XML_daily.asp';
    const SAVE_CURRENCIES = ['USD', 'EUR'];
    private $valueUSD, $valueEUR;
    
    public function onPrepareComponentParams($params)
    {
        global $APPLICATION;
        $result = parent::onPrepareComponentParams($params);
        return $result;
    }
    
    private function filterCurrentDate() {
        $objDateTime = new DateTime();
        $res = $objDateTime->format('d/m/Y');
        return $res;
    }
    
    private function readXMLCbr()
    {
        $cache = Cache::createInstance();
        if ($cache->initCache($this->arParams['CACHE_TIME'], 'currencies'))
        {
            $vars = $cache->getVars();
            $this->arResult = $vars;
        } elseif($cache->startDataCache()) {
            $uri = new Uri(self::PATH_CBR_CURRENCIES);
            $uri->addParams(['date_req' => $this->filterCurrentDate()]);
            $xml = new CDataXML();
            $httpClient = new HttpClient();
            $currencyXML = $httpClient->get($uri->getUri());
            $xml->LoadString($currencyXML);
            $arData = $xml->GetArray();
            $arCurrencyValues = [];
            foreach ($arData['ValCurs']['#']['Valute'] as $arValue)
            {
                $ar = [];
                foreach ($arValue['#'] as $sKey => $sVal)
                {
                    $ar[$sKey] = $sVal[0]['#'];
                }
                $arCurrencyValues[$ar['CharCode']] = $ar['Value'];
            }
            foreach(self::SAVE_CURRENCIES as $currency)
            {
                $result[$currency] = $arCurrencyValues[$currency];
            }
            $this->arResult = $result;
            $cache->endDataCache();
        }
    }
    
    private function sumCurrencyWithPercent($value, $percent) {
        $res = $value + ceil($value * $percent / 100);
        return $res;
    }
    
    private function calculatePercentsForCurrencies() {
        if (CurrentUser::get()->getId())
        {
            $percents = $this->arParams['PERCENTS_FOR_AUTHORIZED_USERS_' . ToUpper(LANGUAGE_ID)];
        } else {
            $percents = $this->arParams['PERCENTS_FOR_UNAUTHORIZED_USERS_' . ToUpper(LANGUAGE_ID)];
        }
        $this->arResult['CALCULATE_PERCENTS_FOR_CURRENCIES'] = [];
        $percents = array_diff($percents, ['']);
        foreach($percents as $percent)
        {
            foreach(self::SAVE_CURRENCIES as $currency)
            {
                $this->arResult['CALCULATE_PERCENTS_FOR_CURRENCIES'][$currency][] = $this->sumCurrencyWithPercent($this->arResult[$currency], $percent);
            }
        }
    }

    public function executeComponent()
    {
        try
		{
            $this->readXMLCbr();
            $this->calculatePercentsForCurrencies();
            $this->includeComponentTemplate();
        }
		catch (Exception $e)
		{
			ShowError($e->getMessage());
		}
    }
}

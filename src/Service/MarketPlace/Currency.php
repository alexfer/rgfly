<?php

namespace App\Service\MarketPlace;

class Currency
{


    public const USD = 'USD';
    public const CAD = 'CAD';
    public const EUR = 'EUR';
    public const AED = 'AED';
    public const AFN = 'AFN';
    public const ALL = 'ALL';
    public const AMD = 'AMD';
    public const ARS = 'ARS';
    public const AUD = 'AUD';
    public const AZN = 'AZN';
    public const BAM = 'BAM';
    public const BDT = 'BDT';
    public const BGN = 'BGN';
    public const BHD = 'BHD';
    public const BIF = 'BIF';
    public const BND = 'BND';
    public const BOB = 'BOB';
    public const BRL = 'BRL';
    public const BWP = 'BWP';
    public const BYN = 'BYN';
    public const BZD = 'BZD';
    public const CDF = 'CDF';
    public const CHF = 'CHF';
    public const CLP = 'CLP';
    public const CNY = 'CNY';
    public const COP = 'COP';
    public const CRC = 'CRC';
    public const CVE = 'CVE';
    public const CZK = 'CZK';
    public const DJF = 'DJF';
    public const DKK = 'DKK';
    public const DOP = 'DOP';
    public const DZD = 'DZD';
    public const EEK = 'EEK';
    public const EGP = 'EGP';
    public const ERN = 'ERN';
    public const ETB = 'ETB';
    public const GBP = 'GBP';
    public const GEL = 'GEL';
    public const GHS = 'GHS';
    public const GNF = 'GNF';
    public const GTQ = 'GTQ';
    public const HKD = 'HKD';
    public const HNL = 'HNL';
    public const HRK = 'HRK';
    public const HUF = 'HUF';
    public const IDR = 'IDR';
    public const ILS = 'ILS';
    public const INR = 'INR';
    public const IQD = 'IQD';
    public const IRR = 'IRR';
    public const ISK = 'ISK';
    public const JMD = 'JMD';
    public const JOD = 'JOD';
    public const JPY = 'JPY';
    public const KES = 'KES';
    public const KHR = 'KHR';
    public const KMF = 'KMF';
    public const KRW = 'KRW';
    public const KWD = 'KWD';
    public const KZT = 'KZT';
    public const LBP = 'LBP';
    public const LKR = 'LKR';
    public const LTL = 'LTL';
    public const LVL = 'LVL';
    public const LYD = 'LYD';
    public const MAD = 'MAD';
    public const MDL = 'MDL';
    public const MGA = 'MGA';
    public const MKD = 'MKD';
    public const MMK = 'MMK';
    public const MOP = 'MOP';
    public const MUR = 'MUR';
    public const MXN = 'MXN';
    public const MYR = 'MYR';
    public const MZN = 'MZN';
    public const NAD = 'NAD';
    public const NGN = 'NGN';
    public const NIO = 'NIO';
    public const NOK = 'NOK';
    public const NPR = 'NPR';
    public const NZD = 'NZD';
    public const OMR = 'OMR';
    public const PAB = 'PAB';
    public const PEN = 'PEN';
    public const PHP = 'PHP';
    public const PKR = 'PKR';
    public const PLN = 'PLN';
    public const PYG = 'PYG';
    public const QAR = 'QAR';
    public const RON = 'RON';
    public const RSD = 'RSD';
    public const RUB = 'RUB';
    public const RWF = 'RWF';
    public const SAR = 'SAR';
    public const SDG = 'SDG';
    public const SEK = 'SEK';
    public const SGD = 'SGD';
    public const SOS = 'SOS';
    public const SYP = 'SYP';
    public const THB = 'THB';
    public const TND = 'TND';
    public const TOP = 'TOP';
    public const TRY = 'TRY';
    public const TTD = 'TTD';
    public const TWD = 'TWD';
    public const TZS = 'TZS';
    public const UAH = 'UAH';
    public const UGX = 'UGX';
    public const UYU = 'UYU';
    public const UZS = 'UZS';
    public const VEF = 'VEF';
    public const VND = 'VND';
    public const XAF = 'XAF';
    public const XOF = 'XOF';
    public const YER = 'YER';
    public const ZAR = 'ZAR';
    public const ZMK = 'ZMK';
    public const ZWL = 'ZWL';
    private const CODE = 'code';
    private const COUNTRY_NAME = 'country_name';
    private const CURRENCY = 'currency';
    private const SYMBOL = 'symbol';
    private const SYMBOL_NATIVE = 'symbol_native';
    private const DECIMAL_DIGITS = 'decimal_digits';
    private const ROUNDING = 'rounding';
    private const NAME_PLURAL = 'name_plural';

    /**
     * @var array|array[]
     */
    public static array $currencies = [
        self::USD => [self::COUNTRY_NAME => 'US Dollar', self::CODE => self::USD, self::CURRENCY => 'US Dollar', self::SYMBOL => '$', self::SYMBOL_NATIVE => '$', self::DECIMAL_DIGITS => 2, self::ROUNDING => 0, self::NAME_PLURAL => 'US dollars'],
        self::CAD => [self::COUNTRY_NAME => 'Canadian Dollar', self::CODE => self::CAD, self::CURRENCY => 'Canadian Dollar', self::SYMBOL => 'CA$', self::SYMBOL_NATIVE => '$', self::DECIMAL_DIGITS => 2, self::ROUNDING => 0, self::NAME_PLURAL => 'Canadian dollars'],
        self::EUR => [self::COUNTRY_NAME => 'Euro', self::CODE => self::EUR, self::CURRENCY => 'Euro', self::SYMBOL => '€', self::SYMBOL_NATIVE => '€', self::DECIMAL_DIGITS => 2, self::ROUNDING => 0, self::NAME_PLURAL => 'euros'],
        self::AED => [self::COUNTRY_NAME => 'United Arab Emirates Dirham', self::CODE => self::AED, self::CURRENCY => 'United Arab Emirates Dirham', self::SYMBOL => 'AED', self::SYMBOL_NATIVE => 'د.إ.‏', self::DECIMAL_DIGITS => 2, self::ROUNDING => 0, self::NAME_PLURAL => 'UAE dirhams'],
        self::AFN => [self::COUNTRY_NAME => 'Afghan Afghani', self::CODE => self::AFN, self::CURRENCY => 'Afghan Afghani', self::SYMBOL => 'Af', self::SYMBOL_NATIVE => '؋', self::DECIMAL_DIGITS => 0, self::ROUNDING => 0, self::NAME_PLURAL => 'Afghan Afghanis'],
        self::ALL => [self::COUNTRY_NAME => 'Albanian Lek', self::CODE => self::ALL, self::CURRENCY => 'Albanian Lek', self::SYMBOL => 'ALL', self::SYMBOL_NATIVE => 'Lek', self::DECIMAL_DIGITS => 0, self::ROUNDING => 0, self::NAME_PLURAL => 'Albanian lekë'],
        self::AMD => [self::COUNTRY_NAME => 'Armenian Dram', self::CODE => self::AMD, self::CURRENCY => 'Armenian Dram', self::SYMBOL => 'AMD', self::SYMBOL_NATIVE => 'դր.', self::DECIMAL_DIGITS => 0, self::ROUNDING => 0, self::NAME_PLURAL => 'Armenian drams'],
        self::ARS => [self::COUNTRY_NAME => 'Argentine Peso', self::CODE => self::ARS, self::CURRENCY => 'Argentine Peso', self::SYMBOL => 'AR$', self::SYMBOL_NATIVE => '$', self::DECIMAL_DIGITS => 2, self::ROUNDING => 0, self::NAME_PLURAL => 'Argentine pesos'],
        self::AUD => [self::COUNTRY_NAME => 'Australian Dollar', self::CODE => self::AUD, self::CURRENCY => 'Australian Dollar', self::SYMBOL => 'AU$', self::SYMBOL_NATIVE => '$', self::DECIMAL_DIGITS => 2, self::ROUNDING => 0, self::NAME_PLURAL => 'Australian dollars'],
        self::AZN => [self::COUNTRY_NAME => 'Azerbaijani Manat', self::CODE => self::AZN, self::CURRENCY => 'Azerbaijani Manat', self::SYMBOL => 'man.', self::SYMBOL_NATIVE => 'ман.', self::DECIMAL_DIGITS => 2, self::ROUNDING => 0, self::NAME_PLURAL => 'Azerbaijani manats'],
        self::BAM => [self::COUNTRY_NAME => 'Bosnia-Herzegovina Convertible Mark', self::CODE => self::BAM, self::CURRENCY => 'Bosnia-Herzegovina Convertible Mark', self::SYMBOL => 'KM', self::SYMBOL_NATIVE => 'KM', self::DECIMAL_DIGITS => 2, self::ROUNDING => 0, self::NAME_PLURAL => 'Bosnia-Herzegovina convertible marks'],
        self::BDT => [self::COUNTRY_NAME => 'Bangladeshi Taka', self::CODE => self::BDT, self::CURRENCY => 'Bangladeshi Taka', self::SYMBOL => 'Tk', self::SYMBOL_NATIVE => '৳', self::DECIMAL_DIGITS => 2, self::ROUNDING => 0, self::NAME_PLURAL => 'Bangladeshi takas'],
        self::BGN => [self::COUNTRY_NAME => 'Bulgarian Lev', self::CODE => self::BGN, self::CURRENCY => 'Bulgarian Lev', self::SYMBOL => 'BGN', self::SYMBOL_NATIVE => 'лв.', self::DECIMAL_DIGITS => 2, self::ROUNDING => 0, self::NAME_PLURAL => 'Bulgarian leva'],
        self::BHD => [self::COUNTRY_NAME => 'Bahraini Dinar', self::CODE => self::BHD, self::CURRENCY => 'Bahraini Dinar', self::SYMBOL => 'BD', self::SYMBOL_NATIVE => 'د.ب.‏', self::DECIMAL_DIGITS => 3, self::ROUNDING => 0, self::NAME_PLURAL => 'Bahraini dinars'],
        self::BIF => [self::COUNTRY_NAME => 'Burundian Franc', self::CODE => self::BIF, self::CURRENCY => 'Burundian Franc', self::SYMBOL => 'FBu', self::SYMBOL_NATIVE => 'FBu', self::DECIMAL_DIGITS => 0, self::ROUNDING => 0, self::NAME_PLURAL => 'Burundian francs'],
        self::BND => [self::COUNTRY_NAME => 'Brunei Dollar', self::CODE => self::BND, self::CURRENCY => 'Brunei Dollar', self::SYMBOL => 'BN$', self::SYMBOL_NATIVE => '$', self::DECIMAL_DIGITS => 2, self::ROUNDING => 0, self::NAME_PLURAL => 'Brunei dollars'],
        self::BOB => [self::COUNTRY_NAME => 'Bolivian Boliviano', self::CODE => self::BOB, self::CURRENCY => 'Bolivian Boliviano', self::SYMBOL => 'Bs', self::SYMBOL_NATIVE => 'Bs', self::DECIMAL_DIGITS => 2, self::ROUNDING => 0, self::NAME_PLURAL => 'Bolivian bolivianos'],
        self::BRL => [self::COUNTRY_NAME => 'Brazilian Real', self::CODE => self::BRL, self::CURRENCY => 'Brazilian Real', self::SYMBOL => 'R$', self::SYMBOL_NATIVE => 'R$', self::DECIMAL_DIGITS => 2, self::ROUNDING => 0, self::NAME_PLURAL => 'Brazilian reals'],
        self::BWP => [self::COUNTRY_NAME => 'Botswanan Pula', self::CODE => self::BWP, self::CURRENCY => 'Botswanan Pula', self::SYMBOL => 'BWP', self::SYMBOL_NATIVE => 'P', self::DECIMAL_DIGITS => 2, self::ROUNDING => 0, self::NAME_PLURAL => 'Botswanan pulas'],
        self::BYN => [self::COUNTRY_NAME => 'Belarusian Ruble', self::CODE => self::BYN, self::CURRENCY => 'Belarusian Ruble', self::SYMBOL => 'Br', self::SYMBOL_NATIVE => 'руб.', self::DECIMAL_DIGITS => 2, self::ROUNDING => 0, self::NAME_PLURAL => 'Belarusian rubles'],
        self::BZD => [self::COUNTRY_NAME => 'Belize Dollar', self::CODE => self::BZD, self::CURRENCY => 'Belize Dollar', self::SYMBOL => 'BZ$', self::SYMBOL_NATIVE => '$', self::DECIMAL_DIGITS => 2, self::ROUNDING => 0, self::NAME_PLURAL => 'Belize dollars'],
        self::CDF => [self::COUNTRY_NAME => 'Congolese Franc', self::CODE => self::CDF, self::CURRENCY => 'Congolese Franc', self::SYMBOL => 'CDF', self::SYMBOL_NATIVE => 'FrCD', self::DECIMAL_DIGITS => 2, self::ROUNDING => 0, self::NAME_PLURAL => 'Congolese francs'],
        self::CHF => [self::COUNTRY_NAME => 'Swiss Franc', self::CODE => self::CHF, self::CURRENCY => 'Swiss Franc', self::SYMBOL => 'CHF', self::SYMBOL_NATIVE => 'CHF', self::DECIMAL_DIGITS => 2, self::ROUNDING => 0.05, self::NAME_PLURAL => 'Swiss francs'],
        self::CLP => [self::COUNTRY_NAME => 'Chilean Peso', self::CODE => self::CLP, self::CURRENCY => 'Chilean Peso', self::SYMBOL => 'CL$', self::SYMBOL_NATIVE => '$', self::DECIMAL_DIGITS => 0, self::ROUNDING => 0, self::NAME_PLURAL => 'Chilean pesos'],
        self::CNY => [self::COUNTRY_NAME => 'Chinese Yuan', self::CODE => self::CNY, self::CURRENCY => 'Chinese Yuan', self::SYMBOL => 'CN¥', self::SYMBOL_NATIVE => 'CN¥', self::DECIMAL_DIGITS => 2, self::ROUNDING => 0, self::NAME_PLURAL => 'Chinese yuan'],
        self::COP => [self::COUNTRY_NAME => 'Colombian Peso', self::CODE => self::COP, self::CURRENCY => 'Colombian Peso', self::SYMBOL => 'CO$', self::SYMBOL_NATIVE => '$', self::DECIMAL_DIGITS => 0, self::ROUNDING => 0, self::NAME_PLURAL => 'Colombian pesos'],
        self::CRC => [self::COUNTRY_NAME => 'Costa Rican Colón', self::CODE => self::CRC, self::CURRENCY => 'Costa Rican Colón', self::SYMBOL => '₡', self::SYMBOL_NATIVE => '₡', self::DECIMAL_DIGITS => 0, self::ROUNDING => 0, self::NAME_PLURAL => 'Costa Rican colóns'],
        self::CVE => [self::COUNTRY_NAME => 'Cape Verdean Escudo', self::CODE => self::CVE, self::CURRENCY => 'Cape Verdean Escudo', self::SYMBOL => 'CV$', self::SYMBOL_NATIVE => 'CV$', self::DECIMAL_DIGITS => 2, self::ROUNDING => 0, self::NAME_PLURAL => 'Cape Verdean escudos'],
        self::CZK => [self::COUNTRY_NAME => 'Czech Republic Koruna', self::CODE => self::CZK, self::CURRENCY => 'Czech Republic Koruna', self::SYMBOL => 'Kč', self::SYMBOL_NATIVE => 'Kč', self::DECIMAL_DIGITS => 2, self::ROUNDING => 0, self::NAME_PLURAL => 'Czech Republic korunas'],
        self::DJF => [self::COUNTRY_NAME => 'Djiboutian Franc', self::CODE => self::DJF, self::CURRENCY => 'Djiboutian Franc', self::SYMBOL => 'Fdj', self::SYMBOL_NATIVE => 'Fdj', self::DECIMAL_DIGITS => 0, self::ROUNDING => 0, self::NAME_PLURAL => 'Djiboutian francs'],
        self::DKK => [self::COUNTRY_NAME => 'Danish Krone', self::CODE => self::DKK, self::CURRENCY => 'Danish Krone', self::SYMBOL => 'Dkr', self::SYMBOL_NATIVE => 'kr', self::DECIMAL_DIGITS => 2, self::ROUNDING => 0, self::NAME_PLURAL => 'Danish kroner'],
        self::DOP => [self::COUNTRY_NAME => 'Dominican Peso', self::CODE => self::DOP, self::CURRENCY => 'Dominican Peso', self::SYMBOL => 'RD$', self::SYMBOL_NATIVE => 'RD$', self::DECIMAL_DIGITS => 2, self::ROUNDING => 0, self::NAME_PLURAL => 'Dominican pesos'],
        self::DZD => [self::COUNTRY_NAME => 'Algerian Dinar', self::CODE => self::DZD, self::CURRENCY => 'Algerian Dinar', self::SYMBOL => 'DA', self::SYMBOL_NATIVE => 'د.ج.‏', self::DECIMAL_DIGITS => 2, self::ROUNDING => 0, self::NAME_PLURAL => 'Algerian dinars'],
        self::EEK => [self::COUNTRY_NAME => 'Estonian Kroon', self::CODE => self::EEK, self::CURRENCY => 'Estonian Kroon', self::SYMBOL => 'Ekr', self::SYMBOL_NATIVE => 'kr', self::DECIMAL_DIGITS => 2, self::ROUNDING => 0, self::NAME_PLURAL => 'Estonian kroons'],
        self::EGP => [self::COUNTRY_NAME => 'Egyptian Pound', self::CODE => self::EGP, self::CURRENCY => 'Egyptian Pound', self::SYMBOL => 'EGP', self::SYMBOL_NATIVE => 'ج.م.‏', self::DECIMAL_DIGITS => 2, self::ROUNDING => 0, self::NAME_PLURAL => 'Egyptian pounds'],
        self::ERN => [self::COUNTRY_NAME => 'Eritrean Nakfa', self::CODE => self::ERN, self::CURRENCY => 'Eritrean Nakfa', self::SYMBOL => 'Nfk', self::SYMBOL_NATIVE => 'Nfk', self::DECIMAL_DIGITS => 2, self::ROUNDING => 0, self::NAME_PLURAL => 'Eritrean nakfas'],
        self::ETB => [self::COUNTRY_NAME => 'Ethiopian Birr', self::CODE => self::ETB, self::CURRENCY => 'Ethiopian Birr', self::SYMBOL => 'Br', self::SYMBOL_NATIVE => 'Br', self::DECIMAL_DIGITS => 2, self::ROUNDING => 0, self::NAME_PLURAL => 'Ethiopian birrs'],
        self::GBP => [self::COUNTRY_NAME => 'British Pound Sterling', self::CODE => self::GBP, self::CURRENCY => 'British Pound Sterling', self::SYMBOL => '£', self::SYMBOL_NATIVE => '£', self::DECIMAL_DIGITS => 2, self::ROUNDING => 0, self::NAME_PLURAL => 'British pounds sterling'],
        self::GEL => [self::COUNTRY_NAME => 'Georgian Lari', self::CODE => self::GEL, self::CURRENCY => 'Georgian Lari', self::SYMBOL => 'GEL', self::SYMBOL_NATIVE => 'GEL', self::DECIMAL_DIGITS => 2, self::ROUNDING => 0, self::NAME_PLURAL => 'Georgian laris'],
        self::GHS => [self::COUNTRY_NAME => 'Ghanaian Cedi', self::CODE => self::GHS, self::CURRENCY => 'Ghanaian Cedi', self::SYMBOL => 'GH₵', self::SYMBOL_NATIVE => 'GH₵', self::DECIMAL_DIGITS => 2, self::ROUNDING => 0, self::NAME_PLURAL => 'Ghanaian cedis'],
        self::GNF => [self::COUNTRY_NAME => 'Guinean Franc', self::CODE => self::GNF, self::CURRENCY => 'Guinean Franc', self::SYMBOL => 'FG', self::SYMBOL_NATIVE => 'FG', self::DECIMAL_DIGITS => 0, self::ROUNDING => 0, self::NAME_PLURAL => 'Guinean francs'],
        self::GTQ => [self::COUNTRY_NAME => 'Guatemalan Quetzal', self::CODE => self::GTQ, self::CURRENCY => 'Guatemalan Quetzal', self::SYMBOL => 'GTQ', self::SYMBOL_NATIVE => 'Q', self::DECIMAL_DIGITS => 2, self::ROUNDING => 0, self::NAME_PLURAL => 'Guatemalan quetzals'],
        self::HKD => [self::COUNTRY_NAME => 'Hong Kong Dollar', self::CODE => self::HKD, self::CURRENCY => 'Hong Kong Dollar', self::SYMBOL => 'HK$', self::SYMBOL_NATIVE => '$', self::DECIMAL_DIGITS => 2, self::ROUNDING => 0, self::NAME_PLURAL => 'Hong Kong dollars'],
        self::HNL => [self::COUNTRY_NAME => 'Honduran Lempira', self::CODE => self::HNL, self::CURRENCY => 'Honduran Lempira', self::SYMBOL => 'HNL', self::SYMBOL_NATIVE => 'L', self::DECIMAL_DIGITS => 2, self::ROUNDING => 0, self::NAME_PLURAL => 'Honduran lempiras'],
        self::HRK => [self::COUNTRY_NAME => 'Croatian Kuna', self::CODE => self::HRK, self::CURRENCY => 'Croatian Kuna', self::SYMBOL => 'kn', self::SYMBOL_NATIVE => 'kn', self::DECIMAL_DIGITS => 2, self::ROUNDING => 0, self::NAME_PLURAL => 'Croatian kunas'],
        self::HUF => [self::COUNTRY_NAME => 'Hungarian Forint', self::CODE => self::HUF, self::CURRENCY => 'Hungarian Forint', self::SYMBOL => 'Ft', self::SYMBOL_NATIVE => 'Ft', self::DECIMAL_DIGITS => 0, self::ROUNDING => 0, self::NAME_PLURAL => 'Hungarian forints'],
        self::IDR => [self::COUNTRY_NAME => 'Indonesian Rupiah', self::CODE => self::IDR, self::CURRENCY => 'Indonesian Rupiah', self::SYMBOL => 'Rp', self::SYMBOL_NATIVE => 'Rp', self::DECIMAL_DIGITS => 0, self::ROUNDING => 0, self::NAME_PLURAL => 'Indonesian rupiahs'],
        self::ILS => [self::COUNTRY_NAME => 'Israeli New Sheqel', self::CODE => self::ILS, self::CURRENCY => 'Israeli New Sheqel', self::SYMBOL => '₪', self::SYMBOL_NATIVE => '₪', self::DECIMAL_DIGITS => 2, self::ROUNDING => 0, self::NAME_PLURAL => 'Israeli new sheqels'],
        self::INR => [self::COUNTRY_NAME => 'Indian Rupee', self::CODE => self::INR, self::CURRENCY => 'Indian Rupee', self::SYMBOL => 'Rs', self::SYMBOL_NATIVE => 'টকা', self::DECIMAL_DIGITS => 2, self::ROUNDING => 0, self::NAME_PLURAL => 'Indian rupees'],
        self::IQD => [self::COUNTRY_NAME => 'Iraqi Dinar', self::CODE => self::IQD, self::CURRENCY => 'Iraqi Dinar', self::SYMBOL => 'IQD', self::SYMBOL_NATIVE => 'د.ع.‏', self::DECIMAL_DIGITS => 0, self::ROUNDING => 0, self::NAME_PLURAL => 'Iraqi dinars'],
        self::IRR => [self::COUNTRY_NAME => 'Iranian Rial', self::CODE => self::IRR, self::CURRENCY => 'Iranian Rial', self::SYMBOL => 'IRR', self::SYMBOL_NATIVE => '﷼', self::DECIMAL_DIGITS => 0, self::ROUNDING => 0, self::NAME_PLURAL => 'Iranian rials'],
        self::ISK => [self::COUNTRY_NAME => 'Icelandic Króna', self::CODE => self::ISK, self::CURRENCY => 'Icelandic Króna', self::SYMBOL => 'Ikr', self::SYMBOL_NATIVE => 'kr', self::DECIMAL_DIGITS => 0, self::ROUNDING => 0, self::NAME_PLURAL => 'Icelandic krónur'],
        self::JMD => [self::COUNTRY_NAME => 'Jamaican Dollar', self::CODE => self::JMD, self::CURRENCY => 'Jamaican Dollar', self::SYMBOL => 'J$', self::SYMBOL_NATIVE => '$', self::DECIMAL_DIGITS => 2, self::ROUNDING => 0, self::NAME_PLURAL => 'Jamaican dollars'],
        self::JOD => [self::COUNTRY_NAME => 'Jordanian Dinar', self::CODE => self::JOD, self::CURRENCY => 'Jordanian Dinar', self::SYMBOL => 'JD', self::SYMBOL_NATIVE => 'د.أ.‏', self::DECIMAL_DIGITS => 3, self::ROUNDING => 0, self::NAME_PLURAL => 'Jordanian dinars'],
        self::JPY => [self::COUNTRY_NAME => 'Japanese Yen', self::CODE => self::JPY, self::CURRENCY => 'Japanese Yen', self::SYMBOL => '¥', self::SYMBOL_NATIVE => '￥', self::DECIMAL_DIGITS => 0, self::ROUNDING => 0, self::NAME_PLURAL => 'Japanese yen'],
        self::KES => [self::COUNTRY_NAME => 'Kenyan Shilling', self::CODE => self::KES, self::CURRENCY => 'Kenyan Shilling', self::SYMBOL => 'Ksh', self::SYMBOL_NATIVE => 'Ksh', self::DECIMAL_DIGITS => 2, self::ROUNDING => 0, self::NAME_PLURAL => 'Kenyan shillings'],
        self::KHR => [self::COUNTRY_NAME => 'Cambodian Riel', self::CODE => self::KHR, self::CURRENCY => 'Cambodian Riel', self::SYMBOL => 'KHR', self::SYMBOL_NATIVE => '៛', self::DECIMAL_DIGITS => 2, self::ROUNDING => 0, self::NAME_PLURAL => 'Cambodian riels'],
        self::KMF => [self::COUNTRY_NAME => 'Comorian Franc', self::CODE => self::KMF, self::CURRENCY => 'Comorian Franc', self::SYMBOL => 'CF', self::SYMBOL_NATIVE => 'FC', self::DECIMAL_DIGITS => 0, self::ROUNDING => 0, self::NAME_PLURAL => 'Comorian francs'],
        self::KRW => [self::COUNTRY_NAME => 'South Korean Won', self::CODE => self::KRW, self::CURRENCY => 'South Korean Won', self::SYMBOL => '₩', self::SYMBOL_NATIVE => '₩', self::DECIMAL_DIGITS => 0, self::ROUNDING => 0, self::NAME_PLURAL => 'South Korean won'],
        self::KWD => [self::COUNTRY_NAME => 'Kuwaiti Dinar', self::CODE => self::KWD, self::CURRENCY => 'Kuwaiti Dinar', self::SYMBOL => 'KD', self::SYMBOL_NATIVE => 'د.ك.‏', self::DECIMAL_DIGITS => 3, self::ROUNDING => 0, self::NAME_PLURAL => 'Kuwaiti dinars'],
        self::KZT => [self::COUNTRY_NAME => 'Kazakhstani Tenge', self::CODE => self::KZT, self::CURRENCY => 'Kazakhstani Tenge', self::SYMBOL => 'KZT', self::SYMBOL_NATIVE => 'тңг.', self::DECIMAL_DIGITS => 2, self::ROUNDING => 0, self::NAME_PLURAL => 'Kazakhstani tenges'],
        self::LBP => [self::COUNTRY_NAME => 'Lebanese Pound', self::CODE => self::LBP, self::CURRENCY => 'Lebanese Pound', self::SYMBOL => 'L.L.', self::SYMBOL_NATIVE => 'ل.ل.‏', self::DECIMAL_DIGITS => 0, self::ROUNDING => 0, self::NAME_PLURAL => 'Lebanese pounds'],
        self::LKR => [self::COUNTRY_NAME => 'Sri Lankan Rupee', self::CODE => self::LKR, self::CURRENCY => 'Sri Lankan Rupee', self::SYMBOL => 'SLRs', self::SYMBOL_NATIVE => 'SL Re', self::DECIMAL_DIGITS => 2, self::ROUNDING => 0, self::NAME_PLURAL => 'Sri Lankan rupees'],
        self::LTL => [self::COUNTRY_NAME => 'Lithuanian Litas', self::CODE => self::LTL, self::CURRENCY => 'Lithuanian Litas', self::SYMBOL => 'Lt', self::SYMBOL_NATIVE => 'Lt', self::DECIMAL_DIGITS => 2, self::ROUNDING => 0, self::NAME_PLURAL => 'Lithuanian litai'],
        self::LVL => [self::COUNTRY_NAME => 'Latvian Lats', self::CODE => self::LVL, self::CURRENCY => 'Latvian Lats', self::SYMBOL => 'Ls', self::SYMBOL_NATIVE => 'Ls', self::DECIMAL_DIGITS => 2, self::ROUNDING => 0, self::NAME_PLURAL => 'Latvian lati'],
        self::LYD => [self::COUNTRY_NAME => 'Libyan Dinar', self::CODE => self::LYD, self::CURRENCY => 'Libyan Dinar', self::SYMBOL => 'LD', self::SYMBOL_NATIVE => 'د.ل.‏', self::DECIMAL_DIGITS => 3, self::ROUNDING => 0, self::NAME_PLURAL => 'Libyan dinars'],
        self::MAD => [self::COUNTRY_NAME => 'Moroccan Dirham', self::CODE => self::MAD, self::CURRENCY => 'Moroccan Dirham', self::SYMBOL => 'MAD', self::SYMBOL_NATIVE => 'د.م.‏', self::DECIMAL_DIGITS => 2, self::ROUNDING => 0, self::NAME_PLURAL => 'Moroccan dirhams'],
        self::MDL => [self::COUNTRY_NAME => 'Moldovan Leu', self::CODE => self::MDL, self::CURRENCY => 'Moldovan Leu', self::SYMBOL => 'MDL', self::SYMBOL_NATIVE => 'MDL', self::DECIMAL_DIGITS => 2, self::ROUNDING => 0, self::NAME_PLURAL => 'Moldovan lei'],
        self::MGA => [self::COUNTRY_NAME => 'Malagasy Ariary', self::CODE => self::MGA, self::CURRENCY => 'Malagasy Ariary', self::SYMBOL => 'MGA', self::SYMBOL_NATIVE => 'MGA', self::DECIMAL_DIGITS => 0, self::ROUNDING => 0, self::NAME_PLURAL => 'Malagasy Ariaries'],
        self::MKD => [self::COUNTRY_NAME => 'Macedonian Denar', self::CODE => self::MKD, self::CURRENCY => 'Macedonian Denar', self::SYMBOL => 'MKD', self::SYMBOL_NATIVE => 'MKD', self::DECIMAL_DIGITS => 2, self::ROUNDING => 0, self::NAME_PLURAL => 'Macedonian denari'],
        self::MMK => [self::COUNTRY_NAME => 'Myanma Kyat', self::CODE => self::MMK, self::CURRENCY => 'Myanma Kyat', self::SYMBOL => 'MMK', self::SYMBOL_NATIVE => 'K', self::DECIMAL_DIGITS => 0, self::ROUNDING => 0, self::NAME_PLURAL => 'Myanma kyats'],
        self::MOP => [self::COUNTRY_NAME => 'Macanese Pataca', self::CODE => self::MOP, self::CURRENCY => 'Macanese Pataca', self::SYMBOL => 'MOP$', self::SYMBOL_NATIVE => 'MOP$', self::DECIMAL_DIGITS => 2, self::ROUNDING => 0, self::NAME_PLURAL => 'Macanese patacas'],
        self::MUR => [self::COUNTRY_NAME => 'Mauritian Rupee', self::CODE => self::MUR, self::CURRENCY => 'Mauritian Rupee', self::SYMBOL => 'MURs', self::SYMBOL_NATIVE => 'MURs', self::DECIMAL_DIGITS => 0, self::ROUNDING => 0, self::NAME_PLURAL => 'Mauritian rupees'],
        self::MXN => [self::COUNTRY_NAME => 'Mexican Peso', self::CODE => self::MXN, self::CURRENCY => 'Mexican Peso', self::SYMBOL => 'MX$', self::SYMBOL_NATIVE => '$', self::DECIMAL_DIGITS => 2, self::ROUNDING => 0, self::NAME_PLURAL => 'Mexican pesos'],
        self::MYR => [self::COUNTRY_NAME => 'Malaysian Ringgit', self::CODE => self::MYR, self::CURRENCY => 'Malaysian Ringgit', self::SYMBOL => 'RM', self::SYMBOL_NATIVE => 'RM', self::DECIMAL_DIGITS => 2, self::ROUNDING => 0, self::NAME_PLURAL => 'Malaysian ringgits'],
        self::MZN => [self::COUNTRY_NAME => 'Mozambican Metical', self::CODE => self::MZN, self::CURRENCY => 'Mozambican Metical', self::SYMBOL => 'MTn', self::SYMBOL_NATIVE => 'MTn', self::DECIMAL_DIGITS => 2, self::ROUNDING => 0, self::NAME_PLURAL => 'Mozambican meticals'],
        self::NAD => [self::COUNTRY_NAME => 'Namibian Dollar', self::CODE => self::NAD, self::CURRENCY => 'Namibian Dollar', self::SYMBOL => 'N$', self::SYMBOL_NATIVE => 'N$', self::DECIMAL_DIGITS => 2, self::ROUNDING => 0, self::NAME_PLURAL => 'Namibian dollars'],
        self::NGN => [self::COUNTRY_NAME => 'Nigerian Naira', self::CODE => self::NGN, self::CURRENCY => 'Nigerian Naira', self::SYMBOL => '₦', self::SYMBOL_NATIVE => '₦', self::DECIMAL_DIGITS => 2, self::ROUNDING => 0, self::NAME_PLURAL => 'Nigerian nairas'],
        self::NIO => [self::COUNTRY_NAME => 'Nicaraguan Córdoba', self::CODE => self::NIO, self::CURRENCY => 'Nicaraguan Córdoba', self::SYMBOL => 'C$', self::SYMBOL_NATIVE => 'C$', self::DECIMAL_DIGITS => 2, self::ROUNDING => 0, self::NAME_PLURAL => 'Nicaraguan córdobas'],
        self::NOK => [self::COUNTRY_NAME => 'Norwegian Krone', self::CODE => self::NOK, self::CURRENCY => 'Norwegian Krone', self::SYMBOL => 'Nkr', self::SYMBOL_NATIVE => 'kr', self::DECIMAL_DIGITS => 2, self::ROUNDING => 0, self::NAME_PLURAL => 'Norwegian kroner'],
        self::NPR => [self::COUNTRY_NAME => 'Nepalese Rupee', self::CODE => self::NPR, self::CURRENCY => 'Nepalese Rupee', self::SYMBOL => 'NPRs', self::SYMBOL_NATIVE => 'नेरू', self::DECIMAL_DIGITS => 2, self::ROUNDING => 0, self::NAME_PLURAL => 'Nepalese rupees'],
        self::NZD => [self::COUNTRY_NAME => 'New Zealand Dollar', self::CODE => self::NZD, self::CURRENCY => 'New Zealand Dollar', self::SYMBOL => 'NZ$', self::SYMBOL_NATIVE => '$', self::DECIMAL_DIGITS => 2, self::ROUNDING => 0, self::NAME_PLURAL => 'New Zealand dollars'],
        self::OMR => [self::COUNTRY_NAME => 'Omani Rial', self::CODE => self::OMR, self::CURRENCY => 'Omani Rial', self::SYMBOL => 'OMR', self::SYMBOL_NATIVE => 'ر.ع.‏', self::DECIMAL_DIGITS => 3, self::ROUNDING => 0, self::NAME_PLURAL => 'Omani rials'],
        self::PAB => [self::COUNTRY_NAME => 'Panamanian Balboa', self::CODE => self::PAB, self::CURRENCY => 'Panamanian Balboa', self::SYMBOL => 'B/.', self::SYMBOL_NATIVE => 'B/.', self::DECIMAL_DIGITS => 2, self::ROUNDING => 0, self::NAME_PLURAL => 'Panamanian balboas'],
        self::PEN => [self::COUNTRY_NAME => 'Peruvian Nuevo Sol', self::CODE => self::PEN, self::CURRENCY => 'Peruvian Nuevo Sol', self::SYMBOL => 'S/.', self::SYMBOL_NATIVE => 'S/.', self::DECIMAL_DIGITS => 2, self::ROUNDING => 0, self::NAME_PLURAL => 'Peruvian nuevos soles'],
        self::PHP => [self::COUNTRY_NAME => 'Philippine Peso', self::CODE => self::PHP, self::CURRENCY => 'Philippine Peso', self::SYMBOL => '₱', self::SYMBOL_NATIVE => '₱', self::DECIMAL_DIGITS => 2, self::ROUNDING => 0, self::NAME_PLURAL => 'Philippine pesos'],
        self::PKR => [self::COUNTRY_NAME => 'Pakistani Rupee', self::CODE => self::PKR, self::CURRENCY => 'Pakistani Rupee', self::SYMBOL => 'PKRs', self::SYMBOL_NATIVE => '₨', self::DECIMAL_DIGITS => 0, self::ROUNDING => 0, self::NAME_PLURAL => 'Pakistani rupees'],
        self::PLN => [self::COUNTRY_NAME => 'Polish Zloty', self::CODE => self::PLN, self::CURRENCY => 'Polish Zloty', self::SYMBOL => 'zł', self::SYMBOL_NATIVE => 'zł', self::DECIMAL_DIGITS => 2, self::ROUNDING => 0, self::NAME_PLURAL => 'Polish zlotys'],
        self::PYG => [self::COUNTRY_NAME => 'Paraguayan Guarani', self::CODE => self::PYG, self::CURRENCY => 'Paraguayan Guarani', self::SYMBOL => '₲', self::SYMBOL_NATIVE => '₲', self::DECIMAL_DIGITS => 0, self::ROUNDING => 0, self::NAME_PLURAL => 'Paraguayan guaranis'],
        self::QAR => [self::COUNTRY_NAME => 'Qatari Rial', self::CODE => self::QAR, self::CURRENCY => 'Qatari Rial', self::SYMBOL => 'QR', self::SYMBOL_NATIVE => 'ر.ق.‏', self::DECIMAL_DIGITS => 2, self::ROUNDING => 0, self::NAME_PLURAL => 'Qatari rials'],
        self::RON => [self::COUNTRY_NAME => 'Romanian Leu', self::CODE => self::RON, self::CURRENCY => 'Romanian Leu', self::SYMBOL => 'RON', self::SYMBOL_NATIVE => 'RON', self::DECIMAL_DIGITS => 2, self::ROUNDING => 0, self::NAME_PLURAL => 'Romanian lei'],
        self::RSD => [self::COUNTRY_NAME => 'Serbian Dinar', self::CODE => self::RSD, self::CURRENCY => 'Serbian Dinar', self::SYMBOL => 'din.', self::SYMBOL_NATIVE => 'дин.', self::DECIMAL_DIGITS => 0, self::ROUNDING => 0, self::NAME_PLURAL => 'Serbian dinars'],
        self::RUB => [self::COUNTRY_NAME => 'Russian Ruble', self::CODE => self::RUB, self::CURRENCY => 'Russian Ruble', self::SYMBOL => 'RUB', self::SYMBOL_NATIVE => '₽.', self::DECIMAL_DIGITS => 2, self::ROUNDING => 0, self::NAME_PLURAL => 'Russian rubles'],
        self::RWF => [self::COUNTRY_NAME => 'Rwandan Franc', self::CODE => self::RWF, self::CURRENCY => 'Rwandan Franc', self::SYMBOL => 'RWF', self::SYMBOL_NATIVE => 'FR', self::DECIMAL_DIGITS => 0, self::ROUNDING => 0, self::NAME_PLURAL => 'Rwandan francs'],
        self::SAR => [self::COUNTRY_NAME => 'Saudi Riyal', self::CODE => self::SAR, self::CURRENCY => 'Saudi Riyal', self::SYMBOL => 'SR', self::SYMBOL_NATIVE => 'ر.س.‏', self::DECIMAL_DIGITS => 2, self::ROUNDING => 0, self::NAME_PLURAL => 'Saudi riyals'],
        self::SDG => [self::COUNTRY_NAME => 'Sudanese Pound', self::CODE => self::SDG, self::CURRENCY => 'Sudanese Pound', self::SYMBOL => 'SDG', self::SYMBOL_NATIVE => 'SDG', self::DECIMAL_DIGITS => 2, self::ROUNDING => 0, self::NAME_PLURAL => 'Sudanese pounds'],
        self::SEK => [self::COUNTRY_NAME => 'Swedish Krona', self::CODE => self::SEK, self::CURRENCY => 'Swedish Krona', self::SYMBOL => 'Skr', self::SYMBOL_NATIVE => 'kr', self::DECIMAL_DIGITS => 2, self::ROUNDING => 0, self::NAME_PLURAL => 'Swedish kronor'],
        self::SGD => [self::COUNTRY_NAME => 'Singapore Dollar', self::CODE => self::SGD, self::CURRENCY => 'Singapore Dollar', self::SYMBOL => 'S$', self::SYMBOL_NATIVE => '$', self::DECIMAL_DIGITS => 2, self::ROUNDING => 0, self::NAME_PLURAL => 'Singapore dollars'],
        self::SOS => [self::COUNTRY_NAME => 'Somali Shilling', self::CODE => self::SOS, self::CURRENCY => 'Somali Shilling', self::SYMBOL => 'Ssh', self::SYMBOL_NATIVE => 'Ssh', self::DECIMAL_DIGITS => 0, self::ROUNDING => 0, self::NAME_PLURAL => 'Somali shillings'],
        self::SYP => [self::COUNTRY_NAME => 'Syrian Pound', self::CODE => self::SYP, self::CURRENCY => 'Syrian Pound', self::SYMBOL => 'SY£', self::SYMBOL_NATIVE => 'ل.س.‏', self::DECIMAL_DIGITS => 0, self::ROUNDING => 0, self::NAME_PLURAL => 'Syrian pounds'],
        self::THB => [self::COUNTRY_NAME => 'Thai Baht', self::CODE => self::THB, self::CURRENCY => 'Thai Baht', self::SYMBOL => '฿', self::SYMBOL_NATIVE => '฿', self::DECIMAL_DIGITS => 2, self::ROUNDING => 0, self::NAME_PLURAL => 'Thai baht'],
        self::TND => [self::COUNTRY_NAME => 'Tunisian Dinar', self::CODE => self::TND, self::CURRENCY => 'Tunisian Dinar', self::SYMBOL => 'DT', self::SYMBOL_NATIVE => 'د.ت.‏', self::DECIMAL_DIGITS => 3, self::ROUNDING => 0, self::NAME_PLURAL => 'Tunisian dinars'],
        self::TOP => [self::COUNTRY_NAME => 'Tongan Paʻanga', self::CODE => self::TOP, self::CURRENCY => 'Tongan Paʻanga', self::SYMBOL => 'T$', self::SYMBOL_NATIVE => 'T$', self::DECIMAL_DIGITS => 2, self::ROUNDING => 0, self::NAME_PLURAL => 'Tongan paʻanga'],
        self::TRY => [self::COUNTRY_NAME => 'Turkish Lira', self::CODE => self::TRY, self::CURRENCY => 'Turkish Lira', self::SYMBOL => 'TL', self::SYMBOL_NATIVE => 'TL', self::DECIMAL_DIGITS => 2, self::ROUNDING => 0, self::NAME_PLURAL => 'Turkish Lira'],
        self::TTD => [self::COUNTRY_NAME => 'Trinidad and Tobago Dollar', self::CODE => self::TTD, self::CURRENCY => 'Trinidad and Tobago Dollar', self::SYMBOL => 'TT$', self::SYMBOL_NATIVE => '$', self::DECIMAL_DIGITS => 2, self::ROUNDING => 0, self::NAME_PLURAL => 'Trinidad and Tobago dollars'],
        self::TWD => [self::COUNTRY_NAME => 'New Taiwan Dollar', self::CODE => self::TWD, self::CURRENCY => 'New Taiwan Dollar', self::SYMBOL => 'NT$', self::SYMBOL_NATIVE => 'NT$', self::DECIMAL_DIGITS => 2, self::ROUNDING => 0, self::NAME_PLURAL => 'New Taiwan dollars'],
        self::TZS => [self::COUNTRY_NAME => 'Tanzanian Shilling', self::CODE => self::TZS, self::CURRENCY => 'Tanzanian Shilling', self::SYMBOL => 'TSh', self::SYMBOL_NATIVE => 'TSh', self::DECIMAL_DIGITS => 0, self::ROUNDING => 0, self::NAME_PLURAL => 'Tanzanian shillings'],
        self::UAH => [self::COUNTRY_NAME => 'Ukrainian Hryvnia', self::CODE => self::UAH, self::CURRENCY => 'Ukrainian Hryvnia', self::SYMBOL => '₴', self::SYMBOL_NATIVE => '₴', self::DECIMAL_DIGITS => 2, self::ROUNDING => 0, self::NAME_PLURAL => 'Ukrainian hryvnias'],
        self::UGX => [self::COUNTRY_NAME => 'Ugandan Shilling', self::CODE => self::UGX, self::CURRENCY => 'Ugandan Shilling', self::SYMBOL => 'USh', self::SYMBOL_NATIVE => 'USh', self::DECIMAL_DIGITS => 0, self::ROUNDING => 0, self::NAME_PLURAL => 'Ugandan shillings'],
        self::UYU => [self::COUNTRY_NAME => 'Uruguayan Peso', self::CODE => self::UYU, self::CURRENCY => 'Uruguayan Peso', self::SYMBOL => '$U', self::SYMBOL_NATIVE => '$', self::DECIMAL_DIGITS => 2, self::ROUNDING => 0, self::NAME_PLURAL => 'Uruguayan pesos'],
        self::UZS => [self::COUNTRY_NAME => 'Uzbekistan Som', self::CODE => self::UZS, self::CURRENCY => 'Uzbekistan Som', self::SYMBOL => 'UZS', self::SYMBOL_NATIVE => 'UZS', self::DECIMAL_DIGITS => 0, self::ROUNDING => 0, self::NAME_PLURAL => 'Uzbekistan som'],
        self::VEF => [self::COUNTRY_NAME => 'Venezuelan Bolívar', self::CODE => self::VEF, self::CURRENCY => 'Venezuelan Bolívar', self::SYMBOL => 'Bs.F.', self::SYMBOL_NATIVE => 'Bs.F.', self::DECIMAL_DIGITS => 2, self::ROUNDING => 0, self::NAME_PLURAL => 'Venezuelan bolívars'],
        self::VND => [self::COUNTRY_NAME => 'Vietnamese Dong', self::CODE => self::VND, self::CURRENCY => 'Vietnamese Dong', self::SYMBOL => '₫', self::SYMBOL_NATIVE => '₫', self::DECIMAL_DIGITS => 0, self::ROUNDING => 0, self::NAME_PLURAL => 'Vietnamese dong'],
        self::XAF => [self::COUNTRY_NAME => 'CFA Franc BEAC', self::CODE => self::XAF, self::CURRENCY => 'CFA Franc BEAC', self::SYMBOL => 'FCFA', self::SYMBOL_NATIVE => 'FCFA', self::DECIMAL_DIGITS => 0, self::ROUNDING => 0, self::NAME_PLURAL => 'CFA francs BEAC'],
        self::XOF => [self::COUNTRY_NAME => 'CFA Franc BCEAO', self::CODE => self::XOF, self::CURRENCY => 'CFA Franc BCEAO', self::SYMBOL => 'CFA', self::SYMBOL_NATIVE => 'CFA', self::DECIMAL_DIGITS => 0, self::ROUNDING => 0, self::NAME_PLURAL => 'CFA francs BCEAO'],
        self::YER => [self::COUNTRY_NAME => 'Yemeni Rial', self::CODE => self::YER, self::CURRENCY => 'Yemeni Rial', self::SYMBOL => 'YR', self::SYMBOL_NATIVE => 'ر.ي.‏', self::DECIMAL_DIGITS => 0, self::ROUNDING => 0, self::NAME_PLURAL => 'Yemeni rials'],
        self::ZAR => [self::COUNTRY_NAME => 'South African Rand', self::CODE => self::ZAR, self::CURRENCY => 'South African Rand', self::SYMBOL => 'R', self::SYMBOL_NATIVE => 'R', self::DECIMAL_DIGITS => 2, self::ROUNDING => 0, self::NAME_PLURAL => 'South African rand'],
        self::ZMK => [self::COUNTRY_NAME => 'Zambian Kwacha', self::CODE => self::ZMK, self::CURRENCY => 'Zambian Kwacha', self::SYMBOL => 'ZK', self::SYMBOL_NATIVE => 'ZK', self::DECIMAL_DIGITS => 0, self::ROUNDING => 0, self::NAME_PLURAL => 'Zambian kwachas'],
        self::ZWL => [self::COUNTRY_NAME => 'Zimbabwean Dollar', self::CODE => self::ZWL, self::CURRENCY => 'Zimbabwean Dollar', self::SYMBOL => 'ZWL$', self::SYMBOL_NATIVE => 'ZWL$', self::DECIMAL_DIGITS => 0, self::ROUNDING => 0, self::NAME_PLURAL => 'Zimbabwean Dollar']
    ];

    private static string $code;

    /**
     * @param string $code
     * @return array
     */
    public static function currency(string $code = 'USD'): array
    {
        self::$code = $code;
        return self::$currencies[self::$code];
    }
}
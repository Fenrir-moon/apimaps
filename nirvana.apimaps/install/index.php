<?php
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\Config\Option;
use Bitrix\Main\EventManager;
use Bitrix\Main\Application;
use Bitrix\Main\IO\Directory;

Loc::loadMessages(__FILE__);

class nirvana_apimaps extends CModule{
    public function __construct(){

        if(file_exists(__DIR__."/version.php")){

            $arModuleVersion = array();

            include_once(__DIR__."/version.php");

            $this->MODULE_ID            = str_replace("_", ".", get_class($this));
            $this->MODULE_VERSION       = $arModuleVersion["VERSION"];
            $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
            $this->MODULE_NAME          = Loc::getMessage("NIR_APIMAPS_NAME");
            $this->MODULE_DESCRIPTION  = Loc::getMessage("NIR_APIMAPS_DESCRIPTION");
            $this->PARTNER_NAME     = Loc::getMessage("NIR");
            $this->PARTNER_URI      = Loc::getMessage("NIR_URI");
        }

        return false;
    }
    public function DoInstall(){

        global $APPLICATION;

        if(CheckVersion(ModuleManager::getVersion("main"), "14.00.00")){

            $this->InstallFiles();
            $this->InstallDB();

            ModuleManager::registerModule($this->MODULE_ID);

            $this->InstallEvents();
        }else{

            $APPLICATION->ThrowException(
                Loc::getMessage("NIR_APIMAPS_ERROR_VERSION")
            );
        }

        $APPLICATION->IncludeAdminFile(
            Loc::getMessage("NIR_APIMAPS_INSTALL_TITLE")." \"".Loc::getMessage("NIR_APIMAPS_NAME")."\"",
            __DIR__."/step.php"
        );

        return false;
    }
    public function InstallFiles(){
        CopyDirFiles(
            __DIR__."/components",
            Application::getDocumentRoot()."/local/components", true, true);

        return false;
    }
    public function InstallDB(){

        if(CModule::IncludeModule('iblock'))
        {
            COption::SetOptionString("nirvana_maps", "nirvana_maps", "nir_maps");

            $arFields = array(
                'ID' => 'nir_maps',
                'SECTIONS' => 'Y',
                'SORT' => 500,
                "LANG"=>Array(
                    "ru"=>Array(
                        "NAME"=>"Карты",
                    )
                )
            );

            $dbRes = CIBlockType::GetByID($arFields['ID']);
            if(!$dbRes->Fetch())
            {
                $obBlocktype = new CIBlockType;
              $IB_ID = $obBlocktype->Add($arFields);
            }
            if ($IB_ID){
                $obIblock = new CIBlock;
                $arFields = Array(
                    "NAME"=> "API карты",
                    "ACTIVE" => "Y",
                    "CODE" => "if_api_maps",
                    "IBLOCK_TYPE_ID" => 'nir_maps',
                    "API_CODE" => "apimaps",
                    "SITE_ID" => Array("s1"),
                    "VERSION" => 2
                );
                $newIblockID = $obIblock->Add($arFields);
                $propIds = [];
                $dbProperties = CIBlockProperty::GetList(array(), array("IBLOCK_ID"=>$newIblockID));
                if ($dbProperties->SelectedRowsCount() <= 0){
                    $prop = [
                        [
                            'NAME'          => 'Название офиса',
                            'ACTIVE'        => 'Y',
                            'SORT'          => 100,
                            'CODE'          => 'OFFICE',
                            'PROPERTY_TYPE' => 'S',
                            'IBLOCK_ID'     => $newIblockID,
                        ],
                        [
                            'NAME'          => 'Телефон',
                            'ACTIVE'        => 'Y',
                            'SORT'          => 200,
                            'CODE'          => 'PHONE',
                            'PROPERTY_TYPE' => 'S',
                            'IBLOCK_ID'     => $newIblockID,
                        ],
                        [
                            'NAME'          => 'Почта',
                            'ACTIVE'        => 'Y',
                            'SORT'          => 300,
                            'CODE'          => 'EMAIL',
                            'PROPERTY_TYPE' => 'S',
                            'IBLOCK_ID'     => $newIblockID,
                        ],
                        [
                            'NAME'          => 'Координаты',
                            'ACTIVE'        => 'Y',
                            'SORT'          => 400,
                            'CODE'          => 'COORDINATES',
                            'PROPERTY_TYPE' => 'S',
                            'IBLOCK_ID'     => $newIblockID,
                        ],
                        [
                            'NAME'          => 'Город',
                            'ACTIVE'        => 'Y',
                            'SORT'          => 500,
                            'CODE'          => 'CITY',
                            'PROPERTY_TYPE' => 'S',
                            'IBLOCK_ID'     => $newIblockID,
                        ],
                    ];
                    $ibp = new CIBlockProperty;
                    foreach ($prop as $item){
                       $propIds[] = $ibp->Add($item);
                    }

                    $arrData = [
                        [
                            'office' => 'Газпромнефть-Цифровые решения',
                            'phone' => '+7 (812) 448-24-01',
                            'email' => 'ds-info@gazprom-neft.ru',
                            'coordinates' => '59.90176556423369,30.323835999999872',
                            'city' => 'Санкт-Петербург'
                        ],
                        [
                            'office' => 'Газпромнефть-Цифровые решения',
                            'phone' => '+7 (495) 514-03-68',
                            'email' => 'ds-info@gazprom-neft.ru',
                            'coordinates' => '55.65414206907299,37.55588499999994',
                            'city' => 'Москва'
                        ],
                        [
                            'office' => 'Газпромнефть-Цифровые решения',
                            'phone' => '+7 (812) 448-24-01',
                            'email' => 'ds-info@gazprom-neft.ru',
                            'coordinates' => '55.684757,37.339346',
                            'city' => 'Москва',
                        ],
                        [
                            'office' => 'Газпромнефть-Цифровые решения',
                            'phone' => '+7 (812) 448-24-01',
                            'email' => 'ds-info@gazprom-neft.ru',
                            'coordinates' => '59.901766,30.323836',
                            'city' => 'Санкт-Петербург',
                        ],
                        [
                            'office' => 'Газпромнефть-Цифровые решения',
                            'phone' => '+7 (812) 448-24-01',
                            'email' => 'ds-info@gazprom-neft.ru',
                            'coordinates' => '59.884537,30.311592',
                            'city' => 'Санкт-Петербург',
                        ],
                        [
                            'office' => 'Газпромнефть-Цифровые решения',
                            'phone' => '+7 (812) 448-24-01',
                            'email' => 'ds-info@gazprom-neft.ru',
                            'coordinates' => '59.932703,30.298180',
                            'city' => 'Санкт-Петербург',
                        ],
                        [
                            'office' => 'Газпромнефть-Цифровые решения',
                            'phone' => '+7 (812) 448-24-01',
                            'email' => 'ds-info@gazprom-neft.ru',
                            'coordinates' => '54.994455,73.362517',
                            'city' => 'Омск',
                        ],
                        [
                            'office' => 'Газпромнефть-Цифровые решения',
                            'phone' => '+7 (812) 448-24-01',
                            'email' => 'ds-info@gazprom-neft.ru',
                            'coordinates' => '56.448668,84.995960',
                            'city' => 'Томск',
                        ],
                        [
                            'office' => 'Газпромнефть-Цифровые решения',
                            'phone' => '+7 (812) 448-24-01',
                            'email' => 'ds-info@gazprom-neft.ru',
                            'coordinates' => '55.024585,82.923583',
                            'city' => 'Новосибирск',
                        ],
                        [
                            'office' => 'Газпромнефть-Цифровые решения',
                            'phone' => '+7 (812) 448-24-01',
                            'email' => 'ds-info@gazprom-neft.ru',
                            'coordinates' => '57.152521,65.555663',
                            'city' => 'Тюмень',
                        ],
                        [
                            'office' => 'Газпромнефть-Цифровые решения',
                            'phone' => '+7 (812) 448-24-01',
                            'email' => 'ds-info@gazprom-neft.ru',
                            'coordinates' => '56.309943,43.993212',
                            'city' => 'Нижний Новгород',
                        ]
                        ];

                    $el = new CIBlockElement;

                    foreach ($arrData as $datum){
                        $PROP[$propIds[0]] = $datum['office'];
                        $PROP[$propIds[1]] = $datum['phone'];
                        $PROP[$propIds[2]] = $datum['email'];
                        $PROP[$propIds[3]] = $datum['coordinates'];
                        $PROP[$propIds[4]] = $datum['city'];

                        $arLoadProductArray = Array(
                            "IBLOCK_SECTION_ID" => false,
                            "IBLOCK_ID"      => $newIblockID,
                            "PROPERTY_VALUES"=> $PROP,
                            "NAME"           => $datum['office'] . ' ' . $datum['city'],
                            "ACTIVE"         => "Y",
                        );
                        $el->Add($arLoadProductArray);
                    }
                }
            }
        }

        return false;
    }
    public function InstallEvents(){
        return false;
    }
    public function DoUninstall(){

        global $APPLICATION;

        $this->UnInstallFiles();
        $this->UnInstallDB();
        $this->UnInstallEvents();

        ModuleManager::unRegisterModule($this->MODULE_ID);

        $APPLICATION->IncludeAdminFile(
            Loc::getMessage("NIR_APIMAPS_UNINSTALL_TITLE")." \"".Loc::getMessage("NIR_APIMAPS_NAME")."\"",
            __DIR__."/unstep.php"
        );

        return false;
    }
    public function UnInstallFiles(){

        DeleteDirFilesEx(Application::getDocumentRoot()."/local/components/nirvana");
        return false;
    }
    public function UnInstallDB(){

        Option::delete($this->MODULE_ID);

        return false;
    }
    public function UnInstallEvents(){

        return false;
    }
}
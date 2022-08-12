<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");?>
<?

//echo '<pre>'; print_r(); echo '</pre>';
define('LOG_FILENAME', 'E:\openserver\domains\mysite\zadanie-stazhirovka\log.txt');


//Обработчик изменения записей. 
AddEventHandler("iblock", "OnAfterIBlockElementUpdate", Array("UpdateHandler", "OnAfterIBlockElementUpdateHandler"));
class UpdateHandler
{
    public $UpdateElement;

    public function OnAfterIBlockElementUpdateHandler(&$arFields)
    {
        if($arFields["RESULT"] && $arFields["IBLOCK_ID"]!=4)
        {   
            AddMessage2Log('%'.$arFields['ID'].'% |'.$arFields['IBLOCK_ID'].'|');
            //echo 'Обработчик изменения записей работает <br>';
        }
        else
        {
            AddMessage2Log("Ошибка изменения записи ".$arFields["ID"]." (".$arFields["RESULT_MESSAGE"].").");
        }
    } 
}
//----------------------------------------


$PRODUCT_ID = 380; 

//Поиск ID инфоблока изменяемого элемента
$res = CIBlockElement::GetByID($PRODUCT_ID);
if($ar_res = $res->GetNext())
{
  $IBLOCK_SECTION = $ar_res['IBLOCK_SECTION_ID'];
}
//--------------------------------------


//Изменение элемента инфоблока
$el = new CIBlockElement;
$arLoadProductArray = Array(
  "MODIFIED_BY"    => $USER->GetID(), 
  "IBLOCK_SECTION" => $IBLOCK_SECTION,         
  "PROPERTY_VALUES"=> $PROP,
  "NAME"           => "El2",
  "ACTIVE"         => "Y",            
  "PREVIEW_TEXT"   => "текст для списка элементов",
  "DETAIL_TEXT"    => "текст для детального просмотра",
  "DETAIL_PICTURE" => CFile::MakeFileArray($_SERVER["DOCUMENT_ROOT"]."/image.gif")
  );
$PRODUCT_ID = 379;  
$res = $el->Update($PRODUCT_ID, $arLoadProductArray); 
//-----------------------------


$findme = '%';
//Достаём ID элемента из файла логов
$fd = fopen("log.txt", 'r') or die("не удалось открыть файл");
$TextLog = file_get_contents('log.txt');
$pos = strpos($TextLog, $findme);
$pos2 = strpos($TextLog, $findme, $pos+1);
$pos = (int) $pos;
$pos2 = (int) $pos2;
$SizeId = $pos2 - $pos;
$TestLog = file_get_contents('log.txt', FALSE, NULL, $pos, $SizeId);
$ElementId = str_replace($findme, ' ', $TestLog);
fclose($fd);
//echo $ElementId.' - Если здесь корректный результат, ID элемента найден <br>';
//-----------------------------

$findme = ':';
//Достаём дату изменения элемента из файла логов
$fd = fopen("log.txt", 'r') or die("не удалось открыть файл");
$TextLog = file_get_contents('log.txt');
$pos = strpos($TextLog, $findme, 5);
$pos = (int) $pos;
$SizeId = 20;
$ElementData = file_get_contents('log.txt', FALSE, NULL, $pos+1, $SizeId);
fclose($fd);
//echo $ElementId.' - Если здесь корректный результат, дата изменения элемента найдена <br>';


$findme = '|';
//Достаём ID инфоблока из файла логов
$fd = fopen("log.txt", 'r') or die("не удалось открыть файл");
$TextLog = file_get_contents('log.txt');
$pos = strpos($TextLog, $findme);
$pos2 = strpos($TextLog, $findme, $pos+1);
$pos = (int) $pos;
$pos2 = (int) $pos2;
$SizeId = $pos2 - $pos;
$TestLog = file_get_contents('log.txt', FALSE, NULL, $pos, $SizeId);
$IBlockId = str_replace($findme, ' ', $TestLog);
file_put_contents('log.txt', null);
fclose($fd);
//echo $IBlockId.' - Если здесь корректный результат, ID инфоблока найден <br>';
//-----------------------------


//Узнаём имя инфоблока по ID
if(CModule::IncludeModule("iblock"))
{ 
 $iblocks = GetIBlockList(''); 
 while($arIBlock = $iblocks->GetNext()) 
 {
    if ($arIBlock["ID"]==$IBlockId) 
    {
      $IBlockName = $arIBlock["NAME"];
      //echo $IBlockName.' - Если здесь корректный результат, имя инфоблока узнали по ID <br>';
    }  
 }
}
//-----------------------------


$IBlockName2 = $IBlockName; //Сохраняем переменную, т.к. дальше она нам понадобится без дальнейших преобразований


$HitCounter = 0;
//Проверяем, есть ли в инфоблоке логов нужный раздел
$res = CIBlock::GetByID(4);
if($ar_res = $res->GetNext())
{
  $arIBTYPE = CIBlockType::GetByIDLang('test', '');
if($arIBTYPE!==false)
{
  $arFilter = Array('IBLOCK_ID'=>'4');
  $db_list = CIBlockSection::GetList($arFilter);
  while($ar_result = $db_list->GetNext())
  {
  if ($ar_result['IBLOCK_CODE'] == 'LOG') 
   {
    if ($ar_result['NAME'] == $IBlockName2) 
    {
      $HitCounter++; 
    }
    if ($HitCounter==0) 
    {
   $bs = new CIBlockSection;
   $arFields = Array
   (
   "ACTIVE" => $ACTIVE,
   "IBLOCK_SECTION_ID" => false,
   "IBLOCK_ID" => 4,
   "NAME" => $IBlockName2,
   "SORT" => $SORT,
   "DESCRIPTION" => $DESCRIPTION,
   "DESCRIPTION_TYPE" => $DESCRIPTION_TYPE
   );
   if($ID > 0)
{
  $res = $bs->Update($ID, $arFields);
}
else
{
  $ID = $bs->Add($arFields);
  $res = ($ID>0);
}

if(!$res)
  echo $bs->LAST_ERROR;
    } 
  }
  }
    $HitCounter = 0;
    echo $db_list->NavPrint($arIBTYPE["SECTION_NAME"]);
  }
}
//-----------------------------


//Узнаём ID созданного раздела в инфоблоке логов
if(CModule::IncludeModule("iblock"))
{ 
 $iblocks = GetIBlockList('test', 'LOG'); 
 while($arIBlock = $iblocks->GetNext()) 
 {
    if ($arIBlock["ID"]==4) 
    {
      echo $ar_result;
      $arIBTYPE = CIBlockType::GetByIDLang('test', LANGUAGE_ID);
      if($arIBTYPE!==false)
      {
        $arFilter = Array('IBLOCK_ID'=>4);
        $db_list = CIBlockSection::GetList(Array($by=>$order), $arFilter, true);
        $db_list->NavStart(20);
        
        while($ar_result = $db_list->GetNext())
        { 
          if ($ar_result['NAME']==$IBlockName) 
          {
            $LogSectionId = $ar_result['ID'];
          }
        }
      }
    }
  }
} 
//-----------------------------


//Поиск имени раздела изменяемого элемента
$res = CIBlockSection::GetByID($IBLOCK_SECTION);
if($ar_res = $res->GetNext())
$IBLOCK_SECTION_NAME = $ar_res['NAME'];
//-----------------------------


//Поиск имени изменяемого элемента
$res = CIBlockElement::GetByID($ElementId);
if($ar_res = $res->GetNext())
  $ElementName = $ar_res['NAME'];
//-----------------------------


$DETAIL_TEXT = $IBlockName2.' => '.$IBLOCK_SECTION_NAME.' => '.$ElementName.' Дата создания:'.$ElementData;


//Создаём элемент в инфоблоке LOG
$el = new CIBlockElement;
if ($ElementId!='') 
{
$arLoadProductArray = Array(
  "MODIFIED_BY"    => $USER->GetID(), // элемент изменен текущим пользователем
  "IBLOCK_SECTION_ID" => $LogSectionId,      
  "IBLOCK_ID"      => 4,
  "PROPERTY_VALUES"=> $PROP,
  "NAME"           => $ElementId,
  "ACTIVE"         => "Y",            // активен
  "PREVIEW_TEXT"   => '',
  "DETAIL_TEXT"    => $DETAIL_TEXT,
  "DETAIL_PICTURE" => CFile::MakeFileArray($_SERVER["DOCUMENT_ROOT"]."/image.gif")
  );
if($PRODUCT_ID = $el->Add($arLoadProductArray))
  echo "New ID: ".$PRODUCT_ID;
else
echo "Error: ".$el->LAST_ERROR;  
}  
//-----------------------------


?>

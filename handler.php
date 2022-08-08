<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");?>
<?
//echo '<pre>'; print_r(); echo '</pre>';
define('LOG_FILENAME', 'E:\openserver\domains\mysite\zadanie-stazhirovka\log.txt');



//Обработчик добавления записей
AddEventHandler("iblock", "OnAfterIBlockElementAdd", Array("AddHandler", "OnAfterIBlockElementAddHandler"));
class AddHandler
{
    public function OnAfterIBlockElementAddHandler(&$arFields)
    {
        if($arFields["ID"]>0 && $arFields["IBLOCK_ID"]!=4) 
        {
             AddMessage2Log("Запись с кодом ".$arFields["ID"]." добавлена.");
        }
        else
        {
             AddMessage2Log("Ошибка добавления записи (".$arFields["RESULT_MESSAGE"].").");    
        }
        return $NewElementID;
    }
}
//-------------------------------------


//Обработчик изменения записей. У меня задумка в обрабочике получать ID элемента из $arFields['ID']. Но эта переменная живёт только до конца функции, а обратиться к объекту через $this не удаётся.
AddEventHandler("iblock", "OnAfterIBlockElementUpdate", Array("UpdateHandler", "OnAfterIBlockElementUpdateHandler"));
class UpdateHandler
{
    public $UpdateElement;

    public function OnAfterIBlockElementUpdateHandler(&$arFields)
    {
        if($arFields["RESULT"] && $arFields["IBLOCK_ID"]!=4)
        {   
            AddMessage2Log("Запись с кодом ".$arFields["ID"]." изменена.");
            $UpdateElement = $arFields['ID'];
            //$this->UpdateElement = $UpdateElement; //Пытаюсь обратиться к объекту класса, выдаёт ошибку и говорит подробности смотреть в settings.php 
            //echo '<pre>'; print_r($UpdateElement); echo '</pre>';
        }
        else
        {
            AddMessage2Log("Ошибка изменения записи ".$arFields["ID"]." (".$arFields["RESULT_MESSAGE"].").");
        }
    } 
}
//----------------------------------------





//Интерфейс для выбора инфоблока
if(CModule::IncludeModule("iblock"))
{
   $iblocks = GetIBlockList(""); 
   echo '<form method=POST>';
   echo 'Выберите инфоблок <select name="infoblock"> '; 
   while($arIBlock = $iblocks->GetNext()) 
   {
      echo '<option>'.$arIBlock["NAME"].'<br>'; echo '</option>';    
   }
   echo '</select>';
   echo '<br><br><input type="submit" value="Выбрать инфоблок">';
   echo '</form>';
}
$infoblock_name = $_POST['infoblock'];
echo 'Название:'.$infoblock_name;

if(CModule::IncludeModule("iblock"))
{ 
 $iblocks = GetIBlockList(""); 
 while($arIBlock = $iblocks->GetNext()) 
 {
    if ($arIBlock["NAME"]==$infoblock_name) 
    {
      $infoblock_ID = $arIBlock["ID"];
      echo '<br>ID: '.$infoblock_ID.'<br>';
    }  
 }
}
//--------------------------------------



//Изменение элемента инфоблока
$el = new CIBlockElement;
$arLoadProductArray = Array(
  "MODIFIED_BY"    => $USER->GetID(), 
  "IBLOCK_SECTION" => false,         
  "PROPERTY_VALUES"=> $PROP,
  "NAME"           => "NEWEWEWEW",
  "ACTIVE"         => "Y",            
  "PREVIEW_TEXT"   => "текст для списка элементов",
  "DETAIL_TEXT"    => "текст для детального просмотра",
  "DETAIL_PICTURE" => CFile::MakeFileArray($_SERVER["DOCUMENT_ROOT"]."/image.gif")
  );
$PRODUCT_ID = 367;  
$res = $el->Update($PRODUCT_ID, $arLoadProductArray);
//-----------------------------

?>

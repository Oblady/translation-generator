<?php
require_once(__DIR__.'/../../autoload.php');

use Symfony\Component\Yaml\Yaml;

$config = Yaml::parse(file_get_contents("translator_config.yml"));
$result=[];
$open_tag = $config['open_tag'];
$close_tag = $config['close_tag'];
$filter=$config['filter'];
$filter_separator = $config['filter_separator'];

if (array_key_exists(1, $argv)) {
    $output = $argv[1];
}
else {
    $output = $config['output'];
}
$timeoutLimit = $config['timeout'];
$filesToAnalyse = $config['files'];
foreach ($filesToAnalyse as $path=>$fileNames) {
    //var_dump($path,$fileNames);
    
    foreach ($fileNames as $fileName) {
    //Lecture du fichier
        echo $fileName."\n";
        $firstTagPos = 0;
        $timeout=0;
        $fileToTranslate = file_get_contents($path.'/'.$fileName);
        //Recherche de l'ouverture de la balise
        $firstTagPos = strpos( $fileToTranslate,$open_tag, $firstTagPos);
        while ($firstTagPos!=false && $timeout < $timeoutLimit) {
            //Recherche de la fermeture de la balise
            $lastTagPos = strpos($fileToTranslate,$close_tag,$firstTagPos);
            if (!$lastTagPos) {
                
            }
            $tmp = substr($fileToTranslate,$firstTagPos+2,$lastTagPos-$firstTagPos-2);
            $comment = $tmp;
            //Vérification du filter translate
            if (strpos($tmp,$filter_separator)) {
                $tagFilterPos = strpos($tmp,$filter_separator);
                
                if (strpos(substr($tmp,$tagFilterPos,strlen($tmp)),$filter)) {
                    $tmp2 = trim((substr($tmp,0,strpos($tmp,$filter_separator))));
                    if (substr($tmp2,0,1) == "'") {
                        $tmp2 = substr($tmp2,1,strlen($tmp2)-1);
                        $tmp2 = substr($tmp2,0,strlen($tmp2)-1);
                    }
                    if (substr($tmp2,0,1) == '"') {
                        $tmp2 = substr($tmp2,1,strlen($tmp2)-1);
                        $tmp2 = substr($tmp2,0,strlen($tmp2)-1);
                    }
                    if (!in_array($tmp2,$result )) {
                        
                        $result[]='{% trans "briseVueConfig" %}'.$tmp2."{% endtrans %}"."{#".$comment."#}"."\n";
                    }
                }
            }
            $timeout++;
            $firstTagPos = strpos( $fileToTranslate,"{{", $lastTagPos);
        }
    }
}
file_put_contents($output,$result);





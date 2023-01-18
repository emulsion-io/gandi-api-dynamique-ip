<?php 

/**
 * Fabrice Simonet - Emulsion.io
 * 
 * Gandi API pour modifier l'IP d'une entrée DNS.
 * LiveDNS API
 * https://api.gandi.net/docs/livedns/
 * 
 * Renommer le fichier config.ini.dev en config.ini et modifier les valeurs.
 * 
 */

$config = parse_ini_file("config.ini", true);

// ping un service pour récupérer l'IP publique du serveur local
$ip = file_get_contents("http://ipecho.net/plain");

$curl = curl_init();

$domaines = array_map(function($ndd, $name) {
   return ['ndd' => $ndd, 'name' => $name];
}, $config['domaine']['ndd'], $config['domaine']['name']);

foreach ($domaines as $key => $domaine) {

   curl_setopt_array($curl, array(
      CURLOPT_URL => "https://api.gandi.net/v5/livedns/domains/{$domaine['ndd']}/records/{$domaine['name']}",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "PUT",
      CURLOPT_POSTFIELDS => "{\"items\":[{\"rrset_type\":\"A\",\"rrset_values\":[\"{$ip}\"]}]}",
      CURLOPT_HTTPHEADER => array(
         "Authorization: Apikey {$config['api']['key']}",
         "content-type: application/json"
      ),
   ));

   $response = curl_exec($curl);
   $err = curl_error($curl);

   curl_close($curl);

   if($config['app']['debug']) {
      var_dump($config);
      if ($err) {
         echo "cURL Error #:" . $err;
      } else {
         echo $response;
      }
   }

}

?>
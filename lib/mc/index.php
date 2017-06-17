<?php
include('src/MailChimp.php'); 
include('src/Batch.php'); 
include('src/Webhook.php'); 

//use \DrewM\MailChimp\MailChimp;

//$is_ok = http_response('https://us16.api.mailchimp.com/3.0/?apikey=1969d02ecd49110d87964dbce50d2251-us16'); // returns true only if http response code < 400 
//print_r($is_ok);
 
$MailChimp = new MailChimp('1969d02ecd49110d87964dbce50d2251-us16');
$mc_account = $MailChimp->apiKeyValidate();

print_r($mc_account);

//$result = $MailChimp->get('lists');

//print_r($result);
if ($_GET['action']=='submit'){
	$list_id = 'e924f26070';

$result = $MailChimp->post("lists/$list_id/members", [
				'email_address' => 'rodel0421@yahoo.com',
				'status'        => 'subscribed',
			]);
echo "<pre>";
print_r($result);
echo "</pre>";
	//e924f26070
}


function http_response($url, $status = null, $wait = 3) 
{ 
        $time = microtime(true); 
        $expire = $time + $wait; 

        // we fork the process so we don't have to wait for a timeout 
       
		if ($url!="") { 
            // we are the parent 
			
            $ch = curl_init(); 
            curl_setopt($ch, CURLOPT_URL, $url); 
            curl_setopt($ch, CURLOPT_HEADER, false); 
            //curl_setopt($ch, CURLOPT_NOBODY, TRUE); // remove body 
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); 
            $head = curl_exec($ch); 
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE); 
			//print_r($ch);
			
			$someArray = json_decode($head, true);
			
			 
			
			
			echo "<pre>";
			
			print_r($someArray); 
		
			echo "</pre>";
			//print_r($httpCode);
            curl_close($ch); 
            
            if(!$head) 
            { 
                return FALSE; 
            } 
            
            if($status === null) 
            { 
                if($httpCode < 400) 
                { 
                    return TRUE; 
                } 
                else 
                { 
                    return FALSE; 
                } 
            }elseif($status == $httpCode) 
            { 
               // return  $httpCode; 
            } 
            
           //return  $httpCode; 
     
        } 
    } 

?>
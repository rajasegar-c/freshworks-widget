<?php

/**
* Class Name: Fresh_Desk_Plugin_Api
* Fresdesk Api class
* Adding functionality to create ticket.
*/
class Fresh_Desk_Plugin_Api{

	private $apikey, $domain_url, $response, $response_status;

	/**
	* Create a new instance.
	* @param $apikey, $domain_url
	* @return void
	*/
	public function __construct($apikey, $domain_url){
		$this->apikey = $apikey;
		$this->domain_url = $domain_url;
	}	

	/**
	* Function Name: create_ticket
	* @param $email, $subject, $description
	* @return int
	*/
	function create_ticket($email,$subject,$description){
		$datafields =  array (
     									"helpdesk_ticket" =>array("subject"=>$subject,"description_html"=>$description,"email"=>$email)
     								);
 		$jsondata= json_encode($datafields);
 		$this->make_request($this->domain_url."/helpdesk/tickets.json",$jsondata);
 		if($this->response_status!= 200 || empty( $this->response )){
 			return -1;
 		}
 		return $this->response;
	}

	/**
	* Function Name: make_request
	* @param $requestUri, $payload
	* @return void
	*/
	private function make_request($requestUri,$payload){
		$ch = curl_init ($requestUri);
 		$header[] = "Content-type: application/json";
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS,$payload);
		$user_pwd = $this->auth_method();
		// echo "value:".$user_pwd."##";
		curl_setopt($ch, CURLOPT_USERPWD,$user_pwd);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_HEADER, false);
		// receive server response ...
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		$server_output = curl_exec ($ch);

		$fd_response = json_decode($server_output);

		$this->response_status = curl_getinfo($ch,CURLINFO_HTTP_CODE);
		$this->response = isset($fd_response->{'helpdesk_ticket'}->{'display_id'}) ? $fd_response->{'helpdesk_ticket'}->{'display_id'} : '';
		curl_close ($ch);
	}

	/**
	* Function Name: auth_method
	* @return string
	*/
	private function auth_method(){
		return $this->apikey.":X";
	}


}
?>
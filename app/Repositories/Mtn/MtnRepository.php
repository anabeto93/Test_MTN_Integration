<?php

namespace App\Repositories\Mtn;
use Carbon\Carbon;
use DOMDocument;
use Illuminate\Support\Facades\Log;
use SoapClient;

class MtnRepository implements MtnRepositoryContract 
{
    public $url,$debit_body,$spId,$password,$msisdn;

    public function __construct()
    {
        $this->url = env('MTN_PAYMENT_URL');
        $this->password = env('MTN_SP_PASSWORD');
        $this->spId = env('MTN_SP_ID');
        $this->debit_body = $this->getDebit();
    }

    public function transact($transaction)
    {
        Log::info('This is what Repository has received');
        //Log::debug($transaction);
        // TODO: Implement transact() method.
        Log::info('The XML Debig request is ');
        //Log::debug($this->debit_body);

        //Log::debug($this->getFunctions());
        $this->msisdn = $transaction['fld_002'];
        Log::info('The MSISDN '.$transaction['fld_002']);

        return $this->postRequest($this->getDebit());
    }

    public function getDebit()
    {
        $timestamp = implode("",explode('-',explode(' ',Carbon::now())[0]));
        $pwd = base64_encode(hash('sha256', $this->spId.''.$this->password.''.$timestamp));
        $msisdn = $this->msisdn;
        Log::info('Received MSIDN '.$msisdn);
        Log::info('The decoded password is');
        Log::debug(base64_decode($pwd));

        /*return "<soapenv:Envelope xmlns:soapenv=\"http://schemas.xmlsoap.org/soap/envelope/\" xmlns:b2b=\"http://b2b.mobilemoney.mtn.zm_v1.0\">
                    <soapenv:Header>        
                        <RequestSOAPHeader xmlns=\"http://www.huawei.com.cn/schema/common/v2_1\">           
                        <spId>35000001</spId>           
                        <spPassword>".$pwd."</spPassword> 
                        <bundleID>256000039</bundleID>          
                        <serviceId>35000001000035</serviceId>           
                        <timeStamp>".$timestamp."</timeStamp> 
                        </RequestSOAPHeader>     
                    </soapenv:Header>     
                    <soapenv:Body> 
                         <b2b:processRequest>           
                             <serviceId>200</serviceId>           
                             <parameter>              
                                <name>DueAmount</name>              
                                <value>0.1</value>           
                             </parameter>          
                             <parameter>             
                               <name>MSISDNNum</name>             
                               <value>".$msisdn."</value>          
                             </parameter>         
                             <parameter>             
                                <name>ProcessingNumber</name>             
                                <value>555</value>          
                             </parameter>          
                             <parameter>              
                                <name>serviceId</name>              
                                <value>200</value>           
                             </parameter>           
                             <parameter>             
                                <name>AcctRef</name>              
                                <value>112233</value>           
                             </parameter>           
                             <parameter>             
                                <name>AcctBalance</name>              
                                <value>555</value>           
                             </parameter>          
                             <parameter>             
                                <name>MinDueAmount</name>            
                                <value>121212</value>           
                             </parameter>           
                             <parameter>             
                                <name>Narration</name>              
                                <value>121212</value>         
                             </parameter>         
                             <parameter>              
                                <name>PrefLang</name>              
                                <value>121212121</value>           
                             </parameter>           
                             <parameter>              
                                <name>OpCoID</name>             
                                <value>0</value>          
                             </parameter>           
                             <parameter>             
                                <name>CurrCode</name>           
                                <value>GHS</value>           
                             </parameter> 
                         </b2b:processRequest>     
                    </soapenv:Body>  
                </soapenv:Envelope>";*/

    }

    function getFunctions()
    {
        $client = new SoapClient($this->url);
        $response = $client->__getFunctions();
        return $response;
    }

    function postRequest($request)
    {
        $curl = curl_init($this->url);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, ["cache-control: no-cache", "content-type: text/xml",]);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $request);

        xml_parse_into_struct(xml_parser_create(), $request, $value, $index);

        $errors = curl_error($curl);
        if ($errors <> false){
            Log::info('The Soap Fault Errors');
            Log::debug($errors);
            return $errors;
        } else {
            $response = curl_exec($curl);
            xml_parse_into_struct(xml_parser_create(), $response, $data, $index);
            Log::info('the data');
            Log::debug($data);
        }
    }

    function createXML()
    {
        $xml = new DOMDocument('1.0', 'UTF-8');

        $xml->formatOutput = true;

        $soap = $xml->createElement("soapenv:Envelope");
        $soap->setAttribute("xmlns:soapenv", "http://schemas.xmlsoap.org/soap/envelope/");
        $soap->setAttribute("xmlns:b2b", "http://b2b.mobilemoney.mtn.zm_v1.0");

//create the header
        $header = $xml->createElement("soapenv:Header");

//request_header
        $req_hd = $xml->createElement("RequestSOAPHeader");
        $req_hd->setAttribute("xmlns", "http://www.huawei.com.cn/schema/common/v2_1");

//spId for request_header
        $spId = $xml->createElement("spId", "...");//spId in here

//spPassword
        $spPassword = $xml->createElement("spPassword", "...pass...");

        $req_hd->appendChild($spId);
        $req_hd->appendChild($spPassword);

        $header_props = [
            array('bundleId', '..'),
            array('serviceId', '..'),
            array('timeStamp', '..'),
        ];

        foreach ($header_props as $prop) {
            $temp = $xml->createElement($prop[0], $prop[1]);

            $req_hd->appendChild($temp);
        }

        $header->appendChild($req_hd);


        $soap->appendChild($header);

        $params = $this->setParams();

        $body = $this->getRequestBody($xml,$params,"debit");

//append body to soapEnv
        $soap->appendChild($body);

        $xml->appendChild($soap);

        return $xml->saveXML();

    }

    function setParams ($transaction=[]) {

        if(empty($transaction)) {//set the default values
            $transaction = [];

            $transaction['DueAmount'] = "1";
            $transaction['MSISDNNum'] = "0203833803";
            $transaction['ProcessingNumber'] = "555";
            $transaction['serviceId'] = "200";
            $transaction['AcctRef'] = "112233";
            $transaction['AcctBalance'] = "555";
            $transaction['MinDueAmount'] = "121212";
            $transaction['Narration'] = "121212";
            $transaction['PrefLang'] = "121212121";
            $transaction['OpCoID'] = "0";
            $transaction['CurrCode'] = "GHS";
        }

        $b2b_props = [
            array("DueAmount", $transaction['DueAmount']), //amount due
            array("MSISDNNum", $transaction['MSISDNNum']),  //pan
            array("ProcessingNumber", $transaction['ProcessingNumber']),
            array("serviceId", $transaction['serviceId']),
            array("AcctRef", $transaction['AcctRef']),
            array("AcctBalance", $transaction['AcctBalance']),
            array("MinDueAmount", $transaction['MinDueAmount']),
            array("Narration", $transaction['Narration']),
            array("PrefLang", $transaction['PrefLang']),
            array("OpCoID", $transaction['OpCoID']),
            array("CurrCode", $transaction['CurrCode']), //for Ghana Cedis
        ];

        return $b2b_props;
    }

    function getRequestBody ($xml,$parameters, $type="debit") {
        //echo "the type received is ".gettype($xml);
        switch($type) {
            case "debit" :
                $body = $this->getDebitBody($xml, $parameters);
                break;
            case "credit":
                $body = $this->getCreditBody($xml, $parameters);
                break;
            default:
                $body = $this->getDebitBody($xml, $parameters);
                break;
        }

        return $body;
    }

    function getCreditBody($xml, $b2b_parameters) {
        $body = "";

        return $body;
    }

    function getDebitBody($xml,$b2b_parameters) {
        //echo "It is a debit";
        //create the body
        $body = $xml->createElement("soapenv:Body");
        $bd_b2b = $xml->createElement("b2b:processRequest");

        $srv_id = $xml->createElement("serviceId", "200");
        $bd_b2b->appendChild($srv_id);

        foreach ($b2b_parameters as $prop) {
            $temp = $xml->createElement('parameter');
            $name = $xml->createElement('name', $prop[0]);
            $value = $xml->createElement('value', $prop[1]);

            $temp->appendChild($name);
            $temp->appendChild($value);

            $bd_b2b->appendChild($temp);
        }

        $body->appendChild($bd_b2b);

        return $body;
    }
}


